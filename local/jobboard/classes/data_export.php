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

declare(strict_types=1);

/**
 * Data export functionality for local_jobboard.
 *
 * Allows users to export their personal data in compliance with
 * GDPR and Colombian Habeas Data (Ley 1581/2012).
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Class for exporting user data.
 */
class data_export {

    /** @var int The user ID. */
    private int $userid;

    /** @var \stdClass The user object. */
    private \stdClass $user;

    /**
     * Constructor.
     *
     * @param int $userid The user ID.
     */
    public function __construct(int $userid) {
        global $DB;

        $this->userid = $userid;
        $this->user = $DB->get_record('user', ['id' => $userid], '*', MUST_EXIST);
    }

    /**
     * Generate JSON export of user data.
     *
     * @return string JSON encoded data.
     */
    public function export_json(): string {
        $data = $this->collect_data();
        return json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    /**
     * Generate PDF export of user data.
     *
     * @return string PDF content.
     */
    public function export_pdf(): string {
        global $CFG;
        require_once($CFG->libdir . '/pdflib.php');

        $data = $this->collect_data();

        $pdf = new \pdf();
        $pdf->SetCreator('Job Board Plugin');
        $pdf->SetAuthor($CFG->sitename ?? 'Moodle');
        $pdf->SetTitle(get_string('dataexport', 'local_jobboard'));
        $pdf->SetSubject(get_string('dataexport:personal', 'local_jobboard'));

        $pdf->SetMargins(15, 20, 15);
        $pdf->SetAutoPageBreak(true, 20);

        $pdf->AddPage();

        // Title.
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, get_string('dataexport:title', 'local_jobboard'), 0, 1, 'C');
        $pdf->Ln(5);

        // User info.
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, get_string('dataexport:userinfo', 'local_jobboard'), 0, 1);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(0, 6, get_string('fullname') . ': ' . $data['user']['fullname'], 0, 1);
        $pdf->Cell(0, 6, get_string('email') . ': ' . $data['user']['email'], 0, 1);
        $pdf->Cell(0, 6, get_string('dataexport:exportdate', 'local_jobboard') . ': ' .
            userdate(time()), 0, 1);
        $pdf->Ln(10);

