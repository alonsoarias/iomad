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
 * Uses renderer + Mustache template for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

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

// Log view.
\local_jobboard\audit::log('convocatoria_viewed', 'convocatoria', $convocatoria->id);

// Check capabilities.
$canapply = has_capability('local/jobboard:apply', $context);
$canmanage = has_capability('local/jobboard:createvacancy', $context);

// Get vacancies for this convocatoria.
$filters = [
    'convocatoriaid' => $convocatoriaid,
    'status' => 'published',
    'respect_tenant' => true,
    'userid' => $USER->id,
];

$result = \local_jobboard\vacancy::get_list($filters, 'closedate', 'ASC', $page, $perpage);
$vacancies = $result['vacancies'];
$totalvacancies = $result['total'];

// Get statistics.
$stats = [
    'total_vacancies' => $totalvacancies,
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

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the renderer.
$data = $renderer->prepare_view_convocatoria_page_data(
    $convocatoria,
    $vacancies,
    $totalvacancies,
    $stats,
    $canapply,
    $canmanage,
    $page,
    $perpage
);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_view_convocatoria_page($data);

// Initialize JavaScript via AMD modules.
$PAGE->requires->js_call_amd('local_jobboard/card_actions', 'init', [[
    'buttonSelector' => '.jb-vacancy-card .jb-card-action',
    'cardSelector' => '.jb-vacancy-card',
]]);

echo $OUTPUT->footer();
