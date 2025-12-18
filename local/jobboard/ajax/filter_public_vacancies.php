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
 * AJAX endpoint for filtering public vacancies.
 *
 * Returns filtered vacancies HTML without page reload.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);
define('NO_MOODLE_COOKIES', false);

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');

use local_jobboard\helper\iomad_helper;

// Check if public page is enabled.
$enablepublic = get_config('local_jobboard', 'enable_public_page');
if (!$enablepublic) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => get_string('error:publicpagedisabled', 'local_jobboard')]);
    exit;
}

// Get parameters.
$convocatoriaid = required_param('convocatoriaid', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);

// Filter parameters.
$filtercode = optional_param('code', '', PARAM_TEXT);
$filtercontract = optional_param('contracttype', '', PARAM_ALPHANUMEXT);
$filterdepartment = optional_param('department', '', PARAM_TEXT);
$filtercompany = optional_param('companyid', 0, PARAM_INT);
$filtermodality = optional_param('modality', '', PARAM_TEXT);
$filtersearch = optional_param('search', '', PARAM_TEXT);

// Check if user is logged in.
$isloggedin = isloggedin() && !isguestuser();
$context = context_system::instance();

// User capabilities.
$canviewinternal = $isloggedin && has_capability('local/jobboard:viewinternalvacancies', $context);
$canapply = $isloggedin && has_capability('local/jobboard:apply', $context);

// Get the convocatoria.
$convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);

if (!$convocatoria) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => get_string('error:convocatorianotfound', 'local_jobboard')]);
    exit;
}

// Check if convocatoria is open.
if ($convocatoria->status !== 'open' || $convocatoria->enddate < time()) {
    header('HTTP/1.1 403 Forbidden');
    echo json_encode(['error' => get_string('error:convocatoriaclosed', 'local_jobboard')]);
    exit;
}

// Build vacancies query with filters.
$vacancyParams = ['convid' => $convocatoriaid];
$vacancyWhere = "v.convocatoriaid = :convid AND v.status = 'published'";

if (!$canviewinternal) {
    $vacancyWhere .= " AND v.publicationtype = 'public'";
}

// Filter by code.
if (!empty($filtercode)) {
    $vacancyWhere .= " AND " . $DB->sql_like('v.code', ':code', false);
    $vacancyParams['code'] = '%' . $DB->sql_like_escape($filtercode) . '%';
}

// Filter by contract type.
if (!empty($filtercontract)) {
    $vacancyWhere .= " AND v.contracttype = :contracttype";
    $vacancyParams['contracttype'] = $filtercontract;
}

// Filter by department (programa académico).
if (!empty($filterdepartment)) {
    $vacancyWhere .= " AND v.department = :department";
    $vacancyParams['department'] = $filterdepartment;
}

// Filter by company.
if (!empty($filtercompany)) {
    $vacancyWhere .= " AND v.companyid = :companyid";
    $vacancyParams['companyid'] = $filtercompany;
}

// Filter by modality.
if (!empty($filtermodality)) {
    $vacancyWhere .= " AND v.modality = :modality";
    $vacancyParams['modality'] = $filtermodality;
}

// General search.
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

// Get contract types for display.
$contracttypes = local_jobboard_get_contract_types();

// Prepare vacancies data for JSON response.
$vacsdata = [];
$daysRemaining = max(0, (int) floor(($convocatoria->enddate - time()) / 86400));
$isUrgent = $daysRemaining <= 7;

foreach ($vacancies as $vacancy) {
    // Check if user has applied.
    $hasApplied = false;
    if ($isloggedin) {
        $hasApplied = $DB->record_exists('local_jobboard_application', [
            'vacancyid' => $vacancy->id,
            'userid' => $USER->id,
        ]);
    }

    // Location from company.
    $location = '';
    if (!empty($vacancy->companyid)) {
        $location = iomad_helper::get_company_name((int) $vacancy->companyid);
    }
    if (empty($location) && !empty($vacancy->location)) {
        $location = $vacancy->location;
    }

    // Publication type.
    $publicationtypecolor = $vacancy->publicationtype === 'public' ? 'success' : 'secondary';
    $publicationtypelabel = $vacancy->publicationtype === 'public'
        ? get_string('public', 'local_jobboard')
        : get_string('internal', 'local_jobboard');

    $vacsdata[] = [
        'id' => $vacancy->id,
        'code' => format_string($vacancy->code),
        'title' => format_string($vacancy->title),
        'location' => $location,
        'department' => format_string($vacancy->department ?? ''),
        'modality' => format_string($vacancy->modality ?? ''),
        'contracttypelabel' => $contracttypes[$vacancy->contracttype] ?? '',
        'positions' => (int) $vacancy->positions,
        'publicationtypecolor' => $publicationtypecolor,
        'publicationtypelabel' => $publicationtypelabel,
        'closedateformatted' => userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig')),
        'isurgent' => $isUrgent,
        'urgenttext' => $isUrgent ? get_string('closesindays', 'local_jobboard', $daysRemaining) : '',
        'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public_vacancy', 'id' => $vacancy->id]))->out(false),
        'applyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]))->out(false),
        'loginapplyurl' => (new moodle_url('/login/index.php', [
            'wantsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]))->out(false),
        ]))->out(false),
        'hasapplied' => $hasApplied,
        'canapply' => $canapply && !$hasApplied,
        'isloggedin' => $isloggedin,
    ];
}

// Prepare pagination data.
$totalPages = ceil($totalVacancies / $perpage);
$pagination = null;
if ($totalVacancies > $perpage) {
    $pagination = [
        'currentpage' => $page + 1,
        'totalpages' => $totalPages,
        'totalitems' => $totalVacancies,
        'perpage' => $perpage,
    ];
}

// Return JSON response.
header('Content-Type: application/json; charset=utf-8');
echo json_encode([
    'success' => true,
    'data' => [
        'vacancies' => $vacsdata,
        'hasvacancies' => !empty($vacsdata),
        'totalcount' => $totalVacancies,
        'resultstext' => get_string('vacanciesfound', 'local_jobboard', $totalVacancies),
        'pagination' => $pagination,
        'isloggedin' => $isloggedin,
        'canapply' => $canapply,
    ],
    'strings' => [
        'view' => get_string('view'),
        'apply' => get_string('apply', 'local_jobboard'),
        'applied' => get_string('applied', 'local_jobboard'),
        'login' => get_string('login'),
        'positions' => get_string('positions', 'local_jobboard'),
        'novacancies' => get_string('novacancies', 'local_jobboard'),
        'novacancies_desc' => get_string('novacancies_desc', 'local_jobboard'),
    ],
]);
