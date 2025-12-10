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
 * Public vacancies view for local_jobboard.
 *
 * Redesigned to follow dashboard design patterns using ui_helper.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

// Check if public page is enabled.
$enablepublic = get_config('local_jobboard', 'enable_public_page');
if (!$enablepublic) {
    throw new moodle_exception('error:publicpagedisabled', 'local_jobboard');
}

// Check if IOMAD is installed.
$isiomad = local_jobboard_is_iomad_installed();

// Optional parameters.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$contracttype = optional_param('contracttype', '', PARAM_ALPHA);
$companyid = optional_param('companyid', 0, PARAM_INT);
$departmentid = optional_param('departmentid', 0, PARAM_INT);
$publicationtype = optional_param('type', '', PARAM_ALPHA);
$vacancyid = optional_param('id', 0, PARAM_INT);
$convocatoriaid = optional_param('convocatoria', 0, PARAM_INT);

// Set page title from config or default.
$pagetitle = get_config('local_jobboard', 'public_page_title');
if (empty($pagetitle)) {
    $pagetitle = get_string('publicpagetitle', 'local_jobboard');
}
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);
$PAGE->set_pagelayout('standard');
$PAGE->requires->css('/local/jobboard/styles.css');

// Check if user is logged in.
$isloggedin = isloggedin() && !isguestuser();

// Determine which vacancies to show based on authentication.
$canviewinternal = $isloggedin && has_capability('local/jobboard:viewinternalvacancies', $context);
$canviewpublic = has_capability('local/jobboard:viewpublicvacancies', $context);

// If a specific vacancy ID is provided, show vacancy detail.
if ($vacancyid) {
    $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid, 'status' => 'published']);

    if (!$vacancy) {
        throw new moodle_exception('error:vacancynotfound', 'local_jobboard');
    }

    // Check if vacancy is public or user has access.
    if ($vacancy->publicationtype !== 'public' && !$canviewinternal) {
        throw new moodle_exception('error:noaccess', 'local_jobboard');
    }

    // Render vacancy detail.
    local_jobboard_render_public_vacancy_detail($vacancy, $context, $isloggedin);
    return;
}

// Build query conditions (dates from convocatoria).
$conditions = ["v.status = :status", "(c.enddate IS NULL OR c.enddate >= :now)"];
$params = ['status' => 'published', 'now' => time()];

// Filter by publication type.
if (!$canviewinternal) {
    $conditions[] = "v.publicationtype = :pubtype";
    $params['pubtype'] = 'public';
} elseif (!empty($publicationtype) && in_array($publicationtype, ['public', 'internal'])) {
    $conditions[] = "v.publicationtype = :pubtype";
    $params['pubtype'] = $publicationtype;
}

// Filter by convocatoria.
if ($convocatoriaid) {
    $conditions[] = "v.convocatoriaid = :convocatoriaid";
    $params['convocatoriaid'] = $convocatoriaid;
}

// Filter by company (multi-tenant).
if ($isloggedin && function_exists('local_jobboard_get_user_companyid')) {
    $usercompanyid = local_jobboard_get_user_companyid($USER->id);
    if ($usercompanyid && !has_capability('local/jobboard:viewallvacancies', $context)) {
        $conditions[] = "(v.companyid IS NULL OR v.companyid = :usercompanyid)";
        $params['usercompanyid'] = $usercompanyid;
    }
}

// Search filter.
if (!empty($search)) {
    $searchterm = '%' . $DB->sql_like_escape($search) . '%';
    $conditions[] = "(" . $DB->sql_like('v.title', ':search1', false) .
                   " OR " . $DB->sql_like('v.code', ':search2', false) .
                   " OR " . $DB->sql_like('v.description', ':search3', false) . ")";
    $params['search1'] = $searchterm;
    $params['search2'] = $searchterm;
    $params['search3'] = $searchterm;
}

// Contract type filter.
if (!empty($contracttype)) {
    $conditions[] = "v.contracttype = :contracttype";
    $params['contracttype'] = $contracttype;
}

// Company filter (IOMAD).
if ($isiomad && !empty($companyid)) {
    $conditions[] = "v.companyid = :filtercompanyid";
    $params['filtercompanyid'] = $companyid;
}

// Department filter (IOMAD).
if ($isiomad && !empty($departmentid)) {
    // Check if departmentid column exists before using it.
    $dbman = $DB->get_manager();
    $table = new xmldb_table('local_jobboard_vacancy');
    $field = new xmldb_field('departmentid');
    if ($dbman->field_exists($table, $field)) {
        $conditions[] = "v.departmentid = :filterdepartmentid";
        $params['filterdepartmentid'] = $departmentid;
    }
}

// Build WHERE clause.
$where = 'WHERE ' . implode(' AND ', $conditions);

