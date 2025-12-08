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
 * Completely redesigned modern public vacancy browser.
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

// Optional parameters.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$contracttype = optional_param('contracttype', '', PARAM_ALPHA);
$location = optional_param('location', '', PARAM_TEXT);
$modality = optional_param('modality', '', PARAM_TEXT);
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

// Add SEO meta tags.
$PAGE->add_body_class('local-jobboard-public');

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

// Build query conditions.
$conditions = ["v.status = :status", "v.closedate >= :now"];
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

// Location filter.
if (!empty($location)) {
    $conditions[] = $DB->sql_like('v.location', ':location', false);
    $params['location'] = '%' . $DB->sql_like_escape($location) . '%';
}

// Modality filter (only if column exists).
if (!empty($modality) && !empty($modalities)) {
    $conditions[] = $DB->sql_like('v.modality', ':modality', false);
    $params['modality'] = '%' . $DB->sql_like_escape($modality) . '%';
}

// Build WHERE clause.
$where = 'WHERE ' . implode(' AND ', $conditions);

// Get total count.
$total = $DB->count_records_sql("SELECT COUNT(*) FROM {local_jobboard_vacancy} v $where", $params);

// Get records with convocatoria info.
$sql = "SELECT v.*, c.name as convocatoria_name, c.code as convocatoria_code
          FROM {local_jobboard_vacancy} v
          LEFT JOIN {local_jobboard_convocatoria} c ON c.id = v.convocatoriaid
         $where
         ORDER BY v.closedate ASC, v.timecreated DESC";
$vacancies = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

// Get available filter options.
$contracttypes = local_jobboard_get_contract_types();
$locations = $DB->get_records_sql_menu(
    "SELECT DISTINCT location, location FROM {local_jobboard_vacancy}
     WHERE status = 'published' AND location IS NOT NULL AND location != ''
     ORDER BY location"
);

