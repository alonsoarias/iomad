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
 * Public view for local_jobboard - Convocatorias focused.
 *
 * Uses renderer + Mustache template for clean separation of concerns.
 *
 * This view has two modes:
 * 1. Without convocatoriaid: Shows convocatorias as cards
 * 2. With convocatoriaid: Shows vacancies for that convocatoria with filters
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
    // Show friendly error page instead of exception.
    $PAGE->set_pagelayout('standard');
    $PAGE->activityheader->disable();
    $PAGE->set_title(get_string('error:publicpagedisabled_title', 'local_jobboard'));
    $PAGE->set_heading(get_string('error:publicpagedisabled_title', 'local_jobboard'));
    $renderer = $PAGE->get_renderer('local_jobboard');
    echo $OUTPUT->header();
    echo $renderer->render_public_disabled_page();
    echo $OUTPUT->footer();
    exit;
}

// Get parameters.
$convocatoriaid = optional_param('convocatoriaid', 0, PARAM_INT);
$vacancyid = optional_param('id', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);

// Redirect to public_vacancy view if id parameter is provided.
if ($vacancyid > 0) {
    redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'public_vacancy', 'id' => $vacancyid]));
}

// Filter parameters for vacancies.
$filtercode = optional_param('code', '', PARAM_TEXT);
$filtercontract = optional_param('contracttype', '', PARAM_ALPHANUMEXT);
$filterdepartment = optional_param('department', '', PARAM_TEXT);
$filterlocation = optional_param('location', '', PARAM_TEXT);
$filtermodality = optional_param('modality', '', PARAM_ALPHANUMEXT);
$filtersearch = optional_param('search', '', PARAM_TEXT);

// AJAX request flag.
$isajax = optional_param('ajax', 0, PARAM_INT);

// Check if user is logged in.
$isloggedin = isloggedin() && !isguestuser();

// User capabilities.
$canviewinternal = $isloggedin && has_capability('local/jobboard:viewinternalvacancies', $context);
$canapply = $isloggedin && has_capability('local/jobboard:apply', $context);

// Additional capabilities for role-based quick access.
$caps = [];
if ($isloggedin) {
    $caps = [
        'configure' => has_capability('local/jobboard:configure', $context),
        'createvacancy' => has_capability('local/jobboard:createvacancy', $context),
        'manageconvocatorias' => has_capability('local/jobboard:manageconvocatorias', $context),
        'reviewdocuments' => has_capability('local/jobboard:reviewdocuments', $context),
        'viewallapplications' => has_capability('local/jobboard:viewallapplications', $context),
        'viewreports' => has_capability('local/jobboard:viewreports', $context),
        'apply' => $canapply,
    ];
}

// Set page title from config or default.
$pagetitle = get_config('local_jobboard', 'public_page_title');
if (empty($pagetitle)) {
    $pagetitle = get_string('jobboard', 'local_jobboard');
}
$PAGE->set_pagelayout('standard');
$PAGE->activityheader->disable();
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

// Add navigation breadcrumb for the main public page.
$PAGE->navbar->add($pagetitle, new moodle_url('/local/jobboard/index.php', ['view' => 'public']));

// Get contract types for display.
$contracttypes = local_jobboard_get_contract_types();

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// ============================================================================
// MODE: SHOW VACANCIES FOR A SPECIFIC CONVOCATORIA (WITH FILTERS)
// ============================================================================
if ($convocatoriaid > 0) {
    // Get the convocatoria.
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);

    if (!$convocatoria) {
        throw new moodle_exception('error:convocatorianotfound', 'local_jobboard');
    }

    // Check if convocatoria is open.
    if ($convocatoria->status !== 'open' || $convocatoria->enddate < time()) {
        throw new moodle_exception('error:convocatoriaclosed', 'local_jobboard');
    }

    // Check if convocatoria is public or user has permission to view internal.
    if ($convocatoria->publicationtype !== 'public' && !$canviewinternal) {
        throw new moodle_exception('error:convocatorianotpublic', 'local_jobboard');
    }

    // Update page title and breadcrumbs.
    $PAGE->set_title($convocatoria->name . ' - ' . get_string('vacancies', 'local_jobboard'));
    $PAGE->set_heading($convocatoria->name);

    // Add breadcrumb for convocatoria vacancies.
    $PAGE->navbar->add(format_string($convocatoria->name));

    // Build vacancies query with filters.
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

    // Build filter options from all vacancies.
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

    // Prepare template data.
    $data = $renderer->prepare_public_vacancies_data(
        $convocatoria,
        $vacancies,
        $totalVacancies,
        $allVacanciesForStats,
        $filters,
        $filterOptions,
        $isloggedin,
        $canapply,
        $contracttypes,
        $page,
        $perpage
    );

    // Handle AJAX request - return only vacancy results.
    if ($isajax) {
        header('Content-Type: text/html; charset=utf-8');
        echo $renderer->render_public_vacancies_results($data);
        exit;
    }

    // Output page.
    echo $OUTPUT->header();
    echo $renderer->render_public_page($data);

    // Initialize filter auto-submit for all users.
    $PAGE->requires->js_call_amd('local_jobboard/public_filters', 'init', [[
        'formSelector' => '.jb-filter-form',
        'resultsSelector' => '[data-region="filter-results"]',
    ]]);

    echo $OUTPUT->footer();
    exit;
}

// ============================================================================
// DEFAULT MODE: SHOW CONVOCATORIAS LIST
// ============================================================================

// Get all open convocatorias with their vacancy counts.
// Filter by publicationtype: show only public convocatorias unless user can view internal.
$sql = "SELECT c.*,
               (SELECT COUNT(*)
                  FROM {local_jobboard_vacancy} v
                 WHERE v.convocatoriaid = c.id
                   AND v.status = 'published'" .
               ($canviewinternal ? "" : " AND v.publicationtype = 'public'") . "
               ) as vacancy_count,
               (SELECT SUM(v.positions)
                  FROM {local_jobboard_vacancy} v
                 WHERE v.convocatoriaid = c.id
                   AND v.status = 'published'" .
               ($canviewinternal ? "" : " AND v.publicationtype = 'public'") . "
               ) as total_positions
          FROM {local_jobboard_convocatoria} c
         WHERE c.status = 'open'
           AND (c.enddate IS NULL OR c.enddate >= :now)" .
           ($canviewinternal ? "" : " AND c.publicationtype = 'public'") . "
         ORDER BY c.enddate ASC, c.name ASC";

$convocatorias = $DB->get_records_sql($sql, ['now' => time()]);

// Filter out convocatorias with no visible vacancies.
$convocatorias = array_filter($convocatorias, function($c) {
    return $c->vacancy_count > 0;
});

// Get page description.
$description = get_config('local_jobboard', 'public_page_description');
if (empty($description)) {
    $description = get_string('publicpagedesc', 'local_jobboard');
}

// Prepare template data.
$data = $renderer->prepare_public_convocatorias_data(
    $convocatorias,
    $isloggedin,
    $caps,
    $pagetitle,
    $description
);

// Output page.
echo $OUTPUT->header();
echo $renderer->render_public_page($data);

// Initialize filter auto-submit for all users.
$PAGE->requires->js_call_amd('local_jobboard/public_filters', 'init', [[
    'formSelector' => '.jb-filter-form',
]]);

echo $OUTPUT->footer();
