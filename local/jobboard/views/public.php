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
 * This view is accessible without authentication and displays public vacancies.
 * It is included by index.php and should not be accessed directly.
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
$publicationtype = optional_param('type', '', PARAM_ALPHA);
$vacancyid = optional_param('id', 0, PARAM_INT);

// Set page title from config or default.
$pagetitle = get_config('local_jobboard', 'public_page_title');
if (empty($pagetitle)) {
    $pagetitle = get_string('publicpagetitle', 'local_jobboard');
}
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);
$PAGE->set_pagelayout('standard');

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
$conditions = ["status = :status", "closedate >= :now"];
$params = ['status' => 'published', 'now' => time()];

// Filter by publication type.
if (!$canviewinternal) {
    // Not logged in or no capability - only show public vacancies.
    $conditions[] = "publicationtype = :pubtype";
    $params['pubtype'] = 'public';
} elseif (!empty($publicationtype) && in_array($publicationtype, ['public', 'internal'])) {
    // Logged in user filtering by type.
    $conditions[] = "publicationtype = :pubtype";
    $params['pubtype'] = $publicationtype;
}

// Filter by company (multi-tenant).
if ($isloggedin && function_exists('local_jobboard_get_user_companyid')) {
    $usercompanyid = local_jobboard_get_user_companyid($USER->id);
    if ($usercompanyid && !has_capability('local/jobboard:viewallvacancies', $context)) {
        $conditions[] = "(companyid IS NULL OR companyid = :usercompanyid)";
        $params['usercompanyid'] = $usercompanyid;
    }
}

// Search filter.
if (!empty($search)) {
    $searchterm = '%' . $DB->sql_like_escape($search) . '%';
    $conditions[] = "(" . $DB->sql_like('title', ':search1', false) .
                   " OR " . $DB->sql_like('code', ':search2', false) .
                   " OR " . $DB->sql_like('description', ':search3', false) . ")";
    $params['search1'] = $searchterm;
    $params['search2'] = $searchterm;
    $params['search3'] = $searchterm;
}

// Contract type filter.
if (!empty($contracttype)) {
    $conditions[] = "contracttype = :contracttype";
    $params['contracttype'] = $contracttype;
}

// Location filter.
if (!empty($location)) {
    $conditions[] = $DB->sql_like('location', ':location', false);
    $params['location'] = '%' . $DB->sql_like_escape($location) . '%';
}

// Build WHERE clause.
$where = 'WHERE ' . implode(' AND ', $conditions);

// Get total count.
$total = $DB->count_records_sql("SELECT COUNT(*) FROM {local_jobboard_vacancy} $where", $params);

// Get records.
$sql = "SELECT * FROM {local_jobboard_vacancy} $where ORDER BY closedate ASC, timecreated DESC";
$vacancies = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

// Get available filter options.
$contracttypes = local_jobboard_get_contract_types();
$locations = $DB->get_records_sql_menu(
    "SELECT DISTINCT location, location FROM {local_jobboard_vacancy}
     WHERE status = 'published' AND location IS NOT NULL AND location != ''
     ORDER BY location"
);

// Start output.
echo $OUTPUT->header();

// Page header with title and description.
echo html_writer::start_div('jobboard-public-header mb-4');
echo html_writer::tag('h1', $pagetitle, ['class' => 'h2']);

$description = get_config('local_jobboard', 'public_page_description');
if (!empty($description)) {
    echo html_writer::tag('p', format_text($description, FORMAT_HTML), ['class' => 'lead']);
}
echo html_writer::end_div();

// Search and filter form.
echo html_writer::start_tag('form', [
    'method' => 'get',
    'action' => new moodle_url('/local/jobboard/index.php'),
    'class' => 'mb-4',
]);
echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'view', 'value' => 'public']);

echo html_writer::start_div('row g-3');

// Search input.
echo html_writer::start_div('col-md-4');
echo html_writer::tag('label', get_string('search'), ['for' => 'search', 'class' => 'form-label']);
echo html_writer::empty_tag('input', [
    'type' => 'text',
    'name' => 'search',
    'id' => 'search',
    'value' => $search,
    'class' => 'form-control',
    'placeholder' => get_string('searchplaceholder', 'local_jobboard'),
]);
echo html_writer::end_div();

// Contract type filter.
echo html_writer::start_div('col-md-3');
echo html_writer::tag('label', get_string('contracttype', 'local_jobboard'), ['for' => 'contracttype', 'class' => 'form-label']);
$contractoptions = ['' => get_string('all')] + $contracttypes;
echo html_writer::select($contractoptions, 'contracttype', $contracttype, null, ['class' => 'form-select']);
echo html_writer::end_div();

