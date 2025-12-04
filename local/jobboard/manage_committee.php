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
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

use local_jobboard\committee;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageworkflow', $context);

// Parameters.
$vacancyid = required_param('vacancy', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

// Get vacancy info.
$vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid], '*', MUST_EXIST);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/manage_committee.php', ['vacancy' => $vacancyid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('managecommittee', 'local_jobboard'));
$PAGE->set_heading(get_string('managecommittee', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('managevacancies', 'local_jobboard'),
    new moodle_url('/local/jobboard/manage_vacancies.php'));
$PAGE->navbar->add(get_string('managecommittee', 'local_jobboard'));

// Get existing committee.
$committee = committee::get_for_vacancy($vacancyid);

// Handle actions.
if ($action === 'addmember' && confirm_sesskey()) {
    $userid = required_param('userid', PARAM_INT);
    $role = required_param('role', PARAM_ALPHA);

    if ($committee) {
        if (committee::add_member($committee->id, $userid, $role)) {
            redirect(new moodle_url('/local/jobboard/manage_committee.php', ['vacancy' => $vacancyid]),
                get_string('memberadded', 'local_jobboard'), null,
                \core\output\notification::NOTIFY_SUCCESS);
        }
    }
}

if ($action === 'removemember' && confirm_sesskey()) {
    $userid = required_param('userid', PARAM_INT);

    if ($committee) {
        if (committee::remove_member($committee->id, $userid)) {
            redirect(new moodle_url('/local/jobboard/manage_committee.php', ['vacancy' => $vacancyid]),
                get_string('memberremoved', 'local_jobboard'), null,
                \core\output\notification::NOTIFY_SUCCESS);
        }
    }
}

if ($action === 'changerole' && confirm_sesskey()) {
    $userid = required_param('userid', PARAM_INT);
    $newrole = required_param('newrole', PARAM_ALPHA);

    if ($committee) {
        if (committee::update_member_role($committee->id, $userid, $newrole)) {
            redirect(new moodle_url('/local/jobboard/manage_committee.php', ['vacancy' => $vacancyid]),
                get_string('rolechanged', 'local_jobboard'), null,
                \core\output\notification::NOTIFY_SUCCESS);
        }
    }
}

// Create committee form.
class create_committee_form extends moodleform {
    protected function definition() {
        global $DB;

        $mform = $this->_form;
        $vacancyid = $this->_customdata['vacancyid'];
        $vacancy = $this->_customdata['vacancy'];

        $mform->addElement('hidden', 'vacancy', $vacancyid);
        $mform->setType('vacancy', PARAM_INT);

        $mform->addElement('header', 'committeeheader', get_string('createcommittee', 'local_jobboard'));

        // Committee name.
        $mform->addElement('text', 'name', get_string('committeename', 'local_jobboard'), ['size' => 50]);
        $mform->setType('name', PARAM_TEXT);
        $mform->setDefault('name', get_string('defaultcommitteename', 'local_jobboard', $vacancy->title));
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        // Chair selection.
        $context = \context_system::instance();
        $potentialusers = get_users_by_capability($context, 'local/jobboard:manageworkflow',
            'u.id, u.firstname, u.lastname, u.email', 'u.lastname, u.firstname');

        $useroptions = [];
        foreach ($potentialusers as $user) {
            $useroptions[$user->id] = fullname($user) . ' (' . $user->email . ')';
        }

        $mform->addElement('select', 'chair', get_string('committeechair', 'local_jobboard'), $useroptions);
        $mform->addRule('chair', get_string('required'), 'required', null, 'client');

        // Initial members.
        $select = $mform->addElement('select', 'members', get_string('initialmembers', 'local_jobboard'),
            $useroptions);
        $select->setMultiple(true);
        $mform->addHelpButton('members', 'initialmembers', 'local_jobboard');

        $this->add_action_buttons(true, get_string('createcommittee', 'local_jobboard'));
    }
}

// Create committee if not exists.
if (!$committee) {
    $mform = new create_committee_form(null, ['vacancyid' => $vacancyid, 'vacancy' => $vacancy]);

    if ($mform->is_cancelled()) {
        redirect(new moodle_url('/local/jobboard/view_vacancy.php', ['id' => $vacancyid]));
    } else if ($data = $mform->get_data()) {
        // Build members array.
        $members = [];
        $members[] = ['userid' => $data->chair, 'role' => committee::ROLE_CHAIR];

        if (!empty($data->members)) {
            foreach ($data->members as $memberid) {
                if ($memberid != $data->chair) {
                    $members[] = ['userid' => $memberid, 'role' => committee::ROLE_EVALUATOR];
                }
            }
        }

        $committeeid = committee::create($vacancyid, $data->name, $members);

        if ($committeeid) {
            redirect(new moodle_url('/local/jobboard/manage_committee.php', ['vacancy' => $vacancyid]),
                get_string('committeecreated', 'local_jobboard'), null,
                \core\output\notification::NOTIFY_SUCCESS);
        }
    }

    // Display create form.
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('createcommittee', 'local_jobboard'));

    echo '<div class="alert alert-info">';
    echo '<strong>' . get_string('vacancy', 'local_jobboard') . ':</strong> ' .
        format_string($vacancy->code . ' - ' . $vacancy->title);
    echo '</div>';

    $mform->display();
    echo $OUTPUT->footer();
    exit;
}

