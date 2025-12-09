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
 * View convocatoria details with vacancies for applicants.
 *
 * Shows the convocatoria information and lists all published vacancies
 * within this convocatoria, allowing users to browse and apply.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

// Parameters.
$convocatoriaid = required_param('id', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);

// Load convocatoria.
$convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid], '*', MUST_EXIST);

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title($convocatoria->name);
$PAGE->set_heading($convocatoria->name);
$PAGE->requires->css('/local/jobboard/styles.css');

// Log view.
\local_jobboard\audit::log('convocatoria_viewed', 'convocatoria', $convocatoria->id);

// Check capabilities.
$canapply = has_capability('local/jobboard:apply', $context);
$canmanage = has_capability('local/jobboard:createvacancy', $context);

// Calculate timing.
$now = time();
$daysRemaining = ceil(($convocatoria->enddate - $now) / 86400);
$isOpen = ($convocatoria->status === 'open' && $convocatoria->enddate >= $now);
$isClosingSoon = ($daysRemaining <= 7 && $daysRemaining > 0 && $isOpen);

// Get vacancies for this convocatoria.
$filters = [
    'convocatoriaid' => $convocatoriaid,
    'status' => 'published',
    'respect_tenant' => true,
    'userid' => $USER->id,
];

$result = \local_jobboard\vacancy::get_list($filters, 'closedate', 'ASC', $page, $perpage);
$vacancies = $result['vacancies'];
$totalVacancies = $result['total'];

// Get statistics.
$stats = [
    'total_vacancies' => $totalVacancies,
    'positions' => 0,
    'applications' => 0,
];

foreach ($vacancies as $v) {
    $stats['positions'] += $v->positions;
}

if ($canmanage) {
    $stats['applications'] = $DB->count_records_sql(
        "SELECT COUNT(a.id) FROM {local_jobboard_application} a
           JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
          WHERE v.convocatoriaid = :convid",
        ['convid' => $convocatoriaid]
    );
}

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-view-convocatoria');

// ============================================================================
// PAGE HEADER
// ============================================================================
$breadcrumbs = [
    get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
    get_string('convocatorias', 'local_jobboard') => new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']),
    s($convocatoria->name) => null,
];

$headerActions = [];
if ($canmanage) {
    $headerActions[] = [
        'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'id' => $convocatoria->id]),
        'label' => get_string('edit', 'local_jobboard'),
        'icon' => 'edit',
        'class' => 'btn btn-outline-primary',
    ];
}

echo ui_helper::page_header(
    s($convocatoria->name),
    $breadcrumbs,
    $headerActions
);

// ============================================================================
// CONVOCATORIA INFO CARD
// ============================================================================
$statusColor = $isOpen ? ($isClosingSoon ? 'warning' : 'success') : 'secondary';

echo html_writer::start_div('card shadow-sm mb-4 border-' . $statusColor);
echo html_writer::start_div('card-header bg-' . $statusColor . ' text-white d-flex justify-content-between align-items-center');
echo html_writer::tag('h5', html_writer::tag('code', s($convocatoria->code), ['class' => 'mr-2']) . s($convocatoria->name),
    ['class' => 'mb-0']);
if ($isClosingSoon) {
    echo html_writer::tag('span',
        '<i class="fa fa-clock mr-1"></i>' . get_string('daysleft', 'local_jobboard', $daysRemaining),
        ['class' => 'badge badge-light']
    );
} else {
    echo html_writer::tag('span',
        get_string('convocatoria_status_' . $convocatoria->status, 'local_jobboard'),
        ['class' => 'badge badge-light']
    );
}
echo html_writer::end_div();

echo html_writer::start_div('card-body');

// Description.
if (!empty($convocatoria->description)) {
    echo html_writer::tag('div', format_text($convocatoria->description, FORMAT_HTML),
        ['class' => 'mb-4']);
}

// Dates row.
echo html_writer::start_div('row mb-4');
echo html_writer::start_div('col-md-6');
echo html_writer::tag('small', get_string('convocatoriastartdate', 'local_jobboard'), ['class' => 'd-block text-muted']);
echo html_writer::tag('strong',
    '<i class="fa fa-calendar-alt text-success mr-2"></i>' .
    userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig'))
);
echo html_writer::end_div();
echo html_writer::start_div('col-md-6');
echo html_writer::tag('small', get_string('convocatoriaenddate', 'local_jobboard'), ['class' => 'd-block text-muted']);
echo html_writer::tag('strong',
    '<i class="fa fa-calendar-times text-danger mr-2"></i>' .
    userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig'))
);
echo html_writer::end_div();
echo html_writer::end_div();