// Get modalities for filter (safely handle if column doesn't exist yet).
$modalities = [];
try {
    $dbman = $DB->get_manager();
    $table = new xmldb_table('local_jobboard_vacancy');
    $field = new xmldb_field('modality');
    if ($dbman->field_exists($table, $field)) {
        $modalities = $DB->get_records_sql_menu(
            "SELECT DISTINCT modality, modality FROM {local_jobboard_vacancy}
             WHERE status = 'published' AND modality IS NOT NULL AND modality != ''
             ORDER BY modality"
        );
    }
} catch (Exception $e) {
    $modalities = [];
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

echo html_writer::start_div('local-jobboard-public');

// ============================================================================
// HERO SECTION
// ============================================================================
echo html_writer::start_div('jb-public-hero');
echo html_writer::start_div('jb-hero-content');
echo html_writer::tag('h1', format_string($pagetitle), ['class' => 'jb-hero-title']);

$description = get_config('local_jobboard', 'public_page_description');
if (!empty($description)) {
    echo html_writer::tag('p', format_text($description, FORMAT_HTML), ['class' => 'jb-hero-subtitle']);
} else {
    echo html_writer::tag('p', get_string('publicpagedesc', 'local_jobboard'), ['class' => 'jb-hero-subtitle']);
}

// Quick stats in hero.
echo html_writer::start_div('jb-hero-stats');
echo html_writer::tag('span',
    html_writer::tag('strong', $total) . ' ' . get_string('openvacancies', 'local_jobboard'),
    ['class' => 'jb-hero-stat']
);
if ($urgentCount > 0) {
    echo html_writer::tag('span',
        html_writer::tag('strong', $urgentCount) . ' ' . get_string('closingsoon', 'local_jobboard'),
        ['class' => 'jb-hero-stat jb-hero-stat-warning']
    );
}
echo html_writer::end_div();

echo html_writer::end_div(); // jb-hero-content
echo html_writer::end_div(); // jb-public-hero

// ============================================================================
// STATISTICS CARDS
// ============================================================================
echo html_writer::start_div('jb-stats-section');
echo html_writer::start_div('jb-stats-grid');

// Total vacancies.
echo html_writer::start_div('jb-stat-card jb-stat-primary');
echo html_writer::tag('div', html_writer::tag('i', '', ['class' => 'fa fa-briefcase']), ['class' => 'jb-stat-icon']);
echo html_writer::tag('div', $total, ['class' => 'jb-stat-number']);
echo html_writer::tag('div', get_string('openvacancies', 'local_jobboard'), ['class' => 'jb-stat-label']);
echo html_writer::end_div();

// Urgent.
echo html_writer::start_div('jb-stat-card jb-stat-warning');
echo html_writer::tag('div', html_writer::tag('i', '', ['class' => 'fa fa-clock']), ['class' => 'jb-stat-icon']);
echo html_writer::tag('div', $urgentCount, ['class' => 'jb-stat-number']);
echo html_writer::tag('div', get_string('closingsoondays', 'local_jobboard', 7), ['class' => 'jb-stat-label']);
echo html_writer::end_div();

// Convocatorias.
echo html_writer::start_div('jb-stat-card jb-stat-info');
echo html_writer::tag('div', html_writer::tag('i', '', ['class' => 'fa fa-folder-open']), ['class' => 'jb-stat-icon']);
echo html_writer::tag('div', count($convocatorias), ['class' => 'jb-stat-number']);
echo html_writer::tag('div', get_string('activeconvocatorias', 'local_jobboard'), ['class' => 'jb-stat-label']);
echo html_writer::end_div();

// My applications (if logged in).
if ($isloggedin) {
    $myApps = $DB->count_records('local_jobboard_application', ['userid' => $USER->id]);
    echo html_writer::start_div('jb-stat-card jb-stat-success');
    echo html_writer::tag('div', html_writer::tag('i', '', ['class' => 'fa fa-file-alt']), ['class' => 'jb-stat-icon']);
    echo html_writer::tag('div', $myApps, ['class' => 'jb-stat-number']);
    echo html_writer::tag('div', get_string('myapplications', 'local_jobboard'), ['class' => 'jb-stat-label']);
    echo html_writer::end_div();
}

echo html_writer::end_div(); // jb-stats-grid
echo html_writer::end_div(); // jb-stats-section

// ============================================================================
// FILTER SECTION
// ============================================================================
echo html_writer::start_div('jb-filter-section');
echo html_writer::start_div('jb-filter-card');
echo html_writer::tag('h5',
    html_writer::tag('i', '', ['class' => 'fa fa-filter mr-2']) . get_string('filtervacancies', 'local_jobboard'),
    ['class' => 'jb-filter-title']
);

$formurl = new moodle_url('/local/jobboard/index.php');
echo html_writer::start_tag('form', ['method' => 'get', 'action' => $formurl->out_omit_querystring(), 'class' => 'jb-filter-form']);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'view', 'value' => 'public']);

echo html_writer::start_div('jb-filter-grid');

// Search.
echo html_writer::start_div('jb-filter-item jb-filter-search');
echo html_writer::tag('label', get_string('search'), ['for' => 'filter-search', 'class' => 'jb-filter-label']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'id' => 'filter-search',
    'value' => $search,
    'placeholder' => get_string('searchplaceholder', 'local_jobboard'),
    'class' => 'jb-filter-input',
]);
echo html_writer::end_div();

// Location.
$locationOptions = ['' => get_string('alllocations', 'local_jobboard')] + ($locations ?: []);
echo html_writer::start_div('jb-filter-item');
echo html_writer::tag('label', get_string('location', 'local_jobboard'), ['for' => 'filter-location', 'class' => 'jb-filter-label']);
echo html_writer::start_tag('select', [
    'name' => 'location',
    'id' => 'filter-location',
    'class' => 'jb-filter-select'
]);
foreach ($locationOptions as $val => $label) {
    echo html_writer::tag('option', $label, [
        'value' => $val,
        'selected' => $location === $val ? 'selected' : null
    ]);
}
echo html_writer::end_tag('select');
echo html_writer::end_div();

