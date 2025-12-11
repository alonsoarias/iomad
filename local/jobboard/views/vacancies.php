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
 * Role-based view with card layout for better UX.
 * Uses Mustache templates via renderer for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

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

// Capability checks.
$canapply = has_capability('local/jobboard:apply', $context);
$canviewall = has_capability('local/jobboard:viewallvacancies', $context);

// Build filters.
$filters = [
    'userid' => $USER->id,
    'respect_tenant' => true,
    'search' => $search,
    'companyid' => $companyid,
    'departmentid' => $departmentid,
    'contracttype' => $contracttype,
    'convocatoriaid' => $convocatoriaid,
];

if ($status) {
    $filters['status'] = $status;
} else {
    if (!$canviewall) {
        $filters['status'] = 'published';
    }
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

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the renderer.
$data = $renderer->prepare_vacancies_page_data(
    $vacancies,
    $total,
    $urgentCount,
    $filters,
    $page,
    $perpage,
    $convocatoria,
    $canapply,
    $canviewall
);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_vacancies_page($data);

// Initialize JavaScript via AMD modules.
$PAGE->requires->js_call_amd('local_jobboard/card_actions', 'init', [[
    'buttonSelector' => '.jb-vacancy-card .jb-card-action',
    'cardSelector' => '.jb-vacancy-card'
]]);

// IOMAD department filter AJAX.
if ($isiomad && $canviewall) {
    $allDepartmentsLabel = get_string('alldepartments', 'local_jobboard');
    $PAGE->requires->js_call_amd('local_jobboard/public_filters', 'init', [[
        'companySelector' => '#filter-companyid',
        'departmentSelector' => '#filter-departmentid',
        'preselect' => $departmentid,
        'allLabel' => $allDepartmentsLabel,
    ]]);
}

echo $OUTPUT->footer();
