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
 * Vacancy detail view for local_jobboard.
 *
 * Modern redesign with improved layout and UX.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

// Parameters.
$id = required_param('id', PARAM_INT);

$vacancy = \local_jobboard\vacancy::get($id);
if (!$vacancy) {
    throw new moodle_exception('error:vacancynotfound', 'local_jobboard');
}

// Check if user can view this vacancy.
if (!local_jobboard_can_view_vacancy($vacancy->to_record())) {
    throw new moodle_exception('error:noaccess', 'local_jobboard');
}

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title($vacancy->title);
$PAGE->set_heading($vacancy->title);
$PAGE->requires->css('/local/jobboard/styles.css');

// Load convocatoria if vacancy belongs to one (for breadcrumbs).
$convocatoria = null;
if ($vacancy->convocatoriaid) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $vacancy->convocatoriaid]);
}

// Set up Moodle navbar with proper hierarchy.
if ($convocatoria) {
    $PAGE->navbar->add(get_string('convocatorias', 'local_jobboard'),
        new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']));
    $PAGE->navbar->add($convocatoria->name,
        new moodle_url('/local/jobboard/index.php', ['view' => 'view_convocatoria', 'id' => $convocatoria->id]));
} else {
    $PAGE->navbar->add(get_string('vacancies', 'local_jobboard'),
        new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']));
}
$PAGE->navbar->add($vacancy->title);

// Log view.
\local_jobboard\audit::log('vacancy_viewed', 'vacancy', $vacancy->id);

// Check capabilities.
$canmanage = has_capability('local/jobboard:createvacancy', $context);
$canapply = has_capability('local/jobboard:apply', $context);

// Check if user already applied.
$hasApplied = $DB->record_exists('local_jobboard_application', [
    'vacancyid' => $vacancy->id,
    'userid' => $USER->id,
]);

// Calculate days remaining.
$daysRemaining = local_jobboard_days_between(time(), $vacancy->closedate);
$isUrgent = ($daysRemaining <= 7 && $daysRemaining >= 0);
$isClosed = ($vacancy->closedate < time() || $vacancy->status === 'closed');

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-vacancy-detail');

// ============================================================================
// PAGE HEADER
// ============================================================================
$breadcrumbs = [
    get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
];

// Add convocatoria to breadcrumbs if vacancy belongs to one.
if ($convocatoria) {
    $breadcrumbs[get_string('convocatorias', 'local_jobboard')] = new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']);
    $breadcrumbs[s($convocatoria->name)] = new moodle_url('/local/jobboard/index.php', ['view' => 'view_convocatoria', 'id' => $convocatoria->id]);
} else {
    $breadcrumbs[get_string('vacancies', 'local_jobboard')] = new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']);
}

$breadcrumbs[s($vacancy->title)] = null;

$headerActions = [];
if ($vacancy->is_open() && $canapply && !$hasApplied) {
    $headerActions[] = [
        'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
        'label' => get_string('apply', 'local_jobboard'),
        'icon' => 'paper-plane',
        'class' => 'btn btn-success btn-lg',
    ];
}
if ($canmanage && $vacancy->can_edit()) {
    $headerActions[] = [
        'url' => new moodle_url('/local/jobboard/edit.php', ['id' => $vacancy->id]),
        'label' => get_string('edit', 'local_jobboard'),
        'icon' => 'edit',
        'class' => 'btn btn-outline-primary',
    ];
}

echo ui_helper::page_header(
    s($vacancy->title),
    $breadcrumbs,
    $headerActions
);

// ============================================================================
// STATUS BANNER
// ============================================================================
$bannerClass = 'alert d-flex justify-content-between align-items-center mb-4';
$bannerIcon = 'info-circle';

if ($hasApplied) {
    $bannerClass .= ' alert-info';
    $bannerMessage = get_string('error:alreadyapplied', 'local_jobboard');
    $bannerIcon = 'check-circle';
} elseif ($isClosed) {
    $bannerClass .= ' alert-secondary';
    $bannerMessage = get_string('error:vacancyclosed', 'local_jobboard');
    $bannerIcon = 'lock';
} elseif ($isUrgent) {
    $bannerClass .= ' alert-warning';
    $bannerMessage = get_string('closingsoondays', 'local_jobboard', $daysRemaining);
    $bannerIcon = 'clock';
} else {
    $bannerClass .= ' alert-success';
    $bannerMessage = get_string('vacancyopen', 'local_jobboard');
    $bannerIcon = 'door-open';
}

echo html_writer::start_div($bannerClass);
echo html_writer::tag('span',
    '<i class="fa fa-' . $bannerIcon . ' mr-2"></i>' . $bannerMessage,
    ['class' => 'font-weight-medium']
);
echo html_writer::start_div();
echo ui_helper::status_badge($vacancy->status, 'vacancy');
echo html_writer::tag('code', ' ' . s($vacancy->code), ['class' => 'ml-2']);
echo html_writer::end_div();
echo html_writer::end_div();

// ============================================================================
// MAIN CONTENT
// ============================================================================
echo html_writer::start_div('row');

// Left column - Main content.
echo html_writer::start_div('col-lg-8');

// Description card.
if (!empty($vacancy->description)) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-file-alt text-primary mr-2"></i>' . get_string('vacancydescription', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo format_text($vacancy->description, FORMAT_HTML, ['context' => $context]);
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Requirements card.
if (!empty($vacancy->requirements)) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-list-check text-warning mr-2"></i>' . get_string('requirements', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo format_text($vacancy->requirements, FORMAT_HTML, ['context' => $context]);
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Desirable card.
if (!empty($vacancy->desirable)) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-star text-info mr-2"></i>' . get_string('desirable', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo format_text($vacancy->desirable, FORMAT_HTML, ['context' => $context]);
    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // col-lg-8

// Right column - Sidebar.
echo html_writer::start_div('col-lg-4');

// Key details card.
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-primary text-white');
echo html_writer::tag('h5',
    '<i class="fa fa-info-circle mr-2"></i>' . get_string('details', 'local_jobboard'),
    ['class' => 'mb-0']
);
echo html_writer::end_div();
echo html_writer::start_div('card-body p-0');

echo html_writer::start_tag('ul', ['class' => 'list-group list-group-flush']);

// Contract type.
if (!empty($vacancy->contracttype)) {
    $contracttypes = local_jobboard_get_contract_types();
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    echo html_writer::tag('span', '<i class="fa fa-file-contract text-muted mr-2"></i>' . get_string('contracttype', 'local_jobboard'));
    echo html_writer::tag('strong', $contracttypes[$vacancy->contracttype] ?? $vacancy->contracttype);
    echo html_writer::end_tag('li');
}

// Duration.
if (!empty($vacancy->duration)) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    echo html_writer::tag('span', '<i class="fa fa-hourglass-half text-muted mr-2"></i>' . get_string('duration', 'local_jobboard'));
    echo html_writer::tag('strong', s($vacancy->duration));
    echo html_writer::end_tag('li');
}

// Salary.
if (!empty($vacancy->salary)) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    echo html_writer::tag('span', '<i class="fa fa-money-bill-wave text-muted mr-2"></i>' . get_string('salary', 'local_jobboard'));
    echo html_writer::tag('strong', s($vacancy->salary));
    echo html_writer::end_tag('li');
}