        // Applications.
        if (!empty($data['applications'])) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 8, get_string('applications', 'local_jobboard') .
                ' (' . count($data['applications']) . ')', 0, 1);
            $pdf->SetFont('helvetica', '', 10);

            foreach ($data['applications'] as $app) {
                $pdf->SetFont('helvetica', 'B', 10);
                $pdf->Cell(0, 6, $app['vacancy_code'] . ' - ' . $app['vacancy_title'], 0, 1);
                $pdf->SetFont('helvetica', '', 9);
                $pdf->Cell(0, 5, get_string('status') . ': ' . $app['status'], 0, 1);
                $pdf->Cell(0, 5, get_string('date') . ': ' . $app['created'], 0, 1);

                if (!empty($app['documents'])) {
                    $pdf->Cell(0, 5, get_string('documents', 'local_jobboard') .
                        ': ' . count($app['documents']), 0, 1);
                }
                $pdf->Ln(3);
            }
            $pdf->Ln(5);
        }

        // Exemptions.
        if (!empty($data['exemptions'])) {
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->Cell(0, 8, get_string('exemptions', 'local_jobboard'), 0, 1);
            $pdf->SetFont('helvetica', '', 10);

            foreach ($data['exemptions'] as $ex) {
                $pdf->Cell(0, 5, get_string('type') . ': ' . $ex['type'], 0, 1);
                $pdf->Cell(0, 5, get_string('validfrom', 'local_jobboard') . ': ' . $ex['valid_from'], 0, 1);
                $pdf->Ln(2);
            }
            $pdf->Ln(5);
        }

        // Consent records.
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 8, get_string('dataexport:consent', 'local_jobboard'), 0, 1);
        $pdf->SetFont('helvetica', '', 10);

        foreach ($data['applications'] as $app) {
            if ($app['consent_given']) {
                $pdf->Cell(0, 5, $app['vacancy_code'] . ': ' .
                    get_string('yes') . ' (' . $app['consent_timestamp'] . ')', 0, 1);
            }
        }

        return $pdf->Output('', 'S');
    }

    /**
     * Collect all user data for export.
     *
     * @return array The collected data.
     */
    private function collect_data(): array {
        global $DB;

        $data = [
            'export_date' => userdate(time()),
            'export_format_version' => '1.0',
            'user' => [
                'id' => $this->user->id,
                'username' => $this->user->username,
                'fullname' => fullname($this->user),
                'email' => $this->user->email,
                'firstname' => $this->user->firstname,
                'lastname' => $this->user->lastname,
            ],
            'applications' => [],
            'exemptions' => [],
            'notifications' => [],
            'audit_summary' => [],
        ];

        // Applications.
        $applications = $DB->get_records('local_jobboard_application', ['userid' => $this->userid]);

        foreach ($applications as $app) {
            $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $app->vacancyid]);

            $appdata = [
                'id' => $app->id,
                'vacancy_id' => $app->vacancyid,
                'vacancy_code' => $vacancy ? $vacancy->code : 'N/A',
                'vacancy_title' => $vacancy ? $vacancy->title : 'N/A',
                'status' => $app->status,
                'digital_signature' => $app->digitalsignature,
                'cover_letter' => $app->coverletter,
                'is_iser_exemption' => (bool) $app->isexemption,
                'exemption_reason' => $app->exemptionreason,
                'consent_given' => (bool) $app->consentgiven,
                'consent_timestamp' => $app->consenttimestamp
                    ? userdate($app->consenttimestamp) : null,
                'consent_ip' => $app->consentip,
                'created' => userdate($app->timecreated),
                'modified' => $app->timemodified ? userdate($app->timemodified) : null,
                'documents' => [],
                'history' => [],
            ];

            // Documents.
            $documents = $DB->get_records('local_jobboard_document', ['applicationid' => $app->id]);
            foreach ($documents as $doc) {
                $validation = $DB->get_record('local_jobboard_doc_validation', ['documentid' => $doc->id]);

                $appdata['documents'][] = [
                    'id' => $doc->id,
                    'type' => $doc->documenttype,
                    'filename' => $doc->filename,
                    'filesize' => $doc->filesize,
                    'issue_date' => $doc->issuedate ? userdate($doc->issuedate, '%Y-%m-%d') : null,
                    'is_superseded' => (bool) $doc->issuperseded,
                    'validation_status' => $validation
                        ? ($validation->isvalid ? 'approved' : 'pending') : 'pending',
                    'uploaded' => userdate($doc->timecreated),
                ];
            }

            // Workflow history.
            $logs = $DB->get_records('local_jobboard_workflow_log', ['applicationid' => $app->id], 'timecreated ASC');
            foreach ($logs as $log) {
                $appdata['history'][] = [
                    'from_status' => $log->previousstatus,
                    'to_status' => $log->newstatus,
                    'comments' => $log->comments,
                    'timestamp' => userdate($log->timecreated),
                ];
            }

            $data['applications'][] = $appdata;
        }

        // Exemptions.
        $exemptions = $DB->get_records('local_jobboard_exemption', ['userid' => $this->userid]);
        foreach ($exemptions as $ex) {
            $data['exemptions'][] = [
                'id' => $ex->id,
                'type' => $ex->exemptiontype,
                'document_reference' => $ex->documentref,
                'exempted_documents' => json_decode($ex->exempteddocs ?? '[]', true),
                'valid_from' => userdate($ex->validfrom, '%Y-%m-%d'),
                'valid_until' => $ex->validuntil ? userdate($ex->validuntil, '%Y-%m-%d') : null,
                'notes' => $ex->notes,
                'is_revoked' => (bool) $ex->timerevoked,
                'revoke_reason' => $ex->revokereason,
                'created' => userdate($ex->timecreated),
            ];
        }

        // Notification summary (last 100).
        $notifications = $DB->get_records('local_jobboard_notification', ['userid' => $this->userid],
            'timecreated DESC', '*', 0, 100);
        foreach ($notifications as $notif) {
            $data['notifications'][] = [
                'template' => $notif->templatecode,
                'status' => $notif->status,
                'sent' => $notif->timesent ? userdate($notif->timesent) : null,
                'created' => userdate($notif->timecreated),
            ];
        }

        // Audit summary (count by action type).
        $sql = "SELECT action, COUNT(*) as count
                FROM {local_jobboard_audit}
                WHERE userid = :userid
                GROUP BY action
                ORDER BY count DESC";
        $auditcounts = $DB->get_records_sql($sql, ['userid' => $this->userid]);
        foreach ($auditcounts as $record) {
            $data['audit_summary'][] = [
                'action' => $record->action,
                'count' => (int) $record->count,
            ];
        }

        return $data;
    }

    /**
     * Get download filename.
     *
     * @param string $format Export format (json or pdf).
     * @return string The filename.
     */
    public function get_filename(string $format): string {
        $date = date('Y-m-d');
        $safename = preg_replace('/[^a-z0-9]/i', '_', $this->user->username);
        return "jobboard_export_{$safename}_{$date}.{$format}";
    }

    /**
     * Send export file to browser.
     *
     * @param string $format Export format (json or pdf).
     */
    public function download(string $format): void {
        $filename = $this->get_filename($format);

        if ($format === 'pdf') {
            $content = $this->export_pdf();
            $contenttype = 'application/pdf';
        } else {
            $content = $this->export_json();
            $contenttype = 'application/json';
        }

        // Log the export.
        audit::log('data_exported', 'user', $this->userid, ['format' => $format]);

        header('Content-Type: ' . $contenttype);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($content));
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');

        echo $content;
        exit;
    }
}
