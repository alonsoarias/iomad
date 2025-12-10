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
 * This view has two modes:
 * 1. Without convocatoriaid: Shows convocatorias as cards with options to view details or vacancies
 * 2. With convocatoriaid: Shows vacancies for that convocatoria as individual cards
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
// MODE: SHOW SINGLE VACANCY DETAIL
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

    echo $OUTPUT->header();
    echo html_writer::start_div('local-jobboard-dashboard');

    // Breadcrumb navigation.
    echo html_writer::start_div('mb-4');
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        '<i class="fa fa-arrow-left mr-2"></i>' . get_string('publicpagetitle', 'local_jobboard'),
        ['class' => 'btn btn-sm btn-outline-secondary mr-2']
    );
    if ($convocatoria) {
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoria->id]),
            '<i class="fa fa-folder-open mr-2"></i>' . s($convocatoria->name),
            ['class' => 'btn btn-sm btn-outline-secondary']
        );
    }
    echo html_writer::end_div();

    // Calculate days remaining.
    $closedate = $convocatoria ? $convocatoria->enddate : ($vacancy->closedate ?? time());
    $daysRemaining = max(0, (int) floor(($closedate - time()) / 86400));
    $isUrgent = $daysRemaining <= 7;

    // Vacancy detail card.
    $cardBorder = $isUrgent ? 'border-warning' : '';
    echo html_writer::start_div('card shadow-sm ' . $cardBorder);

    // Card header.
    $headerClass = $isUrgent ? 'card-header bg-warning text-dark' : 'card-header bg-primary text-white';
    echo html_writer::start_div($headerClass);
    echo html_writer::start_div('d-flex justify-content-between align-items-center flex-wrap');
    echo html_writer::start_div();
    echo html_writer::tag('code', s($vacancy->code), ['class' => 'mr-2 badge badge-light']);
    echo html_writer::tag('h4', s($vacancy->title), ['class' => 'd-inline mb-0']);
    echo html_writer::end_div();
    if ($isUrgent) {
        echo html_writer::tag('span',
            '<i class="fa fa-exclamation-triangle mr-1"></i>' . get_string('closesindays', 'local_jobboard', $daysRemaining),
            ['class' => 'badge badge-danger']
        );
    }
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Card body.
    echo html_writer::start_div('card-body');

    // Description.
    if (!empty($vacancy->description)) {
        echo html_writer::start_div('mb-4');
        echo html_writer::tag('h6', '<i class="fa fa-info-circle mr-2 text-primary"></i>' . get_string('description'),
            ['class' => 'font-weight-bold mb-3']);
        echo html_writer::div(format_text($vacancy->description, FORMAT_HTML), 'text-muted');
        echo html_writer::end_div();
    }

    // Details grid.
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
    echo html_writer::start_div('d-flex align-items-center');
    echo html_writer::tag('i', '', ['class' => 'fa fa-map-marker-alt fa-2x text-primary mr-3']);
    echo html_writer::start_div();
    echo html_writer::tag('small', get_string('location', 'local_jobboard'), ['class' => 'text-muted d-block']);
    echo html_writer::tag('strong', $locationText);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Contract type.
    if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
        echo html_writer::start_div('col-md-6 col-lg-3 mb-3');
        echo html_writer::start_div('d-flex align-items-center');
        echo html_writer::tag('i', '', ['class' => 'fa fa-file-contract fa-2x text-success mr-3']);
        echo html_writer::start_div();
        echo html_writer::tag('small', get_string('contracttype', 'local_jobboard'), ['class' => 'text-muted d-block']);
        echo html_writer::tag('strong', $contracttypes[$vacancy->contracttype]);
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    // Positions.
    echo html_writer::start_div('col-md-6 col-lg-3 mb-3');
    echo html_writer::start_div('d-flex align-items-center');
    echo html_writer::tag('i', '', ['class' => 'fa fa-users fa-2x text-info mr-3']);
    echo html_writer::start_div();
    echo html_writer::tag('small', get_string('positions', 'local_jobboard'), ['class' => 'text-muted d-block']);
    echo html_writer::tag('strong', $vacancy->positions);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Close date.
    echo html_writer::start_div('col-md-6 col-lg-3 mb-3');
    echo html_writer::start_div('d-flex align-items-center');
    $dateIconClass = $isUrgent ? 'fa fa-calendar-times fa-2x text-danger mr-3' : 'fa fa-calendar-times fa-2x text-warning mr-3';
    echo html_writer::tag('i', '', ['class' => $dateIconClass]);
    echo html_writer::start_div();
    echo html_writer::tag('small', get_string('closedate', 'local_jobboard'), ['class' => 'text-muted d-block']);
    $closeDateClass = $isUrgent ? 'text-danger' : '';
    echo html_writer::tag('strong', userdate($closedate, get_string('strftimedate', 'langconfig')), ['class' => $closeDateClass]);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // row

    // Convocatoria info.
    if ($convocatoria) {
        echo html_writer::start_div('alert alert-info');
        echo html_writer::tag('h6',
            '<i class="fa fa-folder-open mr-2"></i>' . get_string('convocatoria', 'local_jobboard'),
            ['class' => 'alert-heading mb-2']
        );
        echo html_writer::tag('p',
            html_writer::tag('code', s($convocatoria->code), ['class' => 'mr-2']) . s($convocatoria->name),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
    }

    // Requirements.
    if (!empty($vacancy->requirements)) {
        echo html_writer::start_div('mb-4');
        echo html_writer::tag('h6', '<i class="fa fa-list-check mr-2 text-primary"></i>' . get_string('requirements', 'local_jobboard'),
            ['class' => 'font-weight-bold mb-3']);
        echo html_writer::div(format_text($vacancy->requirements, FORMAT_HTML), 'text-muted');
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // card-body

    // Card footer - Action buttons.
    echo html_writer::start_div('card-footer bg-light');
    echo html_writer::start_div('d-flex justify-content-between align-items-center flex-wrap');

    // Publication type badge.
    $typeBadge = $vacancy->publicationtype === 'public'
        ? '<span class="badge badge-success">' . get_string('public', 'local_jobboard') . '</span>'
        : '<span class="badge badge-secondary">' . get_string('internal', 'local_jobboard') . '</span>';
    echo html_writer::tag('div', $typeBadge);

    // Apply button.
    echo html_writer::start_div('mt-2 mt-md-0');
    if ($canapply) {
        $hasApplied = $DB->record_exists('local_jobboard_application', [
            'vacancyid' => $vacancy->id,
            'userid' => $USER->id,
        ]);

        if ($hasApplied) {
            echo html_writer::tag('span',
                '<i class="fa fa-check mr-2"></i>' . get_string('applied', 'local_jobboard'),
                ['class' => 'btn btn-success disabled']
            );
        } else {
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]),
                '<i class="fa fa-paper-plane mr-2"></i>' . get_string('apply', 'local_jobboard'),
                ['class' => 'btn btn-primary']
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
            '<i class="fa fa-sign-in-alt mr-2"></i>' . get_string('loginandapply', 'local_jobboard'),
            ['class' => 'btn btn-primary']
        );
    }
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // card

    echo html_writer::end_div(); // local-jobboard-dashboard
    echo $OUTPUT->footer();
    exit;
}

