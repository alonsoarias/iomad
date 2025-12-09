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
 * Vacancies listing view for local_jobboard.
 *
 * Modern redesign with card-based layout for better UX.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

// Parameters.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$status = optional_param('status', '', PARAM_ALPHA);
$companyid = optional_param('companyid', 0, PARAM_INT);
$departmentid = optional_param('departmentid', 0, PARAM_INT);
$contracttype = optional_param('contracttype', '', PARAM_ALPHA);
$convocatoriaid = optional_param('convocatoriaid', 0, PARAM_INT);

// Check if IOMAD is installed.
$isiomad = local_jobboard_is_iomad_installed();

// Load convocatoria if filtering by it.
$convocatoria = null;
if ($convocatoriaid) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
}

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('vacancies', 'local_jobboard'));
$PAGE->set_heading(get_string('vacancies', 'local_jobboard'));
$PAGE->requires->css('/local/jobboard/styles.css');

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
    if (!has_capability('local/jobboard:viewallvacancies', $context)) {
        $filters['status'] = 'published';
    }
}

if ($companyid) {
    $filters['companyid'] = $companyid;
}

if ($departmentid) {
    $filters['departmentid'] = $departmentid;
}

if ($contracttype) {
    $filters['contracttype'] = $contracttype;
}

if ($convocatoriaid) {
    $filters['convocatoriaid'] = $convocatoriaid;
}

// Get vacancies.
$result = \local_jobboard\vacancy::get_list($filters, 'closedate', 'ASC', $page, $perpage);
$vacancies = $result['vacancies'];
$total = $result['total'];

// Count by urgency.
$urgentCount = 0;
foreach ($vacancies as $v) {
    $daysRemaining = local_jobboard_days_between(time(), $v->closedate);
    if ($daysRemaining <= 7 && $daysRemaining >= 0) {
        $urgentCount++;
    }
}

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-vacancies');

// ============================================================================
// PAGE HEADER
// ============================================================================
$breadcrumbs = [
    get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
];

// Add convocatoria to breadcrumbs if filtering by it.
if ($convocatoria) {
    $breadcrumbs[get_string('convocatorias', 'local_jobboard')] = new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']);
    $breadcrumbs[s($convocatoria->name)] = new moodle_url('/local/jobboard/index.php', ['view' => 'view_convocatoria', 'id' => $convocatoria->id]);
}

$breadcrumbs[get_string('vacancies', 'local_jobboard')] = null;

$pageTitle = $convocatoria
    ? get_string('vacancies', 'local_jobboard') . ': ' . s($convocatoria->name)
    : get_string('vacancies', 'local_jobboard');

echo ui_helper::page_header(
    $pageTitle,
    $breadcrumbs
);

// ============================================================================
// WELCOME BANNER
// ============================================================================
echo html_writer::start_div('jb-welcome-banner bg-gradient-success text-white rounded-lg p-4 mb-4');
echo html_writer::start_div('d-flex justify-content-between align-items-center');
echo html_writer::start_div();
echo html_writer::tag('h4', get_string('explorevacancias', 'local_jobboard'), ['class' => 'mb-1 font-weight-bold']);
echo html_writer::tag('p', get_string('browse_vacancies_desc', 'local_jobboard'), ['class' => 'mb-0 opacity-75']);
echo html_writer::end_div();
echo html_writer::tag('i', '', ['class' => 'fa fa-search fa-3x opacity-25']);
echo html_writer::end_div();
echo html_writer::end_div();

// ============================================================================
// STATISTICS ROW
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card(
    (string) $total,
    get_string('availablevacancies', 'local_jobboard'),
    'success', 'briefcase'
);
echo ui_helper::stat_card(
    (string) $urgentCount,
    get_string('closingsoon', 'local_jobboard'),
    'warning', 'clock'
);
echo html_writer::end_div();