// Get total count (join with convocatoria for date filtering).
$countsql = "SELECT COUNT(*)
               FROM {local_jobboard_vacancy} v
               LEFT JOIN {local_jobboard_convocatoria} c ON c.id = v.convocatoriaid
              $where";
$total = $DB->count_records_sql($countsql, $params);

// Get records with convocatoria info.
$sql = "SELECT v.*, c.name as convocatoria_name, c.code as convocatoria_code,
               COALESCE(c.enddate, 0) as closedate
          FROM {local_jobboard_vacancy} v
          LEFT JOIN {local_jobboard_convocatoria} c ON c.id = v.convocatoriaid
         $where
         ORDER BY c.enddate ASC, v.timecreated DESC";
$vacancies = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

// Get available filter options.
$contracttypes = local_jobboard_get_contract_types();

// Get companies for filter (IOMAD).
$companies = [];
if ($isiomad) {
    $companies = local_jobboard_get_companies();
}

// Get departments for selected company (IOMAD).
$departments = [];
if ($isiomad && !empty($companyid)) {
    $departments = local_jobboard_get_departments($companyid);
}

// Get open convocatorias for filter.
$convocatorias = $DB->get_records_sql(
    "SELECT DISTINCT c.id, c.code, c.name
       FROM {local_jobboard_convocatoria} c
       JOIN {local_jobboard_vacancy} v ON v.convocatoriaid = c.id
      WHERE c.status = 'open' AND v.status = 'published'
      ORDER BY c.code"
);

// Count urgent vacancies.
$urgentCount = 0;
foreach ($vacancies as $v) {
    if (($v->closedate - time()) <= 7 * 86400) {
        $urgentCount++;
    }
}

// Start output.
echo $OUTPUT->header();

echo html_writer::start_div('local-jobboard-dashboard');

// ============================================================================
// WELCOME HEADER (Following dashboard pattern)
// ============================================================================
$description = get_config('local_jobboard', 'public_page_description');
if (empty($description)) {
    $description = get_string('publicpagedesc', 'local_jobboard');
}

echo html_writer::start_div('jb-welcome-section bg-gradient-primary text-white rounded-lg p-4 mb-4');
echo html_writer::start_div('d-flex justify-content-between align-items-center');
echo html_writer::start_div();
echo html_writer::tag('h2', format_string($pagetitle), ['class' => 'mb-1 font-weight-bold']);
echo html_writer::tag('p', format_text($description, FORMAT_HTML), ['class' => 'mb-0 opacity-75']);
echo html_writer::end_div();
echo html_writer::tag('i', '', ['class' => 'fa fa-briefcase fa-3x opacity-25']);
echo html_writer::end_div();
echo html_writer::end_div();

// ============================================================================
// STATISTICS ROW (Using ui_helper::stat_card)
// ============================================================================
echo html_writer::start_div('row mb-4');

echo ui_helper::stat_card(
    $total,
    get_string('openvacancies', 'local_jobboard'),
    'primary', 'briefcase'
);

echo ui_helper::stat_card(
    $urgentCount,
    get_string('closingsoondays', 'local_jobboard', 7),
    'warning', 'clock'
);

echo ui_helper::stat_card(
    count($convocatorias),
    get_string('activeconvocatorias', 'local_jobboard'),
    'info', 'folder-open'
);

if ($isloggedin) {
    $myApps = $DB->count_records('local_jobboard_application', ['userid' => $USER->id]);
    echo ui_helper::stat_card(
        $myApps,
        get_string('myapplications', 'local_jobboard'),
        'success', 'file-alt',
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications'])
    );
}

echo html_writer::end_div();