// ============================================================================
// MODE: SHOW VACANCIES FOR A SPECIFIC CONVOCATORIA
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

    // Get vacancies for this convocatoria.
    $vacancySql = "SELECT v.*
                     FROM {local_jobboard_vacancy} v
                    WHERE v.convocatoriaid = :convid
                      AND v.status = 'published'" .
                  ($canviewinternal ? "" : " AND v.publicationtype = 'public'") . "
                    ORDER BY v.code ASC";

    $allVacancies = $DB->get_records_sql($vacancySql, ['convid' => $convocatoriaid]);
    $totalVacancies = count($allVacancies);

    // Paginate.
    $vacancies = array_slice($allVacancies, $page * $perpage, $perpage, true);

    // Calculate convocatoria stats.
    $daysRemaining = max(0, (int) floor(($convocatoria->enddate - time()) / 86400));
    $isUrgent = $daysRemaining <= 7;
    $totalPositions = 0;
    foreach ($allVacancies as $v) {
        $totalPositions += $v->positions;
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
        '<i class="fa fa-briefcase mr-1"></i>' . $totalVacancies . ' ' . get_string('vacancies', 'local_jobboard'),
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
    // Dates.
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
    // View details link.
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $convocatoria->id]),
        '<i class="fa fa-info-circle mr-1"></i>' . get_string('viewdetails', 'local_jobboard'),
        ['class' => 'btn btn-sm btn-outline-info ml-auto']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // card

    // Vacancies grid.
    echo html_writer::tag('h5',
        '<i class="fa fa-briefcase mr-2 text-primary"></i>' . get_string('vacancies', 'local_jobboard'),
        ['class' => 'mb-3']
    );

    if (empty($vacancies)) {
        echo ui_helper::empty_state(
            get_string('novacanciesfound', 'local_jobboard'),
            'briefcase'
        );
    } else {
        echo html_writer::start_div('row');

        foreach ($vacancies as $vacancy) {
            $vacDaysRemaining = $daysRemaining;
            $isVacUrgent = $isUrgent;

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
            if ($canapply) {
                $hasApplied = $DB->record_exists('local_jobboard_application', [
                    'vacancyid' => $vacancy->id,
                    'userid' => $USER->id,
                ]);

                if ($hasApplied) {
                    echo html_writer::tag('span',
                        '<i class="fa fa-check"></i>',
                        ['class' => 'btn btn-sm btn-success disabled', 'title' => get_string('applied', 'local_jobboard')]
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
                    '<i class="fa fa-sign-in-alt"></i>',
                    ['class' => 'btn btn-sm btn-outline-secondary', 'title' => get_string('login')]
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
