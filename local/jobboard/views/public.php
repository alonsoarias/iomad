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
 * Modern redesign with card-based layout for public vacancy browsing.
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

// Optional parameters.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);
$search = optional_param('search', '', PARAM_TEXT);
$contracttype = optional_param('contracttype', '', PARAM_ALPHA);
$location = optional_param('location', '', PARAM_TEXT);
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

// Get open convocatorias for filter.
$convocatorias = $DB->get_records_sql(
    "SELECT DISTINCT c.id, c.code, c.name
       FROM {local_jobboard_convocatoria} c
       JOIN {local_jobboard_vacancy} v ON v.convocatoriaid = c.id
      WHERE c.status = 'open' AND v.status = 'published'
      ORDER BY c.code"
);

// Start output.
echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-public');

// ============================================================================
// PAGE HEADER
// ============================================================================
echo html_writer::start_div('jb-public-hero text-center py-5 mb-4 bg-primary text-white rounded');

echo html_writer::tag('h1', $pagetitle, ['class' => 'display-4 mb-3']);

$description = get_config('local_jobboard', 'public_page_description');
if (!empty($description)) {
    echo html_writer::tag('p', format_text($description, FORMAT_HTML), ['class' => 'lead mb-0']);
}

echo html_writer::end_div();

// ============================================================================
// STATISTICS ROW
// ============================================================================
$urgentCount = 0;
foreach ($vacancies as $v) {
    if (($v->closedate - time()) <= 7 * 86400) {
        $urgentCount++;
    }
}

echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card(
    (string)$total,
    get_string('openvacancies', 'local_jobboard'),
    'primary', 'briefcase'
);
echo ui_helper::stat_card(
    (string)$urgentCount,
    get_string('closingsoondays', 'local_jobboard', 7),
    'warning', 'clock'
);
echo ui_helper::stat_card(
    (string)count($convocatorias),
    get_string('activeconvocatorias', 'local_jobboard'),
    'info', 'folder-open'
);
if ($isloggedin) {
    $myApps = $DB->count_records('local_jobboard_application', ['userid' => $USER->id]);
    echo ui_helper::stat_card(
        (string)$myApps,
        get_string('myapplications', 'local_jobboard'),
        'success', 'file-alt'
    );
}
echo html_writer::end_div();

// ============================================================================
// FILTER FORM
// ============================================================================
$contractOptions = ['' => get_string('allcontracttypes', 'local_jobboard')] + $contracttypes;
$locationOptions = ['' => get_string('alllocations', 'local_jobboard')] + ($locations ?: []);
$convocatoriaOptions = [0 => get_string('allconvocatorias', 'local_jobboard')];
foreach ($convocatorias as $conv) {
    $convocatoriaOptions[$conv->id] = format_string($conv->code . ' - ' . $conv->name);
}

$filterDefinitions = [
    [
        'type' => 'text',
        'name' => 'search',
        'placeholder' => get_string('searchplaceholder', 'local_jobboard'),
        'col' => 'col-md-4',
    ],
    [
        'type' => 'select',
        'name' => 'contracttype',
        'options' => $contractOptions,
        'col' => 'col-md-2',
    ],
    [
        'type' => 'select',
        'name' => 'location',
        'options' => $locationOptions,
        'col' => 'col-md-2',
    ],
    [
        'type' => 'select',
        'name' => 'convocatoria',
        'options' => $convocatoriaOptions,
        'col' => 'col-md-3',
    ],
];

// Type filter (only for authenticated users).
if ($canviewinternal) {
    $typeOptions = [
        '' => get_string('all'),
        'public' => get_string('publicationtype:public', 'local_jobboard'),
        'internal' => get_string('publicationtype:internal', 'local_jobboard'),
    ];
    $filterDefinitions[] = [
        'type' => 'select',
        'name' => 'type',
        'options' => $typeOptions,
        'col' => 'col-md-2',
    ];
}

