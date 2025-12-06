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
 * Browse convocatorias view for applicants.
 *
 * This is the primary entry point for users to browse available job calls.
 * Shows open convocatorias with their vacancy counts and allows navigation
 * to view vacancies within each convocatoria.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

// Parameters.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);
$status = optional_param('status', 'open', PARAM_ALPHA);

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('convocatorias', 'local_jobboard'));
$PAGE->set_heading(get_string('convocatorias', 'local_jobboard'));
$PAGE->requires->css('/local/jobboard/styles.css');

// Check IOMAD installation.
$isiomad = local_jobboard_is_iomad_installed();

// Build query.
$where = ['1=1'];
$params = [];

// Filter by status (default: open convocatorias).
if ($status) {
    $where[] = 'c.status = :status';
    $params['status'] = $status;
}

// For non-admin users, respect tenant filtering.
if ($isiomad && !has_capability('local/jobboard:viewallvacancies', $context)) {
    $usercompanyid = local_jobboard_get_user_companyid();
    if ($usercompanyid) {
        $where[] = '(c.companyid IS NULL OR c.companyid = :companyid)';
        $params['companyid'] = $usercompanyid;
    }
}

// Get total count.
$wheresql = implode(' AND ', $where);
$countsql = "SELECT COUNT(*) FROM {local_jobboard_convocatoria} c WHERE $wheresql";
$total = $DB->count_records_sql($countsql, $params);

// Get convocatorias with vacancy counts.
$sql = "SELECT c.*,
               (SELECT COUNT(*) FROM {local_jobboard_vacancy} v
                WHERE v.convocatoriaid = c.id AND v.status = 'published') as vacancy_count
          FROM {local_jobboard_convocatoria} c
         WHERE $wheresql
         ORDER BY c.startdate DESC, c.name ASC";

$convocatorias = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

// Count by status for filters.
$statusCounts = [
    'open' => $DB->count_records('local_jobboard_convocatoria', ['status' => 'open']),
    'closed' => $DB->count_records('local_jobboard_convocatoria', ['status' => 'closed']),
];

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-browse-convocatorias');

// ============================================================================
// PAGE HEADER
// ============================================================================
$breadcrumbs = [
    get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
    get_string('convocatorias', 'local_jobboard') => null,
];

echo ui_helper::page_header(
    get_string('convocatorias', 'local_jobboard'),
    $breadcrumbs
);

// ============================================================================
// WELCOME BANNER
// ============================================================================
echo html_writer::start_div('jb-welcome-banner bg-gradient-primary text-white rounded-lg p-4 mb-4');
echo html_writer::start_div('d-flex justify-content-between align-items-center');
echo html_writer::start_div();
echo html_writer::tag('h4', get_string('browseconvocatorias', 'local_jobboard'), ['class' => 'mb-1 font-weight-bold']);
echo html_writer::tag('p', get_string('convocatoriahelp', 'local_jobboard'), ['class' => 'mb-0 opacity-75']);
echo html_writer::end_div();
echo html_writer::tag('i', '', ['class' => 'fa fa-calendar-alt fa-3x opacity-25']);
echo html_writer::end_div();
echo html_writer::end_div();

// ============================================================================
// STATISTICS ROW
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card(
    (string) $statusCounts['open'],
    get_string('convocatoriaactive', 'local_jobboard'),
    'success', 'calendar-check'
);
echo ui_helper::stat_card(
    (string) $statusCounts['closed'],
    get_string('convocatoriaclosed', 'local_jobboard'),
    'secondary', 'calendar-times'
);
echo html_writer::end_div();

// ============================================================================
// FILTER TABS
// ============================================================================
echo html_writer::start_tag('ul', ['class' => 'nav nav-tabs mb-4']);

$activeTab = ($status === 'open') ? 'active' : '';
echo html_writer::start_tag('li', ['class' => 'nav-item']);
echo html_writer::link(
    new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias', 'status' => 'open']),
    get_string('convocatoriaactive', 'local_jobboard') . ' ' .
    html_writer::tag('span', $statusCounts['open'], ['class' => 'badge badge-success']),
    ['class' => 'nav-link ' . $activeTab]
);
echo html_writer::end_tag('li');

$closedTab = ($status === 'closed') ? 'active' : '';
echo html_writer::start_tag('li', ['class' => 'nav-item']);
echo html_writer::link(
    new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias', 'status' => 'closed']),
    get_string('convocatoriaclosed', 'local_jobboard') . ' ' .
    html_writer::tag('span', $statusCounts['closed'], ['class' => 'badge badge-secondary']),
    ['class' => 'nav-link ' . $closedTab]
);
echo html_writer::end_tag('li');

echo html_writer::end_tag('ul');

// ============================================================================
// RESULTS INFO
// ============================================================================
$showing = new stdClass();
$showing->from = $total > 0 ? ($page * $perpage) + 1 : 0;
$showing->to = min(($page + 1) * $perpage, $total);
$showing->total = $total;
echo html_writer::tag('p', get_string('showing', 'local_jobboard', $showing),
    ['class' => 'text-muted small mb-3']);

