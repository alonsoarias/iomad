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
 * Role-based dashboard with organized sections per user capabilities.
 *
 * Roles and their main sections:
 * - Administrator (configure): Full system management
 * - Manager (createvacancy): Content management + Review
 * - Reviewer (reviewdocuments): Review tasks
 * - Applicant (apply): Browse and apply
 * - Viewer (view): Public access only
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

// ============================================================================
// USER CAPABILITIES
// ============================================================================
$caps = [
    // Administration.
    'configure' => has_capability('local/jobboard:configure', $context),
    'managedoctypes' => has_capability('local/jobboard:managedoctypes', $context),
    'manageemailtemplates' => has_capability('local/jobboard:manageemailtemplates', $context),
    'manageexemptions' => has_capability('local/jobboard:manageexemptions', $context),
    'manageworkflow' => has_capability('local/jobboard:manageworkflow', $context),

    // Content management.
    'manageconvocatorias' => has_capability('local/jobboard:manageconvocatorias', $context),
    'createvacancy' => has_capability('local/jobboard:createvacancy', $context),
    'editvacancy' => has_capability('local/jobboard:editvacancy', $context),
    'publishvacancy' => has_capability('local/jobboard:publishvacancy', $context),
    'viewallvacancies' => has_capability('local/jobboard:viewallvacancies', $context),

    // Review and evaluation.
    'reviewdocuments' => has_capability('local/jobboard:reviewdocuments', $context),
    'validatedocuments' => has_capability('local/jobboard:validatedocuments', $context),
    'assignreviewers' => has_capability('local/jobboard:assignreviewers', $context),
    'evaluate' => has_capability('local/jobboard:evaluate', $context),
    'viewevaluations' => has_capability('local/jobboard:viewevaluations', $context),
    'viewallapplications' => has_capability('local/jobboard:viewallapplications', $context),
    'changeapplicationstatus' => has_capability('local/jobboard:changeapplicationstatus', $context),

    // Reports and data.
    'viewreports' => has_capability('local/jobboard:viewreports', $context),
    'exportreports' => has_capability('local/jobboard:exportreports', $context),
    'exportdata' => has_capability('local/jobboard:exportdata', $context),

    // Applicant.
    'apply' => has_capability('local/jobboard:apply', $context),
    'viewownapplications' => has_capability('local/jobboard:viewownapplications', $context),

    // General view.
    'view' => has_capability('local/jobboard:view', $context),
    'viewinternalvacancies' => has_capability('local/jobboard:viewinternalvacancies', $context),
];

// Derived role flags for cleaner conditionals.
$isAdmin = $caps['configure'];
$isManager = $caps['createvacancy'] || $caps['manageconvocatorias'];
$isReviewer = $caps['reviewdocuments'] || $caps['validatedocuments'];
$isApplicant = $caps['apply'];
$canManageContent = $isAdmin || $isManager;

// Get statistics.
$stats = local_jobboard_get_dashboard_stats($USER->id, $canManageContent, $isReviewer);

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-dashboard');

// ============================================================================
// WELCOME HEADER
// ============================================================================
$roleLabel = $isAdmin ? get_string('role_administrator', 'local_jobboard') :
             ($isManager ? get_string('role_manager', 'local_jobboard') :
             ($isReviewer ? get_string('role_reviewer', 'local_jobboard') :
             ($isApplicant ? get_string('role_applicant', 'local_jobboard') : '')));

$welcomeMsg = $isAdmin ? get_string('dashboard_admin_welcome', 'local_jobboard') :
              ($isManager ? get_string('dashboard_manager_welcome', 'local_jobboard') :
              ($isReviewer ? get_string('dashboard_reviewer_welcome', 'local_jobboard') :
              ($isApplicant ? get_string('dashboard_applicant_welcome', 'local_jobboard') : '')));

