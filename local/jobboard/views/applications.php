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
 * User's applications list view.
 *
 * Modern redesign with card-based layout and progress tracking.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/tablelib.php');

use local_jobboard\application;
use local_jobboard\exemption;
use local_jobboard\output\ui_helper;

// Require apply capability.
require_capability('local/jobboard:apply', $context);

// Parameters.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$status = optional_param('status', '', PARAM_ALPHA);

// Set up page.
$PAGE->set_title(get_string('myapplications', 'local_jobboard'));
$PAGE->set_heading(get_string('myapplications', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

// Get user's applications.
$filters = ['userid' => $USER->id];
if (!empty($status)) {
    $filters['status'] = $status;
}

$result = application::get_list($filters, 'timecreated', 'DESC', $page, $perpage);
$applications = $result['applications'];
$total = $result['total'];

// Get all applications for stats (without status filter).
$allAppsResult = application::get_list(['userid' => $USER->id], 'timecreated', 'DESC', 0, 1000);
$allApplications = $allAppsResult['applications'];

// Calculate statistics.
$stats = [
    'total' => count($allApplications),
    'submitted' => 0,
    'under_review' => 0,
    'docs_validated' => 0,
    'selected' => 0,
    'rejected' => 0,
    'pending_docs' => 0,
];

foreach ($allApplications as $app) {
    if (isset($stats[$app->status])) {
        $stats[$app->status]++;
    }
    // Count pending documents.
    $doccount = $app->document_count ?? 0;
    if ($doccount == 0 && in_array($app->status, ['submitted', 'under_review', 'docs_rejected'])) {
        $stats['pending_docs']++;
    }
}

// Check for exemption.
$exemption = exemption::get_active_for_user($USER->id);

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-applications');

// ============================================================================
// PAGE HEADER
// ============================================================================
$breadcrumbs = [
    get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
    get_string('myapplications', 'local_jobboard') => null,
];

echo ui_helper::page_header(
    get_string('myapplications', 'local_jobboard'),
    $breadcrumbs,
    [[
        'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']),
        'label' => get_string('browsevacancies', 'local_jobboard'),
        'icon' => 'search',
        'class' => 'btn btn-primary',
    ]]
);