// ============================================================================
// CONVOCATORIAS GRID
// ============================================================================
if (empty($convocatorias)) {
    echo ui_helper::empty_state(
        get_string('noconvocatorias', 'local_jobboard'),
        'calendar-alt'
    );
} else {
    echo html_writer::start_div('row');

    foreach ($convocatorias as $conv) {
        $now = time();
        $daysRemaining = ceil(($conv->enddate - $now) / 86400);
        $isClosingSoon = ($daysRemaining <= 7 && $daysRemaining > 0 && $conv->status === 'open');
        $isClosed = ($conv->status === 'closed' || $conv->enddate < $now);

        // Card column.
        echo html_writer::start_div('col-lg-4 col-md-6 mb-4');

        $cardClass = 'card h-100 shadow-sm jb-convocatoria-card';
        if ($isClosingSoon) {
            $cardClass .= ' border-warning';
        } elseif ($isClosed) {
            $cardClass .= ' border-secondary';
        } else {
            $cardClass .= ' border-success';
        }

        echo html_writer::start_div($cardClass);

        // Card header.
        $headerClass = $isClosed ? 'bg-secondary' : ($isClosingSoon ? 'bg-warning' : 'bg-success');
        echo html_writer::start_div('card-header ' . $headerClass . ' text-white d-flex justify-content-between align-items-center');
        echo html_writer::tag('code', s($conv->code), ['class' => 'small']);
        if ($isClosingSoon) {
            echo html_writer::tag('span',
                '<i class="fa fa-clock mr-1"></i>' . get_string('daysleft', 'local_jobboard', $daysRemaining),
                ['class' => 'badge badge-light']
            );
        } elseif ($conv->status === 'open') {
            echo html_writer::tag('span',
                get_string('convocatoria_status_open', 'local_jobboard'),
                ['class' => 'badge badge-light']
            );
        } else {
            echo html_writer::tag('span',
                get_string('convocatoria_status_closed', 'local_jobboard'),
                ['class' => 'badge badge-dark']
            );
        }
        echo html_writer::end_div();

        // Card body.
        echo html_writer::start_div('card-body');

        // Title.
        $viewUrl = new moodle_url('/local/jobboard/index.php', ['view' => 'view_convocatoria', 'id' => $conv->id]);
        echo html_writer::tag('h5',
            html_writer::link($viewUrl, s($conv->name), ['class' => 'text-dark']),
            ['class' => 'card-title mb-3']
        );

        // Description (truncated).
        if (!empty($conv->description)) {
            $desc = strip_tags($conv->description);
            if (strlen($desc) > 120) {
                $desc = substr($desc, 0, 120) . '...';
            }
            echo html_writer::tag('p', $desc, ['class' => 'card-text text-muted small mb-3']);
        }

        // Dates.
        echo html_writer::start_div('small mb-2');
        echo html_writer::tag('i', '', ['class' => 'fa fa-calendar-alt text-muted mr-2']);
        echo userdate($conv->startdate, get_string('strftimedate', 'langconfig'));
        echo html_writer::tag('span', ' - ', ['class' => 'mx-1']);
        echo userdate($conv->enddate, get_string('strftimedate', 'langconfig'));
        echo html_writer::end_div();

        // Vacancy count.
        echo html_writer::start_div('d-flex align-items-center');
        echo html_writer::tag('i', '', ['class' => 'fa fa-briefcase text-primary mr-2']);
        if ($conv->vacancy_count > 0) {
            echo html_writer::tag('span',
                get_string('convocatoriavacancycount', 'local_jobboard', $conv->vacancy_count),
                ['class' => 'text-primary font-weight-medium']
            );
        } else {
            echo html_writer::tag('span',
                get_string('novacancies', 'local_jobboard'),
                ['class' => 'text-muted']
            );
        }
        echo html_writer::end_div();

        echo html_writer::end_div(); // card-body

        // Card footer.
        echo html_writer::start_div('card-footer bg-white');
        if ($conv->vacancy_count > 0 && $conv->status === 'open') {
            echo html_writer::link(
                $viewUrl,
                '<i class="fa fa-arrow-right mr-2"></i>' . get_string('viewvacancies', 'local_jobboard'),
                ['class' => 'btn btn-success btn-block']
            );
        } elseif ($conv->vacancy_count > 0) {
            echo html_writer::link(
                $viewUrl,
                '<i class="fa fa-eye mr-2"></i>' . get_string('viewconvocatoria', 'local_jobboard'),
                ['class' => 'btn btn-outline-secondary btn-block']
            );
        } else {
            echo html_writer::tag('span',
                get_string('novacancies', 'local_jobboard'),
                ['class' => 'btn btn-outline-secondary btn-block disabled']
            );
        }
        echo html_writer::end_div(); // card-footer

        echo html_writer::end_div(); // card
        echo html_writer::end_div(); // col
    }

    echo html_writer::end_div(); // row
}

// ============================================================================
// PAGINATION
// ============================================================================
if ($total > $perpage) {
    $baseurl = new moodle_url('/local/jobboard/index.php', [
        'view' => 'browse_convocatorias',
        'status' => $status,
        'perpage' => $perpage,
    ]);
    echo $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
}

echo html_writer::end_div(); // local-jobboard-browse-convocatorias

echo $OUTPUT->footer();