echo html_writer::start_div('jb-welcome-section bg-gradient-primary text-white rounded-lg p-4 mb-4');
echo html_writer::start_div('d-flex justify-content-between align-items-center');
echo html_writer::start_div();
echo html_writer::tag('h2', get_string('dashboard', 'local_jobboard'), ['class' => 'mb-1 font-weight-bold']);
if ($roleLabel) {
    echo html_writer::tag('span', $roleLabel, ['class' => 'badge badge-light mr-2']);
}
echo html_writer::tag('p', $welcomeMsg, ['class' => 'mb-0 opacity-75 mt-2']);
echo html_writer::end_div();
echo html_writer::tag('i', '', ['class' => 'fa fa-briefcase fa-3x opacity-25']);
echo html_writer::end_div();

// Quick access to public view for everyone.
$enablepublic = get_config('local_jobboard', 'enable_public_page');
if ($enablepublic) {
    echo html_writer::start_div('mt-3 pt-3 border-top border-light');
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
        '<i class="fa fa-globe mr-2"></i>' . get_string('viewpublicpage', 'local_jobboard'),
        ['class' => 'btn btn-light btn-sm mr-2']
    );
    if ($isApplicant) {
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'applications']),
            '<i class="fa fa-folder-open mr-2"></i>' . get_string('myapplications', 'local_jobboard'),
            ['class' => 'btn btn-outline-light btn-sm']
        );
    }
    echo html_writer::end_div();
}
echo html_writer::end_div();

