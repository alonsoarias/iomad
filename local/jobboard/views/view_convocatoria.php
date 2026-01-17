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
 * Reuses the same renderer methods as the public page for consistency.
 * Shows all vacancies from the convocatoria without company filtering.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

// Parameters - same filters as public page.
$convocatoriaid = required_param('id', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);
$filtercode = optional_param('code', '', PARAM_TEXT);
$filtercontract = optional_param('contracttype', '', PARAM_ALPHANUMEXT);
$filterdepartment = optional_param('department', '', PARAM_TEXT);
$filterlocation = optional_param('location', '', PARAM_TEXT);
$filtermodality = optional_param('modality', '', PARAM_ALPHANUMEXT);
$filtersearch = optional_param('search', '', PARAM_TEXT);

// AJAX request flag.
$isajax = optional_param('ajax', 0, PARAM_INT);

// Load convocatoria.
$convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid], '*', MUST_EXIST);

// Check capabilities early to validate convocatoria access.
$canviewinternal = has_capability('local/jobboard:viewinternalvacancies', $context);

// Check if convocatoria is public or user has permission to view internal.
if ($convocatoria->publicationtype !== 'public' && !$canviewinternal) {
    throw new moodle_exception('error:convocatorianotpublic', 'local_jobboard');
}

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->activityheader->disable();
$PAGE->set_title($convocatoria->name);
$PAGE->set_heading($convocatoria->name);

// Set up breadcrumbs via Moodle's native navbar.
$PAGE->navbar->add(get_string('dashboard', 'local_jobboard'),
    new moodle_url('/local/jobboard/index.php'));