// ============================================================================
// EXEMPTION NOTICE
// ============================================================================
if ($exemption) {
    echo html_writer::start_div('alert alert-success border-left-success d-flex align-items-center mb-4');
    echo html_writer::tag('i', '', ['class' => 'fa fa-certificate fa-2x mr-3 text-success']);
    echo html_writer::start_div();
    echo html_writer::tag('strong', get_string('exemptionactive', 'local_jobboard'), ['class' => 'd-block']);
    echo html_writer::tag('span',
        get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard') .
        (!empty($exemption->documentref) ? ' (' . format_string($exemption->documentref) . ')' : ''),
        ['class' => 'text-muted']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// ============================================================================
// STATISTICS ROW
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card(
    (string) $stats['total'],
    get_string('myapplications', 'local_jobboard'),
    'primary', 'file-alt'
);
echo ui_helper::stat_card(
    (string) ($stats['submitted'] + $stats['under_review']),
    get_string('inprogress', 'local_jobboard'),
    'info', 'spinner'
);
echo ui_helper::stat_card(
    (string) $stats['docs_validated'],
    get_string('documentsvalidated', 'local_jobboard'),
    'success', 'check-circle'
);
echo ui_helper::stat_card(
    (string) $stats['selected'],
    get_string('appstatus:selected', 'local_jobboard'),
    'success', 'trophy'
);
echo html_writer::end_div();

// ============================================================================
// PENDING ACTIONS ALERT
// ============================================================================
if ($stats['pending_docs'] > 0) {
    echo html_writer::start_div('alert alert-warning border-left-warning d-flex justify-content-between align-items-center mb-4');
    echo html_writer::tag('span',
        '<i class="fa fa-exclamation-triangle mr-2"></i>' .
        get_string('pending_docs_alert', 'local_jobboard', $stats['pending_docs'])
    );
    echo html_writer::end_div();
}

// ============================================================================
// FILTER FORM
// ============================================================================
$statusOptions = ['' => get_string('allstatuses', 'local_jobboard')];
foreach (application::STATUSES as $s) {
    $statusOptions[$s] = get_string('status_' . $s, 'local_jobboard');
}

$filterDefinitions = [
    [
        'type' => 'select',
        'name' => 'status',
        'options' => $statusOptions,
        'col' => 'col-md-4',
    ],
];

echo ui_helper::filter_form(
    (new moodle_url('/local/jobboard/index.php'))->out(false),
    $filterDefinitions,
    ['status' => $status],
    ['view' => 'applications']
);

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
// APPLICATIONS LIST
// ============================================================================
if (empty($applications)) {
    echo ui_helper::empty_state(
        get_string('noapplicationsfound', 'local_jobboard'),
        'file-alt',
        [
            'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']),
            'label' => get_string('browsevacancies', 'local_jobboard'),
            'class' => 'btn btn-primary',
        ]
    );
} else {
    echo html_writer::start_div('row');

    foreach ($applications as $app) {
        echo html_writer::start_div('col-lg-6 mb-4');
        echo html_writer::start_div('card h-100 shadow-sm jb-application-card');

        // Card header with status.
        $statusClass = 'bg-secondary';
        switch ($app->status) {
            case 'submitted':
            case 'under_review':
                $statusClass = 'bg-info';
                break;
            case 'docs_validated':
            case 'selected':
                $statusClass = 'bg-success';
                break;
            case 'docs_rejected':
            case 'rejected':
                $statusClass = 'bg-danger';
                break;
            case 'interview':
                $statusClass = 'bg-warning';
                break;
            case 'withdrawn':
                $statusClass = 'bg-secondary';
                break;
        }

        echo html_writer::start_div('card-header d-flex justify-content-between align-items-center ' . $statusClass . ' text-white');
        echo html_writer::tag('span',
            get_string('status_' . $app->status, 'local_jobboard'),
            ['class' => 'font-weight-bold']
        );
        echo html_writer::tag('small',
            userdate($app->timecreated, get_string('strftimedate', 'langconfig'))
        );
        echo html_writer::end_div();

        // Card body.
        echo html_writer::start_div('card-body');

        // Vacancy info.
        $vacancyUrl = new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $app->vacancyid]);
        echo html_writer::tag('h5',
            html_writer::link($vacancyUrl,
                format_string($app->vacancy_title ?? get_string('unknownvacancy', 'local_jobboard')),
                ['class' => 'text-dark']
            ),
            ['class' => 'card-title mb-3']
        );

        // Application details.
        echo html_writer::start_div('small mb-3');

        // Documents count.
        $doccount = $app->document_count ?? 0;
        $docBadgeClass = $doccount > 0 ? 'badge-success' : 'badge-warning';
        echo html_writer::div(
            '<i class="fa fa-file-alt text-muted mr-2"></i>' .
            get_string('documents', 'local_jobboard') . ': ' .
            html_writer::tag('span', $doccount, ['class' => 'badge ' . $docBadgeClass])
        );

        // Status notes (if any).
        if (!empty($app->statusnotes)) {
            echo html_writer::div(
                '<i class="fa fa-comment text-muted mr-2"></i>' .
                '<em>' . format_string($app->statusnotes) . '</em>',
                'mt-2 text-muted'
            );
        }

        echo html_writer::end_div();

        // Progress indicator.
        $progressSteps = ['submitted', 'under_review', 'docs_validated', 'interview', 'selected'];
        $currentIndex = array_search($app->status, $progressSteps);
        if ($currentIndex === false) {
            $currentIndex = -1;
        }

        echo html_writer::start_div('progress-tracker mb-3');
        echo html_writer::start_div('d-flex justify-content-between small text-muted mb-1');
        foreach ($progressSteps as $i => $step) {
            $stepClass = $i <= $currentIndex ? 'text-success font-weight-bold' : '';
            echo html_writer::tag('span', '', ['class' => 'fa fa-circle ' . $stepClass]);
        }
        echo html_writer::end_div();
        echo html_writer::start_div('progress', ['style' => 'height: 4px;']);
        $progressPercent = $currentIndex >= 0 ? (($currentIndex + 1) / count($progressSteps)) * 100 : 0;
        echo html_writer::div('', 'progress-bar bg-success', [
            'role' => 'progressbar',
            'style' => 'width: ' . $progressPercent . '%',
        ]);
        echo html_writer::end_div();
        echo html_writer::end_div();

        echo html_writer::end_div(); // card-body

        // Card footer with actions.
        echo html_writer::start_div('card-footer bg-white');
        echo html_writer::start_div('d-flex justify-content-between');

        $viewUrl = new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $app->id]);
        echo html_writer::link($viewUrl,
            '<i class="fa fa-eye mr-1"></i>' . get_string('viewdetails', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-primary']
        );

        // Withdraw button if applicable.
        if (in_array($app->status, ['submitted', 'under_review'])) {
            $withdrawUrl = new moodle_url('/local/jobboard/index.php',
                ['view' => 'application', 'id' => $app->id, 'action' => 'withdraw']);
            echo html_writer::link($withdrawUrl,
                '<i class="fa fa-times mr-1"></i>' . get_string('withdraw', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-danger']
            );
        }

        echo html_writer::end_div();
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
        'view' => 'applications',
        'status' => $status,
    ]);
    echo $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
}

echo html_writer::end_div(); // local-jobboard-applications

// Styles consolidated in styles.css - Application Card section.

echo $OUTPUT->footer();
