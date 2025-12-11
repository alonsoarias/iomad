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
 * Uses renderer + Mustache template for clean separation of concerns.
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

// Load convocatoria if vacancy belongs to one.
$convocatoria = null;
if ($vacancy->convocatoriaid) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $vacancy->convocatoriaid]);
}

// Log view.
\local_jobboard\audit::log('vacancy_viewed', 'vacancy', $vacancy->id);

// Check capabilities.
$canmanage = has_capability('local/jobboard:createvacancy', $context);
$canapply = has_capability('local/jobboard:apply', $context);
$canedit = $canmanage && $vacancy->can_edit();

// Check if user already applied.
$hasapplied = $DB->record_exists('local_jobboard_application', [
    'vacancyid' => $vacancy->id,
    'userid' => $USER->id,
]);

// Build application stats for managers.
$applicationstats = [];
if ($canmanage) {
    $appcount = $vacancy->get_application_count();
    $pendingcount = $vacancy->get_application_count('submitted');
    $reviewcount = $vacancy->get_application_count('under_review');
    $validatedcount = $vacancy->get_application_count('docs_validated');
    $selectedcount = $vacancy->get_application_count('selected');

    if ($appcount > 0) {
        $applicationstats = [
            'total' => $appcount,
            'pending' => $pendingcount + $reviewcount,
            'validated' => $validatedcount + $selectedcount,
            'manageurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancy->id]))->out(false),
        ];
    }
}

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the renderer.
$data = $renderer->prepare_vacancy_detail_page_data(
    $vacancy,
    $convocatoria,
    $canapply,
    $hasapplied,
    $canedit,
    $canmanage,
    $applicationstats
);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_vacancy_detail_page($data);
echo $OUTPUT->footer();
