<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Reports view for local_jobboard.
 *
 * Uses renderer + Mustache template for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Require reports capability.
require_capability('local/jobboard:viewreports', $context);

// Parameters.
$reporttype = optional_param('report', 'overview', PARAM_ALPHA);
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$datefrom = optional_param('datefrom', 0, PARAM_INT);
$dateto = optional_param('dateto', 0, PARAM_INT);
$format = optional_param('format', '', PARAM_ALPHA);

// Default date range: last 30 days.
if (!$datefrom) {
    $datefrom = strtotime('-30 days');
}
if (!$dateto) {
    $dateto = time();
}

// Set up page.
$PAGE->set_title(get_string('reports', 'local_jobboard'));
$PAGE->set_heading(get_string('reports', 'local_jobboard'));
$PAGE->set_pagelayout('report');

// Handle export.
if ($format === 'csv' || $format === 'excel' || $format === 'pdf') {
    local_jobboard_export_report($reporttype, $vacancyid, $datefrom, $dateto, $format);
    exit;
}

// Get vacancy list for filter.
$vacancies = $DB->get_records_select('local_jobboard_vacancy', '1=1', null, 'code ASC', 'id, code, title');

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data.
$data = $renderer->prepare_reports_page_data(
    $reporttype,
    $vacancyid,
    $datefrom,
    $dateto,
    $vacancies,
    $context
);

// Output page.
echo $OUTPUT->header();
echo $renderer->render_reports_page($data);
echo $OUTPUT->footer();

/**
 * Export report data.
 *
 * @param string $reporttype Report type.
 * @param int $vacancyid Vacancy ID filter.
 * @param int $datefrom Start date timestamp.
 * @param int $dateto End date timestamp.
 * @param string $format Export format.
 */
