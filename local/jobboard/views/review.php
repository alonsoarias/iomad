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
use local_jobboard\faculty_reviewer;
use local_jobboard\helper\iomad_helper;
use local_jobboard\helper\role_access_helper;

// Check access - Admin, Dean (reviewprofiles) or HR/Reviewer (reviewdocuments) can access.
$can_manage_workflow = has_capability('local/jobboard:manageworkflow', $context);
$can_review_profiles = has_capability('local/jobboard:reviewprofiles', $context);
$can_review_documents = has_capability('local/jobboard:reviewdocuments', $context);
$can_validate_hr = has_capability('local/jobboard:validatehr', $context);
$can_approve_profile = has_capability('local/jobboard:approveprofile', $context);

// Administrators with manageworkflow can do everything.
if ($can_manage_workflow) {
    $can_review_profiles = true;
    $can_review_documents = true;
    $can_validate_hr = true;
    $can_approve_profile = true;
}

if (!$can_review_profiles && !$can_review_documents && !$can_manage_workflow) {
    throw new \moodle_exception('nopermission', 'local_jobboard');
}

// Determine user role for filtering and UI.
// Administrators are NOT restricted by role-specific limitations.
$is_dean = !$can_manage_workflow && role_access_helper::is_dean();
$is_hr = !$can_manage_workflow && role_access_helper::is_hr();
$is_admin = $can_manage_workflow;

