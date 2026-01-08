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
 * Faculty reviewer (Dean) management page.
 *
 * Allows administrators to assign deans and reviewers to faculties
 * for the faculty-based dean review workflow.
 *
 * @package   local_jobboard
 * @copyright 2024-2026 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

use local_jobboard\faculty_reviewer;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:configure', $context);

// Parameters.
$action = optional_param('action', '', PARAM_ALPHA);
$facultyid = optional_param('facultyid', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$role = optional_param('role', faculty_reviewer::ROLE_DEAN, PARAM_ALPHA);
$assignmentid = optional_param('id', 0, PARAM_INT);

// Page setup.
$PAGE->set_url(new moodle_url('/local/jobboard/admin/faculty_reviewers.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('managefacultyreviewers', 'local_jobboard'));
$PAGE->set_heading(get_string('managefacultyreviewers', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Add breadcrumbs for navigation.
$PAGE->navbar->add(get_string('dashboard', 'local_jobboard'),
    new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('administration', 'local_jobboard'),
    new moodle_url('/local/jobboard/admin/'));
$PAGE->navbar->add(get_string('managefacultyreviewers', 'local_jobboard'));

// Handle actions.
if ($action === 'assign' && confirm_sesskey()) {
    $selecteduserids = required_param_array('userids', PARAM_INT);

    if ($facultyid && !empty($selecteduserids)) {
        $assigned = 0;
        foreach ($selecteduserids as $uid) {
            try {
                faculty_reviewer::assign($facultyid, $uid, $role);
                $assigned++;
            } catch (\moodle_exception $e) {
                // User already assigned, skip.
            }
        }
        if ($assigned > 0) {
            \core\notification::success(
                get_string('facultyreviewersassigned', 'local_jobboard', $assigned)
            );
        }
    }
    redirect(new moodle_url('/local/jobboard/admin/faculty_reviewers.php', ['facultyid' => $facultyid]));
}

if ($action === 'unassign' && confirm_sesskey() && $assignmentid) {
    $assignment = faculty_reviewer::get($assignmentid);
    if ($assignment) {
        $assignment->deactivate();
        \core\notification::success(get_string('facultyreviewerunassigned', 'local_jobboard'));
    }
    redirect(new moodle_url('/local/jobboard/admin/faculty_reviewers.php', ['facultyid' => $facultyid]));
}

if ($action === 'delete' && confirm_sesskey() && $assignmentid) {
    $assignment = faculty_reviewer::get($assignmentid);
    if ($assignment) {
        $assignment->delete();
        \core\notification::success(get_string('facultyreviewerdeleted', 'local_jobboard'));
    }
    redirect(new moodle_url('/local/jobboard/admin/faculty_reviewers.php', ['facultyid' => $facultyid]));
}

if ($action === 'changerole' && confirm_sesskey() && $assignmentid) {
    $newrole = required_param('newrole', PARAM_ALPHA);
    $assignment = faculty_reviewer::get($assignmentid);
    if ($assignment) {
        $assignment->update_role($newrole);
        \core\notification::success(get_string('facultyreviewerrolechanged', 'local_jobboard'));
    }
    redirect(new moodle_url('/local/jobboard/admin/faculty_reviewers.php', ['facultyid' => $facultyid]));
}

if ($action === 'reactivate' && confirm_sesskey() && $assignmentid) {
    $assignment = faculty_reviewer::get($assignmentid);
    if ($assignment) {
        $assignment->reactivate();
        \core\notification::success(get_string('facultyreviewerreactivated', 'local_jobboard'));
    }
    redirect(new moodle_url('/local/jobboard/admin/faculty_reviewers.php', ['facultyid' => $facultyid]));
}

// Get faculties for the dropdown.
$faculties = $DB->get_records('local_jobboard_faculty', ['enabled' => 1], 'name ASC');

// Get reviewers for selected faculty.
$reviewers = [];
$availableusers = [];
if ($facultyid) {
    // Get all faculty reviewers (including inactive for display).
    $reviewers = faculty_reviewer::get_list(['facultyid' => $facultyid, 'status' => '']);

    // Get users who can be assigned (have appropriate capability).
    // Typically managers or users with the dean role.
    $availableusers = get_users_by_capability(
        $context,
        'local/jobboard:approveprofile',
        'u.id, u.firstname, u.lastname, u.email',
        'u.lastname, u.firstname'
    );

    // Filter out already assigned users.
    $assigneduserids = array_column($reviewers, 'userid');
    $availableusers = array_filter($availableusers, function($user) use ($assigneduserids) {
        return !in_array($user->id, $assigneduserids);
    });
}

// Output page.
echo $OUTPUT->header();

echo '<div class="container-fluid">';
echo '<h2>' . get_string('managefacultyreviewers', 'local_jobboard') . '</h2>';
echo '<p class="lead">' . get_string('managefacultyreviewers_desc', 'local_jobboard') . '</p>';

// Faculty selector.
echo '<div class="card mb-4">';
echo '<div class="card-header bg-primary text-white">';
echo '<h5 class="mb-0"><i class="fa fa-building mr-2"></i>' . get_string('selectfaculty', 'local_jobboard') . '</h5>';
echo '</div>';
echo '<div class="card-body">';
echo '<form method="get" action="" class="form-inline">';
echo '<select name="facultyid" class="form-control mr-2" onchange="this.form.submit()">';
echo '<option value="">' . get_string('selectfaculty', 'local_jobboard') . '...</option>';
foreach ($faculties as $faculty) {
    $selected = ($faculty->id == $facultyid) ? 'selected' : '';
    echo '<option value="' . $faculty->id . '" ' . $selected . '>' . format_string($faculty->name) . ' (' . $faculty->code . ')</option>';
}
echo '</select>';
echo '</form>';
echo '</div>';
echo '</div>';

if ($facultyid) {
    $selectedfaculty = $DB->get_record('local_jobboard_faculty', ['id' => $facultyid]);

    // Current reviewers section.
    echo '<div class="card mb-4">';
    echo '<div class="card-header bg-success text-white">';
    echo '<h5 class="mb-0"><i class="fa fa-users mr-2"></i>' . get_string('currentfacultyreviewers', 'local_jobboard') . ': ' . format_string($selectedfaculty->name) . '</h5>';
    echo '</div>';
    echo '<div class="card-body">';

    if (empty($reviewers)) {
        echo '<div class="alert alert-info">' . get_string('nofacultyreviewers', 'local_jobboard') . '</div>';
    } else {
        echo '<div class="table-responsive">';
        echo '<table class="table table-striped">';
        echo '<thead class="thead-dark"><tr>';
        echo '<th>' . get_string('user') . '</th>';
        echo '<th>' . get_string('email') . '</th>';
        echo '<th>' . get_string('role', 'local_jobboard') . '</th>';
        echo '<th>' . get_string('status') . '</th>';
        echo '<th>' . get_string('dateadded', 'local_jobboard') . '</th>';
        echo '<th>' . get_string('actions') . '</th>';
        echo '</tr></thead><tbody>';

        foreach ($reviewers as $reviewer) {
            $statusclass = $reviewer->status === 'active' ? 'badge-success' : 'badge-secondary';
            $rolename = get_string('faculty_reviewer_role_' . $reviewer->role, 'local_jobboard');
            $statusname = get_string('faculty_reviewer_status_' . $reviewer->status, 'local_jobboard');

            echo '<tr>';
            echo '<td>' . fullname($reviewer) . '</td>';
            echo '<td>' . $reviewer->email . '</td>';
            echo '<td>';
            // Role dropdown.
            echo '<form method="post" action="" class="form-inline d-inline">';
            echo '<input type="hidden" name="action" value="changerole">';
            echo '<input type="hidden" name="id" value="' . $reviewer->id . '">';
            echo '<input type="hidden" name="facultyid" value="' . $facultyid . '">';
            echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
            echo '<select name="newrole" class="form-control form-control-sm" onchange="this.form.submit()">';
            echo '<option value="dean"' . ($reviewer->role === 'dean' ? ' selected' : '') . '>' . get_string('faculty_reviewer_role_dean', 'local_jobboard') . '</option>';
            echo '<option value="lead_reviewer"' . ($reviewer->role === 'lead_reviewer' ? ' selected' : '') . '>' . get_string('faculty_reviewer_role_lead_reviewer', 'local_jobboard') . '</option>';
            echo '<option value="reviewer"' . ($reviewer->role === 'reviewer' ? ' selected' : '') . '>' . get_string('faculty_reviewer_role_reviewer', 'local_jobboard') . '</option>';
            echo '</select>';
            echo '</form>';
            echo '</td>';
            echo '<td><span class="badge ' . $statusclass . '">' . $statusname . '</span></td>';
            echo '<td>' . userdate($reviewer->timecreated, get_string('strftimedateshort')) . '</td>';
            echo '<td>';

            if ($reviewer->status === 'active') {
                // Deactivate button.
                $unassignurl = new moodle_url('/local/jobboard/admin/faculty_reviewers.php', [
                    'action' => 'unassign',
                    'id' => $reviewer->id,
                    'facultyid' => $facultyid,
                    'sesskey' => sesskey(),
                ]);
                echo '<a href="' . $unassignurl->out() . '" class="btn btn-warning btn-sm mr-1" title="' . get_string('deactivate', 'local_jobboard') . '">';
                echo '<i class="fa fa-pause"></i>';
                echo '</a>';
            } else {
                // Reactivate button.
                $reactivateurl = new moodle_url('/local/jobboard/admin/faculty_reviewers.php', [
                    'action' => 'reactivate',
                    'id' => $reviewer->id,
                    'facultyid' => $facultyid,
                    'sesskey' => sesskey(),
                ]);
                echo '<a href="' . $reactivateurl->out() . '" class="btn btn-success btn-sm mr-1" title="' . get_string('reactivate', 'local_jobboard') . '">';
                echo '<i class="fa fa-play"></i>';
                echo '</a>';
            }

            // Delete button.
            $deleteurl = new moodle_url('/local/jobboard/admin/faculty_reviewers.php', [
                'action' => 'delete',
                'id' => $reviewer->id,
                'facultyid' => $facultyid,
                'sesskey' => sesskey(),
            ]);
            echo '<a href="' . $deleteurl->out() . '" class="btn btn-danger btn-sm" onclick="return confirm(\'' . get_string('confirmdelete', 'local_jobboard') . '\');" title="' . get_string('delete') . '">';
            echo '<i class="fa fa-trash"></i>';
            echo '</a>';

            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';

    // Add new reviewer section.
    if (!empty($availableusers)) {
        echo '<div class="card mb-4">';
        echo '<div class="card-header bg-info text-white">';
        echo '<h5 class="mb-0"><i class="fa fa-user-plus mr-2"></i>' . get_string('addfacultyreviewer', 'local_jobboard') . '</h5>';
        echo '</div>';
        echo '<div class="card-body">';

        echo '<form method="post" action="">';
        echo '<input type="hidden" name="action" value="assign">';
        echo '<input type="hidden" name="facultyid" value="' . $facultyid . '">';
        echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';

        echo '<div class="form-group">';
        echo '<label for="usersearch">' . get_string('searchuser', 'local_jobboard') . '</label>';
        echo '<input type="text" id="usersearch" class="form-control mb-2" placeholder="' . get_string('searchuser', 'local_jobboard') . '...">';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<label for="userselect">' . get_string('selectusers', 'local_jobboard') . '</label>';
        echo '<select name="userids[]" id="userselect" class="form-control" multiple size="8">';
        foreach ($availableusers as $user) {
            echo '<option value="' . $user->id . '">' . fullname($user) . ' (' . $user->email . ')</option>';
        }
        echo '</select>';
        echo '<small class="form-text text-muted">' . get_string('selectmultiple', 'local_jobboard') . '</small>';
        echo '</div>';

        echo '<div class="form-group">';
        echo '<label for="roleselect">' . get_string('assignrole', 'local_jobboard') . '</label>';
        echo '<select name="role" id="roleselect" class="form-control">';
        echo '<option value="dean">' . get_string('faculty_reviewer_role_dean', 'local_jobboard') . '</option>';
        echo '<option value="lead_reviewer">' . get_string('faculty_reviewer_role_lead_reviewer', 'local_jobboard') . '</option>';
        echo '<option value="reviewer">' . get_string('faculty_reviewer_role_reviewer', 'local_jobboard') . '</option>';
        echo '</select>';
        echo '</div>';

        echo '<button type="submit" class="btn btn-primary">';
        echo '<i class="fa fa-plus mr-2"></i>' . get_string('assigntofaculty', 'local_jobboard');
        echo '</button>';

        echo '</form>';
        echo '</div>';
        echo '</div>';
    } else {
        echo '<div class="alert alert-warning">' . get_string('nousersavailableforassignment', 'local_jobboard') . '</div>';
    }
}

echo '</div>';

// Add JavaScript for user search filtering.
if ($facultyid && !empty($availableusers)) {
    $PAGE->requires->js_init_code("
        document.getElementById('usersearch').addEventListener('keyup', function() {
            var filter = this.value.toLowerCase();
            var options = document.querySelectorAll('#userselect option');
            options.forEach(function(option) {
                var text = option.textContent.toLowerCase();
                option.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    ");
}

echo $OUTPUT->footer();
