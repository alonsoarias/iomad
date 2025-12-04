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
 * Manage applications for a vacancy.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/tablelib.php');

use local_jobboard\vacancy;
use local_jobboard\application;

$vacancyid = required_param('vacancyid', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$status = optional_param('status', '', PARAM_ALPHA);
$search = optional_param('search', '', PARAM_TEXT);
$sort = optional_param('sort', 'timecreated', PARAM_ALPHA);
$order = optional_param('order', 'DESC', PARAM_ALPHA);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:reviewdocuments', $context);

// Get vacancy.
$vacancy = vacancy::get($vacancyid);
if (!$vacancy) {
    throw new moodle_exception('vacancynotfound', 'local_jobboard');
}

// Set up page.
$baseurl = new moodle_url('/local/jobboard/manage_applications.php', [
    'vacancyid' => $vacancyid,
    'status' => $status,
    'search' => $search,
    'sort' => $sort,
    'order' => $order,
]);

$PAGE->set_url($baseurl);
$PAGE->set_context($context);
$PAGE->set_title(get_string('manageapplications', 'local_jobboard'));
$PAGE->set_heading(get_string('manageapplications', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('managevacancies', 'local_jobboard'), new moodle_url('/local/jobboard/manage.php'));
$PAGE->navbar->add(format_string($vacancy->code));
$PAGE->navbar->add(get_string('applications', 'local_jobboard'));

// Get applications.
$filters = ['vacancyid' => $vacancyid];
if (!empty($status)) {
    $filters['status'] = $status;
}
if (!empty($search)) {
    $filters['search'] = $search;
}

$result = application::get_list($filters, $sort, $order, $page, $perpage);
$applications = $result['applications'];
$total = $result['total'];

// Get application stats.
$stats = application::get_stats_for_vacancy($vacancyid);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('manageapplications', 'local_jobboard') . ': ' . format_string($vacancy->title));

// Back link.
echo '<p><a href="' . new moodle_url('/local/jobboard/manage.php') . '">' .
    get_string('backtomanage', 'local_jobboard') . '</a></p>';

// Stats cards.
echo '<div class="row mb-4">';
$statuscards = [
    'submitted' => 'info',
    'under_review' => 'primary',
    'docs_validated' => 'success',
    'docs_rejected' => 'danger',
    'interview' => 'warning',
    'selected' => 'success',
    'rejected' => 'danger',
    'withdrawn' => 'secondary',
];

foreach ($statuscards as $s => $color) {
    $count = $stats[$s] ?? 0;
    echo '<div class="col-md-3 col-sm-6 mb-2">';
    echo '<div class="card bg-' . $color . ' text-white">';
    echo '<div class="card-body text-center">';
    echo '<h3>' . $count . '</h3>';
    echo '<p class="mb-0">' . get_string('status_' . $s, 'local_jobboard') . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
echo '</div>';

// Filters.
echo '<form class="form-inline mb-3" method="get">';
echo '<input type="hidden" name="vacancyid" value="' . $vacancyid . '">';

echo '<div class="form-group mr-2">';
echo '<label class="sr-only">' . get_string('search') . '</label>';
echo '<input type="text" name="search" class="form-control" placeholder="' .
    get_string('searchapplicant', 'local_jobboard') . '" value="' . s($search) . '">';
echo '</div>';

echo '<div class="form-group mr-2">';
echo '<select name="status" class="form-control">';
echo '<option value="">' . get_string('allstatuses', 'local_jobboard') . '</option>';
foreach (application::STATUSES as $s) {
    $selected = ($status === $s) ? 'selected' : '';
    echo '<option value="' . $s . '" ' . $selected . '>' .
        get_string('status_' . $s, 'local_jobboard') . '</option>';
}
echo '</select>';
echo '</div>';

echo '<button type="submit" class="btn btn-primary mr-2">' . get_string('filter') . '</button>';
echo '<a href="' . new moodle_url('/local/jobboard/manage_applications.php', ['vacancyid' => $vacancyid]) .
    '" class="btn btn-secondary">' . get_string('reset') . '</a>';
echo '</form>';

if (empty($applications)) {
    echo $OUTPUT->notification(get_string('noapplicationsfound', 'local_jobboard'), 'info');
} else {
    // Build sortable table.
    $table = new html_table();
    $table->head = [];
    $table->attributes['class'] = 'generaltable applications-table';

    // Sortable headers.
    $columns = [
        'applicant' => get_string('applicant', 'local_jobboard'),
        'timecreated' => get_string('dateapplied', 'local_jobboard'),
        'status' => get_string('status', 'local_jobboard'),
        'documents' => get_string('documents', 'local_jobboard'),
        'actions' => get_string('actions'),
    ];

    foreach ($columns as $col => $label) {
        if (in_array($col, ['timecreated', 'status'])) {
            // Sortable column.
            $neworder = ($sort === $col && $order === 'ASC') ? 'DESC' : 'ASC';
            $sorturl = new moodle_url('/local/jobboard/manage_applications.php', [
                'vacancyid' => $vacancyid,
                'status' => $status,
                'search' => $search,
                'sort' => $col,
                'order' => $neworder,
            ]);
            $icon = '';
            if ($sort === $col) {
                $icon = ($order === 'ASC') ? ' ▲' : ' ▼';
            }
            $table->head[] = html_writer::link($sorturl, $label) . $icon;
        } else {
            $table->head[] = $label;
        }
    }

    foreach ($applications as $app) {
        $row = [];

        // Applicant info.
        $applicanthtml = '<strong>' . format_string($app->userfirstname . ' ' . $app->userlastname) . '</strong>';
        $applicanthtml .= '<br><small>' . format_string($app->useremail) . '</small>';
        if ($app->isexemption) {
            $applicanthtml .= '<br><span class="badge badge-info">' .
                get_string('exemption', 'local_jobboard') . '</span>';
        }
        $row[] = $applicanthtml;

        // Date applied.
        $row[] = userdate($app->timecreated, get_string('strftimedatetime', 'langconfig'));

        // Status.
        $statusclass = 'badge-secondary';
        switch ($app->status) {
            case 'submitted':
                $statusclass = 'badge-info';
                break;
            case 'under_review':
                $statusclass = 'badge-primary';
                break;
            case 'docs_validated':
            case 'selected':
                $statusclass = 'badge-success';
                break;
            case 'docs_rejected':
            case 'rejected':
                $statusclass = 'badge-danger';
                break;
            case 'interview':
                $statusclass = 'badge-warning';
                break;
            case 'withdrawn':
                $statusclass = 'badge-secondary';
                break;
        }
        $row[] = '<span class="badge ' . $statusclass . '">' .
            get_string('status_' . $app->status, 'local_jobboard') . '</span>';

        // Documents.
        $docstatus = '';
        $pendingdocs = $app->pending_validations ?? 0;
        $totaldocs = $app->document_count ?? 0;
        if ($totaldocs > 0) {
            $validated = $totaldocs - $pendingdocs;
            $docstatus = "{$validated}/{$totaldocs} " . get_string('validated', 'local_jobboard');
            if ($pendingdocs > 0) {
                $docstatus .= '<br><span class="badge badge-warning">' . $pendingdocs . ' ' .
                    get_string('pending', 'local_jobboard') . '</span>';
            }
        } else {
            $docstatus = get_string('nodocuments', 'local_jobboard');
        }
        $row[] = $docstatus;

        // Actions.
        $actions = [];
        $viewurl = new moodle_url('/local/jobboard/application.php', ['id' => $app->id]);
        $actions[] = html_writer::link($viewurl, get_string('view'), ['class' => 'btn btn-sm btn-primary']);

        $row[] = implode(' ', $actions);

        $table->data[] = $row;
    }

    echo html_writer::table($table);

    // Pagination.
    echo $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
}

// Export buttons.
echo '<div class="mt-4">';
echo '<h5>' . get_string('export', 'local_jobboard') . '</h5>';
$exporturl = new moodle_url('/local/jobboard/export_applications.php', [
    'vacancyid' => $vacancyid,
    'status' => $status,
]);
echo '<a href="' . $exporturl . '&format=csv" class="btn btn-outline-primary mr-2">' .
    get_string('exportcsv', 'local_jobboard') . '</a>';
echo '<a href="' . $exporturl . '&format=excel" class="btn btn-outline-primary">' .
    get_string('exportexcel', 'local_jobboard') . '</a>';
echo '</div>';

echo $OUTPUT->footer();
