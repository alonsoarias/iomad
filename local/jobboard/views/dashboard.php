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
 * Modern dashboard with role-based content and quick actions.
 * Organized in logical sections for better UX.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_jobboard\output\ui_helper;

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('dashboard', 'local_jobboard'));
$PAGE->set_heading(get_string('jobboard', 'local_jobboard'));
$PAGE->requires->css('/local/jobboard/styles.css');

// User capabilities.
$canmanage = has_capability('local/jobboard:createvacancy', $context);
$canreview = has_capability('local/jobboard:reviewdocuments', $context);
$canapply = has_capability('local/jobboard:apply', $context);
$canviewreports = has_capability('local/jobboard:viewreports', $context);
$canconfigure = has_capability('local/jobboard:configure', $context);
$canmanageexemptions = has_capability('local/jobboard:manageexemptions', $context);
$canexportdata = has_capability('local/jobboard:exportdata', $context);

// Get statistics.
$stats = local_jobboard_get_dashboard_stats($USER->id, $canmanage, $canreview);

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-dashboard');

// ============================================================================
// WELCOME HEADER
// ============================================================================
$welcomeMsg = $canmanage ? get_string('dashboard_admin_welcome', 'local_jobboard') :
              ($canapply ? get_string('dashboard_applicant_welcome', 'local_jobboard') : '');

echo html_writer::start_div('jb-welcome-section bg-gradient-primary text-white rounded-lg p-4 mb-4');
echo html_writer::start_div('d-flex justify-content-between align-items-center');
echo html_writer::start_div();
echo html_writer::tag('h2', get_string('dashboard', 'local_jobboard'), ['class' => 'mb-1 font-weight-bold']);
echo html_writer::tag('p', $welcomeMsg, ['class' => 'mb-0 opacity-75']);
echo html_writer::end_div();
echo html_writer::tag('i', '', ['class' => 'fa fa-briefcase fa-3x opacity-25']);
echo html_writer::end_div();
echo html_writer::end_div();

