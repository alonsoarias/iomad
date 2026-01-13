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
 * Export dean applications to XLSX.
 *
 * Allows deans to download an Excel file with applicant information,
 * statuses, and observations for their assigned faculties.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/excellib.class.php');

require_login();

$context = context_system::instance();

// Check dean capability.
$isdean = has_capability('local/jobboard:reviewprofiles', $context) ||
          has_capability('local/jobboard:approveprofile', $context);

if (!$isdean) {
    throw new required_capability_exception($context, 'local/jobboard:reviewprofiles', 'nopermissions', '');
}

// Get dean's assigned faculties.
$deanfaculties = $DB->get_records_sql(
    "SELECT fr.facultyid, f.code as facultycode, f.name as facultyname
       FROM {local_jobboard_faculty_reviewer} fr
       JOIN {local_jobboard_faculty} f ON f.id = fr.facultyid
      WHERE fr.userid = :userid AND fr.status = 'active'",
    ['userid' => $USER->id]
);

if (empty($deanfaculties)) {
    throw new moodle_exception('nofacultyassignment', 'local_jobboard');
}

// Build patterns for dean's faculties.
$codepatterns = [];
$params = [];
$i = 0;
foreach ($deanfaculties as $fac) {
    $paramname = 'faccode' . $i;
    $codepatterns[] = "v.code LIKE :{$paramname}";
    $params[$paramname] = $fac->facultycode . '%';
    $i++;
}
$patternclause = '(' . implode(' OR ', $codepatterns) . ')';

// Get all applications for dean's faculties.
$sql = "SELECT a.id, a.status, a.timecreated, a.timemodified,
               v.code as vacancy_code, v.title as vacancy_title,
               c.name as convocatoria_name,
               u.id as userid, u.firstname, u.lastname, u.email,
               u.phone1, u.phone2
          FROM {local_jobboard_application} a
          JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
          LEFT JOIN {local_jobboard_convocatoria} c ON c.id = v.convocatoriaid
          JOIN {user} u ON u.id = a.userid
         WHERE $patternclause
         ORDER BY v.code ASC, a.timecreated ASC";

$applications = $DB->get_records_sql($sql, $params);

if (empty($applications)) {
    throw new moodle_exception('noapplicationsfound', 'local_jobboard');
}

// Get workflow log comments for all applications.
$appids = array_keys($applications);
list($inappidsql, $appidparams) = $DB->get_in_or_equal($appids, SQL_PARAMS_NAMED, 'appid');

$commentsql = "SELECT wl.id, wl.applicationid, wl.oldstatus, wl.newstatus, wl.comments, wl.timechanged,
                      u.firstname as changedby_firstname, u.lastname as changedby_lastname
                 FROM {local_jobboard_workflow_log} wl
                 JOIN {user} u ON u.id = wl.changedby
                WHERE wl.applicationid $inappidsql
                ORDER BY wl.timechanged DESC";
$allcomments = $DB->get_records_sql($commentsql, $appidparams);

// Group comments by application, keep latest.
$latestcomments = [];
$allcommentsbyapp = [];
foreach ($allcomments as $comment) {
    if (!isset($latestcomments[$comment->applicationid])) {
        $latestcomments[$comment->applicationid] = $comment;
    }
    if (!isset($allcommentsbyapp[$comment->applicationid])) {
        $allcommentsbyapp[$comment->applicationid] = [];
    }
    $allcommentsbyapp[$comment->applicationid][] = $comment;
}

// Create the workbook.
$filename = 'postulantes_' . userdate(time(), '%Y%m%d_%H%M') . '.xlsx';
$workbook = new MoodleExcelWorkbook($filename);

// Create worksheet.
$worksheet = $workbook->add_worksheet(get_string('applications', 'local_jobboard'));

// Define formats.
$formatheader = $workbook->add_format([
    'bold' => 1,
    'bg_color' => '#1b9e88',
    'color' => 'white',
    'align' => 'center',
    'border' => 1,
]);

$formatnormal = $workbook->add_format([
    'border' => 1,
    'text_wrap' => true,
]);

$formatapproved = $workbook->add_format([
    'border' => 1,
    'bg_color' => '#d4edda',
]);

$formatrejected = $workbook->add_format([
    'border' => 1,
    'bg_color' => '#f8d7da',
]);

$formatpending = $workbook->add_format([
    'border' => 1,
    'bg_color' => '#fff3cd',
]);