// Location filter.
echo html_writer::start_div('col-md-3');
echo html_writer::tag('label', get_string('location', 'local_jobboard'), ['for' => 'location', 'class' => 'form-label']);
$locationoptions = ['' => get_string('all')] + $locations;
echo html_writer::select($locationoptions, 'location', $location, null, ['class' => 'form-select']);
echo html_writer::end_div();

// Type filter (only for authenticated users).
if ($canviewinternal) {
    echo html_writer::start_div('col-md-2');
    echo html_writer::tag('label', get_string('publicationtype', 'local_jobboard'), ['for' => 'type', 'class' => 'form-label']);
    $typeoptions = [
        '' => get_string('all'),
        'public' => get_string('publicationtype:public', 'local_jobboard'),
        'internal' => get_string('publicationtype:internal', 'local_jobboard'),
    ];
    echo html_writer::select($typeoptions, 'type', $publicationtype, null, ['class' => 'form-select']);
    echo html_writer::end_div();
}

echo html_writer::end_div(); // .row.

echo html_writer::start_div('row mt-3');
echo html_writer::start_div('col-12');
echo html_writer::tag('button', get_string('search'), [
    'type' => 'submit',
    'class' => 'btn btn-primary',
]);
if (!empty($search) || !empty($contracttype) || !empty($location) || !empty($publicationtype)) {
    echo ' ';
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        get_string('clearfilters', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary']
    );
}
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_tag('form');

// Results count.
echo html_writer::tag('p', get_string('vacanciesfound', 'local_jobboard', $total), ['class' => 'text-muted mb-3']);

