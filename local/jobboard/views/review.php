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
use local_jobboard\helper\iomad_helper;

// Require review capability.
require_capability('local/jobboard:reviewdocuments', $context);

// Parameters.
$applicationid = optional_param('applicationid', 0, PARAM_INT);
$documentid = optional_param('documentid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$statusfilter = optional_param('status', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

// Advanced filter parameters.
$facultyid = optional_param('facultyid', 0, PARAM_INT);
$programid = optional_param('programid', 0, PARAM_INT);
$idnumber = optional_param('idnumber', '', PARAM_TEXT);
$datefrom = optional_param('datefrom', '', PARAM_TEXT);
$dateto = optional_param('dateto', '', PARAM_TEXT);

// Page setup.
$PAGE->set_title(get_string('reviewdocuments', 'local_jobboard'));
$PAGE->set_heading(get_string('reviewdocuments', 'local_jobboard'));
$PAGE->set_pagelayout('standard');
$PAGE->activityheader->disable();

// Set up breadcrumbs via Moodle's native navbar.
$PAGE->navbar->add(get_string('dashboard', 'local_jobboard'),
    new moodle_url('/local/jobboard/index.php'));
$PAGE->navbar->add(get_string('reviewdocuments', 'local_jobboard'));

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

                // Update application status with observations as comments.
                $app->change_status($newstatus, $observations, $USER->id);

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

        case 'changestatus':
            // Reviewer can manually change application status.
            if ($applicationid) {
                require_capability('local/jobboard:manageworkflow', $context);
                $newstatus = required_param('newstatus', PARAM_ALPHA);
                $notes = optional_param('notes', '', PARAM_TEXT);

                $app = new application($applicationid);
                if ($app->id) {
                    $validstatuses = ['submitted', 'under_review', 'docs_validated', 'docs_rejected', 'interview', 'selected', 'rejected'];
                    if (in_array($newstatus, $validstatuses)) {
                        $app->change_status($newstatus, $notes, $USER->id);
                        \core\notification::success(get_string('statuschanged', 'local_jobboard'));
                    }
                }
            }
            break;

        case 'deleteapplication':
            // Reviewer can delete an application with full audit.
            if ($applicationid) {
                require_capability('local/jobboard:manageworkflow', $context);
                $reason = required_param('reason', PARAM_TEXT);

                $app = new application($applicationid);
                if ($app->id) {
                    // Capture full application data for audit before deletion.
                    $auditdata = [
                        'applicationid' => $app->id,
                        'userid' => $app->userid,
                        'vacancyid' => $app->vacancyid,
                        'status' => $app->status,
                        'reason' => $reason,
                        'deletedby' => $USER->id,
                        'deletedat' => time(),
                    ];

                    // Get applicant info for audit.
                    $applicant = $DB->get_record('user', ['id' => $app->userid]);
                    if ($applicant) {
                        $auditdata['applicantname'] = fullname($applicant);
                        $auditdata['applicantemail'] = $applicant->email;
                    }

                    // Delete the application (this will log the audit internally).
                    $app->delete($reason);

                    // Additional audit log for the deletion action.
                    \local_jobboard\audit::log(
                        \local_jobboard\audit::ACTION_DELETE,
                        \local_jobboard\audit::ENTITY_APPLICATION,
                        $applicationid,
                        $auditdata
                    );

                    \core\notification::success(get_string('applicationdeleted', 'local_jobboard'));

                    // Redirect to review list after deletion.
                    redirect(new moodle_url('/local/jobboard/index.php', [
                        'view' => 'review',
                        'vacancyid' => $vacancyid,
                    ]));
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
    'statusfilter' => $statusfilter,
    'page' => $page,
    'perpage' => $perpage,
    'facultyid' => $facultyid,
    'programid' => $programid,
    'idnumber' => $idnumber,
    'datefrom' => $datefrom,
    'dateto' => $dateto,
];

// If no application selected, show list of applications pending review.
if (!$applicationid) {
    // Build filter - show all applications that may need review or reference.
    // - submitted/under_review: pending initial review
    // - docs_rejected: needs re-review after document reupload
    // - docs_validated: reviewed and approved (for reference)
    // - rejected: rejected by reviewer (for reference)
    // - withdrawn: withdrawn by applicant (for reference)
    $validstatuses = ['submitted', 'under_review', 'docs_rejected', 'docs_validated', 'rejected', 'withdrawn'];
    $sqlparams = [];

    // Apply status filter if provided.
    if (!empty($statusfilter) && in_array($statusfilter, $validstatuses)) {
        $where = "a.status = :statusfilter";
        $sqlparams['statusfilter'] = $statusfilter;
    } else {
        $where = "a.status IN ('submitted', 'under_review', 'docs_rejected', 'docs_validated', 'rejected', 'withdrawn')";
    }

    if ($vacancyid) {
        $where .= " AND a.vacancyid = :vacancyid";
        $sqlparams['vacancyid'] = $vacancyid;
    }

    // Faculty filter (via vacancy -> convocatoria or direct).
    if ($facultyid) {
        $where .= " AND EXISTS (
            SELECT 1 FROM {local_jobboard_program} p
            JOIN {local_jobboard_faculty} f ON f.id = p.facultyid
            WHERE f.id = :facultyid AND (
                p.name LIKE CONCAT('%', v.department, '%')
                OR v.department LIKE CONCAT('%', p.name, '%')
                OR v.title LIKE CONCAT('%', f.name, '%')
            )
        )";
        $sqlparams['facultyid'] = $facultyid;
    }

    // Program filter (via vacancy department or title).
    if ($programid) {
        $where .= " AND EXISTS (
            SELECT 1 FROM {local_jobboard_program} p
            WHERE p.id = :programid AND (
                p.name LIKE CONCAT('%', v.department, '%')
                OR v.department LIKE CONCAT('%', p.name, '%')
                OR v.title LIKE CONCAT('%', p.name, '%')
            )
        )";
        $sqlparams['programid'] = $programid;
    }

    // ID number (cedula) filter.
    if (!empty($idnumber)) {
        $where .= " AND " . $DB->sql_like('u.idnumber', ':idnumber', false, false);
        $sqlparams['idnumber'] = '%' . $DB->sql_like_escape($idnumber) . '%';
    }

    // Date range filter.
    if (!empty($datefrom)) {
        $fromtimestamp = strtotime($datefrom);
        if ($fromtimestamp) {
            $where .= " AND a.timecreated >= :datefrom";
            $sqlparams['datefrom'] = $fromtimestamp;
        }
    }
    if (!empty($dateto)) {
        $totimestamp = strtotime($dateto . ' 23:59:59');
        if ($totimestamp) {
            $where .= " AND a.timecreated <= :dateto";
            $sqlparams['dateto'] = $totimestamp;
        }
    }

    // Multi-tenant filter.
    if (iomad_helper::is_iomad_installed() && !has_capability('local/jobboard:viewallvacancies', $context)) {
        $usercompanyid = iomad_helper::get_user_companyid();
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
                   v.department as vacancy_department,
                   COALESCE(c.enddate, 0) as closedate,
                   u.id as userid, u.firstname, u.lastname, u.email, u.idnumber,
                   u.firstnamephonetic, u.lastnamephonetic, u.middlename, u.alternatename,
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
    if (!$applicant) {
        throw new moodle_exception('error:usernotfound', 'local_jobboard');
    }
    $documents = document::get_by_application($applicationid);

    // Build navigation data - include all reviewable statuses for navigation.
    $navwhere = "a.status IN ('submitted', 'under_review', 'docs_rejected', 'docs_validated', 'rejected', 'withdrawn')";
    $navparams = [];
    if ($vacancyid) {
        $navwhere .= " AND a.vacancyid = :vacancyid";
        $navparams['vacancyid'] = $vacancyid;
    }
    // Multi-tenant filter.
    if (iomad_helper::is_iomad_installed() && !has_capability('local/jobboard:viewallvacancies', $context)) {
        $usercompanyid = iomad_helper::get_user_companyid();
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

// Initialize document review module if viewing a single application.
if ($applicationid && isset($application) && $application->id) {
    $PAGE->requires->js_call_amd('local_jobboard/document_review', 'init', [[
        'applicationId' => $applicationid,
    ]]);
}

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_review_page($data);

// Initialize filter auto-submit for all users.
$PAGE->requires->js_call_amd('local_jobboard/public_filters', 'init', [[
    'formSelector' => '.jb-filter-form',
]]);

echo $OUTPUT->footer();
