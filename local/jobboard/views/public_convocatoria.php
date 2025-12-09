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
 * Public convocatoria view for local_jobboard.
 *
 * Shows convocatoria details and published vacancies without requiring authentication.
 * Visitors can view details but must log in to apply.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

// Check if public page is enabled.
$enablepublic = get_config('local_jobboard', 'enable_public_page');
if (!$enablepublic) {
    throw new moodle_exception('error:publicpagedisabled', 'local_jobboard');
}

// Parameters.
$convocatoriaid = required_param('id', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);

// Load convocatoria.
$convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);

if (!$convocatoria) {
    throw new moodle_exception('error:convocatorianotfound', 'local_jobboard');
}

// Check convocatoria is open or published.
$now = time();
if ($convocatoria->status !== 'open' || $convocatoria->enddate < $now) {
    throw new moodle_exception('error:convocatoriaclosed', 'local_jobboard');
}

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title($convocatoria->name);
$PAGE->set_heading($convocatoria->name);
$PAGE->requires->css('/local/jobboard/styles.css');

// Log view (anonymous).
\local_jobboard\audit::log('public_convocatoria_viewed', 'convocatoria', $convocatoria->id);

// Check if user is logged in.
$isloggedin = isloggedin() && !isguestuser();

// Calculate timing.
$daysRemaining = ceil(($convocatoria->enddate - $now) / 86400);
$isOpen = ($convocatoria->status === 'open' && $convocatoria->enddate >= $now);
$isClosingSoon = ($daysRemaining <= 7 && $daysRemaining > 0 && $isOpen);

// Get published public vacancies for this convocatoria.
$sql = "SELECT v.*
          FROM {local_jobboard_vacancy} v
         WHERE v.convocatoriaid = :convid
           AND v.status = :status
           AND v.publicationtype = :pubtype
         ORDER BY v.code ASC";

$params = [
    'convid' => $convocatoriaid,
    'status' => 'published',
    'pubtype' => 'public',
];

$vacancies = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);
$totalVacancies = $DB->count_records_sql(
    "SELECT COUNT(*) FROM {local_jobboard_vacancy} v
      WHERE v.convocatoriaid = :convid AND v.status = :status AND v.publicationtype = :pubtype",
    $params
);

// Get statistics.
$stats = [
    'total_vacancies' => $totalVacancies,
    'positions' => 0,
];

$allVacancies = $DB->get_records_sql(
    "SELECT v.positions FROM {local_jobboard_vacancy} v
      WHERE v.convocatoriaid = :convid AND v.status = :status AND v.publicationtype = :pubtype",
    $params
);
foreach ($allVacancies as $v) {
    $stats['positions'] += $v->positions;
}

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-public-convocatoria');

// ============================================================================
// PAGE HEADER
// ============================================================================
$breadcrumbs = [
    get_string('publicpagetitle', 'local_jobboard') => new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
    s($convocatoria->name) => null,
];

echo ui_helper::page_header(s($convocatoria->name), $breadcrumbs, []);

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

// Login/Register CTA for non-authenticated users.
if (!$isloggedin) {
    echo html_writer::start_div('alert alert-warning border-warning');
    echo html_writer::tag('h5', '<i class="fa fa-sign-in-alt mr-2"></i>' . get_string('loginrequired', 'local_jobboard'),
        ['class' => 'alert-heading mb-2']);
    echo html_writer::tag('p', get_string('loginrequired_desc', 'local_jobboard'), ['class' => 'mb-3']);

    echo html_writer::start_div('d-flex flex-wrap gap-2');
    echo html_writer::link(
        new moodle_url('/login/index.php'),
        '<i class="fa fa-sign-in-alt mr-2"></i>' . get_string('login'),
        ['class' => 'btn btn-primary mr-2 mb-2']
    );
    echo html_writer::link(
        new moodle_url('/local/jobboard/signup.php'),
        '<i class="fa fa-user-plus mr-2"></i>' . get_string('signup', 'local_jobboard'),
        ['class' => 'btn btn-outline-primary mb-2']
    );
    echo html_writer::end_div();
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
        get_string('nopublicvacancies', 'local_jobboard'),
        'briefcase'
    );
} else {
    echo html_writer::start_div('row');

    foreach ($vacancies as $vacancy) {
        // Get closedate from convocatoria.
        $vacClosedate = $convocatoria->enddate;
        $vacDaysRemaining = local_jobboard_days_between($now, $vacClosedate);
        $isVacUrgent = ($vacDaysRemaining <= 7 && $vacDaysRemaining >= 0);

        // Card column.
        echo html_writer::start_div('col-lg-4 col-md-6 mb-4');

        $cardClass = 'card h-100 shadow-sm jb-vacancy-card';
        if ($isVacUrgent) {
            $cardClass .= ' border-warning';
        }

        echo html_writer::start_div($cardClass);

        // Card header with status and urgency.
        echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center py-2');
        echo html_writer::tag('code', s($vacancy->code), ['class' => 'small text-muted']);

        $badges = '';
        if ($isVacUrgent) {
            $badges .= html_writer::tag('span',
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
                new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]),
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

        // View details link.
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]),
            get_string('viewdetails', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-primary']
        );

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
        'view' => 'public_convocatoria',
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
    new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
    '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtovacancies', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary']
);
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-public-convocatoria

echo $OUTPUT->footer();