// Modality.
$modalityOptions = ['' => get_string('allmodalities', 'local_jobboard')] + ($modalities ?: []);
echo html_writer::start_div('jb-filter-item');
echo html_writer::tag('label', get_string('modality', 'local_jobboard'), ['for' => 'filter-modality', 'class' => 'jb-filter-label']);
echo html_writer::start_tag('select', [
    'name' => 'modality',
    'id' => 'filter-modality',
    'class' => 'jb-filter-select'
]);
foreach ($modalityOptions as $val => $label) {
    echo html_writer::tag('option', $label, [
        'value' => $val,
        'selected' => $modality === $val ? 'selected' : null
    ]);
}
echo html_writer::end_tag('select');
echo html_writer::end_div();

// Contract type.
$contractOptions = ['' => get_string('allcontracttypes', 'local_jobboard')] + $contracttypes;
echo html_writer::start_div('jb-filter-item');
echo html_writer::tag('label', get_string('contracttype', 'local_jobboard'), ['for' => 'filter-contract', 'class' => 'jb-filter-label']);
echo html_writer::start_tag('select', [
    'name' => 'contracttype',
    'id' => 'filter-contract',
    'class' => 'jb-filter-select'
]);
foreach ($contractOptions as $val => $label) {
    echo html_writer::tag('option', $label, [
        'value' => $val,
        'selected' => $contracttype === $val ? 'selected' : null
    ]);
}
echo html_writer::end_tag('select');
echo html_writer::end_div();

// Convocatoria.
$convocatoriaOptions = [0 => get_string('allconvocatorias', 'local_jobboard')];
foreach ($convocatorias as $conv) {
    $convocatoriaOptions[$conv->id] = format_string($conv->code . ' - ' . $conv->name);
}
echo html_writer::start_div('jb-filter-item');
echo html_writer::tag('label', get_string('convocatoria', 'local_jobboard'), ['for' => 'filter-convocatoria', 'class' => 'jb-filter-label']);
echo html_writer::start_tag('select', [
    'name' => 'convocatoria',
    'id' => 'filter-convocatoria',
    'class' => 'jb-filter-select'
]);
foreach ($convocatoriaOptions as $val => $label) {
    echo html_writer::tag('option', $label, [
        'value' => $val,
        'selected' => $convocatoriaid == $val ? 'selected' : null
    ]);
}
echo html_writer::end_tag('select');
echo html_writer::end_div();

echo html_writer::end_div(); // jb-filter-grid

// Filter actions.
echo html_writer::start_div('jb-filter-actions');
echo html_writer::tag('button',
    html_writer::tag('i', '', ['class' => 'fa fa-search mr-2']) . get_string('search'),
    ['type' => 'submit', 'class' => 'jb-btn jb-btn-primary']
);

if (!empty($search) || !empty($contracttype) || !empty($location) || !empty($modality) || !empty($publicationtype) || $convocatoriaid) {
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        html_writer::tag('i', '', ['class' => 'fa fa-times mr-2']) . get_string('clearfilters', 'local_jobboard'),
        ['class' => 'jb-btn jb-btn-secondary']
    );
}
echo html_writer::end_div();

echo html_writer::end_tag('form');
echo html_writer::end_div(); // jb-filter-card
echo html_writer::end_div(); // jb-filter-section

// ============================================================================
// RESULTS SECTION
// ============================================================================
echo html_writer::start_div('jb-results-section');

// Results header.
echo html_writer::start_div('jb-results-header');
echo html_writer::tag('span',
    get_string('vacanciesfound', 'local_jobboard', $total),
    ['class' => 'jb-results-count']
);
echo html_writer::end_div();

