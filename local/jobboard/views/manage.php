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
 * Vacancy management view for local_jobboard.
 *
 * This file is included by index.php and should not be accessed directly.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

// Require manage capability.
require_capability('local/jobboard:createvacancy', $context);

// Parameters.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 25, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);
$companyid = optional_param('companyid', 0, PARAM_INT);
$convocatoriaid = optional_param('convocatoriaid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$vacancyid = optional_param('id', 0, PARAM_INT);

// Page setup.
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('managevacancies', 'local_jobboard'));
$PAGE->set_heading(get_string('managevacancies', 'local_jobboard'));

// Handle actions that don't need sesskey (just redirects).
if ($action === 'create') {
    redirect(new moodle_url('/local/jobboard/edit.php'));
}

if ($action === 'edit' && $vacancyid) {
    redirect(new moodle_url('/local/jobboard/edit.php', ['id' => $vacancyid]));
}

// Handle actions that modify data (require sesskey).
if ($action && $vacancyid && in_array($action, ['publish', 'close', 'delete'])) {
    require_sesskey();

    $vacancy = \local_jobboard\vacancy::get($vacancyid);

    if (!$vacancy) {
        throw new moodle_exception('error:vacancynotfound', 'local_jobboard');
    }

    switch ($action) {
        case 'publish':
            require_capability('local/jobboard:publishvacancy', $context);
            try {
                $vacancy->publish();
                redirect(
                    new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
                    get_string('vacancypublished', 'local_jobboard'),
                    null,
                    \core\output\notification::NOTIFY_SUCCESS
                );
            } catch (Exception $e) {
                redirect(
                    new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
                    $e->getMessage(),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
            }
            break;

        case 'close':
            require_capability('local/jobboard:editvacancy', $context);
            $vacancy->close();
            redirect(
                new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
                get_string('vacancyclosed', 'local_jobboard'),
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
            break;

        case 'delete':
            require_capability('local/jobboard:deletevacancy', $context);
            try {
                $vacancy->delete();
                redirect(
                    new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
                    get_string('vacancydeleted', 'local_jobboard'),
                    null,
                    \core\output\notification::NOTIFY_SUCCESS
                );
            } catch (Exception $e) {
                redirect(
                    new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
                    $e->getMessage(),
                    null,
                    \core\output\notification::NOTIFY_ERROR
                );
            }
            break;
    }
}

// Build filters.
$filters = [];

if ($search) {
    $filters['search'] = $search;
}

if ($status) {
    $filters['status'] = $status;
}

if ($companyid) {
    $filters['companyid'] = $companyid;
}

if ($convocatoriaid) {
    $filters['convocatoriaid'] = $convocatoriaid;
}

// For non-admin users, filter by their company.
if (!has_capability('local/jobboard:viewallvacancies', $context)) {
    $usercompanyid = local_jobboard_get_user_companyid();
    if ($usercompanyid) {
        $filters['companyid'] = $usercompanyid;
    }
}

// Get vacancies.
$result = \local_jobboard\vacancy::get_list($filters, 'timecreated', 'DESC', $page, $perpage);
$vacancies = $result['vacancies'];
$total = $result['total'];

echo $OUTPUT->header();

// Page title.
echo html_writer::tag('h2', get_string('managevacancies', 'local_jobboard'));

// If filtering by convocatoria, show context info.
$convocatoriainfo = null;
if ($convocatoriaid) {
    $convocatoriainfo = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
    if ($convocatoriainfo) {
        $statusclass = [
            'draft' => 'secondary',
            'open' => 'success',
            'closed' => 'warning',
            'archived' => 'dark',
        ];
        echo html_writer::start_div('alert alert-info d-flex justify-content-between align-items-center');
        echo html_writer::tag('span',
            get_string('vacanciesforconvocatoria', 'local_jobboard') . ': ' .
            html_writer::tag('strong', s($convocatoriainfo->name)) . ' ' .
            html_writer::tag('span', get_string('convocatoria_status_' . $convocatoriainfo->status, 'local_jobboard'),
                ['class' => 'badge badge-' . ($statusclass[$convocatoriainfo->status] ?? 'secondary')])
        );
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
            get_string('backtoconvocatorias', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-primary']
        );
        echo html_writer::end_div();
    }
}

// Add new vacancy button.
$newvacancyurl = new moodle_url('/local/jobboard/edit.php');
if ($convocatoriaid) {
    $newvacancyurl->param('convocatoriaid', $convocatoriaid);
}
echo html_writer::div(
    html_writer::link(
        $newvacancyurl,
        get_string('newvacancy', 'local_jobboard'),
        ['class' => 'btn btn-primary']
    ),
    'mb-4'
);

// Search and filter form.
$formurl = new moodle_url('/local/jobboard/index.php');
echo html_writer::start_tag('form', ['method' => 'get', 'action' => $formurl, 'class' => 'mb-4']);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'view', 'value' => 'manage']);
if ($convocatoriaid) {
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'convocatoriaid', 'value' => $convocatoriaid]);
}

echo html_writer::start_div('row');

// Search box.
echo html_writer::start_div('col-md-4');
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'value' => $search,
    'class' => 'form-control',
    'placeholder' => get_string('search', 'local_jobboard') . '...',
]);
echo html_writer::end_div();

