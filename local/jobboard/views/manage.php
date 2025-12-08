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
 * Modern redesign using ui_helper for consistent styling.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

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
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('managevacancies', 'local_jobboard'));
$PAGE->set_heading(get_string('managevacancies', 'local_jobboard'));
$PAGE->requires->css('/local/jobboard/styles.css');

// Handle actions that don't need sesskey (just redirects).
if ($action === 'create') {
    redirect(new moodle_url('/local/jobboard/edit.php'));
}

if ($action === 'edit' && $vacancyid) {
    redirect(new moodle_url('/local/jobboard/edit.php', ['id' => $vacancyid]));
}

// Handle actions that modify data (require sesskey).
if ($action && $vacancyid && in_array($action, ['publish', 'unpublish', 'close', 'reopen', 'delete'])) {
    require_sesskey();

    $vacancy = \local_jobboard\vacancy::get($vacancyid);

    if (!$vacancy) {
        throw new moodle_exception('error:vacancynotfound', 'local_jobboard');
    }

    $redirecturl = new moodle_url('/local/jobboard/index.php', ['view' => 'manage']);

    switch ($action) {
        case 'publish':
            require_capability('local/jobboard:publishvacancy', $context);
            try {
                $vacancy->publish();
                redirect($redirecturl, get_string('vacancypublished', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } catch (Exception $e) {
                redirect($redirecturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
            }
            break;

        case 'unpublish':
            require_capability('local/jobboard:editvacancy', $context);
            try {
                $vacancy->unpublish();
                redirect($redirecturl, get_string('vacancyunpublished', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } catch (Exception $e) {
                redirect($redirecturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
            }
            break;

        case 'close':
            require_capability('local/jobboard:editvacancy', $context);
            try {
                $vacancy->close();
                redirect($redirecturl, get_string('vacancyclosed', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } catch (Exception $e) {
                redirect($redirecturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
            }
            break;

        case 'reopen':
            require_capability('local/jobboard:publishvacancy', $context);
            try {
                $vacancy->reopen();
                redirect($redirecturl, get_string('vacancyreopened', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } catch (Exception $e) {
                redirect($redirecturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
            }
            break;

        case 'delete':
            require_capability('local/jobboard:deletevacancy', $context);
            try {
                if (!$vacancy->can_delete()) {
                    $reason = $vacancy->get_delete_restriction_reason();
                    throw new moodle_exception('error:cannotdelete', 'local_jobboard', '', $reason);
                }
                $vacancy->delete();
                redirect($redirecturl, get_string('vacancydeleted', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } catch (Exception $e) {
                redirect($redirecturl, $e->getMessage(), null, \core\output\notification::NOTIFY_ERROR);
            }
            break;
    }
}

// Handle bulk actions.
$bulkaction = optional_param('bulkaction', '', PARAM_ALPHA);
$selectedids = optional_param_array('selected', [], PARAM_INT);

if ($bulkaction && !empty($selectedids) && confirm_sesskey()) {
    $redirecturl = new moodle_url('/local/jobboard/index.php', ['view' => 'manage']);
    $successcount = 0;
    $errorcount = 0;

    foreach ($selectedids as $vid) {
        try {
            $v = \local_jobboard\vacancy::get($vid);
            if (!$v) {
                continue;
            }

            switch ($bulkaction) {
                case 'publish':
                    if (has_capability('local/jobboard:publishvacancy', $context) && $v->can_publish()) {
                        $v->publish();
                        $successcount++;
                    }
                    break;
                case 'unpublish':
                    if (has_capability('local/jobboard:editvacancy', $context) && $v->status === 'published') {
                        $v->unpublish();
                        $successcount++;
                    }
                    break;
                case 'close':
                    if (has_capability('local/jobboard:editvacancy', $context) && $v->status === 'published') {
                        $v->close();
                        $successcount++;
                    }
                    break;
                case 'delete':
                    if (has_capability('local/jobboard:deletevacancy', $context) && $v->can_delete()) {
                        $v->delete();
                        $successcount++;
                    }
                    break;
            }
        } catch (Exception $e) {
            $errorcount++;
        }
    }

    if ($successcount > 0) {
        $msgkey = 'items' . $bulkaction . 'ed';
        if ($bulkaction === 'publish') {
            $msgkey = 'itemspublished';
        } elseif ($bulkaction === 'delete') {
            $msgkey = 'itemsdeleted';
        } elseif ($bulkaction === 'close') {
            $msgkey = 'itemsclosed';
        }
        \core\notification::success(get_string($msgkey, 'local_jobboard', $successcount));
    }
    if ($errorcount > 0) {
        \core\notification::error(get_string('bulkactionerrors', 'local_jobboard', $errorcount));
    }
    redirect($redirecturl);
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

// Get convocatoria info if filtering.
$convocatoriainfo = null;
if ($convocatoriaid) {
    $convocatoriainfo = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
}

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-manage');

// ============================================================================
// PAGE HEADER
// ============================================================================
$breadcrumbs = [
    get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
];

if ($convocatoriainfo) {
    $breadcrumbs[get_string('manageconvocatorias', 'local_jobboard')] =
        new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']);
    $breadcrumbs[s($convocatoriainfo->name)] = null;
} else {
    $breadcrumbs[get_string('managevacancies', 'local_jobboard')] = null;
}

$newvacancyurl = new moodle_url('/local/jobboard/edit.php');
if ($convocatoriaid) {
    $newvacancyurl->param('convocatoriaid', $convocatoriaid);
}

echo ui_helper::page_header(
    $convocatoriainfo
        ? get_string('vacanciesforconvocatoria', 'local_jobboard') . ': ' . s($convocatoriainfo->name)
        : get_string('managevacancies', 'local_jobboard'),
    $breadcrumbs,
    [[
        'url' => $newvacancyurl,
        'label' => get_string('newvacancy', 'local_jobboard'),
        'icon' => 'plus',
        'class' => 'btn btn-primary',
    ]]
);

// ============================================================================
// CONVOCATORIA INFO BANNER
// ============================================================================
if ($convocatoriainfo) {
    $statusColors = ['draft' => 'secondary', 'open' => 'success', 'closed' => 'warning', 'archived' => 'dark'];
    $convColor = $statusColors[$convocatoriainfo->status] ?? 'secondary';

    echo html_writer::start_div('alert alert-info border-left-info d-flex justify-content-between align-items-center mb-4');
    echo html_writer::start_div('d-flex align-items-center');
    echo html_writer::tag('i', '', ['class' => 'fa fa-calendar-alt mr-3 fa-lg']);
    echo html_writer::start_div();
    echo html_writer::tag('strong', s($convocatoriainfo->name), ['class' => 'd-block']);
    echo html_writer::tag('small',
        userdate($convocatoriainfo->startdate, get_string('strftimedate', 'langconfig')) . ' - ' .
        userdate($convocatoriainfo->enddate, get_string('strftimedate', 'langconfig')),
        ['class' => 'text-muted']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::tag('span',
        get_string('convocatoria_status_' . $convocatoriainfo->status, 'local_jobboard'),
        ['class' => 'badge badge-' . $convColor]
    );
    echo html_writer::end_div();
}

// ============================================================================
// STATISTICS ROW
// ============================================================================
$stats = [
    'draft' => 0,
    'published' => 0,
    'closed' => 0,
    'assigned' => 0,
];
foreach ($vacancies as $v) {
    if (isset($stats[$v->status])) {
        $stats[$v->status]++;
    }
}

echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card(
    (string) $total,
    get_string('totalvacancies', 'local_jobboard'),
    'primary', 'briefcase'
);
echo ui_helper::stat_card(
    (string) $stats['draft'],
    get_string('status:draft', 'local_jobboard'),
    'secondary', 'edit'
);
echo ui_helper::stat_card(
    (string) $stats['published'],
    get_string('status:published', 'local_jobboard'),
    'success', 'check-circle'
);
echo ui_helper::stat_card(
    (string) $stats['closed'],
    get_string('status:closed', 'local_jobboard'),
    'warning', 'lock'
);
echo html_writer::end_div();

// ============================================================================
// FILTER FORM
// ============================================================================
$filterDefinitions = [
    [
        'type' => 'text',
        'name' => 'search',
        'placeholder' => get_string('search', 'local_jobboard') . '...',
        'col' => 'col-md-4',
    ],
    [
        'type' => 'select',
        'name' => 'status',
        'options' => ['' => get_string('allstatuses', 'local_jobboard')] + local_jobboard_get_vacancy_statuses(),
        'col' => 'col-md-3',
    ],
];

// Company filter for Iomad.
if (local_jobboard_is_iomad_installed() && has_capability('local/jobboard:viewallvacancies', $context)) {
    $filterDefinitions[] = [
        'type' => 'select',
        'name' => 'companyid',
        'options' => [0 => get_string('allcompanies', 'local_jobboard')] + local_jobboard_get_companies(),
        'col' => 'col-md-3',
    ];
}

$hiddenFields = ['view' => 'manage'];
if ($convocatoriaid) {
    $hiddenFields['convocatoriaid'] = $convocatoriaid;
}

echo ui_helper::filter_form(
    (new moodle_url('/local/jobboard/index.php'))->out(false),
    $filterDefinitions,
    ['search' => $search, 'status' => $status, 'companyid' => $companyid],
    $hiddenFields
);

// ============================================================================
// VACANCIES TABLE WITH BULK ACTIONS
// ============================================================================
if (empty($vacancies)) {
    echo ui_helper::empty_state(
        get_string('noresults', 'local_jobboard'),
        'briefcase',
        [
            'url' => $newvacancyurl,
            'label' => get_string('newvacancy', 'local_jobboard'),
            'class' => 'btn btn-primary',
        ]
    );
} else {
    // Start form for bulk actions.
    echo html_writer::start_tag('form', [
        'method' => 'post',
        'action' => new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
        'id' => 'jb-bulk-form',
    ]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

    // Bulk actions toolbar.
    $bulkActions = [
        [
            'action' => 'publish',
            'label' => get_string('bulkpublish', 'local_jobboard'),
            'icon' => 'check-circle',
            'class' => 'btn-outline-success',
            'confirm' => get_string('confirmpublish', 'local_jobboard'),
        ],
        [
            'action' => 'unpublish',
            'label' => get_string('bulkunpublish', 'local_jobboard'),
            'icon' => 'eye-slash',
            'class' => 'btn-outline-warning',
            'confirm' => get_string('confirmunpublish', 'local_jobboard'),
        ],
        [
            'action' => 'close',
            'label' => get_string('bulkclose', 'local_jobboard'),
            'icon' => 'times-circle',
            'class' => 'btn-outline-secondary',
            'confirm' => get_string('confirmclose', 'local_jobboard'),
        ],
        [
            'action' => 'delete',
            'label' => get_string('bulkdelete', 'local_jobboard'),
            'icon' => 'trash',
            'class' => 'btn-outline-danger',
            'confirm' => get_string('confirmdelete', 'local_jobboard'),
        ],
    ];
    echo ui_helper::bulk_actions_toolbar('jb-bulk-form', $bulkActions, 'jb-bulk-item');

    $headers = [
        '<input type="checkbox" class="jb-select-all" data-target=".jb-bulk-item" id="select-all-header">',
        get_string('thcode', 'local_jobboard'),
        get_string('thtitle', 'local_jobboard'),
        get_string('thstatus', 'local_jobboard'),
        get_string('opendate', 'local_jobboard'),
        get_string('closedate', 'local_jobboard'),
        get_string('applications', 'local_jobboard'),
        get_string('thactions', 'local_jobboard'),
    ];

    $rows = [];
    foreach ($vacancies as $vacancy) {
        $row = [];

        // Checkbox for bulk selection.
        $row[] = html_writer::empty_tag('input', [
            'type' => 'checkbox',
            'name' => 'selected[]',
            'value' => $vacancy->id,
            'class' => 'jb-bulk-item',
        ]);

        // Code.
        $row[] = html_writer::tag('code', s($vacancy->code), ['class' => 'font-weight-bold']);

        // Title with company name.
        $title = html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancy->id]),
            s($vacancy->title),
            ['class' => 'font-weight-medium']
        );
        if ($vacancy->companyid) {
            $title .= html_writer::tag('small', ' (' . $vacancy->get_company_name() . ')', ['class' => 'text-muted d-block']);
        }
        $row[] = $title;

        // Status badge.
        $row[] = ui_helper::status_badge($vacancy->status, 'vacancy');

        // Dates.
        $row[] = html_writer::tag('small', local_jobboard_format_date($vacancy->opendate));
        $row[] = html_writer::tag('small', local_jobboard_format_date($vacancy->closedate));

        // Applications count.
        $appcount = $vacancy->get_application_count();
        $appBadgeClass = $appcount > 0 ? 'badge-info' : 'badge-secondary';
        $row[] = html_writer::tag('span', $appcount, ['class' => 'badge ' . $appBadgeClass]);

        // Actions.
        $actions = [];

        // Edit.
        if ($vacancy->can_edit() && has_capability('local/jobboard:editvacancy', $context)) {
            $actions[] = [
                'url' => new moodle_url('/local/jobboard/edit.php', ['id' => $vacancy->id]),
                'icon' => 't/edit',
                'title' => get_string('edit', 'local_jobboard'),
                'class' => 'btn-outline-primary',
            ];
        }

        // Publish.
        if ($vacancy->can_publish() && has_capability('local/jobboard:publishvacancy', $context)) {
            $actions[] = [
                'url' => new moodle_url('/local/jobboard/index.php', [
                    'view' => 'manage', 'action' => 'publish', 'id' => $vacancy->id, 'sesskey' => sesskey(),
                ]),
                'icon' => 't/show',
                'title' => get_string('publish', 'local_jobboard'),
                'class' => 'btn-outline-success',
                'confirm' => get_string('confirmpublish', 'local_jobboard'),
            ];
        }

        // Unpublish.
        if ($vacancy->can_unpublish() && has_capability('local/jobboard:editvacancy', $context)) {
            $actions[] = [
                'url' => new moodle_url('/local/jobboard/index.php', [
                    'view' => 'manage', 'action' => 'unpublish', 'id' => $vacancy->id, 'sesskey' => sesskey(),
                ]),
                'icon' => 't/hide',
                'title' => get_string('unpublish', 'local_jobboard'),
                'class' => 'btn-outline-secondary',
                'confirm' => get_string('confirmunpublish', 'local_jobboard'),
            ];
        }

        // Close.
        if ($vacancy->can_close() && has_capability('local/jobboard:editvacancy', $context)) {
            $actions[] = [
                'url' => new moodle_url('/local/jobboard/index.php', [
                    'view' => 'manage', 'action' => 'close', 'id' => $vacancy->id, 'sesskey' => sesskey(),
                ]),
                'icon' => 't/block',
                'title' => get_string('close', 'local_jobboard'),
                'class' => 'btn-outline-warning',
                'confirm' => get_string('confirmclose', 'local_jobboard'),
            ];
        }

        // Reopen.
        if ($vacancy->can_reopen() && has_capability('local/jobboard:publishvacancy', $context)) {
            $actions[] = [
                'url' => new moodle_url('/local/jobboard/index.php', [
                    'view' => 'manage', 'action' => 'reopen', 'id' => $vacancy->id, 'sesskey' => sesskey(),
                ]),
                'icon' => 't/restore',
                'title' => get_string('reopen', 'local_jobboard'),
                'class' => 'btn-outline-success',
                'confirm' => get_string('confirmreopen', 'local_jobboard'),
            ];
        }

        // View applications.
        if (has_capability('local/jobboard:viewallapplications', $context)) {
            $actions[] = [
                'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancy->id]),
                'icon' => 'i/users',
                'title' => get_string('reviewapplications', 'local_jobboard'),
                'class' => 'btn-outline-info',
            ];
        }

        // Delete.
        if ($vacancy->can_delete() && has_capability('local/jobboard:deletevacancy', $context)) {
            $actions[] = [
                'url' => new moodle_url('/local/jobboard/index.php', [
                    'view' => 'manage', 'action' => 'delete', 'id' => $vacancy->id, 'sesskey' => sesskey(),
                ]),
                'icon' => 't/delete',
                'title' => get_string('delete', 'local_jobboard'),
                'class' => 'btn-outline-danger',
                'confirm' => get_string('confirmdeletevacancy', 'local_jobboard'),
            ];
        }

        $row[] = ui_helper::action_buttons($actions);

        $rows[] = $row;
    }

    echo ui_helper::data_table($headers, $rows);

    // Close form.
    echo html_writer::end_tag('form');

    // Confirmation modal.
    echo ui_helper::confirmation_modal(
        'jb-confirm-modal',
        get_string('confirmaction', 'local_jobboard'),
        get_string('confirmdelete', 'local_jobboard'),
        get_string('confirm'),
        'btn-danger'
    );

    // Bulk actions JavaScript.
    echo ui_helper::get_bulk_actions_js();
}

// ============================================================================
// PAGINATION WITH PERPAGE SELECTOR
// ============================================================================
$baseurl = new moodle_url('/local/jobboard/index.php', [
    'view' => 'manage',
    'search' => $search,
    'status' => $status,
    'companyid' => $companyid,
    'convocatoriaid' => $convocatoriaid,
]);
echo ui_helper::pagination_bar($total, $page, $perpage, $baseurl);

echo html_writer::end_div(); // local-jobboard-manage

echo $OUTPUT->footer();
