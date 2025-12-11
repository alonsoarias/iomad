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
    throw new moodle_exception('error:publicpagedisabled', 'local_jobboard');
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
$filtercontract = optional_param('contracttype', '', PARAM_ALPHANUMEXT);
$filterlocation = optional_param('location', '', PARAM_TEXT);
$filtersearch = optional_param('search', '', PARAM_TEXT);

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
    $pagetitle = get_string('publicpagetitle', 'local_jobboard');
}
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);
$PAGE->set_pagelayout('standard');

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

    // Update page title.
    $PAGE->set_title($convocatoria->name . ' - ' . get_string('vacancies', 'local_jobboard'));
    $PAGE->set_heading($convocatoria->name);

    // Build vacancies query with filters.
    $vacancyParams = ['convid' => $convocatoriaid];
    $vacancyWhere = "v.convocatoriaid = :convid AND v.status = 'published'";

    if (!$canviewinternal) {
        $vacancyWhere .= " AND v.publicationtype = 'public'";
    }

    if (!empty($filtercontract)) {
        $vacancyWhere .= " AND v.contracttype = :contracttype";
        $vacancyParams['contracttype'] = $filtercontract;
    }

    if (!empty($filterlocation)) {
        $vacancyWhere .= " AND " . $DB->sql_like('v.location', ':location', false);
        $vacancyParams['location'] = '%' . $DB->sql_like_escape($filterlocation) . '%';
    }

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

    // Build filter options.
    $locationsList = [];
    $contractTypesList = [];
    foreach ($allVacanciesForStats as $v) {
        if (!empty($v->location) && !in_array($v->location, $locationsList)) {
            $locationsList[] = $v->location;
        }
        if (!empty($v->contracttype)) {
            $contractTypesList[$v->contracttype] = $contracttypes[$v->contracttype] ?? $v->contracttype;
        }
    }

    $filters = [
        'contracttype' => $filtercontract,
        'location' => $filterlocation,
        'search' => $filtersearch,
    ];

    $filterOptions = [
        'contracttypes' => $contractTypesList,
        'locations' => $locationsList,
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

    // Output page.
    echo $OUTPUT->header();
    echo $renderer->render_public_page($data);
    echo $OUTPUT->footer();
    exit;
}

// ============================================================================
// DEFAULT MODE: SHOW CONVOCATORIAS LIST
// ============================================================================

// Get all open convocatorias with their vacancy counts.
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
           AND (c.enddate IS NULL OR c.enddate >= :now)
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
echo $OUTPUT->footer();