// Display committee management.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('managecommittee', 'local_jobboard'));

// Vacancy info.
echo '<div class="card mb-4">';
echo '<div class="card-body">';
echo '<h5>' . format_string($vacancy->code . ' - ' . $vacancy->title) . '</h5>';
echo '<p class="mb-0"><strong>' . get_string('committeename', 'local_jobboard') . ':</strong> ' .
    format_string($committee->name) . '</p>';
echo '</div>';
echo '</div>';

// Committee members.
echo '<div class="card mb-4">';
echo '<div class="card-header d-flex justify-content-between align-items-center">';
echo '<h5 class="mb-0">' . get_string('committeemembers', 'local_jobboard') . '</h5>';
echo '<button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addMemberModal">';
echo '<i class="fa fa-plus mr-1"></i>' . get_string('addmember', 'local_jobboard');
echo '</button>';
echo '</div>';
echo '<div class="card-body">';

if (!empty($committee->members)) {
    $table = new html_table();
    $table->head = [
        get_string('name'),
        get_string('email'),
        get_string('role', 'local_jobboard'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'table table-striped';

    foreach ($committee->members as $member) {
        // Role badge.
        $roleclass = 'secondary';
        if ($member->role === 'chair') {
            $roleclass = 'primary';
        } else if ($member->role === 'secretary') {
            $roleclass = 'info';
        } else if ($member->role === 'observer') {
            $roleclass = 'warning';
        }

        // Role dropdown.
        $roleactions = '<div class="dropdown d-inline-block mr-2">';
        $roleactions .= '<button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" ' .
            'data-toggle="dropdown">' . get_string('role_' . $member->role, 'local_jobboard') . '</button>';
        $roleactions .= '<div class="dropdown-menu">';
        foreach (['chair', 'evaluator', 'secretary', 'observer'] as $role) {
            $roleactions .= '<a class="dropdown-item' . ($member->role === $role ? ' active' : '') . '" href="' .
                new moodle_url('/local/jobboard/manage_committee.php', [
                    'vacancy' => $vacancyid,
                    'action' => 'changerole',
                    'userid' => $member->userid,
                    'newrole' => $role,
                    'sesskey' => sesskey(),
                ]) . '">' . get_string('role_' . $role, 'local_jobboard') . '</a>';
        }
        $roleactions .= '</div></div>';

        // Remove button.
        $removeurl = new moodle_url('/local/jobboard/manage_committee.php', [
            'vacancy' => $vacancyid,
            'action' => 'removemember',
            'userid' => $member->userid,
            'sesskey' => sesskey(),
        ]);
        $removeactions = '<a href="' . $removeurl . '" class="btn btn-sm btn-outline-danger" ' .
            'onclick="return confirm(\'' . get_string('confirmremovemember', 'local_jobboard') . '\')">' .
            '<i class="fa fa-times"></i></a>';

        $table->data[] = [
            fullname($member),
            $member->email,
            $roleactions,
            $removeactions,
        ];
    }

    echo html_writer::table($table);
} else {
    echo '<p class="text-muted">' . get_string('nomembers', 'local_jobboard') . '</p>';
}

echo '</div>';
echo '</div>';

// Evaluation criteria.
echo '<div class="card mb-4">';
echo '<div class="card-header">';
echo '<h5 class="mb-0">' . get_string('evaluationcriteria', 'local_jobboard') . '</h5>';
echo '</div>';
echo '<div class="card-body">';

$criteria = committee::get_criteria($vacancyid);
if (!empty($criteria)) {
    echo '<table class="table table-sm">';
    echo '<thead><tr>';
    echo '<th>' . get_string('criterion', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('weight', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('maxscore', 'local_jobboard') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';
    foreach ($criteria as $criterion) {
        echo '<tr>';
        echo '<td>' . format_string($criterion->name);
        if (!empty($criterion->description)) {
            echo '<br><small class="text-muted">' . format_string($criterion->description) . '</small>';
        }
        echo '</td>';
        echo '<td>' . $criterion->weight . '</td>';
        echo '<td>' . $criterion->maxscore . '</td>';
        echo '</tr>';
    }
    echo '</tbody></table>';
} else {
    echo '<p class="text-muted">' . get_string('nocriteria', 'local_jobboard') . '</p>';
}

echo '<a href="' . new moodle_url('/local/jobboard/edit_criteria.php', ['vacancy' => $vacancyid]) .
    '" class="btn btn-outline-secondary">' .
    '<i class="fa fa-edit mr-1"></i>' . get_string('editcriteria', 'local_jobboard') . '</a>';

echo '</div>';
echo '</div>';

// Ranking & Evaluations.
$ranking = committee::get_ranking($vacancyid);
if (!empty($ranking)) {
    echo '<div class="card mb-4">';
    echo '<div class="card-header">';
    echo '<h5 class="mb-0">' . get_string('applicantranking', 'local_jobboard') . '</h5>';
    echo '</div>';
    echo '<div class="card-body">';

    echo '<table class="table table-striped">';
    echo '<thead><tr>';
    echo '<th>' . get_string('rank', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('applicant', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('avgscore', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('votes', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('status') . '</th>';
    echo '<th>' . get_string('actions') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    foreach ($ranking as $app) {
        $statusclass = 'secondary';
        if ($app->status === 'selected') {
            $statusclass = 'success';
        } else if ($app->status === 'rejected') {
            $statusclass = 'danger';
        }

        echo '<tr>';
        echo '<td><strong>#' . $app->rank . '</strong></td>';
        echo '<td>' . fullname($app) . '<br><small class="text-muted">' . $app->email . '</small></td>';
        echo '<td>' . round($app->avg_score, 1) . '</td>';
        echo '<td>';
        echo '<span class="badge badge-success">' . $app->approve_votes . ' ✓</span> ';
        echo '<span class="badge badge-danger">' . $app->reject_votes . ' ✗</span>';
        echo '</td>';
        echo '<td><span class="badge badge-' . $statusclass . '">' .
            get_string('status_' . $app->status, 'local_jobboard') . '</span></td>';
        echo '<td>';
        echo '<a href="' . new moodle_url('/local/jobboard/evaluate_application.php', [
            'application' => $app->id,
            'committee' => $committee->id,
        ]) . '" class="btn btn-sm btn-outline-primary">' . get_string('evaluate', 'local_jobboard') . '</a>';

        if (!in_array($app->status, ['selected', 'rejected'])) {
            echo ' <a href="' . new moodle_url('/local/jobboard/make_decision.php', [
                'application' => $app->id,
                'committee' => $committee->id,
            ]) . '" class="btn btn-sm btn-outline-success">' . get_string('decide', 'local_jobboard') . '</a>';
        }
        echo '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';
    echo '</div>';
    echo '</div>';
}

// Add member modal.
echo '<div class="modal fade" id="addMemberModal" tabindex="-1" role="dialog">';
echo '<div class="modal-dialog" role="document">';
echo '<div class="modal-content">';
echo '<div class="modal-header">';
echo '<h5 class="modal-title">' . get_string('addmember', 'local_jobboard') . '</h5>';
echo '<button type="button" class="close" data-dismiss="modal">';
echo '<span>&times;</span>';
echo '</button>';
echo '</div>';
echo '<form method="get" action="">';
echo '<div class="modal-body">';
echo '<input type="hidden" name="vacancy" value="' . $vacancyid . '">';
echo '<input type="hidden" name="action" value="addmember">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';

// User selection.
$potentialusers = get_users_by_capability($context, 'local/jobboard:manageworkflow',
    'u.id, u.firstname, u.lastname, u.email', 'u.lastname, u.firstname');

// Exclude existing members.
$existingids = array_column($committee->members, 'userid');

echo '<div class="form-group">';
echo '<label for="userid">' . get_string('user') . '</label>';
echo '<select name="userid" id="userid" class="form-control" required>';
echo '<option value="">' . get_string('select') . '</option>';
foreach ($potentialusers as $user) {
    if (!in_array($user->id, $existingids)) {
        echo '<option value="' . $user->id . '">' . fullname($user) . ' (' . $user->email . ')</option>';
    }
}
echo '</select>';
echo '</div>';

echo '<div class="form-group">';
echo '<label for="role">' . get_string('role', 'local_jobboard') . '</label>';
echo '<select name="role" id="role" class="form-control" required>';
echo '<option value="evaluator">' . get_string('role_evaluator', 'local_jobboard') . '</option>';
echo '<option value="secretary">' . get_string('role_secretary', 'local_jobboard') . '</option>';
echo '<option value="observer">' . get_string('role_observer', 'local_jobboard') . '</option>';
echo '<option value="chair">' . get_string('role_chair', 'local_jobboard') . '</option>';
echo '</select>';
echo '</div>';

echo '</div>';
echo '<div class="modal-footer">';
echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">' . get_string('cancel') . '</button>';
echo '<button type="submit" class="btn btn-primary">' . get_string('add') . '</button>';
echo '</div>';
echo '</form>';
echo '</div>';
echo '</div>';
echo '</div>';

echo $OUTPUT->footer();
