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
 * Public vacancy detail view for local_jobboard.
 *
 * Uses renderer + Mustache template for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

// Check if public page is enabled.
$enablepublic = get_config('local_jobboard', 'enable_public_page');
if (!$enablepublic) {
    throw new moodle_exception('error:publicpagedisabled', 'local_jobboard');
}

// Get vacancy ID.
$id = required_param('id', PARAM_INT);

// Get the vacancy.
$vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $id]);

if (!$vacancy || $vacancy->status !== 'published') {
    throw new moodle_exception('error:vacancynotfound', 'local_jobboard');
}

// Check if user is logged in.
$isloggedin = isloggedin() && !isguestuser();

// User capabilities.
$canviewinternal = $isloggedin && has_capability('local/jobboard:viewinternalvacancies', $context);
$canapply = $isloggedin && has_capability('local/jobboard:apply', $context);

// Check if vacancy is viewable (public or user has internal view capability).
if ($vacancy->publicationtype !== 'public' && !$canviewinternal) {
    throw new moodle_exception('error:vacancynotpublic', 'local_jobboard');
}

// Get convocatoria info.
$convocatoria = null;
if ($vacancy->convocatoriaid) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $vacancy->convocatoriaid]);
}

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title($vacancy->title);
$PAGE->set_heading($vacancy->title);

// Check if user has already applied.
$hasapplied = false;
$userapplication = null;
if ($isloggedin) {
    $userapplication = $DB->get_record('local_jobboard_application', [
        'vacancyid' => $vacancy->id,
        'userid' => $USER->id,
    ]) ?: null;
    $hasapplied = !empty($userapplication);
}

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the renderer.
$data = $renderer->prepare_public_vacancy_page_data(
    $vacancy,
    $convocatoria,
    $isloggedin,
    $canapply,
    $hasapplied,
    $userapplication
);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_public_vacancy_page($data);
echo $OUTPUT->footer();