function local_jobboard_export_report(string $reporttype, int $vacancyid, int $datefrom, int $dateto, string $format): void {
    global $DB;

    $params = ['from' => $datefrom, 'to' => $dateto];
    $vacancywhere = '';
    if ($vacancyid) {
        $vacancywhere = ' AND a.vacancyid = :vacancyid';
        $params['vacancyid'] = $vacancyid;
    }

    // Get data based on report type.
    $data = [];
    $headers = [];

    switch ($reporttype) {
        case 'applications':
            $headers = ['Vacancy', 'Applicant', 'Email', 'Status', 'Date Applied'];
            $records = $DB->get_records_sql(
                "SELECT a.id, v.code, v.title, u.firstname, u.lastname, u.email, a.status, a.timecreated
                   FROM {local_jobboard_application} a
                   JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                   JOIN {user} u ON u.id = a.userid
                  WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}
                  ORDER BY a.timecreated DESC",
                $params
            );

            foreach ($records as $r) {
                $data[] = [
                    $r->code . ' - ' . $r->title,
                    $r->firstname . ' ' . $r->lastname,
                    $r->email,
                    get_string('status_' . $r->status, 'local_jobboard'),
                    userdate($r->timecreated, '%Y-%m-%d %H:%M'),
                ];
            }
            break;

        case 'documents':
            $headers = ['Application ID', 'Document Type', 'Status', 'Uploaded', 'Validated By'];
            $records = $DB->get_records_sql(
                "SELECT d.id, d.applicationid, d.documenttype, d.timecreated,
                        COALESCE(dv.status, 'pending') as docstatus, dv.validatedby
                   FROM {local_jobboard_document} d
                   LEFT JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                   JOIN {local_jobboard_application} a ON a.id = d.applicationid
                  WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}
                  ORDER BY d.timecreated DESC",
                $params
            );

            foreach ($records as $r) {
                $validatedby = '';
                if ($r->validatedby) {
                    $validator = $DB->get_record('user', ['id' => $r->validatedby]);
                    $validatedby = $validator ? fullname($validator) : '';
                }
                $data[] = [
                    $r->applicationid,
                    get_string('doctype_' . $r->documenttype, 'local_jobboard'),
                    get_string('docstatus_' . $r->docstatus, 'local_jobboard'),
                    userdate($r->timecreated, '%Y-%m-%d %H:%M'),
                    $validatedby,
                ];
            }
            break;

        case 'reviewers':
            $headers = ['Reviewer', 'Workload', 'Reviewed', 'Validated', 'Rejected'];
            $reviewers = \local_jobboard\reviewer::get_all_with_workload();

            foreach ($reviewers as $rev) {
                $data[] = [
                    fullname($rev),
                    (int) $rev->workload,
                    (int) ($rev->stats['reviewed'] ?? 0),
                    (int) ($rev->stats['validated'] ?? 0),
                    (int) ($rev->stats['rejected'] ?? 0),
                ];
            }
            break;

        case 'timeline':
            $headers = ['Date', 'Applications'];
            $sql = "SELECT DATE(FROM_UNIXTIME(a.timecreated)) as day, COUNT(*) as count
                      FROM {local_jobboard_application} a
                     WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}
                     GROUP BY DATE(FROM_UNIXTIME(a.timecreated))
                     ORDER BY day ASC";
            $daily = $DB->get_records_sql($sql, $params);

            foreach ($daily as $row) {
                $data[] = [$row->day, (int) $row->count];
            }
            break;

        default: // overview.
            $headers = ['Metric', 'Value'];
            $totalapps = $DB->count_records_sql(
                "SELECT COUNT(*) FROM {local_jobboard_application} a
                  WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}",
                $params
            );
            $selectedapps = $DB->count_records_sql(
                "SELECT COUNT(*) FROM {local_jobboard_application} a
                  WHERE a.timecreated BETWEEN :from AND :to AND a.status = 'selected' {$vacancywhere}",
                $params
            );
            $rejectedapps = $DB->count_records_sql(
                "SELECT COUNT(*) FROM {local_jobboard_application} a
                  WHERE a.timecreated BETWEEN :from AND :to AND a.status = 'rejected' {$vacancywhere}",
                $params
            );

            $data = [
                [get_string('totalapplications', 'local_jobboard'), $totalapps],
                [get_string('selected', 'local_jobboard'), $selectedapps],
                [get_string('rejected', 'local_jobboard'), $rejectedapps],
            ];
    }

    // Output based on format.
    if ($format === 'csv') {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="report_' . $reporttype . '_' . date('Y-m-d') . '.csv"');

        $output = fopen('php://output', 'w');
        fputcsv($output, $headers);
        foreach ($data as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
    } else if ($format === 'excel') {
        global $CFG;
        require_once($CFG->libdir . '/excellib.class.php');

        $filename = 'report_' . $reporttype . '_' . date('Y-m-d') . '.xlsx';
        $workbook = new MoodleExcelWorkbook($filename);
        $worksheet = $workbook->add_worksheet($reporttype);

        // Write headers.
        $col = 0;
        foreach ($headers as $header) {
            $worksheet->write(0, $col++, $header);
        }

        // Write data.
        $row = 1;
        foreach ($data as $rowdata) {
            $col = 0;
            foreach ($rowdata as $cell) {
                $worksheet->write($row, $col++, $cell);
            }
            $row++;
        }

        $workbook->close();
    } else if ($format === 'pdf') {
        global $CFG;
        require_once($CFG->libdir . '/pdflib.php');

        $filename = 'report_' . $reporttype . '_' . date('Y-m-d') . '.pdf';

        // Create PDF document.
        $pdf = new pdf('L', 'mm', 'A4', true, 'UTF-8');

        // Set document information.
        $pdf->SetCreator('Job Board');
        $pdf->SetAuthor('Job Board');
        $pdf->SetTitle(get_string('reports', 'local_jobboard') . ' - ' . $reporttype);
        $pdf->SetSubject(get_string('reports', 'local_jobboard'));

        // Set margins.
        $pdf->SetMargins(15, 15, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);

        // Set auto page breaks.
        $pdf->SetAutoPageBreak(true, 25);

        // Add a page.
        $pdf->AddPage();

        // Report title.
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, get_string('reports', 'local_jobboard') . ': ' . ucfirst($reporttype), 0, 1, 'C');
        $pdf->Ln(5);

        // Date range.
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, get_string('datefrom', 'local_jobboard') . ': ' . date('Y-m-d', $datefrom) .
            ' - ' . get_string('dateto', 'local_jobboard') . ': ' . date('Y-m-d', $dateto), 0, 1, 'C');
        $pdf->Ln(10);

        // Build HTML table.
        $html = '<table border="1" cellpadding="5">';
        $html .= '<thead><tr style="background-color: #4472C4; color: white; font-weight: bold;">';
        foreach ($headers as $header) {
            $html .= '<th>' . htmlspecialchars($header) . '</th>';
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';

        $rownum = 0;
        foreach ($data as $rowdata) {
            $bgcolor = ($rownum % 2 == 0) ? '#ffffff' : '#f2f2f2';
            $html .= '<tr style="background-color: ' . $bgcolor . ';">';
            foreach ($rowdata as $cell) {
                $html .= '<td>' . htmlspecialchars($cell) . '</td>';
            }
            $html .= '</tr>';
            $rownum++;
        }

        $html .= '</tbody></table>';

        // Write HTML table.
        $pdf->SetFont('helvetica', '', 9);
        $pdf->writeHTML($html, true, false, true, false, '');

        // Footer with generation date.
        $pdf->Ln(10);
        $pdf->SetFont('helvetica', 'I', 8);
        $pdf->Cell(0, 6, get_string('generatedon', 'local_jobboard') . ': ' . date('Y-m-d H:i:s'), 0, 1, 'R');

        // Output PDF.
        $pdf->Output($filename, 'D');
    }
}
