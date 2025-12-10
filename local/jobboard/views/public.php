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
 * This view has three modes:
 * 1. Without convocatoriaid: Shows convocatorias as cards with options to view details or vacancies
 * 2. With convocatoriaid: Shows vacancies for that convocatoria with filters
 * 3. With id (vacancy): Shows detailed vacancy view with apply options
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

// Get parameters.
$convocatoriaid = optional_param('convocatoriaid', 0, PARAM_INT);
$vacancyid = optional_param('id', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);

// Filter parameters for vacancies.
$filtercontract = optional_param('contracttype', '', PARAM_ALPHANUMEXT);
$filterlocation = optional_param('location', '', PARAM_TEXT);
$filtersearch = optional_param('search', '', PARAM_TEXT);

// Check if user is logged in.
$isloggedin = isloggedin() && !isguestuser();

// User capabilities.
$canviewinternal = $isloggedin && has_capability('local/jobboard:viewinternalvacancies', $context);
$canapply = $isloggedin && has_capability('local/jobboard:apply', $context);

// Set page title from config or default.
$pagetitle = get_config('local_jobboard', 'public_page_title');
if (empty($pagetitle)) {
    $pagetitle = get_string('publicpagetitle', 'local_jobboard');
}
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);
$PAGE->set_pagelayout('standard');
$PAGE->requires->css('/local/jobboard/styles.css');

// Get contract types for display.
$contracttypes = local_jobboard_get_contract_types();