// ============================================================================
// FILTER FORM
// ============================================================================
$filterDefinitions = [
    [
        'type' => 'text',
        'name' => 'search',
        'placeholder' => get_string('searchvacancies', 'local_jobboard') . '...',
        'col' => 'col-md-4',
    ],
    [
        'type' => 'select',
        'name' => 'contracttype',
        'options' => ['' => get_string('allcontracttypes', 'local_jobboard')] + local_jobboard_get_contract_types(),
        'col' => 'col-md-3',
    ],
];

// Status filter (only for managers).
if (has_capability('local/jobboard:viewallvacancies', $context)) {
    $filterDefinitions[] = [
        'type' => 'select',
        'name' => 'status',
        'options' => ['' => get_string('allstatuses', 'local_jobboard')] + local_jobboard_get_vacancy_statuses(),
        'col' => 'col-md-2',
    ];
}

// Company and Department filters (IOMAD).
if ($isiomad && has_capability('local/jobboard:viewallvacancies', $context)) {
    $filterDefinitions[] = [
        'type' => 'select',
        'name' => 'companyid',
        'options' => [0 => get_string('allcompanies', 'local_jobboard')] + local_jobboard_get_companies(),
        'col' => 'col-md-2',
    ];

    // Department filter - populated via AJAX when company is selected.
    $departmentOptions = [0 => get_string('alldepartments', 'local_jobboard')];
    if ($companyid) {
        $departmentOptions += local_jobboard_get_departments($companyid);
    }
    $filterDefinitions[] = [
        'type' => 'select',
        'name' => 'departmentid',
        'options' => $departmentOptions,
        'col' => 'col-md-2',
    ];
}

echo ui_helper::filter_form(
    (new moodle_url('/local/jobboard/index.php'))->out(false),
    $filterDefinitions,
    ['search' => $search, 'status' => $status, 'companyid' => $companyid, 'departmentid' => $departmentid, 'contracttype' => $contracttype],
    ['view' => 'vacancies']
);

// Add JavaScript for AJAX department loading (IOMAD).
if ($isiomad && has_capability('local/jobboard:viewallvacancies', $context)) {
    $allDepartmentsLabel = get_string('alldepartments', 'local_jobboard');
    $PAGE->requires->js_call_amd('local_jobboard/public_filters', 'init', [[
        'companySelector' => '#companyid',
        'departmentSelector' => '#departmentid',
        'preselect' => $departmentid,
        'allLabel' => $allDepartmentsLabel,
    ]]);
}

// ============================================================================
// RESULTS INFO
// ============================================================================
$showing = new stdClass();
$showing->from = $total > 0 ? ($page * $perpage) + 1 : 0;
$showing->to = min(($page + 1) * $perpage, $total);
$showing->total = $total;
echo html_writer::tag('p', get_string('showing', 'local_jobboard', $showing),
    ['class' => 'text-muted small mb-3']);

