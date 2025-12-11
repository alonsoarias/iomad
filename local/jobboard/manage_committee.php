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
 * Selection committee management page.
 *
 * Create, edit, and manage selection committees for faculties/companies.
 * Assign users with different roles (chair, evaluator, secretary, observer).
 *
 * Migrated to renderer + template pattern in v3.1.18.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\committee;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageworkflow', $context);

// Parameters.
$companyid = optional_param('companyid', 0, PARAM_INT);
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
// Support old parameter name for backwards compatibility.
if (!$vacancyid) {
    $vacancyid = optional_param('vacancy', 0, PARAM_INT);
}
$action = optional_param('action', '', PARAM_ALPHA);
$committeeid = optional_param('id', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$usersearch = optional_param('usersearch', '', PARAM_TEXT);

// Page setup.
$urlparams = [];
if ($companyid) {
    $urlparams['companyid'] = $companyid;
}
if ($vacancyid) {
    $urlparams['vacancyid'] = $vacancyid;
}
$PAGE->set_url(new moodle_url('/local/jobboard/manage_committee.php', $urlparams));
$PAGE->set_context($context);
$PAGE->set_title(get_string('committees', 'local_jobboard'));
$PAGE->set_heading(get_string('committees', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Handle actions.
if ($action === 'create' && confirm_sesskey()) {
    $createcompanyid = required_param('companyid', PARAM_INT);
    $name = required_param('name', PARAM_TEXT);

    // Get selected members.
    $chairid = required_param('chair', PARAM_INT);
    $members = [
        ['userid' => $chairid, 'role' => committee::ROLE_CHAIR],
    ];

    // Optional members.
    $secretaryid = optional_param('secretary', 0, PARAM_INT);
    if ($secretaryid) {
        $members[] = ['userid' => $secretaryid, 'role' => committee::ROLE_SECRETARY];
    }

    $evaluatorids = optional_param_array('evaluators', [], PARAM_INT);
    foreach ($evaluatorids as $eid) {
        if ($eid && $eid != $chairid && $eid != $secretaryid) {
            $members[] = ['userid' => $eid, 'role' => committee::ROLE_EVALUATOR];
        }
    }

    // Create committee for the faculty/company.
    $committeeid = committee::create_for_company($createcompanyid, $name, $members);
    if ($committeeid) {
        // Assign the jobboard_committee role to all members.
        $committeerole = $DB->get_record('role', ['shortname' => 'jobboard_committee']);
        if ($committeerole) {
            foreach ($members as $member) {
                if (!$DB->record_exists('role_assignments', [
                    'roleid' => $committeerole->id,
                    'contextid' => $context->id,
                    'userid' => $member['userid'],
                ])) {
                    role_assign($committeerole->id, $member['userid'], $context->id);
                }
            }
        }

        \core\notification::success(get_string('committeecreated', 'local_jobboard'));
    } else {
        \core\notification::error(get_string('committeecreateerror', 'local_jobboard'));
    }
    redirect(new moodle_url('/local/jobboard/manage_committee.php', ['companyid' => $createcompanyid]));
}

if ($action === 'addmember' && confirm_sesskey()) {
    // Support both parameter styles.
    $cid = $committeeid ?: optional_param('committee', 0, PARAM_INT);
    $uid = $userid ?: optional_param('userid', 0, PARAM_INT);
    $role = optional_param('memberrole', '', PARAM_ALPHA);
    if (!$role) {
        $role = optional_param('role', committee::ROLE_EVALUATOR, PARAM_ALPHA);
    }

    if ($cid && $uid) {
        $result = committee::add_member($cid, $uid, $role);
        if ($result) {
            // Assign the jobboard_committee role.
            $committeerole = $DB->get_record('role', ['shortname' => 'jobboard_committee']);
            if ($committeerole) {
                if (!$DB->record_exists('role_assignments', [
                    'roleid' => $committeerole->id,
                    'contextid' => $context->id,
                    'userid' => $uid,
                ])) {
                    role_assign($committeerole->id, $uid, $context->id);
                }
            }
            \core\notification::success(get_string('memberadded', 'local_jobboard'));
        } else {
            \core\notification::error(get_string('memberadderror', 'local_jobboard'));
        }

        // Get company ID from committee.
        $comm = $DB->get_record('local_jobboard_committee', ['id' => $cid]);
        $redirectparams = !empty($comm->companyid) ? ['companyid' => $comm->companyid] : ['vacancyid' => $comm->vacancyid];
        redirect(new moodle_url('/local/jobboard/manage_committee.php', $redirectparams));
    }
}

if ($action === 'removemember' && confirm_sesskey()) {
    $cid = $committeeid ?: optional_param('committee', 0, PARAM_INT);
    $uid = $userid ?: optional_param('userid', 0, PARAM_INT);

    if ($cid && $uid) {
        $result = committee::remove_member($cid, $uid);
        if ($result) {
            \core\notification::success(get_string('memberremoved', 'local_jobboard'));
        } else {
            \core\notification::error(get_string('memberremoveerror', 'local_jobboard'));
        }

        $comm = $DB->get_record('local_jobboard_committee', ['id' => $cid]);
        $redirectparams = !empty($comm->companyid) ? ['companyid' => $comm->companyid] : ['vacancyid' => $comm->vacancyid];
        redirect(new moodle_url('/local/jobboard/manage_committee.php', $redirectparams));
    }
}

if ($action === 'changerole' && confirm_sesskey()) {
    $cid = $committeeid ?: optional_param('committee', 0, PARAM_INT);
    $uid = $userid ?: optional_param('userid', 0, PARAM_INT);
    $newrole = required_param('newrole', PARAM_ALPHA);

    if ($cid && $uid) {
        $result = committee::update_member_role($cid, $uid, $newrole);
        if ($result) {
            \core\notification::success(get_string('rolechanged', 'local_jobboard'));
        } else {
            \core\notification::error(get_string('rolechangeerror', 'local_jobboard'));
        }

        $comm = $DB->get_record('local_jobboard_committee', ['id' => $cid]);
        $redirectparams = !empty($comm->companyid) ? ['companyid' => $comm->companyid] : ['vacancyid' => $comm->vacancyid];
        redirect(new moodle_url('/local/jobboard/manage_committee.php', $redirectparams));
    }
}

// Legacy vacancy redirect: if vacancy has a company, redirect to company view.
if ($vacancyid && !$companyid) {
    $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid]);
    if ($vacancy && !empty($vacancy->companyid)) {
        redirect(new moodle_url('/local/jobboard/manage_committee.php', ['companyid' => $vacancy->companyid]));
    }
}

// Render page using renderer + template pattern.
$renderer = $PAGE->get_renderer('local_jobboard');
$data = $renderer->prepare_committee_page_data($companyid, $vacancyid, $usersearch);

echo $OUTPUT->header();
echo $renderer->render_committee_page($data);
echo $OUTPUT->footer();
