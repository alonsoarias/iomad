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
 * Dashboard view for local_jobboard.
 *
 * Redesigned dashboard with hierarchical navigation:
 * - Convocatorias → Vacantes (for administrators)
 * - Public vacancies and applications (for applicants)
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('jobboard', 'local_jobboard'));
$PAGE->set_heading(get_string('jobboard', 'local_jobboard'));

// Add custom CSS for dashboard.
$PAGE->requires->css('/local/jobboard/styles.css');

echo $OUTPUT->header();

// Dashboard content based on user capabilities.
$canmanage = has_capability('local/jobboard:createvacancy', $context);
$canreview = has_capability('local/jobboard:reviewdocuments', $context);
$canapply = has_capability('local/jobboard:apply', $context);
$canviewreports = has_capability('local/jobboard:viewreports', $context);

// Get statistics for dashboard.
$stats = local_jobboard_get_dashboard_stats($USER->id, $canmanage, $canreview);

echo html_writer::start_div('local-jobboard-dashboard');

// Welcome header with role-based message.
echo html_writer::start_div('dashboard-header mb-4 p-4 bg-light rounded');
echo html_writer::tag('h2', get_string('dashboard', 'local_jobboard'), ['class' => 'mb-2']);

if ($canmanage) {
    echo html_writer::tag('p', get_string('dashboard_admin_welcome', 'local_jobboard'), ['class' => 'text-muted mb-0']);
} else if ($canapply) {
    echo html_writer::tag('p', get_string('dashboard_applicant_welcome', 'local_jobboard'), ['class' => 'text-muted mb-0']);
}
echo html_writer::end_div();

