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
 * This file is included by index.php and should not be accessed directly.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

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

$PAGE->navbar->add(get_string('vacancies', 'local_jobboard'), new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']));
$PAGE->navbar->add($vacancy->title);

// Log view.
\local_jobboard\audit::log('vacancy_viewed', 'vacancy', $vacancy->id);

echo $OUTPUT->header();

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

echo html_writer::start_div('vacancy-detail');

// Header with title, code and status.
echo html_writer::start_div('d-flex justify-content-between align-items-start mb-4');
echo html_writer::start_div();
echo html_writer::tag('h2', s($vacancy->title));
echo html_writer::tag('p', get_string('vacancycode', 'local_jobboard') . ': ' .
    html_writer::tag('strong', s($vacancy->code)), ['class' => 'text-muted']);
echo html_writer::end_div();
echo html_writer::span($vacancy->get_status_display(), 'badge badge-' . $statusclass . ' badge-lg');
echo html_writer::end_div();

// Quick action buttons.
$canmanage = has_capability('local/jobboard:createvacancy', $context);
$canapply = has_capability('local/jobboard:apply', $context);

echo html_writer::start_div('mb-4');

if ($vacancy->is_open() && $canapply) {
    // Check if user already applied.
    global $DB;
    $hasapplied = $DB->record_exists('local_jobboard_application', [
        'vacancyid' => $vacancy->id,
        'userid' => $USER->id,
    ]);

    if ($hasapplied) {
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
            get_string('myapplications', 'local_jobboard'),
            ['class' => 'btn btn-outline-primary mr-2']
        );
        echo html_writer::span(get_string('error:alreadyapplied', 'local_jobboard'), 'badge badge-info');
    } else {
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
            get_string('apply', 'local_jobboard'),
            ['class' => 'btn btn-success mr-2']
        );
    }
}

if ($canmanage && $vacancy->can_edit()) {
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'action' => 'edit', 'id' => $vacancy->id]),
        get_string('edit', 'local_jobboard'),
        ['class' => 'btn btn-outline-primary mr-2']
    );
}

