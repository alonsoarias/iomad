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
 * Role-based view with card layout and filters similar to the public page.
 * All published vacancies are visible to authenticated users with filter options.
 * Uses Mustache templates via renderer for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\helper\iomad_helper;
use local_jobboard\helper\date_helper;

// Parameters - same filters as public page.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);
$convocatoriaid = optional_param('convocatoriaid', 0, PARAM_INT);
$filtercode = optional_param('code', '', PARAM_TEXT);
$filtercontract = optional_param('contracttype', '', PARAM_ALPHANUMEXT);
$filterdepartment = optional_param('department', '', PARAM_TEXT);
$filterlocation = optional_param('location', '', PARAM_TEXT);
$filtermodality = optional_param('modality', '', PARAM_ALPHANUMEXT);
$filtersearch = optional_param('search', '', PARAM_TEXT);
$filtercompanyid = optional_param('companyid', null, PARAM_INT);
$showallcompanies = optional_param('allcompanies', 0, PARAM_INT);

// AJAX request flag.
$isajax = optional_param('ajax', 0, PARAM_INT);

// Check if IOMAD is installed.
$isiomad = iomad_helper::is_iomad_installed();

// Default to user's company if no filter specified and not showing all.
$usercompanyid = 0;
if ($isiomad) {
    $usercompanyid = iomad_helper::get_user_companyid();
    // If companyid is null (not set in URL) and not showing all, default to user's company.
    if ($filtercompanyid === null && !$showallcompanies && $usercompanyid) {
        $filtercompanyid = $usercompanyid;
    }
}
// Ensure filtercompanyid is an int (handle null case).
$filtercompanyid = (int)$filtercompanyid;

// Load convocatoria if filtering by it.
$convocatoria = null;
if ($convocatoriaid) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
}

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->activityheader->disable();

if ($convocatoria) {
    $PAGE->set_title($convocatoria->name . ' - ' . get_string('vacancies', 'local_jobboard'));
    $PAGE->set_heading($convocatoria->name);
    $PAGE->navbar->add(get_string('dashboard', 'local_jobboard'),
        new moodle_url('/local/jobboard/index.php'));
    $PAGE->navbar->add(get_string('convocatorias', 'local_jobboard'),
        new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']));
    $PAGE->navbar->add(format_string($convocatoria->name));
} else {
    $PAGE->set_title(get_string('vacancies', 'local_jobboard'));
    $PAGE->set_heading(get_string('vacancies', 'local_jobboard'));
    $PAGE->navbar->add(get_string('dashboard', 'local_jobboard'),
        new moodle_url('/local/jobboard/index.php'));
    $PAGE->navbar->add(get_string('vacancies', 'local_jobboard'));
}

// Capability checks.
$canapply = has_capability('local/jobboard:apply', $context);
$canviewall = has_capability('local/jobboard:viewallvacancies', $context);
$canviewinternal = has_capability('local/jobboard:viewinternalvacancies', $context);

// Get contract types and modalities for display.
$contracttypes = local_jobboard_get_contract_types();
$predefinedModalities = local_jobboard_get_modalities();

// Build vacancies query with filters.
$vacancyParams = [];
$vacancyWhere = "v.status = 'published'";

// Filter by convocatoria if specified.
if ($convocatoriaid) {
    $vacancyWhere .= " AND v.convocatoriaid = :convid";
    $vacancyParams['convid'] = $convocatoriaid;
}

// Only show public vacancies if user cannot view internal ones.
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

// Filter: Company (optional - allows users to filter by company).
if (!empty($filtercompanyid)) {
    $vacancyWhere .= " AND v.companyid = :companyid";
    $vacancyParams['companyid'] = $filtercompanyid;
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
$vacancySql = "SELECT v.* FROM {local_jobboard_vacancy} v WHERE $vacancyWhere ORDER BY v.closedate ASC, v.code ASC";
$vacancies = $DB->get_records_sql($vacancySql, $vacancyParams, $page * $perpage, $perpage);

// Get all vacancies for stats and filter options (unfiltered except convocatoria and publication type).
$statsWhere = "v.status = 'published'";
$statsParams = [];
if ($convocatoriaid) {
    $statsWhere .= " AND v.convocatoriaid = :convid";
    $statsParams['convid'] = $convocatoriaid;
}
if (!$canviewinternal) {
    $statsWhere .= " AND v.publicationtype = 'public'";
}

$allVacanciesForStats = $DB->get_records_sql(
    "SELECT v.* FROM {local_jobboard_vacancy} v WHERE $statsWhere",
    $statsParams
);

// Build filter options from all vacancies.
$departmentsList = [];
$contractTypesList = [];
$locationsList = [];
$modalitiesList = [];
$companiesList = [];

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
        $modalitylabel = $predefinedModalities[$v->modality] ?? $v->modality;
        $modalitiesList[$v->modality] = $modalitylabel;
    }
}