echo ui_helper::filter_form(
    (new moodle_url('/local/jobboard/index.php'))->out(false),
    $filterDefinitions,
    [
        'search' => $search,
        'contracttype' => $contracttype,
        'location' => $location,
        'convocatoria' => $convocatoriaid,
        'type' => $publicationtype,
    ],
    ['view' => 'public']
);

// Clear filters link.
if (!empty($search) || !empty($contracttype) || !empty($location) || !empty($publicationtype) || $convocatoriaid) {
    echo html_writer::div(
        html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
            '<i class="fa fa-times mr-1"></i>' . get_string('clearfilters', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-secondary']
        ),
        'mb-3'
    );
}

// ============================================================================
// RESULTS INFO
// ============================================================================
echo html_writer::tag('p', get_string('vacanciesfound', 'local_jobboard', $total), ['class' => 'text-muted mb-3']);

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
        echo html_writer::start_div('card h-100 shadow-sm jb-vacancy-card' . ($isurgent ? ' border-warning' : ''));

        // Card header with badges.
        echo html_writer::start_div('card-header d-flex justify-content-between align-items-center bg-white');
        echo html_writer::tag('span', $vacancy->code, ['class' => 'badge badge-secondary']);

        $typebadge = $vacancy->publicationtype === 'public'
            ? html_writer::tag('span', get_string('publicationtype:public', 'local_jobboard'), ['class' => 'badge badge-success'])
            : html_writer::tag('span', get_string('publicationtype:internal', 'local_jobboard'), ['class' => 'badge badge-info']);
        echo $typebadge;
        echo html_writer::end_div();

        // Card body.
        echo html_writer::start_div('card-body');

        // Title.
        echo html_writer::tag('h5', format_string($vacancy->title), ['class' => 'card-title']);

        // Convocatoria badge.
        if (!empty($vacancy->convocatoria_name)) {
            echo html_writer::div(
                '<i class="fa fa-folder-open text-muted mr-1"></i>' .
                html_writer::tag('span', format_string($vacancy->convocatoria_code), ['class' => 'badge badge-light']),
                'mb-2'
            );
        }

        // Location and contract type.
        if (!empty($vacancy->location)) {
            echo html_writer::tag('p',
                '<i class="fa fa-map-marker-alt text-muted mr-2"></i>' . s($vacancy->location),
                ['class' => 'card-text text-muted small mb-1']
            );
        }

        if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
            echo html_writer::tag('p',
                '<i class="fa fa-briefcase text-muted mr-2"></i>' . $contracttypes[$vacancy->contracttype],
                ['class' => 'card-text text-muted small mb-2']
            );
        }

        // Description excerpt.
        $vacancydescription = shorten_text(strip_tags($vacancy->description), 100);
        echo html_writer::tag('p', $vacancydescription, ['class' => 'card-text small']);

        // Positions.
        echo html_writer::tag('p',
            '<i class="fa fa-users text-muted mr-2"></i>' .
            get_string('positions', 'local_jobboard') . ': ' .
            html_writer::tag('strong', s($vacancy->positions)),
            ['class' => 'card-text small mb-0']
        );

        echo html_writer::end_div(); // card-body

        // Card footer.
        echo html_writer::start_div('card-footer bg-white');

        // Closing date.
        $closingclass = $isurgent ? 'text-warning font-weight-bold' : 'text-muted';
        $closingicon = $isurgent ? 'fa-exclamation-triangle' : 'fa-calendar-alt';
        echo html_writer::tag('small',
            '<i class="fa ' . $closingicon . ' mr-1"></i>' .
            get_string('closesin', 'local_jobboard', $daysremaining),
            ['class' => $closingclass . ' d-block mb-2']
        );

        // Action buttons.
        echo html_writer::start_div('d-flex justify-content-between');

        // View details button.
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]),
            '<i class="fa fa-eye mr-1"></i>' . get_string('viewdetails', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-primary']
        );

        // Apply button (if logged in).
        if ($isloggedin && has_capability('local/jobboard:apply', $context)) {
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
                '<i class="fa fa-paper-plane mr-1"></i>' . get_string('apply', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-primary']
            );
        } elseif (!$isloggedin) {
            echo html_writer::link(
                new moodle_url('/login/index.php', [
                    'wantsurl' => (new moodle_url('/local/jobboard/index.php', [
                        'view' => 'apply',
                        'vacancyid' => $vacancy->id,
                    ]))->out(false),
                ]),
                '<i class="fa fa-sign-in-alt mr-1"></i>' . get_string('loginandapply', 'local_jobboard') .
                '<span class="sr-only"> ' . get_string('opensnewwindow', 'local_jobboard') . '</span>',
                ['class' => 'btn btn-sm btn-outline-secondary', 'target' => '_blank', 'rel' => 'noopener noreferrer']
            );
        }

        echo html_writer::end_div();
        echo html_writer::end_div(); // card-footer

        echo html_writer::end_div(); // card
        echo html_writer::end_div(); // col
    }

    echo html_writer::end_div(); // row

    // Pagination.
    $baseurl = new moodle_url('/local/jobboard/index.php', [
        'view' => 'public',
        'search' => $search,
        'contracttype' => $contracttype,
        'location' => $location,
        'convocatoria' => $convocatoriaid,
        'type' => $publicationtype,
    ]);
    echo $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
}

