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
 * My reviews view for local_jobboard.
 *
 * Card-based layout for reviewer assignments.
 * Uses Mustache templates via renderer for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_jobboard\reviewer;

// Require review capability.
require_capability('local/jobboard:reviewdocuments', $context);

// Filter parameters.
$status = optional_param('status', '', PARAM_ALPHA);
$vacancyid = optional_param('vacancy', 0, PARAM_INT);
$priority = optional_param('priority', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

// Set up page.
$PAGE->set_title(get_string('myreviews', 'local_jobboard'));
$PAGE->set_heading(get_string('myreviews', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

// Get my stats.
$mystats = reviewer::get_reviewer_stats($USER->id);
$myworkload = reviewer::get_reviewer_workload($USER->id);

// Build query for assigned applications.
$params = ['reviewerid' => $USER->id];
$whereclauses = ['a.reviewerid = :reviewerid'];

if ($status) {
    $whereclauses[] = 'a.status = :status';
    $params['status'] = $status;
}

if ($vacancyid) {
    $whereclauses[] = 'a.vacancyid = :vacancyid';
    $params['vacancyid'] = $vacancyid;
}

$whereclause = implode(' AND ', $whereclauses);

// Priority ordering.
$orderby = 'a.timecreated ASC';
if ($priority === 'closing') {
    $orderby = 'COALESCE(v.closedate, c.enddate) ASC, a.timecreated ASC';
} else if ($priority === 'pending') {
    $orderby = "(SELECT COUNT(*) FROM {local_jobboard_document} d
                  LEFT JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                  WHERE d.applicationid = a.id AND d.issuperseded = 0
                  AND (dv.id IS NULL OR dv.status = 'pending')) DESC,
                 a.timecreated ASC";
}

// Count total.
$countsql = "SELECT COUNT(*)
               FROM {local_jobboard_application} a
               JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
               LEFT JOIN {local_jobboard_convocatoria} c ON c.id = v.convocatoriaid
              WHERE $whereclause";
$totalcount = $DB->count_records_sql($countsql, $params);

// Get assigned applications.
$sql = "SELECT a.*, v.code as vacancy_code, v.title as vacancy_title,
               COALESCE(v.closedate, c.enddate) as closedate,
               u.firstname, u.lastname, u.email,
               (SELECT COUNT(*) FROM {local_jobboard_document} d
                 WHERE d.applicationid = a.id AND d.issuperseded = 0) as total_docs,
               (SELECT COUNT(*) FROM {local_jobboard_document} d
                 JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                 WHERE d.applicationid = a.id AND d.issuperseded = 0 AND dv.status = 'approved') as validated_docs,
               (SELECT COUNT(*) FROM {local_jobboard_document} d
                 JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                 WHERE d.applicationid = a.id AND d.issuperseded = 0 AND dv.status = 'rejected') as rejected_docs,
               (SELECT COUNT(*) FROM {local_jobboard_document} d
                 LEFT JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                 WHERE d.applicationid = a.id AND d.issuperseded = 0
                 AND (dv.id IS NULL OR dv.status = 'pending')) as pending_docs
          FROM {local_jobboard_application} a
          JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
          LEFT JOIN {local_jobboard_convocatoria} c ON c.id = v.convocatoriaid
          JOIN {user} u ON u.id = a.userid
         WHERE $whereclause
         ORDER BY $orderby";

$applications = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

// Get vacancies for filter.
$vacancysql = "SELECT DISTINCT v.id, v.code, v.title
                 FROM {local_jobboard_vacancy} v
                 JOIN {local_jobboard_application} a ON a.vacancyid = v.id
                WHERE a.reviewerid = :reviewerid
                ORDER BY v.code";
$vacancies = $DB->get_records_sql($vacancysql, ['reviewerid' => $USER->id]);

// Build stats array.
$stats = [
    'pending' => $myworkload,
    'validated' => $mystats['validated'],
    'rejected' => $mystats['rejected'],
    'avg_time_hours' => $mystats['avg_time_hours'] ?? 0,
];

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the renderer.
$data = $renderer->prepare_myreviews_page_data(
    $USER->id,
    $applications,
    $totalcount,
    $stats,
    ['status' => $status, 'vacancy' => $vacancyid, 'priority' => $priority],
    $vacancies,
    $page,
    $perpage
);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_myreviews_page($data);
echo $OUTPUT->footer();