// ============================================================================
// VACANCY CARDS GRID
// ============================================================================
if (empty($vacancies)) {
    echo html_writer::start_div('jb-empty-state');
    echo html_writer::tag('i', '', ['class' => 'fa fa-briefcase jb-empty-icon']);
    echo html_writer::tag('h4', get_string('novacanciesfound', 'local_jobboard'), ['class' => 'jb-empty-title']);
    echo html_writer::tag('p', get_string('trydifferentfilters', 'local_jobboard'), ['class' => 'jb-empty-text']);
    if (!$isloggedin) {
        echo html_writer::link(
            new moodle_url('/login/index.php'),
            html_writer::tag('i', '', ['class' => 'fa fa-sign-in-alt mr-2']) . get_string('login'),
            ['class' => 'jb-btn jb-btn-primary']
        );
    }
    echo html_writer::end_div();
} else {
    echo html_writer::start_div('jb-vacancies-grid');

    foreach ($vacancies as $vacancy) {
        $daysremaining = max(0, (int) floor(($vacancy->closedate - time()) / 86400));
        $isurgent = $daysremaining <= 7;
        $urgentClass = $isurgent ? ' jb-card-urgent' : '';

        echo html_writer::start_div('jb-vacancy-card' . $urgentClass);

        // Card header.
        echo html_writer::start_div('jb-card-header');
        echo html_writer::tag('span', $vacancy->code, ['class' => 'jb-card-code']);

        $statusBadge = $vacancy->publicationtype === 'public'
            ? html_writer::tag('span', get_string('publicationtype:public', 'local_jobboard'), ['class' => 'jb-badge jb-badge-success'])
            : html_writer::tag('span', get_string('publicationtype:internal', 'local_jobboard'), ['class' => 'jb-badge jb-badge-info']);
        echo $statusBadge;
        echo html_writer::end_div();

        // Card body.
        echo html_writer::start_div('jb-card-body');

        // Title.
        echo html_writer::tag('h3', format_string($vacancy->title), ['class' => 'jb-card-title']);

        // Meta info.
        echo html_writer::start_div('jb-card-meta');

        if (!empty($vacancy->location)) {
            echo html_writer::tag('span',
                html_writer::tag('i', '', ['class' => 'fa fa-map-marker-alt']) . ' ' . s($vacancy->location),
                ['class' => 'jb-meta-item']
            );
        }

        if (!empty($vacancy->modality)) {
            echo html_writer::tag('span',
                html_writer::tag('i', '', ['class' => 'fa fa-laptop-house']) . ' ' . s($vacancy->modality),
                ['class' => 'jb-meta-item']
            );
        }

        if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
            echo html_writer::tag('span',
                html_writer::tag('i', '', ['class' => 'fa fa-file-contract']) . ' ' . $contracttypes[$vacancy->contracttype],
                ['class' => 'jb-meta-item']
            );
        }

        echo html_writer::tag('span',
            html_writer::tag('i', '', ['class' => 'fa fa-users']) . ' ' . $vacancy->positions . ' ' . get_string('positions', 'local_jobboard'),
            ['class' => 'jb-meta-item']
        );

        echo html_writer::end_div(); // jb-card-meta

        // Convocatoria.
        if (!empty($vacancy->convocatoria_name)) {
            echo html_writer::tag('div',
                html_writer::tag('i', '', ['class' => 'fa fa-folder-open']) . ' ' . format_string($vacancy->convocatoria_code),
                ['class' => 'jb-card-convocatoria']
            );
        }

        // Description excerpt.
        if (!empty($vacancy->description)) {
            $desc = shorten_text(strip_tags($vacancy->description), 120);
            echo html_writer::tag('p', $desc, ['class' => 'jb-card-desc']);
        }

        echo html_writer::end_div(); // jb-card-body

        // Card footer.
        echo html_writer::start_div('jb-card-footer');

        // Closing date.
        $closingClass = $isurgent ? 'jb-closing-urgent' : 'jb-closing-normal';
        echo html_writer::tag('span',
            html_writer::tag('i', '', ['class' => 'fa fa-calendar-alt']) . ' ' .
            get_string('closesin', 'local_jobboard', $daysremaining),
            ['class' => 'jb-card-closing ' . $closingClass]
        );

        // Action buttons.
        echo html_writer::start_div('jb-card-actions');

        // View details.
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]),
            html_writer::tag('i', '', ['class' => 'fa fa-eye']) . ' ' . get_string('viewdetails', 'local_jobboard'),
            ['class' => 'jb-btn jb-btn-outline']
        );

        // Apply button.
        if ($isloggedin && has_capability('local/jobboard:apply', $context)) {
            $hasApplied = $DB->record_exists('local_jobboard_application', [
                'vacancyid' => $vacancy->id,
                'userid' => $USER->id,
            ]);

            if ($hasApplied) {
                echo html_writer::tag('span',
                    html_writer::tag('i', '', ['class' => 'fa fa-check']) . ' ' . get_string('applied', 'local_jobboard'),
                    ['class' => 'jb-badge jb-badge-applied']
                );
            } else {
                echo html_writer::link(
                    new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
                    html_writer::tag('i', '', ['class' => 'fa fa-paper-plane']) . ' ' . get_string('apply', 'local_jobboard'),
                    ['class' => 'jb-btn jb-btn-primary']
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
                html_writer::tag('i', '', ['class' => 'fa fa-sign-in-alt']) . ' ' . get_string('loginandapply', 'local_jobboard'),
                ['class' => 'jb-btn jb-btn-secondary']
            );
        }

        echo html_writer::end_div(); // jb-card-actions
        echo html_writer::end_div(); // jb-card-footer

        echo html_writer::end_div(); // jb-vacancy-card
    }

    echo html_writer::end_div(); // jb-vacancies-grid

    // Pagination.
    if ($total > $perpage) {
        echo html_writer::start_div('jb-pagination');
        $baseurl = new moodle_url('/local/jobboard/index.php', [
            'view' => 'public',
            'search' => $search,
            'contracttype' => $contracttype,
            'location' => $location,
            'modality' => $modality,
            'convocatoria' => $convocatoriaid,
            'type' => $publicationtype,
        ]);
        echo $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
        echo html_writer::end_div();
    }
}