// ============================================================================
// ADMINISTRATOR / MANAGER DASHBOARD
// ============================================================================
if ($canManageContent) {

    // ========================================================================
    // SECTION 1: KEY STATISTICS
    // ========================================================================
    echo html_writer::tag('h4',
        '<i class="fa fa-chart-pie mr-2"></i>' . get_string('overview', 'local_jobboard'),
        ['class' => 'mb-3']
    );
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
    // SECTION 2: CONTENT MANAGEMENT
    // ========================================================================
    echo html_writer::tag('h4',
        '<i class="fa fa-folder-open mr-2"></i>' . get_string('contentmanagement', 'local_jobboard'),
        ['class' => 'mb-3']
    );
    echo html_writer::start_div('row mb-4');

    // Manage Convocatorias.
    if ($caps['manageconvocatorias'] || $isAdmin) {
        echo html_writer::start_div('col-lg-4 col-md-6 mb-4');
        echo html_writer::start_div('card h-100 shadow-sm border-0 jb-action-card');
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('d-flex align-items-center mb-3');
        echo html_writer::tag('div',
            html_writer::tag('i', '', ['class' => 'fa fa-calendar-alt fa-lg text-primary']),
            ['class' => 'jb-icon-circle bg-primary-light mr-3']);
        echo html_writer::tag('h5', get_string('convocatorias', 'local_jobboard'), ['class' => 'mb-0']);
        echo html_writer::end_div();
        echo html_writer::tag('p', get_string('convocatorias_dashboard_desc', 'local_jobboard'),
            ['class' => 'text-muted small mb-3']);

        // Stats mini.
        echo html_writer::start_div('d-flex mb-3');
        echo html_writer::tag('span',
            '<strong>' . ($stats['active_convocatorias'] ?? 0) . '</strong> ' . get_string('active', 'local_jobboard'),
            ['class' => 'badge badge-primary mr-2']
        );
        echo html_writer::tag('span',
            '<strong>' . ($stats['draft_convocatorias'] ?? 0) . '</strong> ' . get_string('draft', 'local_jobboard'),
            ['class' => 'badge badge-secondary']
        );
        echo html_writer::end_div();

        echo html_writer::start_div('d-flex');
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
            '<i class="fa fa-list mr-1"></i>' . get_string('viewall', 'local_jobboard'),
            ['class' => 'btn btn-outline-primary btn-sm mr-2']
        );
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'action' => 'add']),
            '<i class="fa fa-plus mr-1"></i>' . get_string('addnew', 'local_jobboard'),
            ['class' => 'btn btn-primary btn-sm']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    // Manage Vacancies.
    if ($caps['createvacancy']) {
        echo html_writer::start_div('col-lg-4 col-md-6 mb-4');
        echo html_writer::start_div('card h-100 shadow-sm border-0 jb-action-card');
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('d-flex align-items-center mb-3');
        echo html_writer::tag('div',
            html_writer::tag('i', '', ['class' => 'fa fa-briefcase fa-lg text-success']),
            ['class' => 'jb-icon-circle bg-success-light mr-3']);
        echo html_writer::tag('h5', get_string('vacancies', 'local_jobboard'), ['class' => 'mb-0']);
        echo html_writer::end_div();
        echo html_writer::tag('p', get_string('vacancies_dashboard_desc', 'local_jobboard'),
            ['class' => 'text-muted small mb-3']);

        // Stats mini.
        echo html_writer::start_div('d-flex mb-3');
        echo html_writer::tag('span',
            '<strong>' . ($stats['published_vacancies'] ?? 0) . '</strong> ' . get_string('published', 'local_jobboard'),
            ['class' => 'badge badge-success mr-2']
        );
        echo html_writer::tag('span',
            '<strong>' . ($stats['draft_vacancies'] ?? 0) . '</strong> ' . get_string('draft', 'local_jobboard'),
            ['class' => 'badge badge-secondary']
        );
        echo html_writer::end_div();

        echo html_writer::start_div('d-flex');
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'manage']),
            '<i class="fa fa-list mr-1"></i>' . get_string('viewall', 'local_jobboard'),
            ['class' => 'btn btn-outline-success btn-sm mr-2']
        );
        echo html_writer::link(
            new moodle_url('/local/jobboard/edit.php'),
            '<i class="fa fa-plus mr-1"></i>' . get_string('addnew', 'local_jobboard'),
            ['class' => 'btn btn-success btn-sm']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    // Review Applications.
    if ($caps['viewallapplications'] || $isReviewer) {
        echo html_writer::start_div('col-lg-4 col-md-6 mb-4');
        echo html_writer::start_div('card h-100 shadow-sm border-0 jb-action-card');
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('d-flex align-items-center mb-3');
        echo html_writer::tag('div',
            html_writer::tag('i', '', ['class' => 'fa fa-clipboard-check fa-lg text-warning']),
            ['class' => 'jb-icon-circle bg-warning-light mr-3']);
        $reviewTitle = get_string('applications', 'local_jobboard');
        if (($stats['pending_reviews'] ?? 0) > 0) {
            $reviewTitle .= ' ' . html_writer::tag('span', $stats['pending_reviews'], ['class' => 'badge badge-danger']);
        }
        echo html_writer::tag('h5', $reviewTitle, ['class' => 'mb-0']);
        echo html_writer::end_div();
        echo html_writer::tag('p', get_string('review_dashboard_desc', 'local_jobboard'),
            ['class' => 'text-muted small mb-3']);

        // Stats mini.
        echo html_writer::start_div('d-flex mb-3');
        echo html_writer::tag('span',
            '<strong>' . ($stats['pending_reviews'] ?? 0) . '</strong> ' . get_string('pending', 'local_jobboard'),
            ['class' => 'badge badge-warning mr-2']
        );
        echo html_writer::tag('span',
            '<strong>' . ($stats['total_applications'] ?? 0) . '</strong> ' . get_string('total', 'local_jobboard'),
            ['class' => 'badge badge-info']
        );
        echo html_writer::end_div();

        echo html_writer::start_div('d-flex');
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
            '<i class="fa fa-eye mr-1"></i>' . get_string('reviewall', 'local_jobboard'),
            ['class' => 'btn btn-warning btn-sm']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // row

    // ========================================================================
    // SECTION 3: REPORTS & DATA
    // ========================================================================
    if ($caps['viewreports'] || $caps['exportdata']) {
        echo html_writer::tag('h4',
            '<i class="fa fa-chart-bar mr-2"></i>' . get_string('reportsanddata', 'local_jobboard'),
            ['class' => 'mb-3']
        );
        echo html_writer::start_div('row mb-4');

        // Reports.
        if ($caps['viewreports']) {
            echo html_writer::start_div('col-md-4 col-sm-6 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-chart-line fa-2x text-info mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('reports', 'local_jobboard'), ['class' => 'mb-1']);
            echo html_writer::tag('small', get_string('reports_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'reports']),
                '<i class="fa fa-chart-bar mr-1"></i>' . get_string('viewreports', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-info']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        // Import Vacancies.
        if ($caps['createvacancy']) {
            echo html_writer::start_div('col-md-4 col-sm-6 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-file-import fa-2x text-primary mr-3']);
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

        // Export Data.
        if ($caps['exportdata']) {
            echo html_writer::start_div('col-md-4 col-sm-6 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-file-export fa-2x text-success mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('exportdata', 'local_jobboard'), ['class' => 'mb-1']);
            echo html_writer::tag('small', get_string('exportdata_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
                '<i class="fa fa-download mr-1"></i>' . get_string('export', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-success']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        echo html_writer::end_div(); // row
    }

    // ========================================================================
    // SECTION 4: SYSTEM CONFIGURATION (Admin only)
    // ========================================================================
    if ($isAdmin) {
        echo html_writer::tag('h4',
            '<i class="fa fa-cogs mr-2"></i>' . get_string('systemconfiguration', 'local_jobboard'),
            ['class' => 'mb-3']
        );
        echo html_writer::start_div('row mb-4');

        // Plugin Settings.
        echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
        echo html_writer::start_div('card border-0 bg-light h-100');
        echo html_writer::start_div('card-body d-flex align-items-center');
        echo html_writer::tag('i', '', ['class' => 'fa fa-sliders-h fa-2x text-secondary mr-3']);
        echo html_writer::start_div();
        echo html_writer::tag('h6', get_string('pluginsettings', 'local_jobboard'), ['class' => 'mb-1']);
        echo html_writer::tag('small', get_string('pluginsettings_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
        echo html_writer::link(
            new moodle_url('/admin/settings.php', ['section' => 'local_jobboard']),
            '<i class="fa fa-cog mr-1"></i>' . get_string('configure', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-secondary']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();

        // Document Types.
        if ($caps['managedoctypes']) {
            echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-file-alt fa-2x text-primary mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('doctypes', 'local_jobboard'), ['class' => 'mb-1']);
            echo html_writer::tag('small', get_string('doctypes_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/admin/doctypes.php'),
                '<i class="fa fa-list mr-1"></i>' . get_string('manage', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-primary']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        // Email Templates.
        if ($caps['manageemailtemplates']) {
            echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-envelope fa-2x text-info mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('emailtemplates', 'local_jobboard'), ['class' => 'mb-1']);
            echo html_writer::tag('small', get_string('emailtemplates_desc', 'local_jobboard'), ['class' => 'd-block text-muted mb-2']);
            echo html_writer::link(
                new moodle_url('/local/jobboard/admin/email_templates.php'),
                '<i class="fa fa-edit mr-1"></i>' . get_string('manage', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-info']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        // User Exemptions.
        if ($caps['manageexemptions']) {
            echo html_writer::start_div('col-md-3 col-sm-6 mb-3');
            echo html_writer::start_div('card border-0 bg-light h-100');
            echo html_writer::start_div('card-body d-flex align-items-center');
            echo html_writer::tag('i', '', ['class' => 'fa fa-user-shield fa-2x text-warning mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', get_string('exemptions', 'local_jobboard'), ['class' => 'mb-1']);
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

        // Migration Tool (separate card for visibility).
        echo html_writer::start_div('row mb-4');
        echo html_writer::start_div('col-12');
        echo html_writer::start_div('card border-danger shadow-sm');
        echo html_writer::start_div('card-header bg-danger text-white d-flex justify-content-between align-items-center');
        echo html_writer::tag('h5',
            '<i class="fa fa-exchange-alt mr-2"></i>' . get_string('systemmigration', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::tag('span', get_string('adminonly', 'local_jobboard'), ['class' => 'badge badge-light']);
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('row align-items-center');
        echo html_writer::start_div('col-md-9');
        echo html_writer::tag('p', get_string('migrateplugin_full_desc', 'local_jobboard'), ['class' => 'mb-2']);
        echo html_writer::start_tag('ul', ['class' => 'small text-muted mb-0 list-unstyled']);
        echo html_writer::tag('li', '<i class="fa fa-check text-success mr-2"></i>' . get_string('migrate_includes_doctypes', 'local_jobboard'));
        echo html_writer::tag('li', '<i class="fa fa-check text-success mr-2"></i>' . get_string('migrate_includes_convocatorias', 'local_jobboard'));
        echo html_writer::tag('li', '<i class="fa fa-check text-success mr-2"></i>' . get_string('migrate_includes_vacancies', 'local_jobboard'));
        echo html_writer::tag('li', '<i class="fa fa-check text-success mr-2"></i>' . get_string('migrate_includes_applications', 'local_jobboard'));
        echo html_writer::end_tag('ul');
        echo html_writer::end_div();
        echo html_writer::start_div('col-md-3 text-md-right mt-3 mt-md-0');
        echo html_writer::link(
            new moodle_url('/local/jobboard/migrate.php'),
            '<i class="fa fa-exchange-alt mr-2"></i>' . get_string('openmigrationtool', 'local_jobboard'),
            ['class' => 'btn btn-danger']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }
}

// ============================================================================
// REVIEWER DASHBOARD (for reviewers who are not managers)
// ============================================================================
if ($isReviewer && !$canManageContent) {

    // Statistics.
    echo html_writer::tag('h4',
        '<i class="fa fa-chart-pie mr-2"></i>' . get_string('reviewoverview', 'local_jobboard'),
        ['class' => 'mb-3']
    );
    echo html_writer::start_div('row mb-4');

    echo ui_helper::stat_card(
        $stats['my_pending_reviews'] ?? 0,
        get_string('mypendingreviews', 'local_jobboard'),
        'warning', 'clock',
        new moodle_url('/local/jobboard/index.php', ['view' => 'myreviews'])
    );
    echo ui_helper::stat_card(
        $stats['my_completed_reviews'] ?? 0,
        get_string('completedreviews', 'local_jobboard'),
        'success', 'check-circle',
        new moodle_url('/local/jobboard/index.php', ['view' => 'myreviews', 'status' => 'completed'])
    );

    echo html_writer::end_div();

    // Review Tasks.
    echo html_writer::tag('h4',
        '<i class="fa fa-tasks mr-2"></i>' . get_string('reviewertasks', 'local_jobboard'),
        ['class' => 'mb-3']
    );
    echo html_writer::start_div('row mb-4');

    // My Reviews.
    echo html_writer::start_div('col-lg-6 mb-4');
    echo html_writer::start_div('card h-100 shadow-sm border-left-warning');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('i', '', ['class' => 'fa fa-clipboard-list fa-2x text-warning mr-3']);
    $reviewTitle = get_string('myreviews', 'local_jobboard');
    if (($stats['my_pending_reviews'] ?? 0) > 0) {
        $reviewTitle .= ' ' . html_writer::tag('span', $stats['my_pending_reviews'], ['class' => 'badge badge-danger']);
    }
    echo html_writer::tag('h4', $reviewTitle, ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::tag('p', get_string('myreviews_desc', 'local_jobboard'), ['class' => 'text-muted']);

    if (($stats['my_pending_reviews'] ?? 0) > 0) {
        echo html_writer::div(
            '<i class="fa fa-exclamation-circle mr-1"></i>' .
            get_string('pendingreviews_alert', 'local_jobboard', $stats['my_pending_reviews']),
            'alert alert-warning py-2 mb-3'
        );
    }

    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'myreviews']),
        '<i class="fa fa-arrow-right mr-2"></i>' . get_string('viewmyreviews', 'local_jobboard'),
        ['class' => 'btn btn-warning']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Browse Applications (if can view all).
    if ($caps['viewallapplications']) {
        echo html_writer::start_div('col-lg-6 mb-4');
        echo html_writer::start_div('card h-100 shadow-sm border-left-info');
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('d-flex align-items-center mb-3');
        echo html_writer::tag('i', '', ['class' => 'fa fa-users fa-2x text-info mr-3']);
        echo html_writer::tag('h4', get_string('allapplications', 'local_jobboard'), ['class' => 'mb-0']);
        echo html_writer::end_div();
        echo html_writer::tag('p', get_string('allapplications_desc', 'local_jobboard'), ['class' => 'text-muted']);
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
            '<i class="fa fa-arrow-right mr-2"></i>' . get_string('viewall', 'local_jobboard'),
            ['class' => 'btn btn-info']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::end_div();
}

// ============================================================================
// APPLICANT DASHBOARD
// ============================================================================
if ($isApplicant && !$canManageContent && !$isReviewer) {

    // Get counts.
    $activeConvocatorias = $DB->count_records_select('local_jobboard_convocatoria',
        "status = 'open' AND enddate >= :now", ['now' => time()]);

    // Statistics.
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
        'warning', 'file-upload'
    );

    echo html_writer::end_div();

    // Quick Actions.
    echo html_writer::tag('h4',
        '<i class="fa fa-bolt mr-2"></i>' . get_string('quickactions', 'local_jobboard'),
        ['class' => 'mb-3']
    );
    echo html_writer::start_div('row');

    // Browse Convocatorias.
    echo html_writer::start_div('col-lg-4 mb-4');
    echo html_writer::start_div('card h-100 shadow-sm border-left-primary');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('i', '', ['class' => 'fa fa-calendar-alt fa-2x text-primary mr-3']);
    echo html_writer::tag('h4', get_string('convocatorias', 'local_jobboard'), ['class' => 'mb-0']);
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
        '<i class="fa fa-arrow-right mr-2"></i>' . get_string('explore', 'local_jobboard'),
        ['class' => 'btn btn-primary']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Browse Vacancies.
    echo html_writer::start_div('col-lg-4 mb-4');
    echo html_writer::start_div('card h-100 shadow-sm border-left-success');
    echo html_writer::start_div('card-body');
    echo html_writer::start_div('d-flex align-items-center mb-3');
    echo html_writer::tag('i', '', ['class' => 'fa fa-briefcase fa-2x text-success mr-3']);
    echo html_writer::tag('h4', get_string('vacancies', 'local_jobboard'), ['class' => 'mb-0']);
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
        '<i class="fa fa-arrow-right mr-2"></i>' . get_string('explore', 'local_jobboard'),
        ['class' => 'btn btn-success']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // My Applications.
    echo html_writer::start_div('col-lg-4 mb-4');
    echo html_writer::start_div('card h-100 shadow-sm border-left-info');
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
// VIEWER ONLY (no apply capability)
// ============================================================================
if (!$canManageContent && !$isReviewer && !$isApplicant && $caps['view']) {
    echo html_writer::start_div('card shadow-sm');
    echo html_writer::start_div('card-body text-center py-5');
    echo html_writer::tag('i', '', ['class' => 'fa fa-briefcase fa-4x text-muted mb-4']);
    echo html_writer::tag('h3', get_string('welcometojobboard', 'local_jobboard'), ['class' => 'mb-3']);
    echo html_writer::tag('p', get_string('vieweronly_desc', 'local_jobboard'), ['class' => 'text-muted mb-4']);

    if ($enablepublic) {
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'public']),
            '<i class="fa fa-globe mr-2"></i>' . get_string('viewpublicvacancies', 'local_jobboard'),
            ['class' => 'btn btn-primary btn-lg']
        );
    }
    echo html_writer::end_div();
    echo html_writer::end_div();
}

echo html_writer::end_div(); // local-jobboard-dashboard

echo $OUTPUT->footer();
