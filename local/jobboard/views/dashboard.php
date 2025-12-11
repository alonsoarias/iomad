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
 * Uses Mustache templates via renderer for clean separation of concerns.
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

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('dashboard', 'local_jobboard'));
$PAGE->set_heading(get_string('jobboard', 'local_jobboard'));

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

// Derived role flags for statistics.
$isAdmin = $caps['configure'];
$isManager = $caps['createvacancy'] || $caps['manageconvocatorias'];
$isReviewer = $caps['reviewdocuments'] || $caps['validatedocuments'];
$canManageContent = $isAdmin || $isManager;

// Get statistics.
$stats = local_jobboard_get_dashboard_stats($USER->id, $canManageContent, $isReviewer);

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare dashboard data using the renderer.
$data = $renderer->prepare_dashboard_data($USER->id, $caps, $stats);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_dashboard_page($data);
echo $OUTPUT->footer();