// ============================================================================
// ACTIVE CONVOCATORIAS SECTION (with links to detail)
// ============================================================================
if (!empty($convocatorias)) {
    // Get full convocatoria data for display.
    $convIds = array_keys($convocatorias);
    list($insql, $inparams) = $DB->get_in_or_equal($convIds, SQL_PARAMS_NAMED);
    $fullConvocatorias = $DB->get_records_sql(
        "SELECT c.*,
                (SELECT COUNT(*) FROM {local_jobboard_vacancy} v
                 WHERE v.convocatoriaid = c.id AND v.status = 'published') as vacancy_count
           FROM {local_jobboard_convocatoria} c
          WHERE c.id $insql
          ORDER BY c.enddate ASC",
        $inparams
    );

    echo html_writer::start_div('card shadow-sm mb-4 border-info');
    echo html_writer::start_div('card-header bg-info text-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5', '<i class="fa fa-folder-open mr-2"></i>' . get_string('activeconvocatorias', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::tag('span', count($fullConvocatorias), ['class' => 'badge badge-light']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    echo html_writer::start_div('row');
    foreach ($fullConvocatorias as $conv) {
        $convDaysRemaining = max(0, (int) floor(($conv->enddate - time()) / 86400));
        $isConvUrgent = $convDaysRemaining <= 7;

        echo html_writer::start_div('col-lg-4 col-md-6 mb-3');

        $convCardClass = $isConvUrgent ? 'card h-100 border-warning' : 'card h-100';
        echo html_writer::start_div($convCardClass);

        echo html_writer::start_div('card-body py-3');

        // Code and status.
        echo html_writer::start_div('d-flex justify-content-between align-items-start mb-2');
        echo html_writer::tag('code', s($conv->code), ['class' => 'small']);
        if ($isConvUrgent) {
            echo html_writer::tag('span', '<i class="fa fa-clock mr-1"></i>' . $convDaysRemaining . 'd', ['class' => 'badge badge-warning']);
        }
        echo html_writer::end_div();

        // Name as link.
        echo html_writer::tag('h6',
            html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $conv->id]),
                format_string($conv->name),
                ['class' => 'text-dark']
            ),
            ['class' => 'card-title mb-2']
        );

        // Stats.
        echo html_writer::start_div('small text-muted');
        echo html_writer::tag('span', '<i class="fa fa-briefcase mr-1"></i>' . $conv->vacancy_count . ' ' . get_string('vacancies', 'local_jobboard'), ['class' => 'mr-3']);
        echo html_writer::tag('span', '<i class="fa fa-calendar-times mr-1"></i>' . userdate($conv->enddate, get_string('strftimedate', 'langconfig')));
        echo html_writer::end_div();

        echo html_writer::end_div(); // card-body

        // Card footer with action.
        echo html_writer::start_div('card-footer bg-transparent py-2');
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $conv->id]),
            '<i class="fa fa-eye mr-1"></i>' . get_string('viewdetails', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-info btn-block']
        );
        echo html_writer::end_div();

        echo html_writer::end_div(); // card
        echo html_writer::end_div(); // col
    }
    echo html_writer::end_div(); // row

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card
}

// ============================================================================
// FILTER SECTION (Direct HTML for maximum compatibility)
// ============================================================================
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-body py-3');

$formurl = new moodle_url('/local/jobboard/index.php');
echo html_writer::start_tag('form', ['method' => 'get', 'action' => $formurl->out_omit_querystring(), 'class' => 'jb-filter-form mb-0']);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'view', 'value' => 'public']);

echo html_writer::start_div('row align-items-end');

// Search field.
echo html_writer::start_div('col-md-3 col-sm-6 mb-2 mb-md-0');
echo html_writer::tag('label', get_string('search'), ['for' => 'filter-search', 'class' => 'form-label small text-muted mb-1']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'id' => 'filter-search',
    'value' => $search,
    'placeholder' => get_string('searchplaceholder', 'local_jobboard'),
    'class' => 'form-control form-control-sm',
]);
echo html_writer::end_div();

// Company select (IOMAD - Ubicación).
if ($isiomad && !empty($companies)) {
    $companyOptions = [0 => get_string('alllocations', 'local_jobboard')] + $companies;
    echo html_writer::start_div('col-md-2 col-sm-6 mb-2 mb-md-0');
    echo html_writer::tag('label', get_string('location', 'local_jobboard'), ['for' => 'filter-companyid', 'class' => 'form-label small text-muted mb-1']);
    echo '<select name="companyid" id="filter-companyid" class="form-control form-control-sm jb-filter-select">';
    foreach ($companyOptions as $val => $label) {
        $selected = ($companyid == $val) ? ' selected="selected"' : '';
        $optionText = !empty($label) ? s($label) : '';
        if (empty($optionText)) {
            $optionText = get_string('alllocations', 'local_jobboard');
        }
        echo '<option value="' . s($val) . '"' . $selected . '>' . $optionText . '</option>';
    }
    echo '</select>';
    echo html_writer::end_div();

    // Department select (IOMAD - Modalidad).
    $departmentOptions = [0 => get_string('allmodalities', 'local_jobboard')] + $departments;
    echo html_writer::start_div('col-md-2 col-sm-6 mb-2 mb-md-0');
    echo html_writer::tag('label', get_string('modality', 'local_jobboard'), ['for' => 'filter-departmentid', 'class' => 'form-label small text-muted mb-1']);
    echo '<select name="departmentid" id="filter-departmentid" class="form-control form-control-sm jb-filter-select">';
    foreach ($departmentOptions as $val => $label) {
        $selected = ($departmentid == $val) ? ' selected="selected"' : '';
        $optionText = !empty($label) ? s($label) : '';
        if (empty($optionText)) {
            $optionText = get_string('allmodalities', 'local_jobboard');
        }
        echo '<option value="' . s($val) . '"' . $selected . '>' . $optionText . '</option>';
    }
    echo '</select>';
    echo html_writer::end_div();
}

