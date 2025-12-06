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
 * Schedule interview page.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

use local_jobboard\interview;
use local_jobboard\application;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageworkflow', $context);

// Parameters.
$applicationid = required_param('application', PARAM_INT);
$interviewid = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

// Get application info.
$application = $DB->get_record('local_jobboard_application', ['id' => $applicationid], '*', MUST_EXIST);
$vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $application->vacancyid], '*', MUST_EXIST);
$applicant = $DB->get_record('user', ['id' => $application->userid], '*', MUST_EXIST);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/schedule_interview.php', ['application' => $applicationid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('scheduleinterview', 'local_jobboard'));
$PAGE->set_heading(get_string('scheduleinterview', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Handle actions.
if ($action === 'cancel' && $interviewid) {
    require_sesskey();
    $reason = optional_param('reason', '', PARAM_TEXT);

    if (interview::cancel($interviewid, $reason)) {
        redirect(new moodle_url('/local/jobboard/schedule_interview.php', ['application' => $applicationid]),
            get_string('interviewcancelled', 'local_jobboard'), null,
            \core\output\notification::NOTIFY_SUCCESS);
    }
}

if ($action === 'complete' && $interviewid) {
    // Show completion form.
    $interviewdetails = interview::get_details($interviewid);

    class complete_interview_form extends moodleform {
        protected function definition() {
            $mform = $this->_form;

            $mform->addElement('hidden', 'application', $this->_customdata['applicationid']);
            $mform->setType('application', PARAM_INT);
            $mform->addElement('hidden', 'id', $this->_customdata['interviewid']);
            $mform->setType('id', PARAM_INT);
            $mform->addElement('hidden', 'action', 'savecomplete');
            $mform->setType('action', PARAM_ALPHA);

            $mform->addElement('header', 'completeheader', get_string('completeinterview', 'local_jobboard'));

            // Rating.
            $ratings = [
                1 => '1 - ' . get_string('rating_poor', 'local_jobboard'),
                2 => '2 - ' . get_string('rating_fair', 'local_jobboard'),
                3 => '3 - ' . get_string('rating_good', 'local_jobboard'),
                4 => '4 - ' . get_string('rating_verygood', 'local_jobboard'),
                5 => '5 - ' . get_string('rating_excellent', 'local_jobboard'),
            ];
            $mform->addElement('select', 'rating', get_string('overallrating', 'local_jobboard'), $ratings);
            $mform->setDefault('rating', 3);
            $mform->addRule('rating', get_string('required'), 'required', null, 'client');

            // Recommendation.
            $recommendations = [
                'hire' => get_string('recommend_hire', 'local_jobboard'),
                'further_review' => get_string('recommend_furtherreview', 'local_jobboard'),
                'reject' => get_string('recommend_reject', 'local_jobboard'),
            ];
            $mform->addElement('select', 'recommendation', get_string('recommendation', 'local_jobboard'), $recommendations);
            $mform->addRule('recommendation', get_string('required'), 'required', null, 'client');

            // Feedback.
            $mform->addElement('textarea', 'feedback', get_string('interviewfeedback', 'local_jobboard'),
                ['rows' => 6, 'cols' => 60]);
            $mform->setType('feedback', PARAM_TEXT);

            $this->add_action_buttons(true, get_string('saveresults', 'local_jobboard'));
        }
    }

    $mform = new complete_interview_form(null, [
        'applicationid' => $applicationid,
        'interviewid' => $interviewid,
    ]);

    if ($mform->is_cancelled()) {
        redirect(new moodle_url('/local/jobboard/schedule_interview.php', ['application' => $applicationid]));
    }

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('completeinterview', 'local_jobboard'));

    echo '<div class="alert alert-info">';
    echo '<strong>' . get_string('applicant', 'local_jobboard') . ':</strong> ' .
        fullname($applicant) . '<br>';
    echo '<strong>' . get_string('vacancy', 'local_jobboard') . ':</strong> ' .
        format_string($vacancy->title) . '<br>';
    echo '<strong>' . get_string('interviewdate', 'local_jobboard') . ':</strong> ' .
        userdate($interviewdetails->scheduledtime, get_string('strftimedatetime', 'langconfig'));
    echo '</div>';

    $mform->display();
    echo $OUTPUT->footer();
    exit;
}

if ($action === 'savecomplete' && $interviewid) {
    require_sesskey();
    $rating = required_param('rating', PARAM_INT);
    $recommendation = required_param('recommendation', PARAM_ALPHA);
    $feedback = optional_param('feedback', '', PARAM_TEXT);

    if (interview::complete($interviewid, $rating, $feedback, $recommendation)) {
        redirect(new moodle_url('/local/jobboard/schedule_interview.php', ['application' => $applicationid]),
            get_string('interviewcompleted', 'local_jobboard'), null,
            \core\output\notification::NOTIFY_SUCCESS);
    }
}

if ($action === 'noshow' && $interviewid) {
    require_sesskey();
    $notes = optional_param('notes', '', PARAM_TEXT);

    if (interview::mark_noshow($interviewid, $notes)) {
        redirect(new moodle_url('/local/jobboard/schedule_interview.php', ['application' => $applicationid]),
            get_string('markedasnoshow', 'local_jobboard'), null,
            \core\output\notification::NOTIFY_WARNING);
    }
}

// Interview scheduling form.
class schedule_interview_form extends moodleform {
    protected function definition() {
        global $DB;

        $mform = $this->_form;
        $applicationid = $this->_customdata['applicationid'];

        $mform->addElement('hidden', 'application', $applicationid);
        $mform->setType('application', PARAM_INT);

        $mform->addElement('header', 'scheduleheader', get_string('scheduleinterview', 'local_jobboard'));

        // Date and time.
        $mform->addElement('date_time_selector', 'scheduledtime', get_string('dateandtime', 'local_jobboard'));
        $mform->setDefault('scheduledtime', time() + 86400); // Tomorrow.
        $mform->addRule('scheduledtime', get_string('required'), 'required', null, 'client');

        // Duration.
        $durations = [
            15 => '15 ' . get_string('minutes'),
            30 => '30 ' . get_string('minutes'),
            45 => '45 ' . get_string('minutes'),
            60 => '1 ' . get_string('hour'),
            90 => '1.5 ' . get_string('hours'),
            120 => '2 ' . get_string('hours'),
        ];
        $mform->addElement('select', 'duration', get_string('duration', 'local_jobboard'), $durations);
        $mform->setDefault('duration', 30);

        // Interview type.
        $types = [
            'inperson' => get_string('interviewtype_inperson', 'local_jobboard'),
            'video' => get_string('interviewtype_video', 'local_jobboard'),
            'phone' => get_string('interviewtype_phone', 'local_jobboard'),
        ];
        $mform->addElement('select', 'interviewtype', get_string('interviewtype', 'local_jobboard'), $types);
        $mform->setDefault('interviewtype', 'inperson');

        // Location.
        $mform->addElement('text', 'location', get_string('locationorurl', 'local_jobboard'), ['size' => 60]);
        $mform->setType('location', PARAM_TEXT);
        $mform->addHelpButton('location', 'locationorurl', 'local_jobboard');

        // Interviewers.
        $mform->addElement('header', 'interviewersheader', get_string('interviewers', 'local_jobboard'));

        // Get users with interview capability.
        $context = \context_system::instance();
        $potentialinterviewers = get_users_by_capability($context, 'local/jobboard:manageworkflow',
            'u.id, u.firstname, u.lastname, u.email', 'u.lastname, u.firstname');

        $intervieweroptions = [];
        foreach ($potentialinterviewers as $user) {
            $intervieweroptions[$user->id] = fullname($user) . ' (' . $user->email . ')';
        }

        $select = $mform->addElement('select', 'interviewers', get_string('selectinterviewers', 'local_jobboard'),
            $intervieweroptions);
        $select->setMultiple(true);
        $mform->addRule('interviewers', get_string('selectatleastone', 'local_jobboard'), 'required', null, 'client');

        // Notes.
        $mform->addElement('header', 'notesheader', get_string('additionalinfo', 'local_jobboard'));
        $mform->addElement('textarea', 'notes', get_string('interviewinstructions', 'local_jobboard'),
            ['rows' => 4, 'cols' => 60]);
        $mform->setType('notes', PARAM_TEXT);

        $this->add_action_buttons(true, get_string('scheduleinterview', 'local_jobboard'));
    }

    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check date is in the future.
        if ($data['scheduledtime'] < time()) {
            $errors['scheduledtime'] = get_string('error:pastdate', 'local_jobboard');
        }

        // Check for conflicts.
        if (!empty($data['interviewers'])) {
            foreach ($data['interviewers'] as $interviewerid) {
                if (interview::has_conflict($interviewerid, $data['scheduledtime'], $data['duration'])) {
                    $errors['scheduledtime'] = get_string('error:schedulingconflict', 'local_jobboard');
                    break;
                }
            }
        }

        return $errors;
    }
}