$PAGE->navbar->add(get_string('convocatorias', 'local_jobboard'),
    new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']));
$PAGE->navbar->add($convocatoria->name);

// Log view.
\local_jobboard\audit::log('convocatoria_viewed', 'convocatoria', $convocatoria->id);

// Check capabilities.
$canapply = has_capability('local/jobboard:apply', $context);
// Note: $canviewinternal is already defined above during convocatoria access validation.

// Get contract types for display.
$contracttypes = local_jobboard_get_contract_types();

// Build vacancies query with filters (same as public.php - no company filtering).
$vacancyParams = ['convid' => $convocatoriaid];
$vacancyWhere = "v.convocatoriaid = :convid AND v.status = 'published'";

if (!$canviewinternal) {
    $vacancyWhere .= " AND v.publicationtype = 'public'";
}

// Filter: Code.
if (!empty($filtercode)) {
    $vacancyWhere .= " AND " . $DB->sql_like('v.code', ':code', false);
    $vacancyParams['code'] = '%' . $DB->sql_like_escape($filtercode) . '%';
}

// Filter: Contract type.
if (!empty($filtercontract)) {
    $vacancyWhere .= " AND v.contracttype = :contracttype";
    $vacancyParams['contracttype'] = $filtercontract;
}

// Filter: Department (Programa académico).
if (!empty($filterdepartment)) {
    $vacancyWhere .= " AND v.department = :department";
    $vacancyParams['department'] = $filterdepartment;
}

// Filter: Location (Ubicación).
if (!empty($filterlocation)) {
    $vacancyWhere .= " AND v.location = :location";
    $vacancyParams['location'] = $filterlocation;
}

// Filter: Modality.
if (!empty($filtermodality)) {
    $vacancyWhere .= " AND v.modality = :modality";
    $vacancyParams['modality'] = $filtermodality;
}

// Filter: General search.
if (!empty($filtersearch)) {
    $searchlike = '%' . $DB->sql_like_escape($filtersearch) . '%';
    $vacancyWhere .= " AND (" . $DB->sql_like('v.title', ':search1', false) .
                     " OR " . $DB->sql_like('v.code', ':search2', false) .
                     " OR " . $DB->sql_like('v.description', ':search3', false) . ")";
    $vacancyParams['search1'] = $searchlike;
    $vacancyParams['search2'] = $searchlike;
    $vacancyParams['search3'] = $searchlike;
}

// Get total count.
$totalVacancies = $DB->count_records_sql(
    "SELECT COUNT(*) FROM {local_jobboard_vacancy} v WHERE $vacancyWhere",
    $vacancyParams
);

// Get vacancies.
$vacancySql = "SELECT v.* FROM {local_jobboard_vacancy} v WHERE $vacancyWhere ORDER BY v.code ASC";
$vacancies = $DB->get_records_sql($vacancySql, $vacancyParams, $page * $perpage, $perpage);

// Get all vacancies for stats (unfiltered).
$allVacanciesForStats = $DB->get_records_sql(
    "SELECT v.* FROM {local_jobboard_vacancy} v
     WHERE v.convocatoriaid = :convid AND v.status = 'published'" .
    ($canviewinternal ? "" : " AND v.publicationtype = 'public'"),
    ['convid' => $convocatoriaid]
);

// Build filter options from all vacancies (same as public.php).
$departmentsList = [];
$contractTypesList = [];
$locationsList = [];
$modalitiesList = [];

// Get predefined modalities for proper labels.
$predefinedModalities = local_jobboard_get_modalities();

foreach ($allVacanciesForStats as $v) {
    // Departments (Programa académico).
    if (!empty($v->department) && !isset($departmentsList[$v->department])) {
        $departmentsList[$v->department] = $v->department;
    }
    // Contract types (Tipo de Vinculación).
    if (!empty($v->contracttype) && !isset($contractTypesList[$v->contracttype])) {
        $contractTypesList[$v->contracttype] = $contracttypes[$v->contracttype] ?? $v->contracttype;
    }
    // Locations (Ubicación).
    if (!empty($v->location) && !isset($locationsList[$v->location])) {
        $locationsList[$v->location] = $v->location;
    }
    // Modalities.
    if (!empty($v->modality) && !isset($modalitiesList[$v->modality])) {
        // Use predefined label if available, otherwise use stored value.
        $modalitylabel = $predefinedModalities[$v->modality] ?? $v->modality;
        $modalitiesList[$v->modality] = $modalitylabel;
    }
}

// Sort options alphabetically.
asort($departmentsList);
asort($contractTypesList);
asort($locationsList);
asort($modalitiesList);

$filters = [
    'code' => $filtercode,
    'contracttype' => $filtercontract,
    'department' => $filterdepartment,
    'location' => $filterlocation,
    'modality' => $filtermodality,
    'search' => $filtersearch,
];

$filterOptions = [
    'contracttypes' => $contractTypesList,
    'departments' => $departmentsList,
    'locations' => $locationsList,
    'modalities' => $modalitiesList,
];

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the same method as public.php.
$data = $renderer->prepare_public_vacancies_data(
    $convocatoria,
    $vacancies,
    $totalVacancies,
    $allVacanciesForStats,
    $filters,
    $filterOptions,
    true, // isloggedin - always true for authenticated view
    $canapply,
    $contracttypes,
    $page,
    $perpage
);

// Override view parameter in filter form for view_convocatoria view.
foreach ($data['filterform']['hiddenfields'] as $key => $field) {
    if ($field['name'] === 'view') {
        $data['filterform']['hiddenfields'][$key]['value'] = 'view_convocatoria';
    }
    if ($field['name'] === 'convocatoriaid') {
        // Change to 'id' for view_convocatoria.
        $data['filterform']['hiddenfields'][$key]['name'] = 'id';
    }
}

// Update clear filters URL for view_convocatoria view.
$data['clearfiltersurl'] = (new moodle_url('/local/jobboard/index.php', [
    'view' => 'view_convocatoria',
    'id' => $convocatoriaid,
]))->out(false);

// Update back URL for view_convocatoria view.
$data['backtoconvocatoriasurl'] = (new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']))->out(false);

// Handle AJAX request - return only vacancy results.
if ($isajax) {
    header('Content-Type: text/html; charset=utf-8');
    echo $renderer->render_public_vacancies_results($data);
    exit;
}

// Output page using the same template as public.php.
echo $OUTPUT->header();
echo $renderer->render_public_page($data);

// Initialize filter auto-submit for all users.
$PAGE->requires->js_call_amd('local_jobboard/public_filters', 'init', [[
    'formSelector' => '.jb-filter-form',
    'resultsSelector' => '[data-region="filter-results"]',
]]);

echo $OUTPUT->footer();
