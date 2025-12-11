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
 * Document review view for local_jobboard.
 *
 * Uses renderer + Mustache template for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\application;
use local_jobboard\document;
use local_jobboard\vacancy;
use local_jobboard\review_notifier;

// Require review capability.
require_capability('local/jobboard:reviewdocuments', $context);

// Parameters.
$applicationid = optional_param('applicationid', 0, PARAM_INT);
$documentid = optional_param('documentid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

// Page setup.
$PAGE->set_title(get_string('reviewdocuments', 'local_jobboard'));
$PAGE->set_heading(get_string('reviewdocuments', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

// Handle actions.
if ($action && confirm_sesskey()) {
    switch ($action) {
        case 'validate':
            if ($documentid) {
                $doc = new document($documentid);
                $doc->validate($USER->id);
                \core\notification::success(get_string('documentvalidated', 'local_jobboard'));
            }
            break;

        case 'reject':
            if ($documentid) {
                $reason = required_param('reason', PARAM_TEXT);
                $doc = new document($documentid);
                $doc->reject($USER->id, $reason);
                \core\notification::success(get_string('documentrejected', 'local_jobboard'));
            }
            break;

        case 'validateall':
            if ($applicationid) {
                $documents = document::get_by_application($applicationid);
                $validated = 0;
                foreach ($documents as $doc) {
                    if ($doc->status === 'pending') {
                        $doc->validate($USER->id);
                        $validated++;
                    }
                }
                \core\notification::success(get_string('documentvalidated', 'local_jobboard') . " ({$validated})");
            }
            break;

        case 'markreviewed':
            if ($applicationid) {
                $app = new application($applicationid);
                $stats = document::get_stats($applicationid);
                $observations = optional_param('observations', '', PARAM_TEXT);

                // Determine status based on document review outcome.
                if ($stats['rejected'] > 0) {
                    $newstatus = 'docs_rejected';
                } else {
                    $newstatus = 'docs_validated';
                }

                $app->update_status($newstatus, $USER->id);

                // Send consolidated email notification.
                try {
                    review_notifier::notify($applicationid, $observations);
                    \core\notification::success(get_string('reviewsubmitted_with_notification', 'local_jobboard'));
                } catch (\Exception $e) {
                    debugging('Failed to send review notification: ' . $e->getMessage(), DEBUG_DEVELOPER);
                    \core\notification::success(get_string('reviewsubmitted', 'local_jobboard'));
                }
            }
            break;
    }

    // Redirect to avoid form resubmission.
    redirect(new moodle_url('/local/jobboard/index.php', [
        'view' => 'review',
        'applicationid' => $applicationid,
        'vacancyid' => $vacancyid,
    ]));
}

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Common params.
$params = [
    'vacancyid' => $vacancyid,
    'applicationid' => $applicationid,
    'page' => $page,
    'perpage' => $perpage,
];

// If no application selected, show list of applications pending review.
if (!$applicationid) {
    // Build filter.
    $where = "a.status IN ('submitted', 'under_review')";
    $sqlparams = [];

    if ($vacancyid) {
        $where .= " AND a.vacancyid = :vacancyid";
        $sqlparams['vacancyid'] = $vacancyid;
    }

    // Multi-tenant filter.
    if (\local_jobboard_is_iomad_installed() && !has_capability('local/jobboard:viewallvacancies', $context)) {
        $usercompanyid = \local_jobboard_get_user_companyid();
        if ($usercompanyid) {
            $where .= " AND v.companyid = :companyid";
            $sqlparams['companyid'] = $usercompanyid;
        }
    }

    // Count total records for pagination.
    $countsql = "SELECT COUNT(*)
                   FROM {local_jobboard_application} a
                   JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                   JOIN {user} u ON u.id = a.userid
                  WHERE {$where}";
    $total = $DB->count_records_sql($countsql, $sqlparams);

    $sql = "SELECT a.*, v.title as vacancy_title, v.code as vacancy_code,
                   COALESCE(c.enddate, 0) as closedate,
                   u.firstname, u.lastname, u.email,
                   (SELECT COUNT(*) FROM {local_jobboard_document} d WHERE d.applicationid = a.id AND d.issuperseded = 0) as doccount,
                   (SELECT COUNT(*) FROM {local_jobboard_document} d
                    LEFT JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                    WHERE d.applicationid = a.id AND d.issuperseded = 0
                    AND (dv.id IS NULL OR dv.status = 'pending')
                   ) as pendingcount
            FROM {local_jobboard_application} a
            JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
            LEFT JOIN {local_jobboard_convocatoria} c ON c.id = v.convocatoriaid
            JOIN {user} u ON u.id = a.userid
            WHERE {$where}
            ORDER BY a.timecreated ASC";

    $applications = $DB->get_records_sql($sql, $sqlparams, $page * $perpage, $perpage);

    // Get all applications for stats (without pagination).
    $allapplications = $DB->get_records_sql($sql, $sqlparams);

    // Calculate stats from all applications (not just paginated).
    $queuestats = [
        'total' => 0,
        'pending' => 0,
        'urgent' => 0,
    ];
    foreach ($allapplications as $app) {
        $queuestats['total']++;
        $queuestats['pending'] += (int) $app->pendingcount;
        if ($app->closedate && ($app->closedate - time()) <= 7 * 86400) {
            $queuestats['urgent']++;
        }
    }

    // Prepare template data.
    $data = $renderer->prepare_review_page_data(
        $params,
        $context,
        $total,
        $applications,
        $queuestats
    );
} else {
    // Single application review mode.
    $application = new application($applicationid);

    if (!$application->id) {
        throw new moodle_exception('error:invalidapplication', 'local_jobboard');
    }

    $vacancyobj = new vacancy($application->vacancyid);
    $applicant = $DB->get_record('user', ['id' => $application->userid]);
    $documents = document::get_by_application($applicationid);

    // Build navigation data.
    $navwhere = "a.status IN ('submitted', 'under_review')";
    $navparams = [];
    if ($vacancyid) {
        $navwhere .= " AND a.vacancyid = :vacancyid";
        $navparams['vacancyid'] = $vacancyid;
    }
    // Multi-tenant filter.
    if (\local_jobboard_is_iomad_installed() && !has_capability('local/jobboard:viewallvacancies', $context)) {
        $usercompanyid = \local_jobboard_get_user_companyid();
        if ($usercompanyid) {
            $navwhere .= " AND v.companyid = :companyid";
            $navparams['companyid'] = $usercompanyid;
        }
    }
    $navsql = "SELECT a.id FROM {local_jobboard_application} a
               JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
               WHERE {$navwhere}
               ORDER BY a.timecreated ASC";
    $navapplications = $DB->get_records_sql($navsql, $navparams);
    $navids = array_keys($navapplications);
    $currentindex = array_search($applicationid, $navids);
    $previd = ($currentindex > 0) ? $navids[$currentindex - 1] : null;
    $nextid = ($currentindex !== false && $currentindex < count($navids) - 1) ? $navids[$currentindex + 1] : null;
    $navposition = ($currentindex !== false) ? ($currentindex + 1) : 0;
    $navtotal = count($navids);

    $navdata = [
        'previd' => $previd,
        'nextid' => $nextid,
        'navposition' => $navposition,
        'navtotal' => $navtotal,
    ];

    // Prepare template data.
    $data = $renderer->prepare_review_page_data(
        $params,
        $context,
        0,
        [],
        [],
        $application,
        $vacancyobj,
        $applicant,
        $documents,
        $navdata
    );
}

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_review_page($data);
echo $OUTPUT->footer();