// Parameters.
$applicationid = optional_param('applicationid', 0, PARAM_INT);
$documentid = optional_param('documentid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$statusfilter = optional_param('status', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$ajax = optional_param('ajax', 0, PARAM_INT);

// Filter parameters - matching public page style.
$code = optional_param('code', '', PARAM_TEXT);
$contracttype = optional_param('contracttype', '', PARAM_ALPHANUMEXT);
$department = optional_param('department', '', PARAM_TEXT);
$location = optional_param('location', '', PARAM_TEXT);
$search = optional_param('search', '', PARAM_TEXT);
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
            // Only HR or Admin can validate individual documents.
            // Dean can ONLY approve/reject applications, NOT individual documents.
            $can_validate_docs = ($is_hr || $is_admin) && $can_review_documents;
            if ($documentid && $can_validate_docs) {
                $doc = new document($documentid);
                $doc->validate($USER->id);
                \core\notification::success(get_string('documentvalidated', 'local_jobboard'));
            } elseif ($is_dean) {
                \core\notification::error(get_string('error:dean_cannot_validate_docs', 'local_jobboard'));
            } else {
                \core\notification::error(get_string('nopermission', 'local_jobboard'));
            }
            break;

        case 'reject':
            // Only HR or Admin can reject individual documents.
            // Dean can ONLY approve/reject applications, NOT individual documents.
            $can_reject_docs = ($is_hr || $is_admin) && $can_review_documents;
            if ($documentid && $can_reject_docs) {
                $reason = required_param('reason', PARAM_TEXT);
                $doc = new document($documentid);
                $doc->reject($USER->id, $reason);
                \core\notification::success(get_string('documentrejected', 'local_jobboard'));
            } elseif ($is_dean) {
                \core\notification::error(get_string('error:dean_cannot_validate_docs', 'local_jobboard'));
            } else {
                \core\notification::error(get_string('nopermission', 'local_jobboard'));
            }
            break;

        case 'validateall':
            // Only HR or Admin can validate all documents.
            // Dean cannot use this action.
            $can_validate_all = ($is_hr || $is_admin) && $can_review_documents;
            if ($applicationid && $can_validate_all) {
                $documents = document::get_by_application($applicationid);
                $validated = 0;
                foreach ($documents as $doc) {
                    if ($doc->status === 'pending') {
                        $doc->validate($USER->id);
                        $validated++;
                    }
                }
                \core\notification::success(get_string('documentvalidated', 'local_jobboard') . " ({$validated})");
            } elseif ($is_dean) {
                \core\notification::error(get_string('error:dean_cannot_validate_docs', 'local_jobboard'));
            } else {
                \core\notification::error(get_string('nopermission', 'local_jobboard'));
            }
            break;

        case 'markreviewed':
            // Only HR/Reviewer can mark as reviewed (legacy action).
            if ($applicationid && $can_review_documents) {
                $app = new application($applicationid);
                $stats = document::get_stats($applicationid);
                $observations = optional_param('observations', '', PARAM_TEXT);

                // This action is deprecated - use validatehr/rejecthr instead.
                // Kept for backwards compatibility.
                \core\notification::warning(get_string('action_deprecated', 'local_jobboard'));
            } elseif (!$can_review_documents) {
                \core\notification::error(get_string('nopermission', 'local_jobboard'));
            }
            break;

        case 'markreviewed_legacy':
            // Legacy action - kept for reference but disabled.
            if ($applicationid && $can_review_documents) {
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

                // Note: Review notifications to applicants are disabled.
                // Applicants should not see intermediate review statuses.
                \core\notification::success(get_string('reviewsubmitted', 'local_jobboard'));
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
                    $validstatuses = application::STATUSES;
                    if (in_array($newstatus, $validstatuses)) {
                        $app->change_status($newstatus, $notes, $USER->id);
                        \core\notification::success(get_string('statuschanged', 'local_jobboard'));
                    }
                }
            }
            break;

        case 'approveprofile':
            // Dean approves a profile.
            if ($applicationid) {
                require_capability('local/jobboard:approveprofile', $context);
                $comments = optional_param('comments', '', PARAM_TEXT);

                $app = new application($applicationid);
                if ($app->id && in_array($app->status, ['submitted', 'pending_dean_review'])) {
                    $app->approve_profile($comments);
                    \core\notification::success(get_string('profile_approved', 'local_jobboard'));
                }
            }
            break;

        case 'rejectprofile':
            // Dean rejects a profile.
            if ($applicationid) {
                require_capability('local/jobboard:approveprofile', $context);
                $reason = required_param('reason', PARAM_TEXT);

                $app = new application($applicationid);
                if ($app->id && in_array($app->status, ['submitted', 'pending_dean_review'])) {
                    $app->reject_profile($reason);
                    \core\notification::success(get_string('profile_rejected', 'local_jobboard'));
                }
            }
            break;

        case 'validatehr':
            // HR validates final. Admins bypass date restrictions.
            if ($applicationid) {
                require_capability('local/jobboard:validatehr', $context);
                $comments = optional_param('comments', '', PARAM_TEXT);

                $app = new application($applicationid);
                if ($app->id && $app->status === 'pending_hr_validation') {
                    // Admins bypass date-based restrictions.
                    if ($is_admin) {
                        $app->validate_hr($comments);
                        \core\notification::success(get_string('hr_validation_complete', 'local_jobboard'));
                    } else {
                        // Check if HR has access (date-based).
                        $convocatoria = role_access_helper::get_convocatoria_from_vacancy($app->vacancyid);
                        if ($convocatoria && role_access_helper::can_hr_access($convocatoria)) {
                            $app->validate_hr($comments);
                            \core\notification::success(get_string('hr_validation_complete', 'local_jobboard'));
                        } else {
                            \core\notification::error(get_string('error:hr_access_dates', 'local_jobboard'));
                        }
                    }
                }
            }
            break;

        case 'rejecthr':
            // HR rejects. Admins bypass date restrictions.
            if ($applicationid) {
                require_capability('local/jobboard:validatehr', $context);
                $reason = required_param('reason', PARAM_TEXT);

                $app = new application($applicationid);
                if ($app->id && $app->status === 'pending_hr_validation') {
                    // Admins bypass date-based restrictions.
                    if ($is_admin) {
                        $app->reject_hr($reason);
                        \core\notification::success(get_string('profile_rejected', 'local_jobboard'));
                    } else {
                        // Check if HR has access (date-based).
                        $convocatoria = role_access_helper::get_convocatoria_from_vacancy($app->vacancyid);
                        if ($convocatoria && role_access_helper::can_hr_access($convocatoria)) {
                            $app->reject_hr($reason);
                            \core\notification::success(get_string('profile_rejected', 'local_jobboard'));
                        } else {
                            \core\notification::error(get_string('error:hr_access_dates', 'local_jobboard'));
                        }
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
    'code' => $code,
    'contracttype' => $contracttype,
    'department' => $department,
    'location' => $location,
    'search' => $search,
    'datefrom' => $datefrom,
    'dateto' => $dateto,
    // Role flags.
    'is_admin' => $is_admin,
    'is_dean' => $is_dean,
    'is_hr' => $is_hr,
    // Sequential review bypass:
    // - Admin: bypasses all restrictions
    // - Dean: bypasses sequential review (can see all docs) but cannot validate/reject individual docs
    // - HR: must review documents sequentially
    'bypass_sequential_review' => $is_admin || $is_dean,
    // Document validation permission (only HR and Admin can validate/reject individual docs).
    // CRITICAL: Dean can NEVER validate documents, even if they have other roles.
    // Dean only approves/rejects the FULL application, not individual documents.
    'can_validate_documents' => ($is_admin || $is_hr) && !$is_dean,
];

// If no application selected, show list of applications pending review.
if (!$applicationid) {
    // Build filter - show ALL applications with any status.
    // - draft: saved but not yet submitted
    // - submitted/under_review: pending initial review
    // - docs_rejected: needs re-review after document reupload
    // - docs_validated: reviewed and approved
    // - interview: in interview stage
    // - selected: selected for position
    // - rejected: rejected by reviewer
    // - withdrawn: withdrawn by applicant
    $validstatuses = ['draft', 'submitted', 'under_review', 'docs_rejected', 'docs_validated', 'interview', 'selected', 'rejected', 'withdrawn'];
    $sqlparams = [];

    // Apply status filter if provided, otherwise show all statuses.
    if (!empty($statusfilter) && in_array($statusfilter, $validstatuses)) {
        $where = "a.status = :statusfilter";
        $sqlparams['statusfilter'] = $statusfilter;
    } else {
        $where = "1=1"; // Show all statuses.
    }

    if ($vacancyid) {
        $where .= " AND a.vacancyid = :vacancyid";
        $sqlparams['vacancyid'] = $vacancyid;
    }

    // Code filter (vacancy code).
    if (!empty($code)) {
        $where .= " AND " . $DB->sql_like('v.code', ':code', false, false);
        $sqlparams['code'] = '%' . $DB->sql_like_escape($code) . '%';
    }

    // Contract type filter.
    if (!empty($contracttype)) {
        $where .= " AND v.contracttype = :contracttype";
        $sqlparams['contracttype'] = $contracttype;
    }

    // Department filter (Programa académico).
    if (!empty($department)) {
        $where .= " AND v.department = :department";
        $sqlparams['department'] = $department;
    }

    // Location filter (Ubicación).
    if (!empty($location)) {
        $where .= " AND v.location = :location";
        $sqlparams['location'] = $location;
    }

    // General search filter.
    if (!empty($search)) {
        $searchlike = '%' . $DB->sql_like_escape($search) . '%';
        $where .= " AND (" . $DB->sql_like('v.title', ':search1', false, false) .
                 " OR " . $DB->sql_like('v.code', ':search2', false, false) .
                 " OR " . $DB->sql_like('u.firstname', ':search3', false, false) .
                 " OR " . $DB->sql_like('u.lastname', ':search4', false, false) .
                 " OR " . $DB->sql_like('u.email', ':search5', false, false) . ")";
        $sqlparams['search1'] = $searchlike;
        $sqlparams['search2'] = $searchlike;
        $sqlparams['search3'] = $searchlike;
        $sqlparams['search4'] = $searchlike;
        $sqlparams['search5'] = $searchlike;
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

    // Faculty-based filter for deans.
    // Deans can only see applications for vacancies in programs belonging to their assigned faculties.
    if ($is_dean && !$is_admin) {
        $facultyids = faculty_reviewer::get_faculty_ids_for_user($USER->id);
        if (!empty($facultyids)) {
            // Join with program table and filter by faculty.
            list($facultyinsql, $facultyparams) = $DB->get_in_or_equal($facultyids, SQL_PARAMS_NAMED, 'fac');
            $where .= " AND v.programid IN (
                SELECT p.id FROM {local_jobboard_program} p WHERE p.facultyid $facultyinsql
            )";
            $sqlparams = array_merge($sqlparams, $facultyparams);
        } else {
            // Dean has no faculty assignments - show no applications.
            $where .= " AND 1=0";
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

    // Faculty-based access check for deans.
    // Deans can only view applications for vacancies in their assigned faculties.
    if ($is_dean && !$is_admin) {
        // Primary check: Use vacancy code pattern matching (e.g., FCAS-*, FII-*).
        $canaccess = faculty_reviewer::vacancy_belongs_to_user_faculty($vacancyobj->code ?? '', $USER->id);

        // Fallback: also check via program->faculty relationship if vacancy has programid.
        if (!$canaccess && !empty($vacancyobj->programid)) {
            $facultyids = faculty_reviewer::get_faculty_ids_for_user($USER->id);
            $program = $DB->get_record('local_jobboard_program', ['id' => $vacancyobj->programid]);
            if ($program && in_array($program->facultyid, $facultyids)) {
                $canaccess = true;
            }
        }

        if (!$canaccess) {
            throw new moodle_exception('error:noaccesstoapplication', 'local_jobboard');
        }
    }

    $applicant = $DB->get_record('user', ['id' => $application->userid]);
    if (!$applicant) {
        throw new moodle_exception('error:usernotfound', 'local_jobboard');
    }
    $documents = document::get_by_application($applicationid);

    // Build navigation data - include ALL statuses for navigation.
    $navwhere = "1=1"; // All statuses.
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
    // Dean bypasses sequential review (can see all docs) but cannot validate/reject docs.
    // HR has sequential review restriction.
    // Admin bypasses all restrictions.
    $PAGE->requires->js_call_amd('local_jobboard/document_review', 'init', [[
        'applicationId' => $applicationid,
        'isAdmin' => $is_admin,
        'isDean' => $is_dean,
        'isHr' => $is_hr,
        // Dean and Admin bypass sequential review (can see all docs).
        // HR must review documents sequentially.
        'bypassSequentialReview' => $is_admin || $is_dean,
        // Only HR and Admin can validate/reject individual documents.
        // CRITICAL: Dean can NEVER validate documents, even if they have other roles.
        'canValidateDocuments' => ($is_admin || $is_hr) && !$is_dean,
    ]]);
}

// Add role-based capability flags to template data.
$data['can_review_documents'] = $can_review_documents;
$data['can_review_profiles'] = $can_review_profiles;
$data['can_validate_hr'] = $can_validate_hr;
$data['can_approve_profile'] = $can_approve_profile;
$data['can_manage_workflow'] = $can_manage_workflow;
$data['is_dean'] = $is_dean;
$data['is_hr'] = $is_hr;
$data['is_admin'] = $is_admin;

// Add status-based action visibility for Dean/HR workflow.
if (isset($application) && $application->id) {
    // Admins can always see all actions regardless of status.
    // Dean can approve/reject applications with 'submitted' or 'pending_dean_review' status.
    // HR sees actions only for pending_hr_validation status.
    // Show dean actions if:
    // 1. User is admin (can do everything), OR
    // 2. User has approveprofile capability AND application status is 'submitted' or 'pending_dean_review'
    $data['show_dean_actions'] = $is_admin ||
        ($can_approve_profile && in_array($application->status, ['submitted', 'pending_dean_review']));
    $data['show_hr_actions'] = $is_admin || ($application->status === 'pending_hr_validation' && $can_validate_hr);
    // Admins can always review documents. Dean role only reviews profiles, not individual docs.
    // CRITICAL: Dean can NEVER show document validation actions.
    $data['show_document_actions'] = $is_admin || ($can_review_documents && !$is_dean);
    // Admins and Deans bypass sequential review - can see all documents.
    // But Dean still cannot validate/reject individual documents, only view them.
    $data['bypass_sequential_review'] = $is_admin || $is_dean;
}

// Handle AJAX request - return only results HTML without header/footer.
if ($ajax && !$applicationid) {
    // AJAX response - just the results partial.
    echo $renderer->render_review_results($data);
    exit;
}

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_review_page($data);

// Initialize filter auto-submit for all users.
$PAGE->requires->js_call_amd('local_jobboard/public_filters', 'init', [[
    'formSelector' => '.jb-filter-form',
    'resultsSelector' => '[data-region="filter-results"]',
]]);

// Initialize bulk selection module for application list.
if (!$applicationid) {
    $PAGE->requires->js_call_amd('local_jobboard/bulk_selection', 'init', [[]]);
}

echo $OUTPUT->footer();