echo html_writer::end_div(); // jb-results-section

// ============================================================================
// CTA FOR NON-LOGGED IN USERS
// ============================================================================
if (!$isloggedin) {
    echo html_writer::start_div('jb-cta-section');
    echo html_writer::start_div('jb-cta-card');
    echo html_writer::tag('div', html_writer::tag('i', '', ['class' => 'fa fa-user-plus']), ['class' => 'jb-cta-icon']);
    echo html_writer::tag('h3', get_string('wanttoapply', 'local_jobboard'), ['class' => 'jb-cta-title']);
    echo html_writer::tag('p', get_string('createaccounttoapply', 'local_jobboard'), ['class' => 'jb-cta-text']);

    echo html_writer::start_div('jb-cta-buttons');
    $currentpageurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public']))->out(false);
    echo html_writer::link(
        new moodle_url('/login/signup.php'),
        html_writer::tag('i', '', ['class' => 'fa fa-user-plus mr-2']) . get_string('createaccount'),
        ['class' => 'jb-btn jb-btn-primary jb-btn-lg']
    );
    echo html_writer::link(
        new moodle_url('/login/index.php', ['wantsurl' => $currentpageurl]),
        html_writer::tag('i', '', ['class' => 'fa fa-sign-in-alt mr-2']) . get_string('login'),
        ['class' => 'jb-btn jb-btn-outline jb-btn-lg']
    );
    echo html_writer::end_div();

    echo html_writer::end_div(); // jb-cta-card
    echo html_writer::end_div(); // jb-cta-section
}

echo html_writer::end_div(); // local-jobboard-public