$mform = new schedule_interview_form(null, ['applicationid' => $applicationid]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $applicationid]));
} else if ($data = $mform->get_data()) {
    $interviewid = interview::schedule(
        $applicationid,
        $data->scheduledtime,
        $data->duration,
        $data->interviewtype,
        $data->location,
        $data->interviewers,
        $data->notes
    );

    if ($interviewid) {
        redirect(new moodle_url('/local/jobboard/schedule_interview.php', ['application' => $applicationid]),
            get_string('interviewscheduled', 'local_jobboard'), null,
            \core\output\notification::NOTIFY_SUCCESS);
    } else {
        redirect(new moodle_url('/local/jobboard/schedule_interview.php', ['application' => $applicationid]),
            get_string('interviewscheduleerror', 'local_jobboard'), null,
            \core\output\notification::NOTIFY_ERROR);
    }
}

// Display page.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('scheduleinterview', 'local_jobboard'));

// Applicant info card.
echo '<div class="card mb-4">';
echo '<div class="card-header">';
echo '<h5 class="mb-0">' . get_string('applicantinfo', 'local_jobboard') . '</h5>';
echo '</div>';
echo '<div class="card-body">';
echo '<div class="row">';
echo '<div class="col-md-6">';
echo '<dl>';
echo '<dt>' . get_string('name') . '</dt>';
echo '<dd>' . fullname($applicant) . '</dd>';
echo '<dt>' . get_string('email') . '</dt>';
echo '<dd>' . $applicant->email . '</dd>';
echo '</dl>';
echo '</div>';
echo '<div class="col-md-6">';
echo '<dl>';
echo '<dt>' . get_string('vacancy', 'local_jobboard') . '</dt>';
echo '<dd>' . format_string($vacancy->code . ' - ' . $vacancy->title) . '</dd>';
echo '<dt>' . get_string('status') . '</dt>';
echo '<dd><span class="badge badge-primary">' .
    get_string('status_' . $application->status, 'local_jobboard') . '</span></dd>';