// ============================================================================
// MODE: SHOW SINGLE VACANCY DETAIL (REDESIGNED)
// ============================================================================
if ($vacancyid > 0) {
    // Get the vacancy.
    $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid]);

    if (!$vacancy || $vacancy->status !== 'published') {
        throw new moodle_exception('error:vacancynotfound', 'local_jobboard');
    }

    // Check if vacancy is viewable (public or user has internal view capability).
    if ($vacancy->publicationtype !== 'public' && !$canviewinternal) {
        throw new moodle_exception('error:vacancynotpublic', 'local_jobboard');
    }

    // Get convocatoria info.
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $vacancy->convocatoriaid]);

    // Page title.
    $PAGE->set_title($vacancy->title);
    $PAGE->set_heading($vacancy->title);

    // Calculate days remaining.
    $closedate = $convocatoria ? $convocatoria->enddate : ($vacancy->closedate ?? time());
    $daysRemaining = max(0, (int) floor(($closedate - time()) / 86400));
    $isUrgent = $daysRemaining <= 7;

    // Check if user has already applied.
    $hasApplied = false;
    $userApplication = null;
    if ($isloggedin) {
        $userApplication = $DB->get_record('local_jobboard_application', [
            'vacancyid' => $vacancy->id,
            'userid' => $USER->id,
        ]);
        $hasApplied = !empty($userApplication);
    }

    echo $OUTPUT->header();
    echo html_writer::start_div('local-jobboard-dashboard');

    // ========================================================================
    // BREADCRUMB NAVIGATION
    // ========================================================================
    echo html_writer::start_div('d-flex flex-wrap align-items-center mb-4');
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        '<i class="fa fa-home mr-1"></i>' . get_string('publicpagetitle', 'local_jobboard'),
        ['class' => 'btn btn-sm btn-outline-secondary mr-2 mb-2']
    );
    if ($convocatoria) {
        echo html_writer::tag('span', '<i class="fa fa-chevron-right text-muted mx-2"></i>', ['class' => 'mb-2']);
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoria->id]),
            '<i class="fa fa-folder-open mr-1"></i>' . s($convocatoria->name),
            ['class' => 'btn btn-sm btn-outline-secondary mr-2 mb-2']
        );
    }
    echo html_writer::tag('span', '<i class="fa fa-chevron-right text-muted mx-2"></i>', ['class' => 'mb-2']);
    echo html_writer::tag('span', s($vacancy->title), ['class' => 'text-muted mb-2']);
    echo html_writer::end_div();

    // ========================================================================
    // HERO SECTION
    // ========================================================================
    $heroBg = $isUrgent ? 'bg-warning' : 'bg-primary';
    $heroText = $isUrgent ? 'text-dark' : 'text-white';
    echo html_writer::start_div("jb-vacancy-hero $heroBg $heroText rounded-lg p-4 mb-4");
    echo html_writer::start_div('row align-items-center');

    // Left side - Vacancy info.
    echo html_writer::start_div('col-lg-8');
    echo html_writer::start_div('d-flex align-items-start');
    echo html_writer::start_div('mr-3 d-none d-md-block');
    echo html_writer::tag('div',
        '<i class="fa fa-briefcase fa-3x"></i>',
        ['class' => 'rounded-circle bg-white text-primary d-flex align-items-center justify-content-center', 'style' => 'width: 80px; height: 80px;']
    );
    echo html_writer::end_div();
    echo html_writer::start_div();
    echo html_writer::tag('code', s($vacancy->code), ['class' => 'badge badge-light mb-2']);
    echo html_writer::tag('h1', s($vacancy->title), ['class' => 'h2 mb-2 font-weight-bold']);
    if ($convocatoria) {
        echo html_writer::tag('p',
            '<i class="fa fa-folder-open mr-2"></i>' . s($convocatoria->name),
            ['class' => 'mb-0 opacity-75']
        );
    }
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Right side - Quick stats.
    echo html_writer::start_div('col-lg-4 mt-3 mt-lg-0');
    echo html_writer::start_div('d-flex flex-wrap justify-content-lg-end');
    echo html_writer::tag('span',
        '<i class="fa fa-users mr-1"></i>' . $vacancy->positions . ' ' . get_string('positions', 'local_jobboard'),
        ['class' => 'badge badge-light mr-2 mb-2 p-2']
    );
    if ($isUrgent) {
        echo html_writer::tag('span',
            '<i class="fa fa-exclamation-triangle mr-1"></i>' . get_string('closesindays', 'local_jobboard', $daysRemaining),
            ['class' => 'badge badge-danger p-2 mb-2']
        );
    } else {
        echo html_writer::tag('span',
            '<i class="fa fa-calendar-alt mr-1"></i>' . userdate($closedate, get_string('strftimedate', 'langconfig')),
            ['class' => 'badge badge-light p-2 mb-2']
        );
    }
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();

    // ========================================================================
    // MAIN CONTENT
    // ========================================================================
    echo html_writer::start_div('row');

    // Left column - Vacancy details.
    echo html_writer::start_div('col-lg-8');

    // Quick info cards.
    echo html_writer::start_div('row mb-4');

    // Location.
    $locationText = '-';
    if (!empty($vacancy->companyid)) {
        $companyName = local_jobboard_get_company_name($vacancy->companyid);
        if (!empty($companyName)) {
            $locationText = s($companyName);
        }
    } elseif (!empty($vacancy->location)) {
        $locationText = s($vacancy->location);
    }
    echo html_writer::start_div('col-md-6 col-lg-3 mb-3');
    echo html_writer::start_div('card h-100 border-0 bg-light');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('i', '', ['class' => 'fa fa-map-marker-alt fa-2x text-primary mb-2']);
    echo html_writer::tag('h6', get_string('location', 'local_jobboard'), ['class' => 'text-muted small mb-1']);
    echo html_writer::tag('p', $locationText, ['class' => 'font-weight-bold mb-0']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Contract type.
    $contractLabel = '-';
    if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
        $contractLabel = $contracttypes[$vacancy->contracttype];
    }
    echo html_writer::start_div('col-md-6 col-lg-3 mb-3');
    echo html_writer::start_div('card h-100 border-0 bg-light');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('i', '', ['class' => 'fa fa-file-contract fa-2x text-success mb-2']);
    echo html_writer::tag('h6', get_string('contracttype', 'local_jobboard'), ['class' => 'text-muted small mb-1']);
    echo html_writer::tag('p', $contractLabel, ['class' => 'font-weight-bold mb-0']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Positions.
    echo html_writer::start_div('col-md-6 col-lg-3 mb-3');
    echo html_writer::start_div('card h-100 border-0 bg-light');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('i', '', ['class' => 'fa fa-users fa-2x text-info mb-2']);
    echo html_writer::tag('h6', get_string('positions', 'local_jobboard'), ['class' => 'text-muted small mb-1']);
    echo html_writer::tag('p', $vacancy->positions, ['class' => 'font-weight-bold mb-0']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Close date.
    echo html_writer::start_div('col-md-6 col-lg-3 mb-3');
    echo html_writer::start_div('card h-100 border-0 ' . ($isUrgent ? 'bg-warning' : 'bg-light'));
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('i', '', ['class' => 'fa fa-calendar-times fa-2x ' . ($isUrgent ? 'text-dark' : 'text-warning') . ' mb-2']);
    echo html_writer::tag('h6', get_string('closedate', 'local_jobboard'), ['class' => ($isUrgent ? 'text-dark' : 'text-muted') . ' small mb-1']);
    echo html_writer::tag('p', userdate($closedate, get_string('strftimedate', 'langconfig')), ['class' => 'font-weight-bold mb-0']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();

    // Description card.
    if (!empty($vacancy->description)) {
        echo html_writer::start_div('card shadow-sm mb-4');
        echo html_writer::start_div('card-header bg-white');
        echo html_writer::tag('h5',
            '<i class="fa fa-info-circle text-primary mr-2"></i>' . get_string('description'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');
        echo format_text($vacancy->description, FORMAT_HTML);
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    // Requirements card.
    if (!empty($vacancy->requirements)) {
        echo html_writer::start_div('card shadow-sm mb-4');
        echo html_writer::start_div('card-header bg-white');
        echo html_writer::tag('h5',
            '<i class="fa fa-list-check text-primary mr-2"></i>' . get_string('requirements', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');
        echo format_text($vacancy->requirements, FORMAT_HTML);
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    // Convocatoria info card.
    if ($convocatoria) {
        echo html_writer::start_div('card shadow-sm mb-4 border-info');
        echo html_writer::start_div('card-header bg-info text-white');
        echo html_writer::tag('h5',
            '<i class="fa fa-folder-open mr-2"></i>' . get_string('convocatoria', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('d-flex justify-content-between align-items-start');
        echo html_writer::start_div();
        echo html_writer::tag('code', s($convocatoria->code), ['class' => 'badge badge-secondary mr-2']);
        echo html_writer::tag('strong', s($convocatoria->name));
        if (!empty($convocatoria->description)) {
            $excerpt = strip_tags($convocatoria->description);
            if (strlen($excerpt) > 200) {
                $excerpt = substr($excerpt, 0, 200) . '...';
            }
            echo html_writer::tag('p', $excerpt, ['class' => 'text-muted small mt-2 mb-0']);
        }
        echo html_writer::end_div();
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $convocatoria->id]),
            '<i class="fa fa-external-link-alt mr-1"></i>' . get_string('viewdetails', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-light']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // col-lg-8

    // Right column - Action sidebar.
    echo html_writer::start_div('col-lg-4');

    // Application status card (for logged-in users who have applied).
    if ($hasApplied && $userApplication) {
        $statusColor = 'info';
        switch ($userApplication->status) {
            case 'docs_validated':
            case 'selected':
                $statusColor = 'success';
                break;
            case 'docs_rejected':
            case 'rejected':
                $statusColor = 'danger';
                break;
            case 'interview':
                $statusColor = 'warning';
                break;
            case 'withdrawn':
                $statusColor = 'secondary';
                break;
        }

        echo html_writer::start_div('card shadow-sm mb-4 border-' . $statusColor);
        echo html_writer::start_div('card-header bg-' . $statusColor . ' text-white');
        echo html_writer::tag('h5',
            '<i class="fa fa-check-circle mr-2"></i>' . get_string('youhaveapplied', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');
        echo html_writer::tag('p',
            get_string('applicationstatus', 'local_jobboard') . ': ' .
            html_writer::tag('strong', get_string('status_' . $userApplication->status, 'local_jobboard')),
            ['class' => 'mb-3']
        );
        echo html_writer::tag('p',
            '<i class="fa fa-calendar mr-2"></i>' .
            get_string('dateapplied', 'local_jobboard') . ': ' .
            userdate($userApplication->timecreated, get_string('strftimedate', 'langconfig')),
            ['class' => 'text-muted small mb-3']
        );
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $userApplication->id]),
            '<i class="fa fa-eye mr-2"></i>' . get_string('viewmyapplication', 'local_jobboard'),
            ['class' => 'btn btn-' . $statusColor . ' btn-block']
        );
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
            '<i class="fa fa-list mr-2"></i>' . get_string('viewallapplications', 'local_jobboard'),
            ['class' => 'btn btn-outline-secondary btn-block mt-2']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
    } else {
        // Apply card.
        echo html_writer::start_div('card shadow-sm mb-4 border-primary');
        echo html_writer::start_div('card-header bg-primary text-white');
        echo html_writer::tag('h5',
            '<i class="fa fa-paper-plane mr-2"></i>' . get_string('applyforposition', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body text-center');

        if ($canapply) {
            echo html_writer::tag('p', get_string('applynow_desc', 'local_jobboard'), ['class' => 'text-muted mb-4']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
                '<i class="fa fa-paper-plane mr-2"></i>' . get_string('applynow', 'local_jobboard'),
                ['class' => 'btn btn-primary btn-lg btn-block']
            );
        } elseif ($isloggedin) {
            // User is logged in but doesn't have apply capability.
            echo html_writer::tag('p', get_string('noapplycapability', 'local_jobboard'), ['class' => 'text-muted mb-3']);
        } else {
            // User is not logged in.
            echo html_writer::tag('i', '', ['class' => 'fa fa-user-circle fa-4x text-muted mb-3']);
            echo html_writer::tag('p', get_string('loginrequiredtoapply', 'local_jobboard'), ['class' => 'text-muted mb-4']);

            $wantsurl = (new moodle_url('/local/jobboard/index.php', [
                'view' => 'apply',
                'vacancyid' => $vacancy->id,
            ]))->out(false);

            echo html_writer::link(
                new moodle_url('/login/index.php', ['wantsurl' => $wantsurl]),
                '<i class="fa fa-sign-in-alt mr-2"></i>' . get_string('login'),
                ['class' => 'btn btn-primary btn-lg btn-block mb-2']
            );
            echo html_writer::link(
                new moodle_url('/local/jobboard/signup.php'),
                '<i class="fa fa-user-plus mr-2"></i>' . get_string('createaccount'),
                ['class' => 'btn btn-outline-primary btn-block']
            );
        }

        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    // Quick links card.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-link text-primary mr-2"></i>' . get_string('quicklinks', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body p-0');
    echo html_writer::start_tag('ul', ['class' => 'list-group list-group-flush']);

    // Back to convocatoria.
    if ($convocatoria) {
        echo html_writer::start_tag('li', ['class' => 'list-group-item']);
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoria->id]),
            '<i class="fa fa-list mr-2 text-muted"></i>' . get_string('othervacancies', 'local_jobboard'),
            ['class' => 'text-dark']
        );
        echo html_writer::end_tag('li');
    }

    // All convocatorias.
    echo html_writer::start_tag('li', ['class' => 'list-group-item']);
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        '<i class="fa fa-folder-open mr-2 text-muted"></i>' . get_string('allconvocatorias', 'local_jobboard'),
        ['class' => 'text-dark']
    );
    echo html_writer::end_tag('li');

    // My applications (for logged-in users).
    if ($isloggedin) {
        echo html_writer::start_tag('li', ['class' => 'list-group-item']);
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
            '<i class="fa fa-clipboard-list mr-2 text-muted"></i>' . get_string('myapplications', 'local_jobboard'),
            ['class' => 'text-dark']
        );
        echo html_writer::end_tag('li');
    }

    echo html_writer::end_tag('ul');
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Share card.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-share-alt text-primary mr-2"></i>' . get_string('share', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    $shareUrl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]))->out(false);
    echo html_writer::tag('input', '', [
        'type' => 'text',
        'class' => 'form-control form-control-sm',
        'value' => $shareUrl,
        'readonly' => 'readonly',
        'onclick' => 'this.select();',
    ]);
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // col-lg-4
    echo html_writer::end_div(); // row

    echo html_writer::end_div(); // local-jobboard-dashboard
    echo $OUTPUT->footer();
    exit;
}

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

    // Calculate stats.
    $daysRemaining = max(0, (int) floor(($convocatoria->enddate - time()) / 86400));
    $isUrgent = $daysRemaining <= 7;
    $totalPositions = 0;
    $locationsList = [];
    $contractTypesList = [];
    foreach ($allVacanciesForStats as $v) {
        $totalPositions += $v->positions;
        if (!empty($v->location) && !in_array($v->location, $locationsList)) {
            $locationsList[] = $v->location;
        }
        if (!empty($v->contracttype)) {
            $contractTypesList[$v->contracttype] = $contracttypes[$v->contracttype] ?? $v->contracttype;
        }
    }

    echo $OUTPUT->header();
    echo html_writer::start_div('local-jobboard-dashboard');

    // Breadcrumb navigation.
    echo html_writer::start_div('mb-4');
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtoconvocatorias', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary']
    );
    echo html_writer::end_div();

    // Convocatoria info card.
    $infoBorderClass = $isUrgent ? 'border-warning' : 'border-primary';
    echo html_writer::start_div('card shadow-sm mb-4 ' . $infoBorderClass);

    $infoHeaderClass = $isUrgent ? 'card-header bg-warning text-dark' : 'card-header bg-primary text-white';
    echo html_writer::start_div($infoHeaderClass);
    echo html_writer::start_div('d-flex justify-content-between align-items-center flex-wrap');
    echo html_writer::start_div();
    echo html_writer::tag('code', s($convocatoria->code), ['class' => 'badge badge-light mr-2']);
    echo html_writer::tag('h5', s($convocatoria->name), ['class' => 'd-inline mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('d-flex align-items-center mt-2 mt-md-0');
    echo html_writer::tag('span',
        '<i class="fa fa-briefcase mr-1"></i>' . count($allVacanciesForStats) . ' ' . get_string('vacancies', 'local_jobboard'),
        ['class' => 'badge badge-light mr-2']
    );
    echo html_writer::tag('span',
        '<i class="fa fa-users mr-1"></i>' . $totalPositions . ' ' . get_string('positions', 'local_jobboard'),
        ['class' => 'badge badge-light mr-2']
    );
    if ($isUrgent) {
        echo html_writer::tag('span',
            '<i class="fa fa-exclamation-triangle mr-1"></i>' . get_string('closesindays', 'local_jobboard', $daysRemaining),
            ['class' => 'badge badge-danger']
        );
    }
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::start_div('card-body py-3');
    echo html_writer::start_div('d-flex flex-wrap align-items-center');
    echo html_writer::tag('span',
        '<i class="fa fa-calendar-check text-success mr-2"></i>' .
        get_string('startdate', 'local_jobboard') . ': ' .
        html_writer::tag('strong', userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig'))),
        ['class' => 'mr-4 small']
    );
    $endDateClass = $isUrgent ? 'text-danger' : '';
    echo html_writer::tag('span',
        '<i class="fa fa-calendar-times text-warning mr-2"></i>' .
        get_string('enddate', 'local_jobboard') . ': ' .
        html_writer::tag('strong', userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig')), ['class' => $endDateClass]),
        ['class' => 'mr-4 small']
    );
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $convocatoria->id]),
        '<i class="fa fa-info-circle mr-1"></i>' . get_string('viewdetails', 'local_jobboard'),
        ['class' => 'btn btn-sm btn-outline-info ml-auto']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // card

    // ========================================================================
    // FILTERS SECTION
    // ========================================================================
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-body py-3');
    echo html_writer::start_tag('form', [
        'method' => 'get',
        'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
        'class' => 'jb-filter-form',
    ]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'view', 'value' => 'public']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'convocatoriaid', 'value' => $convocatoriaid]);

    echo html_writer::start_div('row align-items-end');

    // Search input.
    echo html_writer::start_div('col-md-4 mb-2 mb-md-0');
    echo html_writer::tag('label', get_string('search'), ['for' => 'filter-search', 'class' => 'small text-muted']);
    echo html_writer::empty_tag('input', [
        'type' => 'text',
        'name' => 'search',
        'id' => 'filter-search',
        'class' => 'form-control form-control-sm',
        'value' => $filtersearch,
        'placeholder' => get_string('searchvacancies', 'local_jobboard'),
    ]);
    echo html_writer::end_div();

    // Contract type filter.
    if (!empty($contractTypesList)) {
        echo html_writer::start_div('col-md-3 mb-2 mb-md-0');
        echo html_writer::tag('label', get_string('contracttype', 'local_jobboard'), ['for' => 'filter-contracttype', 'class' => 'small text-muted']);
        echo html_writer::start_tag('select', ['name' => 'contracttype', 'id' => 'filter-contracttype', 'class' => 'form-control form-control-sm']);
        echo html_writer::tag('option', get_string('all'), ['value' => '']);
        foreach ($contractTypesList as $key => $label) {
            $selected = ($filtercontract === $key) ? ' selected="selected"' : '';
            echo '<option value="' . s($key) . '"' . $selected . '>' . s($label) . '</option>';
        }
        echo html_writer::end_tag('select');
        echo html_writer::end_div();
    }

    // Location filter.
    if (!empty($locationsList)) {
        echo html_writer::start_div('col-md-3 mb-2 mb-md-0');
        echo html_writer::tag('label', get_string('location', 'local_jobboard'), ['for' => 'filter-location', 'class' => 'small text-muted']);
        echo html_writer::start_tag('select', ['name' => 'location', 'id' => 'filter-location', 'class' => 'form-control form-control-sm']);
        echo html_writer::tag('option', get_string('all'), ['value' => '']);
        foreach ($locationsList as $loc) {
            $selected = ($filterlocation === $loc) ? ' selected="selected"' : '';
            echo '<option value="' . s($loc) . '"' . $selected . '>' . s($loc) . '</option>';
        }
        echo html_writer::end_tag('select');
        echo html_writer::end_div();
    }

    // Submit button.
    echo html_writer::start_div('col-md-2 mb-2 mb-md-0');
    echo html_writer::tag('label', '&nbsp;', ['class' => 'small d-block']);
    echo html_writer::tag('button',
        '<i class="fa fa-search mr-1"></i>' . get_string('filter', 'local_jobboard'),
        ['type' => 'submit', 'class' => 'btn btn-primary btn-sm btn-block']
    );
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_tag('form');

    // Clear filters link.
    if (!empty($filtercontract) || !empty($filterlocation) || !empty($filtersearch)) {
        echo html_writer::start_div('mt-2');
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoriaid]),
            '<i class="fa fa-times mr-1"></i>' . get_string('clearfilters', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-secondary']
        );
        echo html_writer::end_div();
    }

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Results count.
    echo html_writer::tag('p',
        get_string('vacanciesfound', 'local_jobboard', $totalVacancies),
        ['class' => 'text-muted small mb-3']
    );

    // Vacancies grid.
    echo html_writer::tag('h5',
        '<i class="fa fa-briefcase mr-2 text-primary"></i>' . get_string('vacancies', 'local_jobboard'),
        ['class' => 'mb-3']
    );

    if (empty($vacancies)) {
        echo ui_helper::empty_state(
            get_string('novacanciesfound', 'local_jobboard'),
            'briefcase',
            (!empty($filtercontract) || !empty($filterlocation) || !empty($filtersearch)) ? [
                'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoriaid]),
                'label' => get_string('clearfilters', 'local_jobboard'),
                'class' => 'btn btn-outline-secondary',
            ] : null
        );
    } else {
        echo html_writer::start_div('row');

        foreach ($vacancies as $vacancy) {
            $vacDaysRemaining = $daysRemaining;
            $isVacUrgent = $isUrgent;

            // Check if user has applied.
            $vacHasApplied = false;
            if ($isloggedin) {
                $vacHasApplied = $DB->record_exists('local_jobboard_application', [
                    'vacancyid' => $vacancy->id,
                    'userid' => $USER->id,
                ]);
            }

            // Card column.
            echo html_writer::start_div('col-lg-4 col-md-6 mb-4');

            $cardClass = 'card h-100 shadow-sm jb-vacancy-card';
            if ($isVacUrgent) {
                $cardClass .= ' border-warning';
            }

            echo html_writer::start_div($cardClass);

            // Card header.
            echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center py-2');
            echo html_writer::tag('code', s($vacancy->code), ['class' => 'small text-muted']);

            $typeBadge = $vacancy->publicationtype === 'public'
                ? '<span class="badge badge-success">' . get_string('public', 'local_jobboard') . '</span>'
                : '<span class="badge badge-secondary">' . get_string('internal', 'local_jobboard') . '</span>';
            echo html_writer::tag('div', $typeBadge);
            echo html_writer::end_div();

            // Card body.
            echo html_writer::start_div('card-body');

            // Title.
            echo html_writer::tag('h5', s($vacancy->title), ['class' => 'card-title mb-3']);

            // Details.
            echo html_writer::start_div('small');

            // Location.
            $locationText = '-';
            if (!empty($vacancy->companyid)) {
                $companyName = local_jobboard_get_company_name($vacancy->companyid);
                if (!empty($companyName)) {
                    $locationText = s($companyName);
                }
            } elseif (!empty($vacancy->location)) {
                $locationText = s($vacancy->location);
            }
            echo html_writer::div(
                '<i class="fa fa-map-marker-alt text-muted mr-2"></i>' . $locationText,
                'mb-1'
            );

            // Contract type.
            if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
                echo html_writer::div(
                    '<i class="fa fa-file-contract text-muted mr-2"></i>' . $contracttypes[$vacancy->contracttype],
                    'mb-1'
                );
            }

            // Positions.
            echo html_writer::div(
                '<i class="fa fa-users text-muted mr-2"></i>' . get_string('positions', 'local_jobboard') . ': ' .
                html_writer::tag('strong', $vacancy->positions),
                'mb-1'
            );

            echo html_writer::end_div();
            echo html_writer::end_div(); // card-body

            // Card footer.
            echo html_writer::start_div('card-footer bg-white');

            // Close date.
            echo html_writer::start_div('d-flex justify-content-between align-items-center mb-2');
            $closeDateClass = $isVacUrgent ? 'text-warning font-weight-bold' : 'text-muted';
            echo html_writer::tag('small',
                '<i class="fa fa-calendar-times mr-1"></i>' . userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig')),
                ['class' => $closeDateClass]
            );
            if ($isVacUrgent) {
                echo html_writer::tag('span',
                    get_string('closesindays', 'local_jobboard', $vacDaysRemaining),
                    ['class' => 'badge badge-warning small']
                );
            }
            echo html_writer::end_div();

            // Action buttons.
            echo html_writer::start_div('d-flex justify-content-between');

            // View details.
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]),
                '<i class="fa fa-eye mr-1"></i>' . get_string('viewdetails', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-primary']
            );

            // Apply button.
            if ($vacHasApplied) {
                echo html_writer::tag('span',
                    '<i class="fa fa-check mr-1"></i>' . get_string('applied', 'local_jobboard'),
                    ['class' => 'btn btn-sm btn-success disabled']
                );
            } elseif ($canapply) {
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
                    '<i class="fa fa-sign-in-alt mr-1"></i>' . get_string('login'),
                    ['class' => 'btn btn-sm btn-outline-secondary']
                );
            }

            echo html_writer::end_div();
            echo html_writer::end_div(); // card-footer

            echo html_writer::end_div(); // card
            echo html_writer::end_div(); // col
        }

        echo html_writer::end_div(); // row

        // Pagination.
        if ($totalVacancies > $perpage) {
            $baseurl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'public',
                'convocatoriaid' => $convocatoriaid,
                'perpage' => $perpage,
                'contracttype' => $filtercontract,
                'location' => $filterlocation,
                'search' => $filtersearch,
            ]);
            echo $OUTPUT->paging_bar($totalVacancies, $page, $perpage, $baseurl);
        }
    }

    // CTA for non-logged in users.
    if (!$isloggedin && !empty($vacancies)) {
        echo html_writer::start_div('card bg-light border-0 mt-4');
        echo html_writer::start_div('card-body text-center py-4');
        echo html_writer::tag('i', '', ['class' => 'fa fa-user-plus fa-2x text-primary mb-2']);
        echo html_writer::tag('h5', get_string('wanttoapply', 'local_jobboard'), ['class' => 'mb-2']);
        echo html_writer::tag('p', get_string('createaccounttoapply', 'local_jobboard'), ['class' => 'text-muted mb-3']);
        echo html_writer::start_div('d-flex justify-content-center');
        echo html_writer::link(
            new moodle_url('/local/jobboard/signup.php'),
            '<i class="fa fa-user-plus mr-2"></i>' . get_string('createaccount'),
            ['class' => 'btn btn-primary mr-2']
        );
        echo html_writer::link(
            new moodle_url('/login/index.php'),
            '<i class="fa fa-sign-in-alt mr-2"></i>' . get_string('login'),
            ['class' => 'btn btn-outline-secondary']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // local-jobboard-dashboard
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

// Calculate statistics.
$totalConvocatorias = count($convocatorias);
$totalVacancies = 0;
$totalPositions = 0;
$urgentCount = 0;

foreach ($convocatorias as $conv) {
    $totalVacancies += $conv->vacancy_count;
    $totalPositions += $conv->total_positions ?? 0;
    $daysRemaining = max(0, (int) floor(($conv->enddate - time()) / 86400));
    if ($daysRemaining <= 7) {
        $urgentCount++;
    }
}

// Start output.
echo $OUTPUT->header();
echo html_writer::start_div('local-jobboard-dashboard');

// ============================================================================
// WELCOME HEADER
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

// Quick access for logged-in users.
if ($isloggedin) {
    echo html_writer::start_div('mt-3 pt-3 border-top border-light');
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
        '<i class="fa fa-clipboard-list mr-2"></i>' . get_string('viewmyapplications', 'local_jobboard'),
        ['class' => 'btn btn-light btn-sm']
    );
    echo html_writer::end_div();
}

echo html_writer::end_div();

// ============================================================================
// STATISTICS ROW
// ============================================================================
echo html_writer::start_div('row mb-4');

echo ui_helper::stat_card(
    $totalConvocatorias,
    get_string('activeconvocatorias', 'local_jobboard'),
    'primary', 'folder-open'
);

echo ui_helper::stat_card(
    $totalVacancies,
    get_string('openvacancies', 'local_jobboard'),
    'success', 'briefcase'
);

echo ui_helper::stat_card(
    $totalPositions,
    get_string('totalpositions', 'local_jobboard'),
    'info', 'users'
);

echo ui_helper::stat_card(
    $urgentCount,
    get_string('closingsoon', 'local_jobboard'),
    'warning', 'clock'
);

echo html_writer::end_div();

// ============================================================================
// CONVOCATORIAS GRID
// ============================================================================
if (empty($convocatorias)) {
    echo ui_helper::empty_state(
        get_string('noconvocatorias', 'local_jobboard'),
        'folder-open',
        $isloggedin ? null : [
            'url' => new moodle_url('/login/index.php'),
            'label' => get_string('login'),
            'class' => 'btn btn-primary',
        ]
    );
} else {
    echo html_writer::start_div('row');

    foreach ($convocatorias as $conv) {
        $daysRemaining = max(0, (int) floor(($conv->enddate - time()) / 86400));
        $isUrgent = $daysRemaining <= 7;

        // Card column.
        echo html_writer::start_div('col-lg-4 col-md-6 mb-4');

        $cardClass = 'card h-100 shadow-sm jb-convocatoria-card';
        if ($isUrgent) {
            $cardClass .= ' border-warning';
        }

        echo html_writer::start_div($cardClass);

        // Card header.
        $headerClass = $isUrgent ? 'card-header bg-warning text-dark py-2' : 'card-header bg-primary text-white py-2';
        echo html_writer::start_div($headerClass);
        echo html_writer::start_div('d-flex justify-content-between align-items-center');
        echo html_writer::tag('code', s($conv->code), ['class' => 'badge badge-light']);
        if ($isUrgent) {
            echo html_writer::tag('span',
                '<i class="fa fa-exclamation-triangle mr-1"></i>' . get_string('closesindays', 'local_jobboard', $daysRemaining),
                ['class' => 'badge badge-danger small']
            );
        } else {
            echo html_writer::tag('span',
                get_string('convocatoria_status_open', 'local_jobboard'),
                ['class' => 'badge badge-light small']
            );
        }
        echo html_writer::end_div();
        echo html_writer::end_div();

        // Card body.
        echo html_writer::start_div('card-body');

        // Title.
        echo html_writer::tag('h5', format_string($conv->name), ['class' => 'card-title mb-3']);

        // Description excerpt.
        if (!empty($conv->description)) {
            $excerpt = strip_tags($conv->description);
            if (strlen($excerpt) > 120) {
                $excerpt = substr($excerpt, 0, 120) . '...';
            }
            echo html_writer::tag('p', $excerpt, ['class' => 'card-text text-muted small mb-3']);
        }

        // Stats row.
        echo html_writer::start_div('d-flex justify-content-between mb-3');
        echo html_writer::tag('span',
            '<i class="fa fa-briefcase text-primary mr-1"></i>' . $conv->vacancy_count . ' ' . get_string('vacancies', 'local_jobboard'),
            ['class' => 'small']
        );
        echo html_writer::tag('span',
            '<i class="fa fa-users text-info mr-1"></i>' . ($conv->total_positions ?? 0) . ' ' . get_string('positions', 'local_jobboard'),
            ['class' => 'small']
        );
        echo html_writer::end_div();

        // Dates.
        echo html_writer::start_div('small text-muted');
        echo html_writer::div(
            '<i class="fa fa-calendar-check text-success mr-2"></i>' .
            get_string('startdate', 'local_jobboard') . ': ' .
            userdate($conv->startdate, get_string('strftimedate', 'langconfig')),
            'mb-1'
        );
        $endDateClass = $isUrgent ? 'text-danger font-weight-bold' : '';
        echo html_writer::div(
            '<i class="fa fa-calendar-times text-warning mr-2"></i>' .
            get_string('enddate', 'local_jobboard') . ': ' .
            html_writer::tag('span', userdate($conv->enddate, get_string('strftimedate', 'langconfig')), ['class' => $endDateClass])
        );
        echo html_writer::end_div();

        echo html_writer::end_div(); // card-body

        // Card footer with action buttons.
        echo html_writer::start_div('card-footer bg-white border-top');
        echo html_writer::start_div('d-flex justify-content-between');

        // View details button.
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $conv->id]),
            '<i class="fa fa-info-circle mr-1"></i>' . get_string('viewdetails', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-info']
        );

        // View vacancies button.
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $conv->id]),
            '<i class="fa fa-briefcase mr-1"></i>' . get_string('viewvacancies', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-primary']
        );

        echo html_writer::end_div();
        echo html_writer::end_div();

        echo html_writer::end_div(); // card
        echo html_writer::end_div(); // col
    }

    echo html_writer::end_div(); // row
}

// ============================================================================
// CTA FOR NON-LOGGED IN USERS
// ============================================================================
if (!$isloggedin && !empty($convocatorias)) {
    echo html_writer::start_div('card bg-light border-0 mt-4');
    echo html_writer::start_div('card-body text-center py-5');
    echo html_writer::tag('i', '', ['class' => 'fa fa-user-plus fa-3x text-primary mb-3']);
    echo html_writer::tag('h4', get_string('wanttoapply', 'local_jobboard'), ['class' => 'mb-2']);
    echo html_writer::tag('p', get_string('createaccounttoapply', 'local_jobboard'), ['class' => 'text-muted mb-4']);

    echo html_writer::start_div('d-flex justify-content-center gap-3');
    echo html_writer::link(
        new moodle_url('/local/jobboard/signup.php'),
        '<i class="fa fa-user-plus mr-2"></i>' . get_string('createaccount'),
        ['class' => 'btn btn-primary btn-lg']
    );
    echo html_writer::link(
        new moodle_url('/login/index.php'),
        '<i class="fa fa-sign-in-alt mr-2"></i>' . get_string('login'),
        ['class' => 'btn btn-outline-secondary btn-lg']
    );
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // local-jobboard-dashboard

echo $OUTPUT->footer();