// ============================================================================
// VACANCIES GRID
// ============================================================================
if (empty($vacancies)) {
    echo ui_helper::empty_state(
        get_string('noresults', 'local_jobboard'),
        'briefcase'
    );
} else {
    echo html_writer::start_div('row');

    foreach ($vacancies as $vacancy) {
        $daysRemaining = local_jobboard_days_between(time(), $vacancy->closedate);
        $isUrgent = ($daysRemaining <= 7 && $daysRemaining >= 0);
        $isClosed = ($vacancy->closedate < time() || $vacancy->status === 'closed');

        // Card column.
        echo html_writer::start_div('col-lg-4 col-md-6 mb-4');

        $cardClass = 'card h-100 shadow-sm jb-vacancy-card';
        if ($isUrgent && !$isClosed) {
            $cardClass .= ' border-warning';
        } elseif ($isClosed) {
            $cardClass .= ' border-secondary opacity-75';
        }

        echo html_writer::start_div($cardClass);

        // Card header with status and urgency.
        echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center py-2');
        echo html_writer::tag('code', s($vacancy->code), ['class' => 'small text-muted']);

        $badges = '';
        $badges .= ui_helper::status_badge($vacancy->status, 'vacancy');
        if ($isUrgent && !$isClosed) {
            $badges .= ' ' . html_writer::tag('span',
                '<i class="fa fa-clock mr-1"></i>' . $daysRemaining . 'd',
                ['class' => 'badge badge-warning']
            );
        }
        echo html_writer::tag('div', $badges);
        echo html_writer::end_div();

        // Card body.
        echo html_writer::start_div('card-body');

        // Title.
        echo html_writer::tag('h5',
            html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancy->id]),
                s($vacancy->title),
                ['class' => 'text-dark stretched-link']
            ),
            ['class' => 'card-title mb-3']
        );

        // Details.
        echo html_writer::start_div('card-text small');

        if (!empty($vacancy->location)) {
            echo html_writer::div(
                '<i class="fa fa-map-marker-alt text-muted mr-2"></i>' . s($vacancy->location),
                'mb-1'
            );
        }

        if (!empty($vacancy->contracttype)) {
            $contractTypes = local_jobboard_get_contract_types();
            echo html_writer::div(
                '<i class="fa fa-file-contract text-muted mr-2"></i>' .
                ($contractTypes[$vacancy->contracttype] ?? $vacancy->contracttype),
                'mb-1'
            );
        }

        echo html_writer::div(
            '<i class="fa fa-users text-muted mr-2"></i>' .
            get_string('positions', 'local_jobboard') . ': ' . $vacancy->positions,
            'mb-1'
        );

        echo html_writer::end_div();
        echo html_writer::end_div(); // card-body

        // Card footer.
        echo html_writer::start_div('card-footer bg-white border-top-0');

        echo html_writer::start_div('d-flex justify-content-between align-items-center');

        // Close date.
        $closeDateClass = $isUrgent ? 'text-warning font-weight-bold' : 'text-muted';
        echo html_writer::tag('small',
            '<i class="fa fa-calendar-times mr-1"></i>' . local_jobboard_format_date($vacancy->closedate),
            ['class' => $closeDateClass]
        );

        // Apply button.
        if ($vacancy->is_open() && has_capability('local/jobboard:apply', $context)) {
            // Check if already applied.
            $hasApplied = $DB->record_exists('local_jobboard_application', [
                'vacancyid' => $vacancy->id,
                'userid' => $USER->id,
            ]);

            if ($hasApplied) {
                echo html_writer::tag('span',
                    '<i class="fa fa-check mr-1"></i>' . get_string('applied', 'local_jobboard'),
                    ['class' => 'badge badge-info']
                );
            } else {
                echo html_writer::link(
                    new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
                    get_string('apply', 'local_jobboard'),
                    ['class' => 'btn btn-sm btn-success jb-card-action']
                );
            }
        } elseif ($isClosed) {
            echo html_writer::tag('span', get_string('status:closed', 'local_jobboard'),
                ['class' => 'badge badge-secondary']);
        }

        echo html_writer::end_div();
        echo html_writer::end_div(); // card-footer

        echo html_writer::end_div(); // card
        echo html_writer::end_div(); // col
    }

    echo html_writer::end_div(); // row
}

// ============================================================================
// PAGINATION
// ============================================================================
if ($total > $perpage) {
    $paginationParams = [
        'view' => 'vacancies',
        'search' => $search,
        'status' => $status,
        'companyid' => $companyid,
        'departmentid' => $departmentid,
        'contracttype' => $contracttype,
        'perpage' => $perpage,
    ];
    if ($convocatoriaid) {
        $paginationParams['convocatoriaid'] = $convocatoriaid;
    }
    $baseurl = new moodle_url('/local/jobboard/index.php', $paginationParams);
    echo $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
}

echo html_writer::end_div(); // local-jobboard-vacancies

// Initialize JavaScript via AMD modules.
$PAGE->requires->js_call_amd('local_jobboard/card_actions', 'init', [[
    'buttonSelector' => '.jb-vacancy-card .jb-card-action',
    'cardSelector' => '.jb-vacancy-card'
]]);

echo $OUTPUT->footer();