// Contract type select.
$contractOptions = ['' => get_string('allcontracttypes', 'local_jobboard')] + $contracttypes;
echo html_writer::start_div('col-md-2 col-sm-6 mb-2 mb-md-0');
echo html_writer::tag('label', get_string('contracttype', 'local_jobboard'), ['for' => 'filter-contract', 'class' => 'form-label small text-muted mb-1']);
echo '<select name="contracttype" id="filter-contract" class="form-control form-control-sm jb-filter-select">';
foreach ($contractOptions as $val => $label) {
    $selected = ($contracttype === (string)$val) ? ' selected="selected"' : '';
    $optionText = !empty($label) ? s($label) : s($val);
    if (empty($optionText)) {
        $optionText = get_string('allcontracttypes', 'local_jobboard');
    }
    echo '<option value="' . s($val) . '"' . $selected . '>' . $optionText . '</option>';
}
echo '</select>';
echo html_writer::end_div();

// Convocatoria select.
$convocatoriaOptions = [0 => get_string('allconvocatorias', 'local_jobboard')];
foreach ($convocatorias as $conv) {
    $convocatoriaOptions[$conv->id] = format_string($conv->code . ' - ' . $conv->name);
}
echo html_writer::start_div('col-md-2 col-sm-6 mb-2 mb-md-0');
echo html_writer::tag('label', get_string('convocatoria', 'local_jobboard'), ['for' => 'filter-convocatoria', 'class' => 'form-label small text-muted mb-1']);
echo '<select name="convocatoria" id="filter-convocatoria" class="form-control form-control-sm jb-filter-select">';
foreach ($convocatoriaOptions as $val => $label) {
    $selected = ($convocatoriaid == $val) ? ' selected="selected"' : '';
    $optionText = !empty($label) ? s($label) : s($val);
    if (empty($optionText)) {
        $optionText = get_string('allconvocatorias', 'local_jobboard');
    }
    echo '<option value="' . s($val) . '"' . $selected . '>' . $optionText . '</option>';
}
echo '</select>';
echo html_writer::end_div();

// Search button.
echo html_writer::start_div('col-md-auto mb-2 mb-md-0');
echo html_writer::tag('button',
    '<i class="fa fa-search mr-1"></i>' . get_string('search'),
    ['type' => 'submit', 'class' => 'btn btn-primary btn-sm']
);
echo html_writer::end_div();

echo html_writer::end_div(); // row
echo html_writer::end_tag('form');
echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// Add JavaScript for AJAX department loading (IOMAD).
if ($isiomad && !empty($companies)) {
    $allModalitiesLabel = get_string('allmodalities', 'local_jobboard');
    $PAGE->requires->js_call_amd('local_jobboard/public_filters', 'init', [[
        'companySelector' => '#filter-companyid',
        'departmentSelector' => '#filter-departmentid',
        'preselect' => $departmentid,
        'allLabel' => $allModalitiesLabel,
    ]]);
}

// Clear filters link.
if (!empty($search) || !empty($contracttype) || $companyid || $departmentid || $convocatoriaid) {
    echo html_writer::start_div('mb-3');
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        '<i class="fa fa-times mr-1"></i>' . get_string('clearfilters', 'local_jobboard'),
        ['class' => 'btn btn-sm btn-outline-secondary']
    );
    echo html_writer::end_div();
}

// ============================================================================
// RESULTS SECTION
// ============================================================================
echo html_writer::start_div('mb-3');
echo html_writer::tag('span',
    get_string('vacanciesfound', 'local_jobboard', $total),
    ['class' => 'text-muted']
);
echo html_writer::end_div();