// Location.
if (!empty($vacancy->location)) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    echo html_writer::tag('span', '<i class="fa fa-map-marker-alt text-muted mr-2"></i>' . get_string('location', 'local_jobboard'));
    echo html_writer::tag('strong', s($vacancy->location));
    echo html_writer::end_tag('li');
}

// Department.
if (!empty($vacancy->department)) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    echo html_writer::tag('span', '<i class="fa fa-building text-muted mr-2"></i>' . get_string('department', 'local_jobboard'));
    echo html_writer::tag('strong', s($vacancy->department));
    echo html_writer::end_tag('li');
}

// Company (Iomad).
if ($vacancy->companyid) {
    echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
    echo html_writer::tag('span', '<i class="fa fa-industry text-muted mr-2"></i>' . get_string('company', 'local_jobboard'));
    echo html_writer::tag('strong', s($vacancy->get_company_name()));
    echo html_writer::end_tag('li');
}

// Positions.
echo html_writer::start_tag('li', ['class' => 'list-group-item d-flex justify-content-between align-items-center']);
echo html_writer::tag('span', '<i class="fa fa-users text-muted mr-2"></i>' . get_string('positions', 'local_jobboard'));
echo html_writer::tag('span', $vacancy->positions, ['class' => 'badge badge-primary badge-pill']);
echo html_writer::end_tag('li');

echo html_writer::end_tag('ul');
echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// Dates card.
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-white');
echo html_writer::tag('h5',
    '<i class="fa fa-calendar-alt text-primary mr-2"></i>' . get_string('dates', 'local_jobboard'),
    ['class' => 'mb-0']
);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

// Opening date.
echo html_writer::start_div('d-flex justify-content-between mb-2');
echo html_writer::tag('span', get_string('opendate', 'local_jobboard'), ['class' => 'text-muted']);
echo html_writer::tag('strong', local_jobboard_format_date($vacancy->opendate));
echo html_writer::end_div();

// Closing date.
$closeDateClass = $isUrgent ? 'text-warning' : ($isClosed ? 'text-secondary' : 'text-success');
echo html_writer::start_div('d-flex justify-content-between mb-2');
echo html_writer::tag('span', get_string('closedate', 'local_jobboard'), ['class' => 'text-muted']);
echo html_writer::start_div();
echo html_writer::tag('strong', local_jobboard_format_date($vacancy->closedate), ['class' => $closeDateClass]);
if (!$isClosed && $daysRemaining >= 0) {
    echo html_writer::tag('span', ' (' . $daysRemaining . ' ' . get_string('days', 'local_jobboard') . ')',
        ['class' => 'small ' . $closeDateClass]);
}
echo html_writer::end_div();
echo html_writer::end_div();

