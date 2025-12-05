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
 * Dashboard view for local_jobboard.
 *
 * This file is included by index.php and should not be accessed directly.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('jobboard', 'local_jobboard'));
$PAGE->set_heading(get_string('jobboard', 'local_jobboard'));

echo $OUTPUT->header();

// Dashboard content based on user capabilities.
$canmanage = has_capability('local/jobboard:createvacancy', $context);
$canreview = has_capability('local/jobboard:reviewdocuments', $context);
$canapply = has_capability('local/jobboard:apply', $context);
$canviewreports = has_capability('local/jobboard:viewreports', $context);

echo html_writer::start_div('local-jobboard-dashboard');

// Header.
echo html_writer::tag('h2', get_string('dashboard', 'local_jobboard'));
echo html_writer::tag('p', get_string('jobboard:desc', 'local_jobboard'));

// Quick action cards.
echo html_writer::start_div('card-deck mt-4');

// View vacancies card.
if ($canapply || $canmanage) {
    echo html_writer::start_div('card');
    echo html_writer::start_div('card-body');
    echo html_writer::tag('h5', get_string('vacancies', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::tag('p', get_string('help:vacancy', 'local_jobboard'), ['class' => 'card-text']);
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']),
        get_string('view', 'local_jobboard') . ' ' . get_string('vacancies', 'local_jobboard'),
        ['class' => 'btn btn-primary']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// My applications card.
if ($canapply) {
    echo html_writer::start_div('card');
    echo html_writer::start_div('card-body');
    echo html_writer::tag('h5', get_string('myapplications', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::tag('p', get_string('help:documents', 'local_jobboard'), ['class' => 'card-text']);
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
        get_string('view', 'local_jobboard') . ' ' . get_string('myapplications', 'local_jobboard'),
        ['class' => 'btn btn-primary']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Manage convocatorias card.
if ($canmanage) {
    echo html_writer::start_div('card');
    echo html_writer::start_div('card-body');
    echo html_writer::tag('h5', get_string('manageconvocatorias', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::tag('p', get_string('convocatoriahelp', 'local_jobboard'), ['class' => 'card-text']);
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
        get_string('manageconvocatorias', 'local_jobboard'),
        ['class' => 'btn btn-primary']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Manage vacancies card.
if ($canmanage) {
    echo html_writer::start_div('card');
    echo html_writer::start_div('card-body');
    echo html_writer::tag('h5', get_string('managevacancies', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::tag('p', get_string('help:vacancy', 'local_jobboard'), ['class' => 'card-text']);
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
        get_string('managevacancies', 'local_jobboard'),
        ['class' => 'btn btn-primary']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Review applications card.
if ($canreview) {
    echo html_writer::start_div('card');
    echo html_writer::start_div('card-body');
    echo html_writer::tag('h5', get_string('reviewapplications', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::tag('p', get_string('help:review', 'local_jobboard'), ['class' => 'card-text']);
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
        get_string('reviewapplications', 'local_jobboard'),
        ['class' => 'btn btn-primary']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Reports card.
if ($canviewreports) {
    echo html_writer::start_div('card');
    echo html_writer::start_div('card-body');
    echo html_writer::tag('h5', get_string('reports', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::tag('p', get_string('report:applications', 'local_jobboard'), ['class' => 'card-text']);
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'reports']),
        get_string('reports', 'local_jobboard'),
        ['class' => 'btn btn-primary']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // card-deck.

echo html_writer::end_div(); // local-jobboard-dashboard.

echo $OUTPUT->footer();