// Terms (if any).
if (!empty($convocatoria->terms)) {
    echo html_writer::start_div('alert alert-info');
    echo html_writer::tag('h6', '<i class="fa fa-info-circle mr-2"></i>' . get_string('convocatoriaterms', 'local_jobboard'),
        ['class' => 'alert-heading']);
    echo format_text($convocatoria->terms, FORMAT_HTML);
    echo html_writer::end_div();
}

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// ============================================================================
// STATISTICS ROW
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card(
    (string) $stats['total_vacancies'],
    get_string('vacancies', 'local_jobboard'),
    'primary', 'briefcase'
);
echo ui_helper::stat_card(
    (string) $stats['positions'],
    get_string('positions', 'local_jobboard'),
    'success', 'users'
);
if ($canmanage) {
    echo ui_helper::stat_card(
        (string) $stats['applications'],
        get_string('applications', 'local_jobboard'),
        'info', 'file-alt'
    );
}
echo html_writer::end_div();

// ============================================================================
// VACANCIES IN THIS CONVOCATORIA
// ============================================================================
echo html_writer::tag('h4',
    '<i class="fa fa-briefcase mr-2"></i>' . get_string('convocatoriavacancies', 'local_jobboard'),
    ['class' => 'mb-3']
);

if (empty($vacancies)) {
    echo ui_helper::empty_state(
        get_string('novacancies', 'local_jobboard'),
        'briefcase'
    );
} else {
    echo html_writer::start_div('row');

    foreach ($vacancies as $vacancy) {
        // Get closedate from convocatoria if not on vacancy.
        $vacClosedate = !empty($vacancy->closedate) ? $vacancy->closedate : $convocatoria->enddate;
        $vacDaysRemaining = local_jobboard_days_between($now, $vacClosedate);
        $isVacUrgent = ($vacDaysRemaining <= 7 && $vacDaysRemaining >= 0);
        $isVacClosed = ($vacClosedate < $now || $vacancy->status === 'closed');

        // Card column.
        echo html_writer::start_div('col-lg-4 col-md-6 mb-4');

        $cardClass = 'card h-100 shadow-sm jb-vacancy-card';
        if ($isVacUrgent && !$isVacClosed) {
            $cardClass .= ' border-warning';
        } elseif ($isVacClosed) {
            $cardClass .= ' border-secondary opacity-75';
        }

        echo html_writer::start_div($cardClass);

        // Card header with status and urgency.
        echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center py-2');
        echo html_writer::tag('code', s($vacancy->code), ['class' => 'small text-muted']);

        $badges = '';
        $badges .= ui_helper::status_badge($vacancy->status, 'vacancy');
        if ($isVacUrgent && !$isVacClosed) {
            $badges .= ' ' . html_writer::tag('span',
                '<i class="fa fa-clock mr-1"></i>' . $vacDaysRemaining . 'd',
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
        $closeDateClass = $isVacUrgent ? 'text-warning font-weight-bold' : 'text-muted';
        echo html_writer::tag('small',
            '<i class="fa fa-calendar-times mr-1"></i>' . local_jobboard_format_date($vacClosedate),
            ['class' => $closeDateClass]
        );

        // Apply button.
        if ($vacancy->is_open() && $canapply) {
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
        } elseif ($isVacClosed) {
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
if ($totalVacancies > $perpage) {
    $baseurl = new moodle_url('/local/jobboard/index.php', [
        'view' => 'view_convocatoria',
        'id' => $convocatoriaid,
        'perpage' => $perpage,
    ]);
    echo $OUTPUT->paging_bar($totalVacancies, $page, $perpage, $baseurl);
}

// ============================================================================
// BACK BUTTON
// ============================================================================
echo html_writer::start_div('mt-4');
echo html_writer::link(
    new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']),
    '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtoconvocatorias', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary']
);
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-view-convocatoria

// Initialize JavaScript via AMD modules.
$PAGE->requires->js_call_amd('local_jobboard/card_actions', 'init', [[
    'buttonSelector' => '.jb-vacancy-card .jb-card-action',
    'cardSelector' => '.jb-vacancy-card'
]]);

echo $OUTPUT->footer();