// Progress bar for deadline.
if (!$isClosed) {
    $totalDays = local_jobboard_days_between($vacancy->opendate, $vacancy->closedate);
    $elapsedDays = local_jobboard_days_between($vacancy->opendate, time());
    $progress = $totalDays > 0 ? min(100, ($elapsedDays / $totalDays) * 100) : 100;
    $progressClass = $progress > 80 ? 'bg-danger' : ($progress > 50 ? 'bg-warning' : 'bg-success');

    echo html_writer::start_div('progress mt-3', ['style' => 'height: 8px;']);
    echo html_writer::div('', 'progress-bar ' . $progressClass, [
        'role' => 'progressbar',
        'style' => 'width: ' . $progress . '%',
        'aria-valuenow' => $progress,
        'aria-valuemin' => '0',
        'aria-valuemax' => '100',
    ]);
    echo html_writer::end_div();
    echo html_writer::tag('small', get_string('deadlineprogress', 'local_jobboard'), ['class' => 'text-muted']);
}

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// Apply CTA card (if not applied and open).
if ($vacancy->is_open() && $canapply && !$hasApplied) {
    echo html_writer::start_div('card shadow-sm mb-4 border-success');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('h5', get_string('readytoapply', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::tag('p', get_string('applynowdesc', 'local_jobboard'), ['class' => 'card-text text-muted']);
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
        '<i class="fa fa-paper-plane mr-2"></i>' . get_string('apply', 'local_jobboard'),
        ['class' => 'btn btn-success btn-lg btn-block']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
} elseif ($hasApplied) {
    echo html_writer::start_div('card shadow-sm mb-4 border-info');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('h5',
        '<i class="fa fa-check-circle text-info mr-2"></i>' . get_string('applied', 'local_jobboard'),
        ['class' => 'card-title']
    );
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
        get_string('viewmyapplications', 'local_jobboard'),
        ['class' => 'btn btn-outline-info']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Statistics card (for managers).
if ($canmanage) {
    $appcount = $vacancy->get_application_count();
    $pendingcount = $vacancy->get_application_count('submitted');
    $reviewcount = $vacancy->get_application_count('under_review');
    $validatedcount = $vacancy->get_application_count('docs_validated');
    $selectedcount = $vacancy->get_application_count('selected');

    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-chart-bar text-primary mr-2"></i>' . get_string('applicationstats', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    echo html_writer::start_div('row text-center');
    echo html_writer::div(
        html_writer::tag('div', $appcount, ['class' => 'h3 mb-0 text-primary']) .
        html_writer::tag('small', get_string('total', 'local_jobboard'), ['class' => 'text-muted']),
        'col-6 mb-3'
    );
    echo html_writer::div(
        html_writer::tag('div', $pendingcount, ['class' => 'h3 mb-0 text-warning']) .
        html_writer::tag('small', get_string('appstatus:submitted', 'local_jobboard'), ['class' => 'text-muted']),
        'col-6 mb-3'
    );
    echo html_writer::div(
        html_writer::tag('div', $reviewcount, ['class' => 'h3 mb-0 text-info']) .
        html_writer::tag('small', get_string('appstatus:under_review', 'local_jobboard'), ['class' => 'text-muted']),
        'col-6'
    );
    echo html_writer::div(
        html_writer::tag('div', $selectedcount, ['class' => 'h3 mb-0 text-success']) .
        html_writer::tag('small', get_string('appstatus:selected', 'local_jobboard'), ['class' => 'text-muted']),
        'col-6'
    );
    echo html_writer::end_div();

    if ($appcount > 0) {
        echo html_writer::div('', 'border-top my-3');
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancy->id]),
            '<i class="fa fa-eye mr-2"></i>' . get_string('reviewapplications', 'local_jobboard'),
            ['class' => 'btn btn-outline-primary btn-block']
        );
    }

    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // col-lg-4
echo html_writer::end_div(); // row

// ============================================================================
// METADATA (for managers)
// ============================================================================
if ($canmanage) {
    echo html_writer::start_div('card shadow-sm mt-4');
    echo html_writer::start_div('card-body bg-light py-2');
    echo html_writer::start_div('row small text-muted');
    echo html_writer::div(
        '<i class="fa fa-user mr-1"></i>' . get_string('createdby', 'local_jobboard') . ': ' .
        fullname(\core_user::get_user($vacancy->createdby)) . ' - ' .
        local_jobboard_format_datetime($vacancy->timecreated),
        'col-md-6'
    );
    if ($vacancy->modifiedby) {
        echo html_writer::div(
            '<i class="fa fa-edit mr-1"></i>' . get_string('modifiedby', 'local_jobboard') . ': ' .
            fullname(\core_user::get_user($vacancy->modifiedby)) . ' - ' .
            local_jobboard_format_datetime($vacancy->timemodified),
            'col-md-6'
        );
    }
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // local-jobboard-vacancy-detail

echo $OUTPUT->footer();
