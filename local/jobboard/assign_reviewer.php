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
 * Assign reviewers to applications.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\reviewer;
use local_jobboard\application;

$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageworkflow', $context);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/assign_reviewer.php', ['vacancyid' => $vacancyid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('assignreviewer', 'local_jobboard'));
$PAGE->set_heading(get_string('assignreviewer', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('managevacancies', 'local_jobboard'), new moodle_url('/local/jobboard/index.php', ['view' => 'manage']));
$PAGE->navbar->add(get_string('assignreviewer', 'local_jobboard'));

// Handle actions.
if ($action === 'assign') {
    require_sesskey();

    $applicationids = required_param_array('applications', PARAM_INT);
    $reviewerid = required_param('reviewerid', PARAM_INT);

    $results = reviewer::bulk_assign($applicationids, $reviewerid);

    $message = get_string('reviewerassigned', 'local_jobboard', $results);
    redirect($PAGE->url, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($action === 'autoassign') {
    require_sesskey();

    $maxperreviewer = optional_param('maxperreviewer', 20, PARAM_INT);
    $assigned = reviewer::auto_assign($vacancyid ?: null, $maxperreviewer);

    $message = get_string('autoassigncomplete', 'local_jobboard', $assigned);
    redirect($PAGE->url, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($action === 'unassign') {
    require_sesskey();

    $applicationid = required_param('applicationid', PARAM_INT);
    reviewer::unassign($applicationid);

    redirect($PAGE->url, get_string('reviewerunassigned', 'local_jobboard'), null,
        \core\output\notification::NOTIFY_SUCCESS);
}

// Get vacancy list for filter.
$vacancies = $DB->get_records_select('local_jobboard_vacancy',
    "status IN ('published', 'closed')", null, 'code ASC', 'id, code, title');

// Get reviewers with workload.
$reviewers = reviewer::get_all_with_workload();

// Get unassigned applications.
$filters = ['reviewerid_null' => true];
if ($vacancyid) {
    $filters['vacancyid'] = $vacancyid;
}
$unassignedresult = application::get_list($filters, 'timecreated', 'ASC', 0, 100);
$unassigned = $unassignedresult['applications'];

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('assignreviewer', 'local_jobboard'));

// Vacancy filter.
echo '<form class="form-inline mb-4" method="get">';
echo '<label class="mr-2">' . get_string('vacancy', 'local_jobboard') . '</label>';
echo '<select name="vacancyid" class="form-control mr-2">';
echo '<option value="0">' . get_string('allvacancies', 'local_jobboard') . '</option>';
foreach ($vacancies as $v) {
    $selected = ($vacancyid == $v->id) ? 'selected' : '';
    echo "<option value=\"{$v->id}\" {$selected}>" . format_string($v->code . ' - ' . $v->title) . '</option>';
}
echo '</select>';
echo '<button type="submit" class="btn btn-primary">' . get_string('filter') . '</button>';
echo '</form>';

// Reviewer workload summary.
echo '<div class="card mb-4">';
echo '<div class="card-header"><h5>' . get_string('reviewerworkload', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';

if (empty($reviewers)) {
    echo '<p>' . get_string('noreviewers', 'local_jobboard') . '</p>';
} else {
    echo '<div class="row">';
    foreach ($reviewers as $rev) {
        $workloadclass = 'success';
        if ($rev->workload > 15) {
            $workloadclass = 'danger';
        } else if ($rev->workload > 10) {
            $workloadclass = 'warning';
        }

        echo '<div class="col-md-4 col-sm-6 mb-3">';
        echo '<div class="card">';
        echo '<div class="card-body">';
        echo '<h6>' . fullname($rev) . '</h6>';
        echo '<span class="badge badge-' . $workloadclass . '">' . $rev->workload . ' ' .
            get_string('activeassignments', 'local_jobboard') . '</span>';
        if (isset($rev->stats)) {
            echo '<br><small class="text-muted">';
            echo get_string('reviewed', 'local_jobboard') . ': ' . $rev->stats['reviewed'];
            echo ' | ' . get_string('avgtime', 'local_jobboard') . ': ' . $rev->stats['avg_review_time'] . 'h';
            echo '</small>';
        }
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
    echo '</div>';
}
echo '</div>';
echo '</div>';

// Auto-assign section.
echo '<div class="card mb-4">';
echo '<div class="card-header"><h5>' . get_string('autoassign', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';
echo '<form method="post" action="' . $PAGE->url . '" class="form-inline">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
echo '<input type="hidden" name="action" value="autoassign">';
echo '<label class="mr-2">' . get_string('maxperreviewer', 'local_jobboard') . '</label>';
echo '<input type="number" name="maxperreviewer" value="20" min="1" max="100" class="form-control mr-2" style="width: 100px">';
echo '<button type="submit" class="btn btn-primary">' . get_string('autoassignall', 'local_jobboard') . '</button>';
echo '</form>';
echo '<small class="text-muted mt-2 d-block">' . get_string('autoassignhelp', 'local_jobboard') . '</small>';
echo '</div>';
echo '</div>';

// Manual assignment section.
echo '<div class="card mb-4">';
echo '<div class="card-header"><h5>' . get_string('manualassign', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';

if (empty($unassigned)) {
    echo '<p>' . get_string('nounassignedapplications', 'local_jobboard') . '</p>';
} else {
    echo '<form method="post" action="' . $PAGE->url . '">';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
    echo '<input type="hidden" name="action" value="assign">';

    // Reviewer selection.
    echo '<div class="form-group mb-3">';
    echo '<label>' . get_string('assignto', 'local_jobboard') . '</label>';
    echo '<select name="reviewerid" class="form-control" required>';
    echo '<option value="">' . get_string('selectreviewer', 'local_jobboard') . '</option>';
    foreach ($reviewers as $rev) {
        echo '<option value="' . $rev->id . '">' . fullname($rev) . ' (' . $rev->workload . ' ' .
            get_string('assigned', 'local_jobboard') . ')</option>';
    }
    echo '</select>';
    echo '</div>';

    // Select all.
    echo '<div class="mb-2">';
    echo '<div class="form-check">';
    echo '<input type="checkbox" class="form-check-input" id="selectallapp">';
    echo '<label class="form-check-label" for="selectallapp">' . get_string('selectall') . '</label>';
    echo '</div>';
    echo '</div>';

    // Applications table.
    echo '<table class="table table-striped">';
    echo '<thead><tr>';
    echo '<th width="40"></th>';
    echo '<th>' . get_string('applicant', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('vacancy', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('status', 'local_jobboard') . '</th>';
    echo '<th>' . get_string('dateapplied', 'local_jobboard') . '</th>';
    echo '</tr></thead>';
    echo '<tbody>';

    foreach ($unassigned as $app) {
        echo '<tr>';
        echo '<td><input type="checkbox" name="applications[]" value="' . $app->id . '" class="app-checkbox"></td>';
        echo '<td>' . format_string($app->userfirstname . ' ' . $app->userlastname) . '</td>';
        echo '<td>' . format_string($app->vacancy_code ?? '') . '</td>';
        echo '<td>' . get_string('status_' . $app->status, 'local_jobboard') . '</td>';
        echo '<td>' . userdate($app->timecreated, get_string('strftimedatetime', 'langconfig')) . '</td>';
        echo '</tr>';
    }

    echo '</tbody></table>';

    echo '<button type="submit" class="btn btn-primary">' . get_string('assignselected', 'local_jobboard') . '</button>';
    echo '</form>';

    echo '<script>
    document.getElementById("selectallapp").addEventListener("change", function() {
        var checkboxes = document.querySelectorAll(".app-checkbox");
        checkboxes.forEach(function(cb) {
            cb.checked = this.checked;
        }.bind(this));
    });
    </script>';
}

echo '</div>';
echo '</div>';

echo $OUTPUT->footer();