// ============================================================================
// ADMINISTRATOR/MANAGER DASHBOARD
// ============================================================================
if ($canmanage) {

    // ========================================================================
    // SECTION 1: STATISTICS
    // ========================================================================
    echo html_writer::start_div('row mb-4');
    echo ui_helper::stat_card(
        $stats['active_convocatorias'] ?? 0,
        get_string('activeconvocatorias', 'local_jobboard'),
        'primary', 'calendar-alt',
        new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias'])
    );
    echo ui_helper::stat_card(
        $stats['published_vacancies'] ?? 0,
        get_string('publishedvacancies', 'local_jobboard'),
        'success', 'briefcase',
        new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'status' => 'published'])
    );
    echo ui_helper::stat_card(
        $stats['total_applications'] ?? 0,
        get_string('totalapplications', 'local_jobboard'),
        'info', 'users',
        new moodle_url('/local/jobboard/index.php', ['view' => 'review'])
    );
    echo ui_helper::stat_card(
        $stats['pending_reviews'] ?? 0,
        get_string('pendingreviews', 'local_jobboard'),
        'warning', 'clock',
        new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'status' => 'submitted'])
    );
    echo html_writer::end_div();

    // ========================================================================
    // SECTION 2: QUICK ACTIONS (Main Management)
    // ========================================================================
    echo html_writer::tag('h4',
        '<i class="fa fa-bolt mr-2"></i>' . get_string('quickactions', 'local_jobboard'),
        ['class' => 'mb-3']
    );
    echo html_writer::start_div('row');

    // Manage Convocatorias Card.
    echo html_writer::start_div('col-lg-4 col-md-6 mb-4');
    echo html_writer::start_div('card h-100 shadow-sm border-0 jb-action-card');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('div',
        html_writer::tag('i', '', ['class' => 'fa fa-calendar-alt fa-lg text-primary']),
        ['class' => 'jb-icon-circle bg-primary-light mr-3']);
    echo html_writer::tag('h5', get_string('manageconvocatorias', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::tag('p', get_string('convocatorias_dashboard_desc', 'local_jobboard'),
        ['class' => 'text-muted small mb-3']);
    echo html_writer::start_div('d-flex');
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
        get_string('view', 'local_jobboard'),
        ['class' => 'btn btn-outline-primary btn-sm mr-2']
    );
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'action' => 'add']),
        '<i class="fa fa-plus mr-1"></i>' . get_string('addconvocatoria', 'local_jobboard'),
        ['class' => 'btn btn-primary btn-sm']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Manage Vacancies Card.
    echo html_writer::start_div('col-lg-4 col-md-6 mb-4');
    echo html_writer::start_div('card h-100 shadow-sm border-0 jb-action-card');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('div',
        html_writer::tag('i', '', ['class' => 'fa fa-briefcase fa-lg text-success']),
        ['class' => 'jb-icon-circle bg-success-light mr-3']);
    echo html_writer::tag('h5', get_string('managevacancies', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::tag('p', get_string('vacancies_dashboard_desc', 'local_jobboard'),
        ['class' => 'text-muted small mb-3']);
    echo html_writer::start_div('d-flex');
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
        get_string('view', 'local_jobboard'),
        ['class' => 'btn btn-outline-success btn-sm mr-2']
    );
    echo html_writer::link(
        new moodle_url('/local/jobboard/edit.php'),
        '<i class="fa fa-plus mr-1"></i>' . get_string('newvacancy', 'local_jobboard'),
        ['class' => 'btn btn-success btn-sm']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Review Applications Card.
    if ($canreview) {
        echo html_writer::start_div('col-lg-4 col-md-6 mb-4');
        echo html_writer::start_div('card h-100 shadow-sm border-0 jb-action-card');
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('d-flex align-items-center mb-3');
        echo html_writer::tag('div',
            html_writer::tag('i', '', ['class' => 'fa fa-file-alt fa-lg text-warning']),
            ['class' => 'jb-icon-circle bg-warning-light mr-3']);
        $reviewTitle = get_string('reviewapplications', 'local_jobboard');
        if (($stats['pending_reviews'] ?? 0) > 0) {
            $reviewTitle .= ' ' . html_writer::tag('span', $stats['pending_reviews'],
                ['class' => 'badge badge-danger']);
        }
        echo html_writer::tag('h5', $reviewTitle, ['class' => 'mb-0']);
        echo html_writer::end_div();
        echo html_writer::tag('p', get_string('review_dashboard_desc', 'local_jobboard'),
            ['class' => 'text-muted small mb-3']);
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
            get_string('gotoreview', 'local_jobboard'),
            ['class' => 'btn btn-warning btn-sm']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // row

    // ========================================================================
    // SECTION 3: CONFIGURATION & MANAGEMENT
    // ========================================================================
    if ($canviewreports || $canconfigure || $canmanageexemptions) {
        echo html_writer::tag('h4',
            '<i class="fa fa-cogs mr-2"></i>' . get_string('configuration', 'local_jobboard'),
            ['class' => 'mb-3 mt-4']
        );
        echo html_writer::start_div('row');

        // Reports.
        if ($canviewreports) {
            echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-chart-bar fa-2x text-info mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('reports', 'local_jobboard'), ['class' => 'mb-1']);
            echo html_writer::tag('small', get_string('reports_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'reports']),
                '<i class="fa fa-chart-line mr-1"></i>' . get_string('viewreports', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-info']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        // Plugin Settings.
        if ($canconfigure) {
            echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-cog fa-2x text-secondary mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('pluginsettings', 'local_jobboard'), ['class' => 'mb-1']);
            echo html_writer::tag('small', get_string('pluginsettings_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
            echo html_writer::link(
                new moodle_url('/admin/settings.php', ['section' => 'local_jobboard']),
                '<i class="fa fa-wrench mr-1"></i>' . get_string('configure', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-secondary']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        // Document Types.
        if ($canconfigure) {
            echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-file-alt fa-2x text-primary mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('doctypes', 'local_jobboard'), ['class' => 'mb-1']);
            echo html_writer::tag('small', get_string('doctypes_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/doctypes.php'),
                '<i class="fa fa-list mr-1"></i>' . get_string('manage', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-primary']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        // Manage User Exemptions.
        if ($canmanageexemptions) {
            echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-user-shield fa-2x text-warning mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('manageexemptions', 'local_jobboard'), ['class' => 'mb-1']);
            echo html_writer::tag('small', get_string('manageexemptions_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/manage_exemptions.php'),
                '<i class="fa fa-id-card mr-1"></i>' . get_string('manage', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-warning']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        echo html_writer::end_div(); // row
    }

    // ========================================================================
    // SECTION 4: DATA IMPORT/EXPORT TOOLS
    // ========================================================================
    if ($canmanage || $canexportdata) {
        echo html_writer::tag('h4',
            '<i class="fa fa-database mr-2"></i>' . get_string('datatools', 'local_jobboard'),
            ['class' => 'mb-3 mt-4']
        );
        echo html_writer::start_div('row');

        // Import Vacancies from CSV.
        if ($canmanage) {
            echo html_writer::start_div('col-md-4 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-file-csv fa-2x text-primary mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('importvacancies', 'local_jobboard'), ['class' => 'mb-1']);
            echo html_writer::tag('small', get_string('importvacancies_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/import_vacancies.php'),
                '<i class="fa fa-upload mr-1"></i>' . get_string('import', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-primary']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        // Import Exemptions from CSV.
        if ($canmanageexemptions) {
            echo html_writer::start_div('col-md-4 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-file-import fa-2x text-success mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('importexemptions', 'local_jobboard'), ['class' => 'mb-1']);
            echo html_writer::tag('small', get_string('importexemptions_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/import_exemptions.php'),
                '<i class="fa fa-upload mr-1"></i>' . get_string('import', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-success']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        // Export Documents ZIP.
        if ($canexportdata) {
            echo html_writer::start_div('col-md-4 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-file-archive fa-2x text-info mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('exportdocuments', 'local_jobboard'), ['class' => 'mb-1']);
            echo html_writer::tag('small', get_string('exportdocuments_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
                '<i class="fa fa-download mr-1"></i>' . get_string('export', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-info']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        echo html_writer::end_div(); // row
    }

    // ========================================================================
    // SECTION 5: SYSTEM MIGRATION (Admin Only)
    // ========================================================================
    if ($canconfigure) {
        echo html_writer::tag('h4',
            '<i class="fa fa-server mr-2"></i>' . get_string('systemmigration', 'local_jobboard'),
            ['class' => 'mb-3 mt-4']
        );

        echo html_writer::start_div('row');
        echo html_writer::start_div('col-12');

        // Full Plugin Migration - Highlighted card.
        echo html_writer::start_div('card border-danger shadow-sm');
        echo html_writer::start_div('card-header bg-danger text-white');
        echo html_writer::tag('h5',
            '<i class="fa fa-exchange-alt mr-2"></i>' . get_string('migrateplugin', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');

        echo html_writer::start_div('row align-items-center');

        // Description column.
        echo html_writer::start_div('col-md-8');
        echo html_writer::tag('p', get_string('migrateplugin_full_desc', 'local_jobboard'), ['class' => 'mb-2']);
        echo html_writer::start_tag('ul', ['class' => 'small text-muted mb-0']);
        echo html_writer::tag('li', get_string('migrate_includes_doctypes', 'local_jobboard'));
        echo html_writer::tag('li', get_string('migrate_includes_convocatorias', 'local_jobboard'));
        echo html_writer::tag('li', get_string('migrate_includes_vacancies', 'local_jobboard'));
        echo html_writer::tag('li', get_string('migrate_includes_applications', 'local_jobboard'));
        echo html_writer::tag('li', get_string('migrate_includes_files', 'local_jobboard'));
        echo html_writer::end_tag('ul');
        echo html_writer::end_div();

        // Button column.
        echo html_writer::start_div('col-md-4 text-md-right mt-3 mt-md-0');
        echo html_writer::link(
            new moodle_url('/local/jobboard/migrate.php'),
            '<i class="fa fa-exchange-alt mr-2"></i>' . get_string('openmigrationtool', 'local_jobboard'),
            ['class' => 'btn btn-danger btn-lg']
        );
        echo html_writer::end_div();

        echo html_writer::end_div(); // row

        echo html_writer::end_div(); // card-body
        echo html_writer::end_div(); // card

        echo html_writer::end_div(); // col-12
        echo html_writer::end_div(); // row
    }
}

// ============================================================================
// APPLICANT DASHBOARD
// ============================================================================
if ($canapply && !$canmanage) {
    // Get active convocatorias count.
    $activeConvocatorias = $DB->count_records('local_jobboard_convocatoria', ['status' => 'open']);

    // Statistics Row.
    echo html_writer::start_div('row mb-4');
    echo ui_helper::stat_card(
        (string) $activeConvocatorias,
        get_string('activeconvocatorias', 'local_jobboard'),
        'primary', 'calendar-alt',
        new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias'])
    );
    echo ui_helper::stat_card(
        $stats['my_applications'] ?? 0,
        get_string('myapplicationcount', 'local_jobboard'),
        'info', 'folder-open',
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications'])
    );
    echo ui_helper::stat_card(
        $stats['available_vacancies'] ?? 0,
        get_string('availablevacancies', 'local_jobboard'),
        'success', 'briefcase',
        new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies'])
    );
    echo ui_helper::stat_card(
        $stats['pending_docs'] ?? 0,
        get_string('pendingdocs', 'local_jobboard'),
        'warning', 'file-alt'
    );
    echo html_writer::end_div();

    // Action Cards.
    echo html_writer::start_div('row');

    // Browse Convocatorias - PRIMARY ENTRY POINT.
    echo html_writer::start_div('col-lg-4 mb-4');
    echo html_writer::start_div('card h-100 shadow-sm border-0 border-left-primary jb-action-card');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('i', '', ['class' => 'fa fa-calendar-alt fa-2x text-primary mr-3']);
    echo html_writer::tag('h4', get_string('browseconvocatorias', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::tag('p', get_string('convocatoriahelp', 'local_jobboard'), ['class' => 'text-muted']);

    if ($activeConvocatorias > 0) {
        echo html_writer::div(
            '<i class="fa fa-info-circle mr-1"></i>' .
            get_string('activeconvocatorias_alert', 'local_jobboard', $activeConvocatorias),
            'alert alert-primary py-2 mb-3'
        );
    }

    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']),
        '<i class="fa fa-arrow-right mr-2"></i>' . get_string('viewconvocatorias', 'local_jobboard'),
        ['class' => 'btn btn-primary']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Browse Vacancies.
    echo html_writer::start_div('col-lg-4 mb-4');
    echo html_writer::start_div('card h-100 shadow-sm border-0 border-left-success jb-action-card');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('i', '', ['class' => 'fa fa-briefcase fa-2x text-success mr-3']);
    echo html_writer::tag('h4', get_string('browservacancies', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::tag('p', get_string('browse_vacancies_desc', 'local_jobboard'), ['class' => 'text-muted']);

    if (($stats['available_vacancies'] ?? 0) > 0) {
        echo html_writer::div(
            '<i class="fa fa-info-circle mr-1"></i>' .
            get_string('available_vacancies_alert', 'local_jobboard', $stats['available_vacancies']),
            'alert alert-success py-2 mb-3'
        );
    }

    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']),
        '<i class="fa fa-arrow-right mr-2"></i>' . get_string('explorevacancias', 'local_jobboard'),
        ['class' => 'btn btn-success']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // My Applications.
    echo html_writer::start_div('col-lg-4 mb-4');
    echo html_writer::start_div('card h-100 shadow-sm border-0 border-left-info jb-action-card');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('i', '', ['class' => 'fa fa-folder-open fa-2x text-info mr-3']);
    $appTitle = get_string('myapplications', 'local_jobboard');
    if (($stats['my_applications'] ?? 0) > 0) {
        $appTitle .= ' ' . html_writer::tag('span', $stats['my_applications'], ['class' => 'badge badge-info']);
    }
    echo html_writer::tag('h4', $appTitle, ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::tag('p', get_string('myapplications_desc', 'local_jobboard'), ['class' => 'text-muted']);

    if (($stats['pending_docs'] ?? 0) > 0) {
        echo html_writer::div(
            '<i class="fa fa-exclamation-triangle mr-1"></i>' .
            get_string('pending_docs_alert', 'local_jobboard', $stats['pending_docs']),
            'alert alert-warning py-2 mb-3'
        );
    }

    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
        '<i class="fa fa-arrow-right mr-2"></i>' . get_string('viewmyapplications', 'local_jobboard'),
        ['class' => 'btn btn-info']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();
}

// ============================================================================
// REVIEWER-ONLY DASHBOARD
// ============================================================================
if ($canreview && !$canmanage) {
    echo html_writer::tag('h4', get_string('reviewertasks', 'local_jobboard'), ['class' => 'mb-3']);

    echo html_writer::start_div('row');

    // My Reviews.
    echo html_writer::start_div('col-lg-6 mb-4');
    echo html_writer::start_div('card h-100 shadow-sm border-0 border-left-warning');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('i', '', ['class' => 'fa fa-tasks fa-2x text-warning mr-3']);
    $reviewTitle = get_string('myreviews', 'local_jobboard');
    if (($stats['my_pending_reviews'] ?? 0) > 0) {
        $reviewTitle .= ' ' . html_writer::tag('span', $stats['my_pending_reviews'],
            ['class' => 'badge badge-danger']);
    }
    echo html_writer::tag('h4', $reviewTitle, ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::tag('p', get_string('myreviews_desc', 'local_jobboard'), ['class' => 'text-muted']);
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'myreviews']),
        '<i class="fa fa-arrow-right mr-2"></i>' . get_string('viewmyreviews', 'local_jobboard'),
        ['class' => 'btn btn-warning']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();
}

echo html_writer::end_div(); // local-jobboard-dashboard

echo $OUTPUT->footer();