// ============================================================================
// VACANCY CARDS
// ============================================================================
if (empty($vacancies)) {
    echo ui_helper::empty_state(
        get_string('novacanciesfound', 'local_jobboard'),
        'briefcase',
        !$isloggedin ? [
            'url' => new moodle_url('/login/index.php'),
            'label' => get_string('login'),
            'class' => 'btn btn-primary',
        ] : null
    );
} else {
    echo html_writer::start_div('row');

    foreach ($vacancies as $vacancy) {
        $daysremaining = max(0, (int) floor(($vacancy->closedate - time()) / 86400));
        $isurgent = $daysremaining <= 7;

        echo html_writer::start_div('col-lg-4 col-md-6 mb-4');

        $cardClass = $isurgent ? 'card h-100 shadow-sm border-0 border-left-warning' : 'card h-100 shadow-sm border-0';
        echo html_writer::start_div($cardClass);

        // Card Header.
        echo html_writer::start_div('card-header bg-transparent d-flex justify-content-between align-items-center py-2');
        echo html_writer::tag('span', s($vacancy->code), ['class' => 'badge badge-secondary']);
        $typeBadgeClass = $vacancy->publicationtype === 'public' ? 'badge badge-success' : 'badge badge-info';
        echo html_writer::tag('span',
            get_string('publicationtype:' . $vacancy->publicationtype, 'local_jobboard'),
            ['class' => $typeBadgeClass]
        );
        echo html_writer::end_div();

        // Card Body.
        echo html_writer::start_div('card-body');

        echo html_writer::tag('h5', format_string($vacancy->title), ['class' => 'card-title mb-3']);

        // Meta info.
        echo html_writer::start_div('small text-muted mb-3');
        if (!empty($vacancy->location)) {
            echo html_writer::tag('div',
                '<i class="fa fa-map-marker-alt mr-1"></i> ' . s($vacancy->location),
                ['class' => 'mb-1']
            );
        }
        if (!empty($vacancy->modality)) {
            echo html_writer::tag('div',
                '<i class="fa fa-laptop-house mr-1"></i> ' . s($vacancy->modality),
                ['class' => 'mb-1']
            );
        }
        if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
            echo html_writer::tag('div',
                '<i class="fa fa-file-contract mr-1"></i> ' . $contracttypes[$vacancy->contracttype],
                ['class' => 'mb-1']
            );
        }
        echo html_writer::tag('div',
            '<i class="fa fa-users mr-1"></i> ' . $vacancy->positions . ' ' . get_string('positions', 'local_jobboard'),
            ['class' => 'mb-1']
        );
        echo html_writer::end_div();

        // Description excerpt.
        if (!empty($vacancy->description)) {
            $desc = shorten_text(strip_tags($vacancy->description), 100);
            echo html_writer::tag('p', $desc, ['class' => 'card-text text-muted small']);
        }

        echo html_writer::end_div(); // card-body

        // Card Footer.
        echo html_writer::start_div('card-footer bg-transparent');

        // Closing date.
        $closingClass = $isurgent ? 'text-danger small' : 'text-muted small';
        echo html_writer::tag('div',
            '<i class="fa fa-calendar-alt mr-1"></i> ' . get_string('closesin', 'local_jobboard', $daysremaining),
            ['class' => $closingClass . ' mb-2']
        );

        // Action buttons.
        echo html_writer::start_div('d-flex flex-wrap gap-2');

        // View details.
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]),
            '<i class="fa fa-eye mr-1"></i>' . get_string('viewdetails', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-primary']
        );

        // Apply button.
        if ($isloggedin && has_capability('local/jobboard:apply', $context)) {
            $hasApplied = $DB->record_exists('local_jobboard_application', [
                'vacancyid' => $vacancy->id,
                'userid' => $USER->id,
            ]);

            if ($hasApplied) {
                echo html_writer::tag('span',
                    '<i class="fa fa-check mr-1"></i>' . get_string('applied', 'local_jobboard'),
                    ['class' => 'badge badge-success align-self-center']
                );
            } else {
                echo html_writer::link(
                    new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
                    '<i class="fa fa-paper-plane mr-1"></i>' . get_string('apply', 'local_jobboard'),
                    ['class' => 'btn btn-sm btn-primary']
                );
            }
        } elseif (!$isloggedin) {
            echo html_writer::link(
                new moodle_url('/login/index.php', [
                    'wantsurl' => (new moodle_url('/local/jobboard/index.php', [
                        'view' => 'apply',
                        'vacancyid' => $vacancy->id,
                    ]))->out(false),
                ]),
                '<i class="fa fa-sign-in-alt mr-1"></i>' . get_string('loginandapply', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-secondary', 'target' => '_blank']
            );
        }

        echo html_writer::end_div(); // d-flex
        echo html_writer::end_div(); // card-footer

        echo html_writer::end_div(); // card
        echo html_writer::end_div(); // col
    }

    echo html_writer::end_div(); // row

    // Pagination.
    if ($total > $perpage) {
        $baseurl = new moodle_url('/local/jobboard/index.php', [
            'view' => 'public',
            'search' => $search,
            'contracttype' => $contracttype,
            'companyid' => $companyid,
            'departmentid' => $departmentid,
            'convocatoria' => $convocatoriaid,
            'type' => $publicationtype,
        ]);
        echo ui_helper::pagination_bar($total, $page, $perpage, $baseurl);
    }
}