// Status filter.
echo html_writer::start_div('col-md-3');
$statusoptions = ['' => get_string('allstatuses', 'local_jobboard')] + local_jobboard_get_vacancy_statuses();
echo html_writer::select($statusoptions, 'status', $status, null, ['class' => 'form-control']);
echo html_writer::end_div();

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
    'class' => 'btn btn-secondary',
]);
echo html_writer::end_div();

echo html_writer::end_div(); // row.
echo html_writer::end_tag('form');

// Results count.
$showing = new stdClass();
$showing->from = $total > 0 ? ($page * $perpage) + 1 : 0;
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
        get_string('thstatus', 'local_jobboard'),
        get_string('opendate', 'local_jobboard'),
        get_string('closedate', 'local_jobboard'),
        get_string('positions', 'local_jobboard'),
        get_string('thactions', 'local_jobboard'),
    ];
    $table->attributes['class'] = 'table table-striped table-hover';

    foreach ($vacancies as $vacancy) {
        $row = [];

        // Code.
        $row[] = html_writer::tag('strong', s($vacancy->code));

        // Title with company name.
        $title = html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancy->id]),
            s($vacancy->title)
        );
        if ($vacancy->companyid) {
            $title .= html_writer::tag('small', ' (' . $vacancy->get_company_name() . ')', ['class' => 'text-muted']);
        }
        $row[] = $title;

        // Status badge.
        $statusclass = 'secondary';
        switch ($vacancy->status) {
            case 'published':
                $statusclass = 'success';
                break;
            case 'draft':
                $statusclass = 'warning';
                break;
            case 'closed':
                $statusclass = 'secondary';
                break;
            case 'assigned':
                $statusclass = 'info';
                break;
        }
        $row[] = html_writer::span($vacancy->get_status_display(), 'badge badge-' . $statusclass);

        // Dates.
        $row[] = local_jobboard_format_date($vacancy->opendate);
        $row[] = local_jobboard_format_date($vacancy->closedate);

        // Applications count.
        $appcount = $vacancy->get_application_count();
        $row[] = html_writer::tag('span', $appcount, ['class' => 'badge badge-secondary']);

        // Actions.
        $actions = [];

        // Edit.
        if ($vacancy->can_edit() && has_capability('local/jobboard:editvacancy', $context)) {
            $actions[] = html_writer::link(
                new moodle_url('/local/jobboard/edit.php', ['id' => $vacancy->id]),
                $OUTPUT->pix_icon('t/edit', get_string('edit', 'local_jobboard')),
                ['class' => 'btn btn-sm btn-outline-primary', 'title' => get_string('edit', 'local_jobboard')]
            );
        }

        // Publish.
        if ($vacancy->can_publish() && has_capability('local/jobboard:publishvacancy', $context)) {
            $actions[] = html_writer::link(
                new moodle_url('/local/jobboard/index.php', [
                    'view' => 'manage',
                    'action' => 'publish',
                    'id' => $vacancy->id,
                    'sesskey' => sesskey(),
                ]),
                $OUTPUT->pix_icon('t/show', get_string('publish', 'local_jobboard')),
                [
                    'class' => 'btn btn-sm btn-outline-success',
                    'title' => get_string('publish', 'local_jobboard'),
                    'onclick' => "return confirm('" . get_string('confirmpublish', 'local_jobboard') . "');",
                ]
            );
        }

        // Close.
        if ($vacancy->status === 'published' && has_capability('local/jobboard:editvacancy', $context)) {
            $actions[] = html_writer::link(
                new moodle_url('/local/jobboard/index.php', [
                    'view' => 'manage',
                    'action' => 'close',
                    'id' => $vacancy->id,
                    'sesskey' => sesskey(),
                ]),
                $OUTPUT->pix_icon('t/block', get_string('close', 'local_jobboard')),
                ['class' => 'btn btn-sm btn-outline-warning', 'title' => get_string('close', 'local_jobboard')]
            );
        }

        // View applications.
        if (has_capability('local/jobboard:viewallapplications', $context)) {
            $actions[] = html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancy->id]),
                $OUTPUT->pix_icon('i/users', get_string('reviewapplications', 'local_jobboard')),
                ['class' => 'btn btn-sm btn-outline-info', 'title' => get_string('reviewapplications', 'local_jobboard')]
            );
        }

        // Delete.
        if ($vacancy->can_delete() && has_capability('local/jobboard:deletevacancy', $context)) {
            $actions[] = html_writer::link(
                new moodle_url('/local/jobboard/index.php', [
                    'view' => 'manage',
                    'action' => 'delete',
                    'id' => $vacancy->id,
                    'sesskey' => sesskey(),
                ]),
                $OUTPUT->pix_icon('t/delete', get_string('delete', 'local_jobboard')),
                [
                    'class' => 'btn btn-sm btn-outline-danger',
                    'title' => get_string('delete', 'local_jobboard'),
                    'onclick' => "return confirm('" . get_string('confirmdeletevacancy', 'local_jobboard') . "');",
                ]
            );
        }

        $row[] = implode(' ', $actions);

        $table->data[] = $row;
    }

    echo html_writer::table($table);
}

// Pagination.
if ($total > $perpage) {
    $baseurl = new moodle_url('/local/jobboard/index.php', [
        'view' => 'manage',
        'search' => $search,
        'status' => $status,
        'companyid' => $companyid,
        'convocatoriaid' => $convocatoriaid,
        'perpage' => $perpage,
    ]);
    echo $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
}

echo $OUTPUT->footer();