// Set column widths.
$worksheet->set_column(0, 0, 8);   // ID
$worksheet->set_column(1, 1, 20);  // Vacancy Code
$worksheet->set_column(2, 2, 35);  // Vacancy Title
$worksheet->set_column(3, 3, 20);  // Convocatoria
$worksheet->set_column(4, 4, 20);  // First Name
$worksheet->set_column(5, 5, 20);  // Last Name
$worksheet->set_column(6, 6, 30);  // Email
$worksheet->set_column(7, 7, 15);  // Phone
$worksheet->set_column(8, 8, 15);  // Status
$worksheet->set_column(9, 9, 20);  // Status Label
$worksheet->set_column(10, 10, 20); // Application Date
$worksheet->set_column(11, 11, 20); // Last Update
$worksheet->set_column(12, 12, 30); // Last Reviewer
$worksheet->set_column(13, 13, 50); // Observations

// Write headers.
$headers = [
    get_string('applicationid', 'local_jobboard'),
    get_string('vacancycode', 'local_jobboard'),
    get_string('vacancy', 'local_jobboard'),
    get_string('convocatoria', 'local_jobboard'),
    get_string('firstname'),
    get_string('lastname'),
    get_string('email'),
    get_string('phone'),
    get_string('status', 'local_jobboard'),
    get_string('statuslabel', 'local_jobboard'),
    get_string('applicationdate', 'local_jobboard'),
    get_string('lastupdate', 'local_jobboard'),
    get_string('lastreviewer', 'local_jobboard'),
    get_string('observations', 'local_jobboard'),
];

$col = 0;
foreach ($headers as $header) {
    $worksheet->write_string(0, $col, $header, $formatheader);
    $col++;
}

// Status labels map.
$statuslabels = [
    'draft' => get_string('status_draft', 'local_jobboard'),
    'submitted' => get_string('status_submitted', 'local_jobboard'),
    'pending_dean_review' => get_string('status_pending_dean_review', 'local_jobboard'),
    'dean_approved' => get_string('status_dean_approved', 'local_jobboard'),
    'dean_rejected' => get_string('status_dean_rejected', 'local_jobboard'),
    'pending_hr_validation' => get_string('status_pending_hr_validation', 'local_jobboard'),
    'hr_validated' => get_string('status_hr_validated', 'local_jobboard'),
    'hr_rejected' => get_string('status_hr_rejected', 'local_jobboard'),
    'docs_validated' => get_string('status_docs_validated', 'local_jobboard'),
    'docs_rejected' => get_string('status_docs_rejected', 'local_jobboard'),
    'selected' => get_string('status_selected', 'local_jobboard'),
    'rejected' => get_string('status_rejected', 'local_jobboard'),
];

// Write application data.
$row = 1;
foreach ($applications as $app) {
    // Determine format based on status.
    $format = $formatnormal;
    if (in_array($app->status, ['dean_approved', 'hr_validated', 'docs_validated', 'selected'])) {
        $format = $formatapproved;
    } else if (in_array($app->status, ['dean_rejected', 'hr_rejected', 'docs_rejected', 'rejected'])) {
        $format = $formatrejected;
    } else if (in_array($app->status, ['submitted', 'pending_dean_review', 'pending_hr_validation'])) {
        $format = $formatpending;
    }

    // Get status label.
    $statuslabel = isset($statuslabels[$app->status]) ? $statuslabels[$app->status] : $app->status;

    // Get latest comment/observation.
    $observation = '';
    $lastreviewer = '';
    if (isset($latestcomments[$app->id])) {
        $comment = $latestcomments[$app->id];
        if (!empty($comment->comments)) {
            $observation = $comment->comments;
        }
        $lastreviewer = $comment->changedby_firstname . ' ' . $comment->changedby_lastname .
                       ' (' . userdate($comment->timechanged, get_string('strftimedatetime', 'langconfig')) . ')';
    }

    // Write row data.
    $worksheet->write_number($row, 0, $app->id, $format);
    $worksheet->write_string($row, 1, $app->vacancy_code ?? '', $format);
    $worksheet->write_string($row, 2, $app->vacancy_title ?? '', $format);
    $worksheet->write_string($row, 3, $app->convocatoria_name ?? '', $format);
    $worksheet->write_string($row, 4, $app->firstname ?? '', $format);
    $worksheet->write_string($row, 5, $app->lastname ?? '', $format);
    $worksheet->write_string($row, 6, $app->email ?? '', $format);
    $worksheet->write_string($row, 7, $app->phone1 ?? $app->phone2 ?? '', $format);
    $worksheet->write_string($row, 8, $app->status ?? '', $format);
    $worksheet->write_string($row, 9, $statuslabel, $format);
    $worksheet->write_string($row, 10, userdate($app->timecreated, get_string('strftimedatetime', 'langconfig')), $format);
    $worksheet->write_string($row, 11, $app->timemodified ? userdate($app->timemodified, get_string('strftimedatetime', 'langconfig')) : '', $format);
    $worksheet->write_string($row, 12, $lastreviewer, $format);
    $worksheet->write_string($row, 13, $observation, $format);

    $row++;
}

// Close the workbook and send to browser.
$workbook->close();
exit;
