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
 * Public convocatoria detail view for local_jobboard.
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

// Parameters.
$convocatoriaid = required_param('id', PARAM_INT);

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

// Log view (anonymous).
\local_jobboard\audit::log('public_convocatoria_viewed', 'convocatoria', $convocatoria->id);

// Check if user is logged in.
$isloggedin = isloggedin() && !isguestuser();

// Get statistics using aggregation.
$stats = $DB->get_record_sql(
    "SELECT COUNT(*) as total_vacancies, COALESCE(SUM(v.positions), 0) as total_positions
       FROM {local_jobboard_vacancy} v
      WHERE v.convocatoriaid = :convid
        AND v.status = :status
        AND v.publicationtype = :pubtype",
    [
        'convid' => $convocatoriaid,
        'status' => 'published',
        'pubtype' => 'public',
    ]
);

$totalvacancies = (int) ($stats->total_vacancies ?? 0);
$totalpositions = (int) ($stats->total_positions ?? 0);

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the renderer.
$data = $renderer->prepare_public_convocatoria_page_data(
    $convocatoria,
    $totalvacancies,
    $totalpositions,
    $isloggedin
);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_public_convocatoria_page($data);
echo $OUTPUT->footer();
