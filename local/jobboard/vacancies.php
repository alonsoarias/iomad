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
 * Public vacancies listing page for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$context = context_system::instance();
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 25, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);
$companyid = optional_param('companyid', 0, PARAM_INT);

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/jobboard/vacancies.php', [
    'page' => $page,
    'perpage' => $perpage,
    'search' => $search,
    'status' => $status,
    'companyid' => $companyid,
]));
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('vacancies', 'local_jobboard'));
$PAGE->set_heading(get_string('vacancies', 'local_jobboard'));

// Build filters.
$filters = [
    'userid' => $USER->id,
    'respect_tenant' => true,
];

if ($search) {
    $filters['search'] = $search;
}

if ($status) {
    $filters['status'] = $status;
} else {
    // Default to published vacancies for regular users.
    if (!has_capability('local/jobboard:viewallvacancies', $context)) {
        $filters['status'] = 'published';
    }
}

if ($companyid) {
    $filters['companyid'] = $companyid;
}

// Get vacancies.
$result = \local_jobboard\vacancy::get_list($filters, 'closedate', 'ASC', $page, $perpage);
$vacancies = $result['vacancies'];
$total = $result['total'];

echo $OUTPUT->header();

// Page title and description.
echo html_writer::tag('h2', get_string('vacancies', 'local_jobboard'));

// Search and filter form.
echo html_writer::start_tag('form', ['method' => 'get', 'action' => $PAGE->url->out_omit_querystring(), 'class' => 'mb-4']);

echo html_writer::start_div('row');

// Search box.
echo html_writer::start_div('col-md-4');
echo html_writer::tag('label', get_string('search', 'local_jobboard'), ['for' => 'search', 'class' => 'sr-only']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'id' => 'search',
    'value' => $search,
    'class' => 'form-control',
    'placeholder' => get_string('search', 'local_jobboard') . '...',
]);
echo html_writer::end_div();

// Status filter (only for managers).
if (has_capability('local/jobboard:viewallvacancies', $context)) {
    echo html_writer::start_div('col-md-3');
    $statusoptions = ['' => get_string('allstatuses', 'local_jobboard')] + local_jobboard_get_vacancy_statuses();
    echo html_writer::select($statusoptions, 'status', $status, null, ['class' => 'form-control']);
    echo html_writer::end_div();
}

// Company filter (Iomad).
if (local_jobboard_is_iomad_installed() && has_capability('local/jobboard:viewallvacancies', $context)) {
    echo html_writer::start_div('col-md-3');
    $companyoptions = [0 => get_string('allcompanies', 'local_jobboard')] + local_jobboard_get_companies();
    echo html_writer::select($companyoptions, 'companyid', $companyid, null, ['class' => 'form-control']);
    echo html_writer::end_div();
}

// Submit button.
echo html_writer::start_div('col-md-2');
echo html_writer::empty_tag('input', [
    'type' => 'submit',
    'value' => get_string('filter', 'local_jobboard'),
    'class' => 'btn btn-primary',
]);
echo html_writer::end_div();

echo html_writer::end_div(); // row
echo html_writer::end_tag('form');

// Results count.
$showing = new stdClass();
$showing->from = ($page * $perpage) + 1;
$showing->to = min(($page + 1) * $perpage, $total);
$showing->total = $total;
echo html_writer::tag('p', get_string('showing', 'local_jobboard', $showing), ['class' => 'text-muted']);

// Vacancies table.
if (empty($vacancies)) {
    echo $OUTPUT->notification(get_string('noresults', 'local_jobboard'), 'info');
} else {
    $table = new html_table();
    $table->head = [
        get_string('thcode', 'local_jobboard'),
        get_string('thtitle', 'local_jobboard'),
        get_string('location', 'local_jobboard'),
        get_string('closedate', 'local_jobboard'),
        get_string('thstatus', 'local_jobboard'),
        get_string('thactions', 'local_jobboard'),
    ];
    $table->attributes['class'] = 'table table-striped';

    foreach ($vacancies as $vacancy) {
        $row = [];

        // Code.
        $row[] = html_writer::tag('strong', s($vacancy->code));

        // Title.
        $row[] = html_writer::link(
            new moodle_url('/local/jobboard/vacancy.php', ['id' => $vacancy->id]),
            s($vacancy->title)
        );

        // Location.
        $row[] = s($vacancy->location);

        // Close date.
        $closedate = local_jobboard_format_date($vacancy->closedate);
        $daysremaining = local_jobboard_days_between(time(), $vacancy->closedate);
        if ($vacancy->closedate < time()) {
            $closedate .= ' (' . get_string('status:closed', 'local_jobboard') . ')';
        } elseif ($daysremaining <= 7) {
            $closedate .= ' (' . $daysremaining . ' ' . get_string('days', 'local_jobboard') . ')';
        }
        $row[] = $closedate;

        // Status.
        $statusbadge = '';
        switch ($vacancy->status) {
            case 'published':
                $statusbadge = html_writer::span($vacancy->get_status_display(), 'badge badge-success');
                break;
            case 'closed':
                $statusbadge = html_writer::span($vacancy->get_status_display(), 'badge badge-secondary');
                break;
            case 'draft':
                $statusbadge = html_writer::span($vacancy->get_status_display(), 'badge badge-warning');
                break;
            case 'assigned':
                $statusbadge = html_writer::span($vacancy->get_status_display(), 'badge badge-info');
                break;
            default:
                $statusbadge = html_writer::span($vacancy->get_status_display(), 'badge badge-secondary');
        }
        $row[] = $statusbadge;

        // Actions.
        $actions = [];

        // View button.
        $actions[] = html_writer::link(
            new moodle_url('/local/jobboard/vacancy.php', ['id' => $vacancy->id]),
            get_string('view', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-primary']
        );

        // Apply button (if vacancy is open and user can apply).
        if ($vacancy->is_open() && has_capability('local/jobboard:apply', $context)) {
            $actions[] = html_writer::link(
                new moodle_url('/local/jobboard/apply.php', ['vacancyid' => $vacancy->id]),
                get_string('apply', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-success']
            );
        }

        $row[] = implode(' ', $actions);

        $table->data[] = $row;
    }

    echo html_writer::table($table);
}

// Pagination.
if ($total > $perpage) {
    $baseurl = new moodle_url('/local/jobboard/vacancies.php', [
        'search' => $search,
        'status' => $status,
        'companyid' => $companyid,
        'perpage' => $perpage,
    ]);
    echo $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
}

echo $OUTPUT->footer();