// Get companies list if IOMAD is installed.
if ($isiomad) {
    $companyids = array_unique(array_filter(array_column($allVacanciesForStats, 'companyid')));
    if (!empty($companyids)) {
        list($insql, $inparams) = $DB->get_in_or_equal($companyids, SQL_PARAMS_NAMED);
        $companies = $DB->get_records_sql(
            "SELECT id, name FROM {company} WHERE id $insql ORDER BY name",
            $inparams
        );
        foreach ($companies as $c) {
            $companiesList[$c->id] = format_string($c->name);
        }
    }
}

// Sort options alphabetically.
asort($departmentsList);
asort($contractTypesList);
asort($locationsList);
asort($modalitiesList);
asort($companiesList);

// Current filters.
$filters = [
    'code' => $filtercode,
    'contracttype' => $filtercontract,
    'department' => $filterdepartment,
    'location' => $filterlocation,
    'modality' => $filtermodality,
    'search' => $filtersearch,
    'companyid' => $filtercompanyid,
    'convocatoriaid' => $convocatoriaid,
    'showallcompanies' => $showallcompanies,
    'usercompanyid' => $usercompanyid,
];

$filterOptions = [
    'contracttypes' => $contractTypesList,
    'departments' => $departmentsList,
    'locations' => $locationsList,
    'modalities' => $modalitiesList,
    'companies' => $companiesList,
];

// Count by urgency.
$urgentCount = 0;
foreach ($vacancies as $v) {
    $daysRemaining = date_helper::days_between(time(), $v->closedate);
    if ($daysRemaining <= 7 && $daysRemaining >= 0) {
        $urgentCount++;
    }
}

// Check if any filters are active (excluding the default company filter).
$hasFilters = !empty($filtercode) || !empty($filtercontract) || !empty($filterdepartment) ||
              !empty($filterlocation) || !empty($filtermodality) || !empty($filtersearch) ||
              ($filtercompanyid && $filtercompanyid != $usercompanyid) || $showallcompanies;

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data.
$data = $renderer->prepare_vacancies_page_data_v2(
    $vacancies,
    $totalVacancies,
    $urgentCount,
    $filters,
    $filterOptions,
    $page,
    $perpage,
    $convocatoria,
    $canapply,
    $canviewall,
    $hasFilters,
    $isiomad,
    $contracttypes
);

// Check for AJAX request.
if ($isajax) {
    // Return only the results partial for AJAX requests.
    header('Content-Type: text/html; charset=utf-8');
    echo $renderer->render_vacancies_results($data);
    exit;
}

// Output the full page.
echo $OUTPUT->header();
echo $renderer->render_vacancies_page($data);

// Initialize filter auto-submit for all users.
$PAGE->requires->js_call_amd('local_jobboard/public_filters', 'init', [[
    'formSelector' => '.jb-filter-form',
    'resultsSelector' => '[data-region="filter-results"]',
]]);

echo $OUTPUT->footer();