// Vacancy cards.
if (empty($vacancies)) {
    echo $OUTPUT->notification(get_string('novacanciesfound', 'local_jobboard'), 'info');
} else {
    echo html_writer::start_div('row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4');

    foreach ($vacancies as $vacancy) {
        $daysremaining = max(0, (int) floor(($vacancy->closedate - time()) / 86400));
        $isurgent = $daysremaining <= 7;

        echo html_writer::start_div('col');
        echo html_writer::start_div('card h-100' . ($isurgent ? ' border-warning' : ''));

        // Card header with badges.
        echo html_writer::start_div('card-header d-flex justify-content-between align-items-center');
        echo html_writer::tag('span', $vacancy->code, ['class' => 'badge bg-secondary']);

        $typebadge = $vacancy->publicationtype === 'public'
            ? html_writer::tag('span', get_string('publicationtype:public', 'local_jobboard'), ['class' => 'badge bg-success'])
            : html_writer::tag('span', get_string('publicationtype:internal', 'local_jobboard'), ['class' => 'badge bg-info']);
        echo $typebadge;

        echo html_writer::end_div();

        // Card body.
        echo html_writer::start_div('card-body');

        echo html_writer::tag('h5', $vacancy->title, ['class' => 'card-title']);

        // Location and contract type.
        if (!empty($vacancy->location)) {
            echo html_writer::tag('p',
                html_writer::tag('i', '', ['class' => 'fa fa-map-marker me-1']) . s($vacancy->location),
                ['class' => 'card-text text-muted small mb-1']
            );
        }

        if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
            echo html_writer::tag('p',
                html_writer::tag('i', '', ['class' => 'fa fa-briefcase me-1']) . $contracttypes[$vacancy->contracttype],
                ['class' => 'card-text text-muted small mb-2']
            );
        }

        // Description excerpt.
        $vacancydescription = shorten_text(strip_tags($vacancy->description), 120);
        echo html_writer::tag('p', $vacancydescription, ['class' => 'card-text']);

        // Positions.
        echo html_writer::tag('p',
            html_writer::tag('strong', get_string('positions', 'local_jobboard') . ': ') . s($vacancy->positions),
            ['class' => 'card-text small']
        );

        echo html_writer::end_div(); // .card-body.

        // Card footer.
        echo html_writer::start_div('card-footer');

        // Closing date.
        $closingclass = $isurgent ? 'text-warning fw-bold' : 'text-muted';
        $closingicon = $isurgent ? 'fa-exclamation-triangle' : 'fa-calendar';
        echo html_writer::tag('small',
            html_writer::tag('i', '', ['class' => "fa $closingicon me-1"]) .
            get_string('closesin', 'local_jobboard', $daysremaining),
            ['class' => $closingclass]
        );

        echo html_writer::start_div('mt-2');

        // View details button.
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]),
            get_string('viewdetails', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-primary me-2']
        );

        // Apply button (if logged in).
        if ($isloggedin && has_capability('local/jobboard:apply', $context)) {
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
                get_string('apply', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-primary']
            );
        } elseif (!$isloggedin) {
            echo html_writer::link(
                new moodle_url('/login/index.php', ['wantsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]))->out(false)]),
                get_string('loginandapply', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-secondary']
            );
        }

        echo html_writer::end_div();
        echo html_writer::end_div(); // .card-footer.

        echo html_writer::end_div(); // .card.
        echo html_writer::end_div(); // .col.
    }

    echo html_writer::end_div(); // .row.

    // Pagination.
    $baseurl = new moodle_url('/local/jobboard/index.php', [
        'view' => 'public',
        'search' => $search,
        'contracttype' => $contracttype,
        'location' => $location,
        'type' => $publicationtype,
    ]);
    echo $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
}

// Call to action for non-logged in users.
if (!$isloggedin) {
    echo html_writer::start_div('card bg-light mt-4');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('h5', get_string('wanttoapply', 'local_jobboard'), ['class' => 'card-title']);
    echo html_writer::tag('p', get_string('createaccounttoapply', 'local_jobboard'), ['class' => 'card-text']);
    echo html_writer::link(
        new moodle_url('/local/jobboard/signup.php'),
        get_string('createaccount'),
        ['class' => 'btn btn-primary me-2']
    );
    echo html_writer::link(
        new moodle_url('/login/index.php'),
        get_string('login'),
        ['class' => 'btn btn-outline-primary']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo $OUTPUT->footer();

/**
 * Render public vacancy detail page.
 *
 * @param object $vacancy The vacancy record.
 * @param context $context The context.
 * @param bool $isloggedin Whether the user is logged in.
 */
function local_jobboard_render_public_vacancy_detail($vacancy, $context, $isloggedin) {
    global $OUTPUT, $PAGE;

    $contracttypes = local_jobboard_get_contract_types();

    $PAGE->set_title($vacancy->title);
    $PAGE->set_heading($vacancy->title);

    $PAGE->navbar->add(get_string('publicvacancies', 'local_jobboard'), new moodle_url('/local/jobboard/index.php', ['view' => 'public']));
    $PAGE->navbar->add($vacancy->title);

    echo $OUTPUT->header();

    // Back button.
    echo html_writer::start_div('mb-3');
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        '&laquo; ' . get_string('backtovacancies', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary']
    );
    echo html_writer::end_div();

    // Vacancy header.
    echo html_writer::start_div('card mb-4');
    echo html_writer::start_div('card-header d-flex justify-content-between align-items-center');
    echo html_writer::tag('span', $vacancy->code, ['class' => 'badge bg-secondary']);

    $typebadge = $vacancy->publicationtype === 'public'
        ? html_writer::tag('span', get_string('publicationtype:public', 'local_jobboard'), ['class' => 'badge bg-success'])
        : html_writer::tag('span', get_string('publicationtype:internal', 'local_jobboard'), ['class' => 'badge bg-info']);
    echo $typebadge;
    echo html_writer::end_div();

    echo html_writer::start_div('card-body');
    echo html_writer::tag('h2', $vacancy->title, ['class' => 'card-title']);

    // Quick details.
    echo html_writer::start_div('row mb-4');
    if (!empty($vacancy->location)) {
        echo html_writer::start_div('col-md-4');
        echo html_writer::tag('strong', get_string('location', 'local_jobboard') . ': ');
        echo s($vacancy->location);
        echo html_writer::end_div();
    }
    if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
        echo html_writer::start_div('col-md-4');
        echo html_writer::tag('strong', get_string('contracttype', 'local_jobboard') . ': ');
        echo $contracttypes[$vacancy->contracttype];
        echo html_writer::end_div();
    }
    echo html_writer::start_div('col-md-4');
    echo html_writer::tag('strong', get_string('positions', 'local_jobboard') . ': ');
    echo s($vacancy->positions);
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Closing date warning.
    $daysremaining = max(0, (int) floor(($vacancy->closedate - time()) / 86400));
    $isurgent = $daysremaining <= 7;

    if ($isurgent) {
        echo html_writer::tag('div',
            html_writer::tag('strong', get_string('closesin', 'local_jobboard', $daysremaining)),
            ['class' => 'alert alert-warning']
        );
    } else {
        echo html_writer::tag('p',
            html_writer::tag('strong', get_string('closedate', 'local_jobboard') . ': ') .
            userdate($vacancy->closedate, get_string('strftimedate', 'langconfig')),
            ['class' => 'text-muted']
        );
    }

    // Apply button.
    echo html_writer::start_div('mb-4');
    if ($isloggedin && has_capability('local/jobboard:apply', $context)) {
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
            get_string('apply', 'local_jobboard'),
            ['class' => 'btn btn-success btn-lg']
        );
    } elseif (!$isloggedin) {
        echo html_writer::link(
            new moodle_url('/login/index.php', ['wantsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]))->out(false)]),
            get_string('loginandapply', 'local_jobboard'),
            ['class' => 'btn btn-primary btn-lg']
        );
    }
    echo html_writer::end_div();

    echo html_writer::end_div(); // card-body.
    echo html_writer::end_div(); // card.

    // Description.
    if (!empty($vacancy->description)) {
        echo html_writer::start_div('card mb-4');
        echo html_writer::tag('div', get_string('vacancydescription', 'local_jobboard'), ['class' => 'card-header']);
        echo html_writer::tag('div', format_text($vacancy->description, FORMAT_HTML), ['class' => 'card-body']);
        echo html_writer::end_div();
    }

    // Requirements.
    if (!empty($vacancy->requirements)) {
        echo html_writer::start_div('card mb-4');
        echo html_writer::tag('div', get_string('requirements', 'local_jobboard'), ['class' => 'card-header']);
        echo html_writer::tag('div', format_text($vacancy->requirements, FORMAT_HTML), ['class' => 'card-body']);
        echo html_writer::end_div();
    }

    // Desirable.
    if (!empty($vacancy->desirable)) {
        echo html_writer::start_div('card mb-4');
        echo html_writer::tag('div', get_string('desirable', 'local_jobboard'), ['class' => 'card-header']);
        echo html_writer::tag('div', format_text($vacancy->desirable, FORMAT_HTML), ['class' => 'card-body']);
        echo html_writer::end_div();
    }

    // Additional details.
    echo html_writer::start_div('card mb-4');
    echo html_writer::tag('div', get_string('details', 'local_jobboard'), ['class' => 'card-header']);
    echo html_writer::start_div('card-body');

    echo html_writer::start_tag('dl', ['class' => 'row']);

    if (!empty($vacancy->duration)) {
        echo html_writer::tag('dt', get_string('duration', 'local_jobboard'), ['class' => 'col-sm-3']);
        echo html_writer::tag('dd', s($vacancy->duration), ['class' => 'col-sm-9']);
    }

    if (!empty($vacancy->salary)) {
        echo html_writer::tag('dt', get_string('salary', 'local_jobboard'), ['class' => 'col-sm-3']);
        echo html_writer::tag('dd', s($vacancy->salary), ['class' => 'col-sm-9']);
    }

    if (!empty($vacancy->department)) {
        echo html_writer::tag('dt', get_string('department', 'local_jobboard'), ['class' => 'col-sm-3']);
        echo html_writer::tag('dd', s($vacancy->department), ['class' => 'col-sm-9']);
    }

    echo html_writer::tag('dt', get_string('opendate', 'local_jobboard'), ['class' => 'col-sm-3']);
    echo html_writer::tag('dd', userdate($vacancy->opendate, get_string('strftimedate', 'langconfig')), ['class' => 'col-sm-9']);

    echo html_writer::tag('dt', get_string('closedate', 'local_jobboard'), ['class' => 'col-sm-3']);
    echo html_writer::tag('dd', userdate($vacancy->closedate, get_string('strftimedate', 'langconfig')), ['class' => 'col-sm-9']);

    echo html_writer::end_tag('dl');

    echo html_writer::end_div(); // card-body.
    echo html_writer::end_div(); // card.

    // Share buttons.
    echo html_writer::start_div('card mb-4');
    echo html_writer::tag('div', get_string('share', 'local_jobboard'), ['class' => 'card-header']);
    echo html_writer::start_div('card-body');

    $shareurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]))->out(false);
    $sharetitle = urlencode($vacancy->title);

    echo html_writer::link(
        'https://www.linkedin.com/sharing/share-offsite/?url=' . urlencode($shareurl),
        'LinkedIn',
        ['class' => 'btn btn-outline-primary me-2', 'target' => '_blank']
    );
    echo html_writer::link(
        'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($shareurl),
        'Facebook',
        ['class' => 'btn btn-outline-primary me-2', 'target' => '_blank']
    );
    echo html_writer::link(
        'https://twitter.com/intent/tweet?url=' . urlencode($shareurl) . '&text=' . $sharetitle,
        'Twitter',
        ['class' => 'btn btn-outline-primary', 'target' => '_blank']
    );

    echo html_writer::end_div(); // card-body.
    echo html_writer::end_div(); // card.

    echo $OUTPUT->footer();
}