// JavaScript to force select option visibility.
$js = <<<'JS'
document.addEventListener('DOMContentLoaded', function() {
    // Force select and option visibility
    var selects = document.querySelectorAll('.jb-filter-select');
    selects.forEach(function(select) {
        select.style.color = '#212529';
        select.style.backgroundColor = '#fff';
        select.style.webkitTextFillColor = '#212529';

        var options = select.querySelectorAll('option');
        options.forEach(function(option) {
            option.style.color = '#212529';
            option.style.backgroundColor = '#fff';
            option.style.webkitTextFillColor = '#212529';
        });
    });
});
JS;
echo html_writer::tag('script', $js);

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

    // Get convocatoria info.
    $convocatoria = null;
    if (!empty($vacancy->convocatoriaid)) {
        $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $vacancy->convocatoriaid]);
    }

    $PAGE->set_title($vacancy->title);
    $PAGE->set_heading($vacancy->title);
    $PAGE->requires->css('/local/jobboard/styles.css');

    echo $OUTPUT->header();

    echo html_writer::start_div('local-jobboard-public-detail');

    // Back button.
    echo html_writer::start_div('jb-detail-nav');
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        html_writer::tag('i', '', ['class' => 'fa fa-arrow-left mr-2']) . get_string('backtovacancies', 'local_jobboard'),
        ['class' => 'jb-btn jb-btn-outline']
    );
    echo html_writer::end_div();

    // Two column layout.
    echo html_writer::start_div('jb-detail-layout');

    // LEFT COLUMN - Main content.
    echo html_writer::start_div('jb-detail-main');

    // Vacancy header card.
    echo html_writer::start_div('jb-detail-header-card');

    // Header badges.
    echo html_writer::start_div('jb-detail-badges');
    echo html_writer::tag('span', $vacancy->code, ['class' => 'jb-badge jb-badge-code']);

    $typebadge = $vacancy->publicationtype === 'public'
        ? html_writer::tag('span', get_string('publicationtype:public', 'local_jobboard'), ['class' => 'jb-badge jb-badge-success'])
        : html_writer::tag('span', get_string('publicationtype:internal', 'local_jobboard'), ['class' => 'jb-badge jb-badge-info']);
    echo $typebadge;
    echo html_writer::end_div();

    // Title.
    echo html_writer::tag('h1', format_string($vacancy->title), ['class' => 'jb-detail-title']);

    // Convocatoria info.
    if ($convocatoria) {
        echo html_writer::tag('div',
            html_writer::tag('i', '', ['class' => 'fa fa-folder-open mr-2']) .
            get_string('convocatoria', 'local_jobboard') . ': ' .
            html_writer::tag('strong', format_string($convocatoria->name)),
            ['class' => 'jb-detail-convocatoria']
        );
    }

    // Quick meta.
    echo html_writer::start_div('jb-detail-meta');

    if (!empty($vacancy->location)) {
        echo html_writer::tag('span',
            html_writer::tag('i', '', ['class' => 'fa fa-map-marker-alt']) . ' ' . s($vacancy->location),
            ['class' => 'jb-meta-badge']
        );
    }
    if (!empty($vacancy->modality)) {
        echo html_writer::tag('span',
            html_writer::tag('i', '', ['class' => 'fa fa-laptop-house']) . ' ' . s($vacancy->modality),
            ['class' => 'jb-meta-badge']
        );
    }
    if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
        echo html_writer::tag('span',
            html_writer::tag('i', '', ['class' => 'fa fa-briefcase']) . ' ' . $contracttypes[$vacancy->contracttype],
            ['class' => 'jb-meta-badge']
        );
    }
    echo html_writer::tag('span',
        html_writer::tag('i', '', ['class' => 'fa fa-users']) . ' ' . $vacancy->positions . ' ' . get_string('positions', 'local_jobboard'),
        ['class' => 'jb-meta-badge']
    );

    echo html_writer::end_div();

    // Closing date warning.
    $daysremaining = max(0, (int) floor(($vacancy->closedate - time()) / 86400));
    $isurgent = $daysremaining <= 7;

    if ($isurgent) {
        echo html_writer::tag('div',
            html_writer::tag('i', '', ['class' => 'fa fa-exclamation-triangle mr-2']) .
            get_string('closesin', 'local_jobboard', $daysremaining),
            ['class' => 'jb-detail-urgent']
        );
    }

    echo html_writer::end_div(); // jb-detail-header-card

    // Description section.
    if (!empty($vacancy->description)) {
        echo html_writer::start_div('jb-detail-section');
        echo html_writer::tag('h3',
            html_writer::tag('i', '', ['class' => 'fa fa-file-alt mr-2']) . get_string('vacancydescription', 'local_jobboard'),
            ['class' => 'jb-section-title']
        );
        echo html_writer::tag('div', format_text($vacancy->description, FORMAT_HTML), ['class' => 'jb-section-content']);
        echo html_writer::end_div();
    }

    // Requirements section.
    if (!empty($vacancy->requirements)) {
        echo html_writer::start_div('jb-detail-section');
        echo html_writer::tag('h3',
            html_writer::tag('i', '', ['class' => 'fa fa-check-circle mr-2']) . get_string('requirements', 'local_jobboard'),
            ['class' => 'jb-section-title']
        );
        echo html_writer::tag('div', format_text($vacancy->requirements, FORMAT_HTML), ['class' => 'jb-section-content']);
        echo html_writer::end_div();
    }

    // Desirable section.
    if (!empty($vacancy->desirable)) {
        echo html_writer::start_div('jb-detail-section');
        echo html_writer::tag('h3',
            html_writer::tag('i', '', ['class' => 'fa fa-star mr-2']) . get_string('desirable', 'local_jobboard'),
            ['class' => 'jb-section-title']
        );
        echo html_writer::tag('div', format_text($vacancy->desirable, FORMAT_HTML), ['class' => 'jb-section-content']);
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // jb-detail-main

    // RIGHT COLUMN - Sidebar.
    echo html_writer::start_div('jb-detail-sidebar');

    // Apply CTA card.
    echo html_writer::start_div('jb-sidebar-card jb-apply-card');
    echo html_writer::tag('h4',
        html_writer::tag('i', '', ['class' => 'fa fa-paper-plane mr-2']) . get_string('readytoapply', 'local_jobboard'),
        ['class' => 'jb-sidebar-title']
    );

    echo html_writer::start_div('jb-sidebar-body');

    if ($isloggedin && has_capability('local/jobboard:apply', $context)) {
        // Check if already applied.
        $hasapplied = $DB->record_exists('local_jobboard_application', [
            'vacancyid' => $vacancy->id,
            'userid' => $USER->id,
        ]);

        if ($hasapplied) {
            echo html_writer::tag('div',
                html_writer::tag('i', '', ['class' => 'fa fa-check-circle fa-3x']) .
                html_writer::tag('p', get_string('alreadyapplied', 'local_jobboard')),
                ['class' => 'jb-applied-status']
            );
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
                get_string('viewmyapplications', 'local_jobboard'),
                ['class' => 'jb-btn jb-btn-outline jb-btn-block']
            );
        } else {
            echo html_writer::tag('p', get_string('applynowdesc', 'local_jobboard'), ['class' => 'jb-apply-desc']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
                html_writer::tag('i', '', ['class' => 'fa fa-paper-plane mr-2']) . get_string('apply', 'local_jobboard'),
                ['class' => 'jb-btn jb-btn-primary jb-btn-lg jb-btn-block']
            );
        }
    } elseif (!$isloggedin) {
        echo html_writer::tag('p', get_string('logintoapply', 'local_jobboard'), ['class' => 'jb-apply-desc']);
        echo html_writer::link(
            new moodle_url('/login/index.php', [
                'wantsurl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'apply',
                    'vacancyid' => $vacancy->id,
                ]))->out(false),
            ]),
            html_writer::tag('i', '', ['class' => 'fa fa-sign-in-alt mr-2']) . get_string('loginandapply', 'local_jobboard'),
            ['class' => 'jb-btn jb-btn-primary jb-btn-lg jb-btn-block']
        );
    }

    echo html_writer::end_div();
    echo html_writer::end_div(); // jb-apply-card

    // Details card.
    echo html_writer::start_div('jb-sidebar-card');
    echo html_writer::tag('h4',
        html_writer::tag('i', '', ['class' => 'fa fa-info-circle mr-2']) . get_string('details', 'local_jobboard'),
        ['class' => 'jb-sidebar-title']
    );

    echo html_writer::start_div('jb-sidebar-body');
    echo html_writer::start_div('jb-details-list');

    if (!empty($vacancy->duration)) {
        echo html_writer::start_div('jb-detail-item');
        echo html_writer::tag('span', get_string('duration', 'local_jobboard'), ['class' => 'jb-detail-label']);
        echo html_writer::tag('span', s($vacancy->duration), ['class' => 'jb-detail-value']);
        echo html_writer::end_div();
    }

    if (!empty($vacancy->salary)) {
        echo html_writer::start_div('jb-detail-item');
        echo html_writer::tag('span', get_string('salary', 'local_jobboard'), ['class' => 'jb-detail-label']);
        echo html_writer::tag('span', s($vacancy->salary), ['class' => 'jb-detail-value']);
        echo html_writer::end_div();
    }

    if (!empty($vacancy->department)) {
        echo html_writer::start_div('jb-detail-item');
        echo html_writer::tag('span', get_string('department', 'local_jobboard'), ['class' => 'jb-detail-label']);
        echo html_writer::tag('span', s($vacancy->department), ['class' => 'jb-detail-value']);
        echo html_writer::end_div();
    }

    echo html_writer::start_div('jb-detail-item');
    echo html_writer::tag('span', get_string('opendate', 'local_jobboard'), ['class' => 'jb-detail-label']);
    echo html_writer::tag('span', userdate($vacancy->opendate, get_string('strftimedate', 'langconfig')), ['class' => 'jb-detail-value']);
    echo html_writer::end_div();

    echo html_writer::start_div('jb-detail-item');
    echo html_writer::tag('span', get_string('closedate', 'local_jobboard'), ['class' => 'jb-detail-label']);
    $closeDateClass = $isurgent ? 'jb-detail-value jb-value-urgent' : 'jb-detail-value';
    echo html_writer::tag('span', userdate($vacancy->closedate, get_string('strftimedate', 'langconfig')), ['class' => $closeDateClass]);
    echo html_writer::end_div();

    echo html_writer::end_div(); // jb-details-list
    echo html_writer::end_div();
    echo html_writer::end_div(); // details card

    // Share card.
    echo html_writer::start_div('jb-sidebar-card');
    echo html_writer::tag('h4',
        html_writer::tag('i', '', ['class' => 'fa fa-share-alt mr-2']) . get_string('share', 'local_jobboard'),
        ['class' => 'jb-sidebar-title']
    );

    echo html_writer::start_div('jb-sidebar-body');

    $shareurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]))->out(false);
    $sharetitle = urlencode($vacancy->title);

    echo html_writer::start_div('jb-share-buttons');
    echo html_writer::link(
        'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($shareurl),
        html_writer::tag('i', '', ['class' => 'fab fa-linkedin-in']),
        ['class' => 'jb-share-btn jb-share-linkedin', 'target' => '_blank', 'title' => 'LinkedIn']
    );
    echo html_writer::link(
        'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareurl),
        html_writer::tag('i', '', ['class' => 'fab fa-facebook-f']),
        ['class' => 'jb-share-btn jb-share-facebook', 'target' => '_blank', 'title' => 'Facebook']
    );
    echo html_writer::link(
        'https://twitter.com/intent/tweet?url=' . urlencode($shareurl) . '&text=' . $sharetitle,
        html_writer::tag('i', '', ['class' => 'fab fa-twitter']),
        ['class' => 'jb-share-btn jb-share-twitter', 'target' => '_blank', 'title' => 'Twitter']
    );
    echo html_writer::link(
        'https://api.whatsapp.com/send?text=' . $sharetitle . '%20' . urlencode($shareurl),
        html_writer::tag('i', '', ['class' => 'fab fa-whatsapp']),
        ['class' => 'jb-share-btn jb-share-whatsapp', 'target' => '_blank', 'title' => 'WhatsApp']
    );
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div(); // share card

    echo html_writer::end_div(); // jb-detail-sidebar

    echo html_writer::end_div(); // jb-detail-layout

    echo html_writer::end_div(); // local-jobboard-public-detail

    echo $OUTPUT->footer();
}