// ============================================================================
// CTA FOR NON-LOGGED IN USERS
// ============================================================================
if (!$isloggedin) {
    echo html_writer::start_div('card bg-light mt-4 border-0');
    echo html_writer::start_div('card-body text-center py-5');
    echo html_writer::tag('i', '', ['class' => 'fa fa-user-plus fa-3x text-primary mb-3']);
    echo html_writer::tag('h4', get_string('wanttoapply', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::tag('p', get_string('createaccounttoapply', 'local_jobboard'), ['class' => 'card-text text-muted mb-4']);
    $currentpageurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public']))->out(false);
    echo html_writer::link(
        new moodle_url('/local/jobboard/signup.php', ['wantsurl' => $currentpageurl]),
        '<i class="fa fa-user-plus mr-2"></i>' . get_string('createaccount') .
        '<span class="sr-only"> ' . get_string('opensnewwindow', 'local_jobboard') . '</span>',
        ['class' => 'btn btn-primary btn-lg mr-3', 'target' => '_blank', 'rel' => 'noopener noreferrer']
    );
    echo html_writer::link(
        new moodle_url('/login/index.php', ['wantsurl' => $currentpageurl]),
        '<i class="fa fa-sign-in-alt mr-2"></i>' . get_string('login') .
        '<span class="sr-only"> ' . get_string('opensnewwindow', 'local_jobboard') . '</span>',
        ['class' => 'btn btn-outline-primary btn-lg', 'target' => '_blank', 'rel' => 'noopener noreferrer']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // local-jobboard-public

// Styles consolidated in styles.css - Public Page Styles section.

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
    echo \local_jobboard\output\ui_helper::get_inline_styles();

    echo html_writer::start_div('local-jobboard-public-detail');

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

    // LEFT COLUMN - Main content.
    echo html_writer::start_div('col-lg-8');

    // Vacancy header card.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header d-flex justify-content-between align-items-center bg-primary text-white');
    echo html_writer::tag('span', $vacancy->code, ['class' => 'badge badge-light']);

    $typebadge = $vacancy->publicationtype === 'public'
        ? html_writer::tag('span', get_string('publicationtype:public', 'local_jobboard'), ['class' => 'badge badge-success'])
        : html_writer::tag('span', get_string('publicationtype:internal', 'local_jobboard'), ['class' => 'badge badge-info']);
    echo $typebadge;
    echo html_writer::end_div();

    echo html_writer::start_div('card-body');
    echo html_writer::tag('h2', format_string($vacancy->title), ['class' => 'card-title mb-3']);

    // Convocatoria info.
    if ($convocatoria) {
        echo html_writer::div(
            '<i class="fa fa-folder-open text-muted mr-2"></i>' .
            get_string('convocatoria', 'local_jobboard') . ': ' .
            html_writer::tag('strong', format_string($convocatoria->name)),
            'mb-3'
        );
    }

    // Quick details in badges.
    echo html_writer::start_div('mb-3');
    if (!empty($vacancy->location)) {
        echo html_writer::tag('span',
            '<i class="fa fa-map-marker-alt mr-1"></i>' . s($vacancy->location),
            ['class' => 'badge badge-light mr-2 mb-1']
        );
    }
    if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
        echo html_writer::tag('span',
            '<i class="fa fa-briefcase mr-1"></i>' . $contracttypes[$vacancy->contracttype],
            ['class' => 'badge badge-light mr-2 mb-1']
        );
    }
    echo html_writer::tag('span',
        '<i class="fa fa-users mr-1"></i>' . get_string('positions', 'local_jobboard') . ': ' . s($vacancy->positions),
        ['class' => 'badge badge-light mb-1']
    );
    echo html_writer::end_div();

    // Closing date warning.
    $daysremaining = max(0, (int) floor(($vacancy->closedate - time()) / 86400));
    $isurgent = $daysremaining <= 7;

    if ($isurgent) {
        echo html_writer::tag('div',
            '<i class="fa fa-exclamation-triangle mr-2"></i>' .
            get_string('closesin', 'local_jobboard', $daysremaining),
            ['class' => 'alert alert-warning']
        );
    }

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card

    // Description card.
    if (!empty($vacancy->description)) {
        echo html_writer::start_div('card shadow-sm mb-4');
        echo html_writer::tag('div',
            '<i class="fa fa-file-alt mr-2"></i>' . get_string('vacancydescription', 'local_jobboard'),
            ['class' => 'card-header bg-white font-weight-bold']
        );
        echo html_writer::tag('div', format_text($vacancy->description, FORMAT_HTML), ['class' => 'card-body']);
        echo html_writer::end_div();
    }

    // Requirements card.
    if (!empty($vacancy->requirements)) {
        echo html_writer::start_div('card shadow-sm mb-4');
        echo html_writer::tag('div',
            '<i class="fa fa-check-circle mr-2"></i>' . get_string('requirements', 'local_jobboard'),
            ['class' => 'card-header bg-white font-weight-bold']
        );
        echo html_writer::tag('div', format_text($vacancy->requirements, FORMAT_HTML), ['class' => 'card-body']);
        echo html_writer::end_div();
    }

    // Desirable card.
    if (!empty($vacancy->desirable)) {
        echo html_writer::start_div('card shadow-sm mb-4');
        echo html_writer::tag('div',
            '<i class="fa fa-star mr-2"></i>' . get_string('desirable', 'local_jobboard'),
            ['class' => 'card-header bg-white font-weight-bold']
        );
        echo html_writer::tag('div', format_text($vacancy->desirable, FORMAT_HTML), ['class' => 'card-body']);
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // col-lg-8

    // RIGHT COLUMN - Sidebar.
    echo html_writer::start_div('col-lg-4');

    // Apply CTA card.
    echo html_writer::start_div('card shadow-sm mb-4 border-primary');
    echo html_writer::tag('div',
        '<i class="fa fa-paper-plane mr-2"></i>' . get_string('readytoapply', 'local_jobboard'),
        ['class' => 'card-header bg-primary text-white font-weight-bold']
    );
    echo html_writer::start_div('card-body text-center');

    if ($isloggedin && has_capability('local/jobboard:apply', $context)) {
        // Check if already applied.
        $hasapplied = $DB->record_exists('local_jobboard_application', [
            'vacancyid' => $vacancy->id,
            'userid' => $USER->id,
        ]);

        if ($hasapplied) {
            echo html_writer::tag('p',
                '<i class="fa fa-check-circle fa-2x text-success mb-2"></i><br>' .
                get_string('alreadyapplied', 'local_jobboard'),
                ['class' => 'text-success']
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
            '<i class="fa fa-sign-in-alt mr-2"></i>' . get_string('loginandapply', 'local_jobboard') .
            '<span class="sr-only"> ' . get_string('opensnewwindow', 'local_jobboard') . '</span>',
            ['class' => 'btn btn-primary btn-lg btn-block', 'target' => '_blank', 'rel' => 'noopener noreferrer']
        );
    }

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Details card.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::tag('div',
        '<i class="fa fa-info-circle mr-2"></i>' . get_string('details', 'local_jobboard'),
        ['class' => 'card-header bg-white font-weight-bold']
    );
    echo html_writer::start_div('card-body');

    echo html_writer::start_tag('dl', ['class' => 'mb-0']);

    if (!empty($vacancy->duration)) {
        echo html_writer::tag('dt', get_string('duration', 'local_jobboard'), ['class' => 'text-muted small']);
        echo html_writer::tag('dd', s($vacancy->duration), ['class' => 'mb-2']);
    }

    if (!empty($vacancy->salary)) {
        echo html_writer::tag('dt', get_string('salary', 'local_jobboard'), ['class' => 'text-muted small']);
        echo html_writer::tag('dd', s($vacancy->salary), ['class' => 'mb-2']);
    }

    if (!empty($vacancy->department)) {
        echo html_writer::tag('dt', get_string('department', 'local_jobboard'), ['class' => 'text-muted small']);
        echo html_writer::tag('dd', s($vacancy->department), ['class' => 'mb-2']);
    }

    echo html_writer::tag('dt', get_string('opendate', 'local_jobboard'), ['class' => 'text-muted small']);
    echo html_writer::tag('dd', userdate($vacancy->opendate, get_string('strftimedate', 'langconfig')), ['class' => 'mb-2']);

    echo html_writer::tag('dt', get_string('closedate', 'local_jobboard'), ['class' => 'text-muted small']);
    $closeDateClass = $isurgent ? 'text-warning font-weight-bold' : '';
    echo html_writer::tag('dd', userdate($vacancy->closedate, get_string('strftimedate', 'langconfig')), ['class' => 'mb-0 ' . $closeDateClass]);

    echo html_writer::end_tag('dl');

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Share card.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::tag('div',
        '<i class="fa fa-share-alt mr-2"></i>' . get_string('share', 'local_jobboard'),
        ['class' => 'card-header bg-white font-weight-bold']
    );
    echo html_writer::start_div('card-body');

    $shareurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]))->out(false);
    $sharetitle = urlencode($vacancy->title);

    echo html_writer::start_div('d-flex justify-content-center');
    echo html_writer::link(
        'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($shareurl),
        '<i class="fa fa-linkedin"></i>',
        ['class' => 'btn btn-outline-primary btn-lg mx-1', 'target' => '_blank', 'title' => 'LinkedIn']
    );
    echo html_writer::link(
        'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareurl),
        '<i class="fa fa-facebook"></i>',
        ['class' => 'btn btn-outline-primary btn-lg mx-1', 'target' => '_blank', 'title' => 'Facebook']
    );
    echo html_writer::link(
        'https://twitter.com/intent/tweet?url=' . urlencode($shareurl) . '&text=' . $sharetitle,
        '<i class="fa fa-twitter"></i>',
        ['class' => 'btn btn-outline-primary btn-lg mx-1', 'target' => '_blank', 'title' => 'Twitter']
    );
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // col-lg-4

    echo html_writer::end_div(); // row

    echo html_writer::end_div(); // local-jobboard-public-detail

    echo $OUTPUT->footer();
}