echo html_writer::link(
    new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']),
    get_string('back', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary']
);

echo html_writer::end_div();

// Vacancy details in cards.
echo html_writer::start_div('row');

// Main info card.
echo html_writer::start_div('col-md-8');
echo html_writer::start_div('card mb-4');
echo html_writer::start_div('card-body');

// Description.
if (!empty($vacancy->description)) {
    echo html_writer::tag('h5', get_string('vacancydescription', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::div(format_text($vacancy->description, FORMAT_HTML), 'card-text mb-4');
}

// Requirements.
if (!empty($vacancy->requirements)) {
    echo html_writer::tag('h5', get_string('requirements', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::div(format_text($vacancy->requirements, FORMAT_HTML), 'card-text mb-4');
}

// Desirable.
if (!empty($vacancy->desirable)) {
    echo html_writer::tag('h5', get_string('desirable', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::div(format_text($vacancy->desirable, FORMAT_HTML), 'card-text');
}

echo html_writer::end_div(); // card-body.
echo html_writer::end_div(); // card.
echo html_writer::end_div(); // col-md-8.

// Side info card.
echo html_writer::start_div('col-md-4');
echo html_writer::start_div('card');
echo html_writer::start_div('card-body');

echo html_writer::tag('h5', get_string('details', 'local_jobboard'), ['class' => 'card-title']);

$details = [];

// Contract type.
if (!empty($vacancy->contracttype)) {
    $contracttypes = local_jobboard_get_contract_types();
    $details[] = [
        'label' => get_string('contracttype', 'local_jobboard'),
        'value' => $contracttypes[$vacancy->contracttype] ?? $vacancy->contracttype,
    ];
}

// Duration.
if (!empty($vacancy->duration)) {
    $details[] = [
        'label' => get_string('duration', 'local_jobboard'),
        'value' => $vacancy->duration,
    ];
}

// Salary.
if (!empty($vacancy->salary)) {
    $details[] = [
        'label' => get_string('salary', 'local_jobboard'),
        'value' => $vacancy->salary,
    ];
}

// Location.
if (!empty($vacancy->location)) {
    $details[] = [
        'label' => get_string('location', 'local_jobboard'),
        'value' => $vacancy->location,
    ];
}

// Department.
if (!empty($vacancy->department)) {
    $details[] = [
        'label' => get_string('department', 'local_jobboard'),
        'value' => $vacancy->department,
    ];
}

// Company (Iomad).
if ($vacancy->companyid) {
    $details[] = [
        'label' => get_string('company', 'local_jobboard'),
        'value' => $vacancy->get_company_name(),
    ];
}

// Positions.
$details[] = [
    'label' => get_string('positions', 'local_jobboard'),
    'value' => $vacancy->positions,
];

// Opening date.
$details[] = [
    'label' => get_string('opendate', 'local_jobboard'),
    'value' => local_jobboard_format_date($vacancy->opendate),
];

// Closing date.
$closeinfo = local_jobboard_format_date($vacancy->closedate);
if ($vacancy->closedate < time()) {
    $closeinfo .= ' (' . get_string('status:closed', 'local_jobboard') . ')';
} else {
    $daysremaining = local_jobboard_days_between(time(), $vacancy->closedate);
    $closeinfo .= ' (' . $daysremaining . ' ' . get_string('days', 'local_jobboard') . ')';
}
$details[] = [
    'label' => get_string('closedate', 'local_jobboard'),
    'value' => $closeinfo,
];

// Render details list.
echo html_writer::start_tag('dl', ['class' => 'row']);
foreach ($details as $detail) {
    echo html_writer::tag('dt', $detail['label'], ['class' => 'col-sm-5']);
    echo html_writer::tag('dd', $detail['value'], ['class' => 'col-sm-7']);
}
echo html_writer::end_tag('dl');

echo html_writer::end_div(); // card-body.
echo html_writer::end_div(); // card.

// Statistics card (for managers).
if ($canmanage) {
    echo html_writer::start_div('card mt-4');
    echo html_writer::start_div('card-body');
    echo html_writer::tag('h5', get_string('reports', 'local_jobboard'), ['class' => 'card-title']);

    $appcount = $vacancy->get_application_count();
    $pendingcount = $vacancy->get_application_count('submitted');
    $reviewcount = $vacancy->get_application_count('under_review');
    $validatedcount = $vacancy->get_application_count('docs_validated');
    $selectedcount = $vacancy->get_application_count('selected');

    echo html_writer::start_tag('ul', ['class' => 'list-unstyled']);
    echo html_writer::tag('li', get_string('totalapplicants', 'local_jobboard') . ': ' .
        html_writer::tag('strong', $appcount));
    echo html_writer::tag('li', get_string('appstatus:submitted', 'local_jobboard') . ': ' .
        html_writer::tag('span', $pendingcount, ['class' => 'badge badge-warning']));
    echo html_writer::tag('li', get_string('appstatus:under_review', 'local_jobboard') . ': ' .
        html_writer::tag('span', $reviewcount, ['class' => 'badge badge-info']));
    echo html_writer::tag('li', get_string('appstatus:docs_validated', 'local_jobboard') . ': ' .
        html_writer::tag('span', $validatedcount, ['class' => 'badge badge-success']));
    echo html_writer::tag('li', get_string('appstatus:selected', 'local_jobboard') . ': ' .
        html_writer::tag('span', $selectedcount, ['class' => 'badge badge-primary']));
    echo html_writer::end_tag('ul');

    if ($appcount > 0) {
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancy->id]),
            get_string('reviewapplications', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-primary']
        );
    }

    echo html_writer::end_div(); // card-body.
    echo html_writer::end_div(); // card.
}

echo html_writer::end_div(); // col-md-4.
echo html_writer::end_div(); // row.

// Metadata (for managers).
if ($canmanage) {
    echo html_writer::start_div('mt-4 text-muted small');
    echo html_writer::tag('p', get_string('createdby', 'local_jobboard') . ': ' .
        fullname(\core_user::get_user($vacancy->createdby)) . ' - ' .
        local_jobboard_format_datetime($vacancy->timecreated));
    if ($vacancy->modifiedby) {
        echo html_writer::tag('p', get_string('modifiedby', 'local_jobboard') . ': ' .
            fullname(\core_user::get_user($vacancy->modifiedby)) . ' - ' .
            local_jobboard_format_datetime($vacancy->timemodified));
    }
    echo html_writer::end_div();
}

echo html_writer::end_div(); // vacancy-detail.

echo $OUTPUT->footer();