// ============================================================================
// CTA FOR NON-LOGGED IN USERS
// ============================================================================
if (!$isloggedin) {
    echo html_writer::start_div('card bg-light border-0 mt-4');
    echo html_writer::start_div('card-body text-center py-5');
    echo html_writer::tag('i', '', ['class' => 'fa fa-user-plus fa-3x text-primary mb-3']);
    echo html_writer::tag('h4', get_string('wanttoapply', 'local_jobboard'), ['class' => 'mb-2']);
    echo html_writer::tag('p', get_string('createaccounttoapply', 'local_jobboard'), ['class' => 'text-muted mb-4']);

    echo html_writer::start_div('d-flex justify-content-center gap-3');
    echo html_writer::link(
        new moodle_url('/local/jobboard/signup.php'),
        '<i class="fa fa-user-plus mr-2"></i>' . get_string('createaccount'),
        ['class' => 'btn btn-primary btn-lg', 'target' => '_blank']
    );
    echo html_writer::link(
        new moodle_url('/login/index.php'),
        '<i class="fa fa-sign-in-alt mr-2"></i>' . get_string('login'),
        ['class' => 'btn btn-outline-secondary btn-lg', 'target' => '_blank']
    );
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // local-jobboard-dashboard

echo $OUTPUT->footer();

/**
 * Render public vacancy detail page.
 *
 * @param object $vacancy The vacancy record.
 * @param context $context The context.
 * @param bool $isloggedin Whether the user is logged in.
 */
function local_jobboard_render_public_vacancy_detail($vacancy, $context, $isloggedin) {
    global $OUTPUT, $PAGE, $DB, $USER;

    $contracttypes = local_jobboard_get_contract_types();

    // Get convocatoria info and populate dates.
    $convocatoria = null;
    if (!empty($vacancy->convocatoriaid)) {
        $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $vacancy->convocatoriaid]);
    }

    // Populate dates from convocatoria (opendate/closedate no longer exist on vacancy).
    $vacancy->opendate = $convocatoria->startdate ?? 0;
    $vacancy->closedate = $convocatoria->enddate ?? 0;

    $PAGE->set_title($vacancy->title);
    $PAGE->set_heading($vacancy->title);
    $PAGE->requires->css('/local/jobboard/styles.css');

    echo $OUTPUT->header();

    echo html_writer::start_div('local-jobboard-dashboard');

    // Back button.
    echo html_writer::start_div('mb-4');
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtovacancies', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary']
    );
    echo html_writer::end_div();

    // Two column layout.
    echo html_writer::start_div('row');

    // LEFT COLUMN - Main content (8 cols).
    echo html_writer::start_div('col-lg-8');

    // Vacancy header card.
    echo html_writer::start_div('card shadow-sm border-0 mb-4');
    echo html_writer::start_div('card-body');

    // Header badges.
    echo html_writer::start_div('mb-3');
    echo html_writer::tag('span', s($vacancy->code), ['class' => 'badge badge-secondary mr-2']);
    $typeBadgeClass = $vacancy->publicationtype === 'public' ? 'badge badge-success' : 'badge badge-info';
    echo html_writer::tag('span',
        get_string('publicationtype:' . $vacancy->publicationtype, 'local_jobboard'),
        ['class' => $typeBadgeClass]
    );
    echo html_writer::end_div();

    // Title.
    echo html_writer::tag('h2', format_string($vacancy->title), ['class' => 'mb-3']);

    // Convocatoria info.
    if ($convocatoria) {
        echo html_writer::tag('div',
            '<i class="fa fa-folder-open mr-2"></i>' .
            get_string('convocatoria', 'local_jobboard') . ': ' .
            html_writer::tag('strong', format_string($convocatoria->name)),
            ['class' => 'text-muted mb-3']
        );
    }

    // Quick meta badges - Using IOMAD company/department names.
    echo html_writer::start_div('d-flex flex-wrap gap-3 mb-3');

    // Location badge (Company name from IOMAD).
    if (!empty($vacancy->companyid)) {
        $companyName = local_jobboard_get_company_name($vacancy->companyid);
        if (!empty($companyName)) {
            echo html_writer::tag('span',
                '<i class="fa fa-map-marker-alt mr-1"></i> ' . s($companyName),
                ['class' => 'badge badge-light text-dark p-2']
            );
        }
    } elseif (!empty($vacancy->location)) {
        // Fallback to location field if no IOMAD company.
        echo html_writer::tag('span',
            '<i class="fa fa-map-marker-alt mr-1"></i> ' . s($vacancy->location),
            ['class' => 'badge badge-light text-dark p-2']
        );
    }

    // Modality badge (Department name from IOMAD).
    if (!empty($vacancy->departmentid)) {
        $departmentName = local_jobboard_get_department_name($vacancy->departmentid);
        if (!empty($departmentName)) {
            echo html_writer::tag('span',
                '<i class="fa fa-laptop-house mr-1"></i> ' . s($departmentName),
                ['class' => 'badge badge-light text-dark p-2']
            );
        }
    } elseif (!empty($vacancy->modality)) {
        // Fallback to modality field if no IOMAD department.
        $modalities = local_jobboard_get_modalities();
        $modalityLabel = $modalities[$vacancy->modality] ?? $vacancy->modality;
        echo html_writer::tag('span',
            '<i class="fa fa-laptop-house mr-1"></i> ' . s($modalityLabel),
            ['class' => 'badge badge-light text-dark p-2']
        );
    }

    if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
        echo html_writer::tag('span',
            '<i class="fa fa-briefcase mr-1"></i> ' . $contracttypes[$vacancy->contracttype],
            ['class' => 'badge badge-light text-dark p-2']
        );
    }
    echo html_writer::tag('span',
        '<i class="fa fa-users mr-1"></i> ' . $vacancy->positions . ' ' . get_string('positions', 'local_jobboard'),
        ['class' => 'badge badge-light text-dark p-2']
    );
    echo html_writer::end_div();

    // Closing date warning.
    $daysremaining = max(0, (int) floor(($vacancy->closedate - time()) / 86400));
    $isurgent = $daysremaining <= 7;

    if ($isurgent) {
        echo html_writer::tag('div',
            '<i class="fa fa-exclamation-triangle mr-2"></i>' .
            get_string('closesin', 'local_jobboard', $daysremaining),
            ['class' => 'alert alert-warning py-2']
        );
    }

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card

    // Description section.
    if (!empty($vacancy->description)) {
        echo html_writer::start_div('card shadow-sm border-0 mb-4');
        echo html_writer::start_div('card-header bg-transparent');
        echo html_writer::tag('h5',
            '<i class="fa fa-file-alt mr-2"></i>' . get_string('vacancydescription', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');
        echo format_text($vacancy->description, FORMAT_HTML);
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    // Requirements section.
    if (!empty($vacancy->requirements)) {
        echo html_writer::start_div('card shadow-sm border-0 mb-4');
        echo html_writer::start_div('card-header bg-transparent');
        echo html_writer::tag('h5',
            '<i class="fa fa-check-circle mr-2"></i>' . get_string('requirements', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');
        echo format_text($vacancy->requirements, FORMAT_HTML);
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    // Desirable section.
    if (!empty($vacancy->desirable)) {
        echo html_writer::start_div('card shadow-sm border-0 mb-4');
        echo html_writer::start_div('card-header bg-transparent');
        echo html_writer::tag('h5',
            '<i class="fa fa-star mr-2"></i>' . get_string('desirable', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');
        echo format_text($vacancy->desirable, FORMAT_HTML);
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // col-lg-8

    // RIGHT COLUMN - Sidebar (4 cols).
    echo html_writer::start_div('col-lg-4');

    // Apply CTA card.
    echo html_writer::start_div('card shadow-sm border-0 border-left-primary mb-4');
    echo html_writer::start_div('card-header bg-transparent');
    echo html_writer::tag('h5',
        '<i class="fa fa-paper-plane mr-2"></i>' . get_string('readytoapply', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if ($isloggedin && has_capability('local/jobboard:apply', $context)) {
        $hasapplied = $DB->record_exists('local_jobboard_application', [
            'vacancyid' => $vacancy->id,
            'userid' => $USER->id,
        ]);

        if ($hasapplied) {
            echo html_writer::tag('div',
                '<i class="fa fa-check-circle fa-3x text-success mb-3"></i>' .
                html_writer::tag('p', get_string('alreadyapplied', 'local_jobboard'), ['class' => 'mb-0']),
                ['class' => 'text-center py-3']
            );
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
                get_string('viewmyapplications', 'local_jobboard'),
                ['class' => 'btn btn-outline-primary btn-block']
            );
        } else {
            echo html_writer::tag('p', get_string('applynowdesc', 'local_jobboard'), ['class' => 'text-muted mb-3']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
                '<i class="fa fa-paper-plane mr-2"></i>' . get_string('apply', 'local_jobboard'),
                ['class' => 'btn btn-primary btn-lg btn-block']
            );
        }
    } elseif (!$isloggedin) {
        echo html_writer::tag('p', get_string('logintoapply', 'local_jobboard'), ['class' => 'text-muted mb-3']);
        echo html_writer::link(
            new moodle_url('/login/index.php', [
                'wantsurl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'apply',
                    'vacancyid' => $vacancy->id,
                ]))->out(false),
            ]),
            '<i class="fa fa-sign-in-alt mr-2"></i>' . get_string('loginandapply', 'local_jobboard'),
            ['class' => 'btn btn-primary btn-lg btn-block', 'target' => '_blank']
        );
        echo html_writer::link(
            new moodle_url('/local/jobboard/signup.php', ['vacancyid' => $vacancy->id]),
            '<i class="fa fa-user-plus mr-2"></i>' . get_string('createaccount'),
            ['class' => 'btn btn-outline-secondary btn-block mt-2', 'target' => '_blank']
        );
    }

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Details card.
    echo html_writer::start_div('card shadow-sm border-0 mb-4');
    echo html_writer::start_div('card-header bg-transparent');
    echo html_writer::tag('h5',
        '<i class="fa fa-info-circle mr-2"></i>' . get_string('details', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    echo html_writer::start_tag('dl', ['class' => 'row mb-0']);

    // Location (Company name from IOMAD).
    if (!empty($vacancy->companyid)) {
        $companyName = local_jobboard_get_company_name($vacancy->companyid);
        if (!empty($companyName)) {
            echo html_writer::tag('dt', get_string('location', 'local_jobboard'), ['class' => 'col-sm-5']);
            echo html_writer::tag('dd',
                '<i class="fa fa-map-marker-alt text-primary mr-1"></i> ' . s($companyName),
                ['class' => 'col-sm-7']
            );
        }
    }

    // Modality (Department name from IOMAD).
    if (!empty($vacancy->departmentid)) {
        $departmentName = local_jobboard_get_department_name($vacancy->departmentid);
        if (!empty($departmentName)) {
            echo html_writer::tag('dt', get_string('modality', 'local_jobboard'), ['class' => 'col-sm-5']);
            echo html_writer::tag('dd',
                '<i class="fa fa-laptop-house text-info mr-1"></i> ' . s($departmentName),
                ['class' => 'col-sm-7']
            );
        }
    }

    if (!empty($vacancy->duration)) {
        echo html_writer::tag('dt', get_string('duration', 'local_jobboard'), ['class' => 'col-sm-5']);
        echo html_writer::tag('dd', s($vacancy->duration), ['class' => 'col-sm-7']);
    }

    // Dates from convocatoria.
    $opendate = null;
    $closedate = null;
    if (!empty($vacancy->convocatoriaid)) {
        $convocatoria = \local_jobboard_get_convocatoria($vacancy->convocatoriaid);
        if ($convocatoria) {
            $opendate = $convocatoria->startdate;
            $closedate = $convocatoria->enddate;
        }
    }
    // Fallback to current time if no dates available.
    if (empty($opendate)) {
        $opendate = time();
    }
    if (empty($closedate)) {
        $closedate = time() + (30 * 24 * 60 * 60); // 30 days from now.
    }

    echo html_writer::tag('dt', get_string('opendate', 'local_jobboard'), ['class' => 'col-sm-5']);
    echo html_writer::tag('dd', userdate($opendate, get_string('strftimedate', 'langconfig')), ['class' => 'col-sm-7']);

    echo html_writer::tag('dt', get_string('closedate', 'local_jobboard'), ['class' => 'col-sm-5']);
    $closeDateClass = $isurgent ? 'col-sm-7 text-danger font-weight-bold' : 'col-sm-7';
    echo html_writer::tag('dd', userdate($closedate, get_string('strftimedate', 'langconfig')), ['class' => $closeDateClass]);

    echo html_writer::end_tag('dl');

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Share card.
    echo html_writer::start_div('card shadow-sm border-0 mb-4');
    echo html_writer::start_div('card-header bg-transparent');
    echo html_writer::tag('h5',
        '<i class="fa fa-share-alt mr-2"></i>' . get_string('share', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    $shareurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]))->out(false);
    $sharetitle = urlencode($vacancy->title);

    echo html_writer::start_div('d-flex justify-content-center gap-2');
    echo html_writer::link(
        'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($shareurl),
        '<i class="fab fa-linkedin-in"></i>',
        ['class' => 'btn btn-sm btn-outline-primary', 'target' => '_blank', 'title' => 'LinkedIn']
    );
    echo html_writer::link(
        'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareurl),
        '<i class="fab fa-facebook-f"></i>',
        ['class' => 'btn btn-sm btn-outline-primary', 'target' => '_blank', 'title' => 'Facebook']
    );
    echo html_writer::link(
        'https://twitter.com/intent/tweet?url=' . urlencode($shareurl) . '&text=' . $sharetitle,
        '<i class="fab fa-twitter"></i>',
        ['class' => 'btn btn-sm btn-outline-primary', 'target' => '_blank', 'title' => 'Twitter']
    );
    echo html_writer::link(
        'https://api.whatsapp.com/send?text=' . $sharetitle . '%20' . urlencode($shareurl),
        '<i class="fab fa-whatsapp"></i>',
        ['class' => 'btn btn-sm btn-outline-success', 'target' => '_blank', 'title' => 'WhatsApp']
    );
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // col-lg-4

    echo html_writer::end_div(); // row

    echo html_writer::end_div(); // local-jobboard-dashboard

    echo $OUTPUT->footer();
}
