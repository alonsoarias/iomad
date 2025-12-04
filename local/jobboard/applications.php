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
 * User's applications list page.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/tablelib.php');

use local_jobboard\application;
use local_jobboard\exemption;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:apply', $context);

$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$status = optional_param('status', '', PARAM_ALPHA);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/applications.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('myapplications', 'local_jobboard'));
$PAGE->set_heading(get_string('myapplications', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('myapplications', 'local_jobboard'));

// Get user's applications.
$filters = ['userid' => $USER->id];
if (!empty($status)) {
    $filters['status'] = $status;
}

$result = application::get_list($filters, 'timecreated', 'DESC', $page, $perpage);
$applications = $result['applications'];
$total = $result['total'];

// Check for exemption.
$exemption = exemption::get_active_for_user($USER->id);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('myapplications', 'local_jobboard'));

// Show exemption status if applicable.
if ($exemption) {
    echo '<div class="alert alert-info">';
    echo '<strong>' . get_string('exemptionactive', 'local_jobboard') . '</strong><br>';
    echo get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard');
    if (!empty($exemption->documentref)) {
        echo ' (' . format_string($exemption->documentref) . ')';
    }
    echo '</div>';
}

// Status filter.
$statusoptions = ['' => get_string('allstatuses', 'local_jobboard')];
foreach (application::STATUSES as $s) {
    $statusoptions[$s] = get_string('status_' . $s, 'local_jobboard');
}

echo '<form class="form-inline mb-3" method="get">';
echo '<label class="mr-2">' . get_string('filterbystatus', 'local_jobboard') . '</label>';
echo '<select name="status" class="form-control mr-2">';
foreach ($statusoptions as $value => $label) {
    $selected = ($status === $value) ? 'selected' : '';
    echo "<option value=\"{$value}\" {$selected}>{$label}</option>";
}
echo '</select>';
echo '<button type="submit" class="btn btn-primary">' . get_string('filter') . '</button>';
echo '</form>';

if (empty($applications)) {
    echo $OUTPUT->notification(get_string('noapplicationsfound', 'local_jobboard'), 'info');

    // Link to browse vacancies.
    echo '<p><a href="' . new moodle_url('/local/jobboard/vacancies.php') . '" class="btn btn-primary">';
    echo get_string('browsevacancies', 'local_jobboard');
    echo '</a></p>';
} else {
    // Build table.
    $table = new html_table();
    $table->head = [
        get_string('vacancy', 'local_jobboard'),
        get_string('dateapplied', 'local_jobboard'),
        get_string('status', 'local_jobboard'),
        get_string('documents', 'local_jobboard'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable applications-table';

    foreach ($applications as $app) {
        $row = [];

        // Vacancy info.
        $vacancyurl = new moodle_url('/local/jobboard/vacancy.php', ['id' => $app->vacancyid]);
        $row[] = html_writer::link($vacancyurl, format_string($app->vacancy_title ?? get_string('unknownvacancy', 'local_jobboard')));

        // Date applied.
        $row[] = userdate($app->timecreated, get_string('strftimedatetime', 'langconfig'));

        // Status with badge.
        $statusclass = 'badge-secondary';
        switch ($app->status) {
            case 'submitted':
                $statusclass = 'badge-info';
                break;
            case 'under_review':
                $statusclass = 'badge-primary';
                break;
            case 'docs_validated':
                $statusclass = 'badge-success';
                break;
            case 'docs_rejected':
                $statusclass = 'badge-danger';
                break;
            case 'interview':
                $statusclass = 'badge-warning';
                break;
            case 'selected':
                $statusclass = 'badge-success';
                break;
            case 'rejected':
                $statusclass = 'badge-danger';
                break;
            case 'withdrawn':
                $statusclass = 'badge-secondary';
                break;
        }
        $row[] = '<span class="badge ' . $statusclass . '">' .
            get_string('status_' . $app->status, 'local_jobboard') . '</span>';

        // Document count.
        $doccount = $app->document_count ?? 0;
        $row[] = $doccount . ' ' . get_string('documentsuploaded', 'local_jobboard');

        // Actions.
        $actions = [];
        $viewurl = new moodle_url('/local/jobboard/application.php', ['id' => $app->id]);
        $actions[] = html_writer::link($viewurl, get_string('view'), ['class' => 'btn btn-sm btn-outline-primary']);

        // Withdraw button if applicable.
        if (in_array($app->status, ['submitted', 'under_review'])) {
            $withdrawurl = new moodle_url('/local/jobboard/application.php', ['id' => $app->id, 'action' => 'withdraw']);
            $actions[] = html_writer::link($withdrawurl, get_string('withdraw', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-danger']);
        }

        $row[] = implode(' ', $actions);

        $table->data[] = $row;
    }

    echo html_writer::table($table);

    // Pagination.
    $baseurl = new moodle_url('/local/jobboard/applications.php', ['status' => $status]);
    echo $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
}

echo $OUTPUT->footer();
