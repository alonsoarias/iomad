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

// Require review capability - allow both document reviewers and deans.
$isreviewer = has_capability('local/jobboard:reviewdocuments', $context);
$isdean = has_capability('local/jobboard:reviewprofiles', $context) || has_capability('local/jobboard:approveprofile', $context);

if (!$isreviewer && !$isdean) {
    throw new required_capability_exception($context, 'local/jobboard:reviewdocuments', 'nopermissions', '');
}

// Filter parameters.
$status = optional_param('status', '', PARAM_ALPHA);
$vacancyid = optional_param('vacancy', 0, PARAM_INT);
$priority = optional_param('priority', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

// Set up page.
$PAGE->set_pagelayout('standard');
$PAGE->activityheader->disable();
$PAGE->set_title(get_string('myreviews', 'local_jobboard'));
$PAGE->set_heading(get_string('myreviews', 'local_jobboard'));

// Set up breadcrumbs via Moodle's native navbar.
$PAGE->navbar->add(get_string('dashboard', 'local_jobboard'),
    new moodle_url('/local/jobboard/index.php'));
$PAGE->navbar->add(get_string('myreviews', 'local_jobboard'));

// Build query for applications based on role (reviewer vs dean).
$params = [];
$whereclauses = [];
$deanfaculties = [];

if ($isdean && !$isreviewer) {
    // Dean: show applications from vacancies in their assigned faculties.
    $deanfaculties = $DB->get_records_sql(
        "SELECT fr.facultyid, f.code as facultycode
           FROM {local_jobboard_faculty_reviewer} fr
           JOIN {local_jobboard_faculty} f ON f.id = fr.facultyid
          WHERE fr.userid = :userid AND fr.status = 'active'",
        ['userid' => $USER->id]
    );

    if (!empty($deanfaculties)) {
        // Build vacancy code patterns for dean's faculties.
        $codepatterns = [];
        $i = 0;
        foreach ($deanfaculties as $fac) {
            $paramname = 'faccode' . $i;
            $codepatterns[] = "v.code LIKE :{$paramname}";
            $params[$paramname] = $fac->facultycode . '%';
            $i++;
        }
        $whereclauses[] = '(' . implode(' OR ', $codepatterns) . ')';

        // Dean can see: pending_dean_review (to review), dean_approved, dean_rejected (reviewed by them).
        // Also submitted for legacy/transition support.
        $deanstatuses = ['submitted', 'pending_dean_review', 'dean_approved', 'dean_rejected', 'pending_hr_validation'];
        list($instatussql, $statusparams) = $DB->get_in_or_equal($deanstatuses, SQL_PARAMS_NAMED, 'dstatus');
        $whereclauses[] = "a.status $instatussql";
        $params = array_merge($params, $statusparams);
    } else {
        // Dean with no faculty assignments - show nothing.
        $whereclauses[] = '1=0';
    }

    // Dean stats: count applications they've approved/rejected.
    $mystats = ['validated' => 0, 'rejected' => 0, 'avg_time_hours' => 0];
    $myworkload = 0;
    if (!empty($deanfaculties)) {
        // Build patterns for stats queries.
        $statparams = ['deanid' => $USER->id];
        $statpatterns = [];
        $i = 0;
        foreach ($deanfaculties as $fac) {
            $paramname = 'sc' . $i;
            $statpatterns[] = "v.code LIKE :{$paramname}";
            $statparams[$paramname] = $fac->facultycode . '%';
            $i++;
        }
        $patternclause = '(' . implode(' OR ', $statpatterns) . ')';

        // Count applications approved by this dean (logged in workflow).
        $mystats['validated'] = (int) $DB->count_records_sql(
            "SELECT COUNT(DISTINCT wl.applicationid)
               FROM {local_jobboard_workflow_log} wl
               JOIN {local_jobboard_application} a ON a.id = wl.applicationid
               JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
              WHERE wl.changedby = :deanid
                AND wl.newstatus IN ('dean_approved', 'pending_hr_validation')
                AND $patternclause",
            $statparams
        );

        // Count applications rejected by this dean.
        $statparams2 = $statparams;
        $mystats['rejected'] = (int) $DB->count_records_sql(
            "SELECT COUNT(DISTINCT wl.applicationid)
               FROM {local_jobboard_workflow_log} wl
               JOIN {local_jobboard_application} a ON a.id = wl.applicationid
               JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
              WHERE wl.changedby = :deanid
                AND wl.newstatus = 'dean_rejected'
                AND $patternclause",
            $statparams2
        );

        // Count pending applications in dean's faculties.
        $countparams = [];
        $countpatterns = [];
        $i = 0;
        foreach ($deanfaculties as $fac) {
            $paramname = 'fc' . $i;
            $countpatterns[] = "v.code LIKE :{$paramname}";
            $countparams[$paramname] = $fac->facultycode . '%';
            $i++;
        }
        $myworkload = $DB->count_records_sql(
            "SELECT COUNT(DISTINCT a.id)
               FROM {local_jobboard_application} a
               JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
              WHERE (" . implode(' OR ', $countpatterns) . ")
                AND a.status IN ('submitted', 'pending_dean_review')",
            $countparams
        );
    }
} else {
    // Reviewer: show applications assigned to them.
    $mystats = reviewer::get_reviewer_stats($USER->id);
    $myworkload = reviewer::get_reviewer_workload($USER->id);

    $params['reviewerid'] = $USER->id;
    $whereclauses[] = 'a.reviewerid = :reviewerid';
}

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

// For deans, fetch the latest review comment for each application.
if ($isdean && !$isreviewer && !empty($applications)) {
    $appids = array_keys($applications);
    list($inappidsql, $appidparams) = $DB->get_in_or_equal($appids, SQL_PARAMS_NAMED, 'appid');

    // Get the latest workflow log entry with comments for each application.
    $commentsql = "SELECT wl.applicationid, wl.comments, wl.newstatus, wl.timechanged, wl.changedby,
                          u.firstname as reviewer_firstname, u.lastname as reviewer_lastname
                     FROM {local_jobboard_workflow_log} wl
                     JOIN {user} u ON u.id = wl.changedby
                    WHERE wl.applicationid $inappidsql
                      AND wl.comments IS NOT NULL
                      AND wl.comments != ''
                    ORDER BY wl.timechanged DESC";
    $allcomments = $DB->get_records_sql($commentsql, $appidparams);

    // Group by applicationid and keep only the latest.
    $latestcomments = [];
    foreach ($allcomments as $comment) {
        if (!isset($latestcomments[$comment->applicationid])) {
            $latestcomments[$comment->applicationid] = $comment;
        }
    }

    // Attach comments to applications.
    foreach ($applications as $appid => $app) {
        if (isset($latestcomments[$appid])) {
            $applications[$appid]->last_comment = $latestcomments[$appid]->comments;
            $applications[$appid]->last_comment_by = $latestcomments[$appid]->reviewer_firstname . ' ' .
                                                      $latestcomments[$appid]->reviewer_lastname;
            $applications[$appid]->last_comment_time = $latestcomments[$appid]->timechanged;
            $applications[$appid]->last_comment_status = $latestcomments[$appid]->newstatus;
        }
    }
}

// Get vacancies for filter.
if ($isdean && !$isreviewer && !empty($deanfaculties)) {
    // Dean: show vacancies from their assigned faculties.
    $vacfilterparams = [];
    $vacfilterpatterns = [];
    $i = 0;
    foreach ($deanfaculties as $fac) {
        $paramname = 'vfc' . $i;
        $vacfilterpatterns[] = "v.code LIKE :{$paramname}";
        $vacfilterparams[$paramname] = $fac->facultycode . '%';
        $i++;
    }
    $vacancysql = "SELECT DISTINCT v.id, v.code, v.title
                     FROM {local_jobboard_vacancy} v
                     JOIN {local_jobboard_application} a ON a.vacancyid = v.id
                    WHERE (" . implode(' OR ', $vacfilterpatterns) . ")
                      AND a.status = 'submitted'
                    ORDER BY v.code";
    $vacancies = $DB->get_records_sql($vacancysql, $vacfilterparams);
} else {
    // Reviewer: show vacancies assigned to them.
    $vacancysql = "SELECT DISTINCT v.id, v.code, v.title
                     FROM {local_jobboard_vacancy} v
                     JOIN {local_jobboard_application} a ON a.vacancyid = v.id
                    WHERE a.reviewerid = :reviewerid
                    ORDER BY v.code";
    $vacancies = $DB->get_records_sql($vacancysql, ['reviewerid' => $USER->id]);
}

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

// Add dean-specific data.
$data['isdean'] = $isdean && !$isreviewer;
$data['isreviewer'] = $isreviewer;
if ($isdean && !$isreviewer) {
    $data['deanfaculties'] = array_values(array_map(function($f) {
        return ['code' => $f->facultycode];
    }, $deanfaculties));
    $data['hasdeanfaculties'] = !empty($deanfaculties);
    // Add isdean to each assignment for template loop access.
    foreach ($data['assignments'] as &$assignment) {
        $assignment['isdean'] = true;
    }
    unset($assignment);

    // Customize stats labels for deans (profiles instead of documents).
    if (!empty($data['stats'])) {
        // Update labels for dean-specific context.
        foreach ($data['stats'] as &$stat) {
            if ($stat['icon'] === 'check-circle') {
                $stat['label'] = get_string('profilesapproved', 'local_jobboard');
            } else if ($stat['icon'] === 'times-circle') {
                $stat['label'] = get_string('profilesrejected', 'local_jobboard');
            } else if ($stat['icon'] === 'tasks') {
                $stat['label'] = get_string('pendingreview', 'local_jobboard');
            }
        }
        unset($stat);

        // Remove avg time stat for deans (not relevant).
        $data['stats'] = array_filter($data['stats'], function($s) {
            return $s['icon'] !== 'clock';
        });
        $data['stats'] = array_values($data['stats']); // Re-index.
    }
}

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_myreviews_page($data);

// Initialize filter auto-submit for all users.
$PAGE->requires->js_call_amd('local_jobboard/public_filters', 'init', [[
    'formSelector' => '.jb-filter-form',
]]);

echo $OUTPUT->footer();