// ============================================================================
// ADMINISTRATOR VIEW - Hierarchical Navigation: Convocatorias → Vacantes
// ============================================================================
if ($canmanage) {
    echo html_writer::start_div('admin-section mb-5');
    echo html_writer::tag('h3', get_string('administracion', 'local_jobboard'), ['class' => 'section-title mb-4']);

    // Statistics cards row.
    echo html_writer::start_div('row mb-4');

    // Active convocatorias stat.
    echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
    echo html_writer::start_div('stat-card card h-100 border-primary');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('div', $stats['active_convocatorias'] ?? 0, ['class' => 'stat-number text-primary']);
    echo html_writer::tag('div', get_string('activeconvocatorias', 'local_jobboard'), ['class' => 'stat-label text-muted']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Published vacancies stat.
    echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
    echo html_writer::start_div('stat-card card h-100 border-success');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('div', $stats['published_vacancies'] ?? 0, ['class' => 'stat-number text-success']);
    echo html_writer::tag('div', get_string('publishedvacancies', 'local_jobboard'), ['class' => 'stat-label text-muted']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Total applications stat.
    echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
    echo html_writer::start_div('stat-card card h-100 border-info');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('div', $stats['total_applications'] ?? 0, ['class' => 'stat-number text-info']);
    echo html_writer::tag('div', get_string('totalapplications', 'local_jobboard'), ['class' => 'stat-label text-muted']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Pending reviews stat.
    echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
    echo html_writer::start_div('stat-card card h-100 border-warning');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('div', $stats['pending_reviews'] ?? 0, ['class' => 'stat-number text-warning']);
    echo html_writer::tag('div', get_string('pendingreviews', 'local_jobboard'), ['class' => 'stat-label text-muted']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // row

    // Main action cards.
    echo html_writer::start_div('row');

    // MAIN CARD: Convocatorias (Entry point for vacancy management).
    echo html_writer::start_div('col-lg-6 mb-4');
    echo html_writer::start_div('action-card card h-100 shadow-sm border-0');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('span', '', ['class' => 'action-icon fa fa-calendar-alt fa-2x text-primary mr-3']);
    echo html_writer::tag('h4', get_string('manageconvocatorias', 'local_jobboard'), ['class' => 'card-title mb-0']);
    echo html_writer::end_div();
    echo html_writer::tag('p', get_string('convocatorias_dashboard_desc', 'local_jobboard'), ['class' => 'card-text text-muted']);

    // Convocatoria flow diagram.
    echo html_writer::start_div('workflow-diagram bg-light p-3 rounded mb-3');
    echo html_writer::tag('small', get_string('workflow_flow', 'local_jobboard') . ':', ['class' => 'd-block mb-2 font-weight-bold']);
    echo html_writer::start_div('d-flex align-items-center flex-wrap');
    echo html_writer::tag('span', get_string('convocatoria', 'local_jobboard'), ['class' => 'badge badge-primary mr-2']);
    echo html_writer::tag('span', '→', ['class' => 'text-muted mr-2']);
    echo html_writer::tag('span', get_string('vacancies', 'local_jobboard'), ['class' => 'badge badge-success mr-2']);
    echo html_writer::tag('span', '→', ['class' => 'text-muted mr-2']);
    echo html_writer::tag('span', get_string('applications', 'local_jobboard'), ['class' => 'badge badge-info mr-2']);
    echo html_writer::tag('span', '→', ['class' => 'text-muted mr-2']);
    echo html_writer::tag('span', get_string('selection', 'local_jobboard'), ['class' => 'badge badge-warning']);
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
        get_string('gotoconvocatorias', 'local_jobboard') . ' <i class="fa fa-arrow-right ml-2"></i>',
        ['class' => 'btn btn-primary btn-lg']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Review Documents Card.
    if ($canreview) {
        echo html_writer::start_div('col-lg-6 mb-4');
        echo html_writer::start_div('action-card card h-100 shadow-sm border-0');
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('d-flex align-items-center mb-3');
        echo html_writer::tag('span', '', ['class' => 'action-icon fa fa-file-alt fa-2x text-warning mr-3']);
        echo html_writer::tag('h4', get_string('reviewapplications', 'local_jobboard'), ['class' => 'card-title mb-0']);
        if (($stats['pending_reviews'] ?? 0) > 0) {
            echo html_writer::tag('span', $stats['pending_reviews'], ['class' => 'badge badge-danger ml-2']);
        }
        echo html_writer::end_div();
        echo html_writer::tag('p', get_string('review_dashboard_desc', 'local_jobboard'), ['class' => 'card-text text-muted']);

        if (($stats['pending_reviews'] ?? 0) > 0) {
            echo html_writer::start_div('alert alert-warning py-2 mb-3');
            echo html_writer::tag('small', get_string('pending_reviews_alert', 'local_jobboard', $stats['pending_reviews']));
            echo html_writer::end_div();
        }

        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
            get_string('gotoreview', 'local_jobboard') . ' <i class="fa fa-arrow-right ml-2"></i>',
            ['class' => 'btn btn-warning btn-lg']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // row

    // Secondary actions row.
    echo html_writer::start_div('row');

    // Reports Card.
    if ($canviewreports) {
        echo html_writer::start_div('col-md-4 mb-4');
        echo html_writer::start_div('action-card card h-100 shadow-sm border-0');
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('d-flex align-items-center mb-3');
        echo html_writer::tag('span', '', ['class' => 'action-icon fa fa-chart-bar fa-2x text-info mr-3']);
        echo html_writer::tag('h5', get_string('reports', 'local_jobboard'), ['class' => 'card-title mb-0']);
        echo html_writer::end_div();
        echo html_writer::tag('p', get_string('reports_dashboard_desc', 'local_jobboard'), ['class' => 'card-text text-muted small']);
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'reports']),
            get_string('viewreports', 'local_jobboard'),
            ['class' => 'btn btn-outline-info']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    // Configuration Card.
    if (has_capability('local/jobboard:configure', $context)) {
        echo html_writer::start_div('col-md-4 mb-4');
        echo html_writer::start_div('action-card card h-100 shadow-sm border-0');
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('d-flex align-items-center mb-3');
        echo html_writer::tag('span', '', ['class' => 'action-icon fa fa-cog fa-2x text-secondary mr-3']);
        echo html_writer::tag('h5', get_string('configuration', 'local_jobboard'), ['class' => 'card-title mb-0']);
        echo html_writer::end_div();
        echo html_writer::tag('p', get_string('config_dashboard_desc', 'local_jobboard'), ['class' => 'card-text text-muted small']);
        echo html_writer::link(
            new moodle_url('/local/jobboard/admin/doctypes.php'),
            get_string('configure', 'local_jobboard'),
            ['class' => 'btn btn-outline-secondary']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    // Exemptions Card.
    if (has_capability('local/jobboard:manageexemptions', $context)) {
        echo html_writer::start_div('col-md-4 mb-4');
        echo html_writer::start_div('action-card card h-100 shadow-sm border-0');
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('d-flex align-items-center mb-3');
        echo html_writer::tag('span', '', ['class' => 'action-icon fa fa-user-check fa-2x text-success mr-3']);
        echo html_writer::tag('h5', get_string('exemptions', 'local_jobboard'), ['class' => 'card-title mb-0']);
        echo html_writer::end_div();
        echo html_writer::tag('p', get_string('exemptions_dashboard_desc', 'local_jobboard'), ['class' => 'card-text text-muted small']);
        echo html_writer::link(
            new moodle_url('/local/jobboard/admin/exemptions.php'),
            get_string('manageexemptions', 'local_jobboard'),
            ['class' => 'btn btn-outline-success']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // row
    echo html_writer::end_div(); // admin-section
}

// ============================================================================
// APPLICANT VIEW - Simple and focused on applying
// ============================================================================
if ($canapply && !$canmanage) {
    echo html_writer::start_div('applicant-section');

    // User's application stats.
    echo html_writer::start_div('row mb-4');

    echo html_writer::start_div('col-md-4 col-sm-6 mb-3');
    echo html_writer::start_div('stat-card card h-100 border-info');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('div', $stats['my_applications'] ?? 0, ['class' => 'stat-number text-info']);
    echo html_writer::tag('div', get_string('myapplicationcount', 'local_jobboard'), ['class' => 'stat-label text-muted']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::start_div('col-md-4 col-sm-6 mb-3');
    echo html_writer::start_div('stat-card card h-100 border-success');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('div', $stats['available_vacancies'] ?? 0, ['class' => 'stat-number text-success']);
    echo html_writer::tag('div', get_string('availablevacancies', 'local_jobboard'), ['class' => 'stat-label text-muted']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::start_div('col-md-4 col-sm-6 mb-3');
    echo html_writer::start_div('stat-card card h-100 border-warning');
    echo html_writer::start_div('card-body text-center');
    echo html_writer::tag('div', $stats['pending_docs'] ?? 0, ['class' => 'stat-number text-warning']);
    echo html_writer::tag('div', get_string('pendingdocs', 'local_jobboard'), ['class' => 'stat-label text-muted']);
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // row

    // Main action cards for applicants.
    echo html_writer::start_div('row');

    // Browse Vacancies Card.
    echo html_writer::start_div('col-lg-6 mb-4');
    echo html_writer::start_div('action-card card h-100 shadow-sm border-0 bg-gradient-primary');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('span', '', ['class' => 'action-icon fa fa-briefcase fa-2x text-primary mr-3']);
    echo html_writer::tag('h4', get_string('browservacancies', 'local_jobboard'), ['class' => 'card-title mb-0']);
    echo html_writer::end_div();
    echo html_writer::tag('p', get_string('browse_vacancies_desc', 'local_jobboard'), ['class' => 'card-text text-muted']);

    if (($stats['available_vacancies'] ?? 0) > 0) {
        echo html_writer::start_div('alert alert-success py-2 mb-3');
        echo html_writer::tag('small', get_string('available_vacancies_alert', 'local_jobboard', $stats['available_vacancies']));
        echo html_writer::end_div();
    }

    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']),
        get_string('explorevacancias', 'local_jobboard') . ' <i class="fa fa-arrow-right ml-2"></i>',
        ['class' => 'btn btn-primary btn-lg']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // My Applications Card.
    echo html_writer::start_div('col-lg-6 mb-4');
    echo html_writer::start_div('action-card card h-100 shadow-sm border-0');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('span', '', ['class' => 'action-icon fa fa-folder-open fa-2x text-info mr-3']);
    echo html_writer::tag('h4', get_string('myapplications', 'local_jobboard'), ['class' => 'card-title mb-0']);
    if (($stats['my_applications'] ?? 0) > 0) {
        echo html_writer::tag('span', $stats['my_applications'], ['class' => 'badge badge-info ml-2']);
    }
    echo html_writer::end_div();
    echo html_writer::tag('p', get_string('myapplications_desc', 'local_jobboard'), ['class' => 'card-text text-muted']);

    if (($stats['pending_docs'] ?? 0) > 0) {
        echo html_writer::start_div('alert alert-warning py-2 mb-3');
        echo html_writer::tag('small', get_string('pending_docs_alert', 'local_jobboard', $stats['pending_docs']));
        echo html_writer::end_div();
    }

    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
        get_string('viewmyapplications', 'local_jobboard') . ' <i class="fa fa-arrow-right ml-2"></i>',
        ['class' => 'btn btn-info btn-lg']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // row
    echo html_writer::end_div(); // applicant-section
}

// ============================================================================
// REVIEWER-ONLY VIEW (not admin, just reviewer)
// ============================================================================
if ($canreview && !$canmanage) {
    echo html_writer::start_div('reviewer-section mb-5');
    echo html_writer::tag('h3', get_string('reviewertasks', 'local_jobboard'), ['class' => 'section-title mb-4']);

    echo html_writer::start_div('row');

    // My Reviews Card.
    echo html_writer::start_div('col-lg-6 mb-4');
    echo html_writer::start_div('action-card card h-100 shadow-sm border-0');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('span', '', ['class' => 'action-icon fa fa-tasks fa-2x text-warning mr-3']);
    echo html_writer::tag('h4', get_string('myreviews', 'local_jobboard'), ['class' => 'card-title mb-0']);
    if (($stats['my_pending_reviews'] ?? 0) > 0) {
        echo html_writer::tag('span', $stats['my_pending_reviews'], ['class' => 'badge badge-danger ml-2']);
    }
    echo html_writer::end_div();
    echo html_writer::tag('p', get_string('myreviews_desc', 'local_jobboard'), ['class' => 'card-text text-muted']);

    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'myreviews']),
        get_string('viewmyreviews', 'local_jobboard') . ' <i class="fa fa-arrow-right ml-2"></i>',
        ['class' => 'btn btn-warning btn-lg']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // row
    echo html_writer::end_div(); // reviewer-section
}

// ============================================================================
// RECENT ACTIVITY SECTION (for admins)
// ============================================================================
if ($canmanage && !empty($stats['recent_activity'])) {
    echo html_writer::start_div('recent-activity-section mt-4');
    echo html_writer::tag('h4', get_string('recentactivity', 'local_jobboard'), ['class' => 'section-title mb-3']);

    echo html_writer::start_div('list-group');
    foreach ($stats['recent_activity'] as $activity) {
        echo html_writer::start_div('list-group-item list-group-item-action d-flex justify-content-between align-items-center');
        echo html_writer::tag('span', $activity->description);
        echo html_writer::tag('small', userdate($activity->timecreated, get_string('strftimedatetime', 'langconfig')), ['class' => 'text-muted']);
        echo html_writer::end_div();
    }
    echo html_writer::end_div();

    echo html_writer::end_div();
}

echo html_writer::end_div(); // local-jobboard-dashboard

// Add inline styles for dashboard (will be moved to styles.css).
echo html_writer::tag('style', '
.local-jobboard-dashboard .stat-number {
    font-size: 2.5rem;
    font-weight: bold;
}
.local-jobboard-dashboard .stat-label {
    font-size: 0.875rem;
}
.local-jobboard-dashboard .stat-card {
    transition: transform 0.2s ease;
}
.local-jobboard-dashboard .stat-card:hover {
    transform: translateY(-2px);
}
.local-jobboard-dashboard .action-card {
    transition: box-shadow 0.2s ease, transform 0.2s ease;
}
.local-jobboard-dashboard .action-card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0,0,0,.15) !important;
    transform: translateY(-3px);
}
.local-jobboard-dashboard .workflow-diagram {
    border-left: 4px solid #007bff;
}
.local-jobboard-dashboard .section-title {
    color: #495057;
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}
.local-jobboard-dashboard .dashboard-header {
    border-left: 4px solid #007bff;
}
');

echo $OUTPUT->footer();
