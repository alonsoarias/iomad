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
 * Shows convocatorias with all their information and vacancies.
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
// CONVOCATORIAS LIST
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
    foreach ($convocatorias as $conv) {
        $daysRemaining = max(0, (int) floor(($conv->enddate - time()) / 86400));
        $isUrgent = $daysRemaining <= 7;
        $cardBorderClass = $isUrgent ? 'card shadow-sm mb-4 border-warning' : 'card shadow-sm mb-4';

        echo html_writer::start_div($cardBorderClass);

        // ====================================================================
        // CONVOCATORIA HEADER
        // ====================================================================
        $headerClass = $isUrgent ? 'card-header bg-warning text-dark' : 'card-header bg-primary text-white';
        echo html_writer::start_div($headerClass);
        echo html_writer::start_div('d-flex justify-content-between align-items-center flex-wrap');

        // Left side: Code and Name.
        echo html_writer::start_div();
        echo html_writer::tag('span', s($conv->code), ['class' => 'badge badge-light mr-2']);
        echo html_writer::tag('h5', format_string($conv->name), ['class' => 'd-inline mb-0']);
        echo html_writer::end_div();

        // Right side: Stats and deadline.
        echo html_writer::start_div('d-flex align-items-center mt-2 mt-md-0');
        echo html_writer::tag('span',
            '<i class="fa fa-briefcase mr-1"></i>' . $conv->vacancy_count . ' ' . get_string('vacancies', 'local_jobboard'),
            ['class' => 'badge badge-light mr-2']
        );
        echo html_writer::tag('span',
            '<i class="fa fa-users mr-1"></i>' . ($conv->total_positions ?? 0) . ' ' . get_string('positions', 'local_jobboard'),
            ['class' => 'badge badge-light mr-2']
        );
        if ($isUrgent) {
            echo html_writer::tag('span',
                '<i class="fa fa-exclamation-triangle mr-1"></i>' . get_string('closesindays', 'local_jobboard', $daysRemaining),
                ['class' => 'badge badge-danger']
            );
        } else {
            echo html_writer::tag('span',
                '<i class="fa fa-calendar-alt mr-1"></i>' . userdate($conv->enddate, get_string('strftimedate', 'langconfig')),
                ['class' => 'badge badge-light']
            );
        }
        echo html_writer::end_div();

        echo html_writer::end_div();
        echo html_writer::end_div();

        // ====================================================================
        // CONVOCATORIA BODY
        // ====================================================================
        echo html_writer::start_div('card-body');

        // Description.
        if (!empty($conv->description)) {
            echo html_writer::start_div('mb-4');
            echo html_writer::tag('h6',
                '<i class="fa fa-info-circle mr-2 text-primary"></i>' . get_string('description'),
                ['class' => 'font-weight-bold mb-2']
            );
            echo html_writer::div(format_text($conv->description, FORMAT_HTML), 'text-muted');
            echo html_writer::end_div();
        }

        // Convocatoria dates.
        echo html_writer::start_div('row mb-4');
        echo html_writer::start_div('col-md-6');
        echo html_writer::start_div('d-flex align-items-center');
        echo html_writer::tag('i', '', ['class' => 'fa fa-calendar-check fa-2x text-success mr-3']);
        echo html_writer::start_div();
        echo html_writer::tag('small', get_string('startdate', 'local_jobboard'), ['class' => 'text-muted d-block']);
        echo html_writer::tag('strong', userdate($conv->startdate, get_string('strftimedate', 'langconfig')));
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();

        echo html_writer::start_div('col-md-6');
        echo html_writer::start_div('d-flex align-items-center');
        $endIconClass = $isUrgent ? 'fa fa-calendar-times fa-2x text-danger mr-3' : 'fa fa-calendar-times fa-2x text-warning mr-3';
        echo html_writer::tag('i', '', ['class' => $endIconClass]);
        echo html_writer::start_div();
        echo html_writer::tag('small', get_string('enddate', 'local_jobboard'), ['class' => 'text-muted d-block']);
        $endDateClass = $isUrgent ? 'text-danger' : '';
        echo html_writer::tag('strong', userdate($conv->enddate, get_string('strftimedate', 'langconfig')), ['class' => $endDateClass]);
        if ($isUrgent) {
            echo html_writer::tag('span', ' (' . get_string('closesindays', 'local_jobboard', $daysRemaining) . ')', ['class' => 'text-danger small']);
        }
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();

        // ====================================================================
        // VACANCIES TABLE
        // ====================================================================
        echo html_writer::tag('h6',
            '<i class="fa fa-list mr-2 text-primary"></i>' . get_string('vacancies', 'local_jobboard'),
            ['class' => 'font-weight-bold mb-3']
        );

        // Get vacancies for this convocatoria.
        $vacancySql = "SELECT v.*
                         FROM {local_jobboard_vacancy} v
                        WHERE v.convocatoriaid = :convid
                          AND v.status = 'published'" .
                      ($canviewinternal ? "" : " AND v.publicationtype = 'public'") . "
                        ORDER BY v.code ASC";
        $vacancies = $DB->get_records_sql($vacancySql, ['convid' => $conv->id]);

        if (!empty($vacancies)) {
            echo html_writer::start_div('table-responsive');
            echo html_writer::start_tag('table', ['class' => 'table table-hover table-striped mb-0']);

            // Table header.
            echo html_writer::start_tag('thead', ['class' => 'thead-light']);
            echo html_writer::start_tag('tr');
            echo html_writer::tag('th', get_string('code', 'local_jobboard'), ['style' => 'width: 100px;']);
            echo html_writer::tag('th', get_string('vacancy', 'local_jobboard'));
            echo html_writer::tag('th', get_string('location', 'local_jobboard'), ['class' => 'd-none d-md-table-cell']);
            echo html_writer::tag('th', get_string('positions', 'local_jobboard'), ['class' => 'text-center', 'style' => 'width: 100px;']);
            echo html_writer::tag('th', get_string('type', 'local_jobboard'), ['class' => 'd-none d-lg-table-cell', 'style' => 'width: 120px;']);
            echo html_writer::tag('th', get_string('actions'), ['class' => 'text-center', 'style' => 'width: 180px;']);
            echo html_writer::end_tag('tr');
            echo html_writer::end_tag('thead');

            // Table body.
            echo html_writer::start_tag('tbody');

            foreach ($vacancies as $vacancy) {
                echo html_writer::start_tag('tr');

                // Code.
                echo html_writer::tag('td',
                    html_writer::tag('code', s($vacancy->code), ['class' => 'small'])
                );

                // Title with description tooltip.
                $titleHtml = html_writer::tag('strong', format_string($vacancy->title));
                if (!empty($vacancy->contracttype) && isset($contracttypes[$vacancy->contracttype])) {
                    $titleHtml .= html_writer::tag('small',
                        '<br><i class="fa fa-file-contract mr-1"></i>' . $contracttypes[$vacancy->contracttype],
                        ['class' => 'text-muted']
                    );
                }
                echo html_writer::tag('td', $titleHtml);

                // Location (from IOMAD company or field).
                $locationText = '-';
                if (!empty($vacancy->companyid)) {
                    $companyName = local_jobboard_get_company_name($vacancy->companyid);
                    if (!empty($companyName)) {
                        $locationText = s($companyName);
                    }
                } elseif (!empty($vacancy->location)) {
                    $locationText = s($vacancy->location);
                }
                echo html_writer::tag('td',
                    '<i class="fa fa-map-marker-alt text-muted mr-1"></i>' . $locationText,
                    ['class' => 'd-none d-md-table-cell small']
                );

                // Positions.
                echo html_writer::tag('td',
                    html_writer::tag('span', $vacancy->positions, ['class' => 'badge badge-info']),
                    ['class' => 'text-center']
                );

                // Publication type.
                $typeBadge = $vacancy->publicationtype === 'public'
                    ? '<span class="badge badge-success">' . get_string('public', 'local_jobboard') . '</span>'
                    : '<span class="badge badge-secondary">' . get_string('internal', 'local_jobboard') . '</span>';
                echo html_writer::tag('td', $typeBadge, ['class' => 'd-none d-lg-table-cell']);

                // Actions.
                echo html_writer::start_tag('td', ['class' => 'text-center']);

                // View details button.
                echo html_writer::link(
                    new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancy->id]),
                    '<i class="fa fa-eye"></i>',
                    ['class' => 'btn btn-sm btn-outline-primary mr-1', 'title' => get_string('viewdetails', 'local_jobboard')]
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
                            '<i class="fa fa-paper-plane"></i> ' . get_string('apply', 'local_jobboard'),
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
                        '<i class="fa fa-sign-in-alt"></i> ' . get_string('login'),
                        ['class' => 'btn btn-sm btn-secondary']
                    );
                }

                echo html_writer::end_tag('td');
                echo html_writer::end_tag('tr');
            }

            echo html_writer::end_tag('tbody');
            echo html_writer::end_tag('table');
            echo html_writer::end_div(); // table-responsive
        }

        echo html_writer::end_div(); // card-body

        // ====================================================================
        // CONVOCATORIA FOOTER
        // ====================================================================
        echo html_writer::start_div('card-footer bg-light');
        echo html_writer::start_div('d-flex justify-content-between align-items-center flex-wrap');

        // Info text.
        echo html_writer::tag('small',
            '<i class="fa fa-info-circle mr-1"></i>' .
            get_string('convocatoria_footer_info', 'local_jobboard', [
                'vacancies' => $conv->vacancy_count,
                'positions' => $conv->total_positions ?? 0,
            ]),
            ['class' => 'text-muted']
        );

        // CTA for non-logged users.
        if (!$isloggedin) {
            echo html_writer::start_div('mt-2 mt-md-0');
            echo html_writer::link(
                new moodle_url('/local/jobboard/signup.php'),
                '<i class="fa fa-user-plus mr-1"></i>' . get_string('createaccount'),
                ['class' => 'btn btn-sm btn-outline-primary mr-2']
            );
            echo html_writer::link(
                new moodle_url('/login/index.php'),
                '<i class="fa fa-sign-in-alt mr-1"></i>' . get_string('login'),
                ['class' => 'btn btn-sm btn-primary']
            );
            echo html_writer::end_div();
        }

        echo html_writer::end_div();
        echo html_writer::end_div();

        echo html_writer::end_div(); // card
    }
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