echo '</dl>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';

// Existing interviews.
$interviews = interview::get_for_application($applicationid);
if (!empty($interviews)) {
    echo '<div class="card mb-4">';
    echo '<div class="card-header">';
    echo '<h5 class="mb-0">' . get_string('scheduledinterviews', 'local_jobboard') . '</h5>';
    echo '</div>';
    echo '<div class="card-body">';

    $table = new html_table();
    $table->head = [
        get_string('dateandtime', 'local_jobboard'),
        get_string('type'),
        get_string('location'),
        get_string('status'),
        get_string('result', 'local_jobboard'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'table table-striped';

    foreach ($interviews as $int) {
        // Status badge.
        $statusclass = 'secondary';
        if ($int->status === 'confirmed') {
            $statusclass = 'success';
        } else if ($int->status === 'completed') {
            $statusclass = 'info';
        } else if (in_array($int->status, ['cancelled', 'noshow'])) {
            $statusclass = 'danger';
        } else if ($int->status === 'rescheduled') {
            $statusclass = 'warning';
        }

        // Result.
        $result = '-';
        if ($int->status === 'completed' && !empty($int->recommendation)) {
            $recclass = $int->recommendation === 'hire' ? 'success' :
                ($int->recommendation === 'reject' ? 'danger' : 'warning');
            $result = '<span class="badge badge-' . $recclass . '">' .
                get_string('recommend_' . $int->recommendation, 'local_jobboard') . '</span>';
            $result .= '<br><small>Rating: ' . $int->rating . '/5</small>';
        }

        // Actions.
        $actions = '';
        if (in_array($int->status, ['scheduled', 'confirmed'])) {
            $actions .= '<a href="' . new moodle_url('/local/jobboard/schedule_interview.php', [
                'application' => $applicationid,
                'id' => $int->id,
                'action' => 'complete',
            ]) . '" class="btn btn-sm btn-success mr-1" title="' .
                get_string('complete') . '"><i class="fa fa-check"></i></a>';

            $actions .= '<a href="' . new moodle_url('/local/jobboard/schedule_interview.php', [
                'application' => $applicationid,
                'id' => $int->id,
                'action' => 'noshow',
                'sesskey' => sesskey(),
            ]) . '" class="btn btn-sm btn-warning mr-1" title="' .
                get_string('noshow', 'local_jobboard') . '" onclick="return confirm(\'' .
                get_string('confirmnoshow', 'local_jobboard') . '\')"><i class="fa fa-user-times"></i></a>';

            $actions .= '<a href="' . new moodle_url('/local/jobboard/schedule_interview.php', [
                'application' => $applicationid,
                'id' => $int->id,
                'action' => 'cancel',
                'sesskey' => sesskey(),
            ]) . '" class="btn btn-sm btn-danger" title="' .
                get_string('cancel') . '" onclick="return confirm(\'' .
                get_string('confirmcancel', 'local_jobboard') . '\')"><i class="fa fa-times"></i></a>';
        }

        $table->data[] = [
            userdate($int->scheduledtime, get_string('strftimedatetime', 'langconfig')) .
                '<br><small>' . $int->duration . ' ' . get_string('minutes') . '</small>',
            get_string('interviewtype_' . $int->interviewtype, 'local_jobboard'),
            format_string($int->location),
            '<span class="badge badge-' . $statusclass . '">' .
                get_string('interviewstatus_' . $int->status, 'local_jobboard') . '</span>',
            $result,
            $actions,
        ];
    }

    echo html_writer::table($table);
    echo '</div>';
    echo '</div>';
}

// New interview form.
echo '<div class="card">';
echo '<div class="card-header">';
echo '<h5 class="mb-0">' . get_string('schedulenewinterview', 'local_jobboard') . '</h5>';
echo '</div>';
echo '<div class="card-body">';
$mform->display();
echo '</div>';
echo '</div>';

echo $OUTPUT->footer();
