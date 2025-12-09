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
 * Main entry point for local_jobboard.
 *
 * This file serves as the unified router for all jobboard views.
 * All URLs should use this entry point with the 'view' parameter.
 *
 * URL Structure:
 *   /local/jobboard/index.php                              - Dashboard (default)
 *   /local/jobboard/index.php?view=browse_convocatorias    - Browse convocatorias (main entry for users)
 *   /local/jobboard/index.php?view=view_convocatoria&id=X  - View convocatoria with vacancies
 *   /local/jobboard/index.php?view=convocatorias           - Manage convocatorias (admin)
 *   /local/jobboard/index.php?view=convocatoria&id=X       - Edit convocatoria
 *   /local/jobboard/index.php?view=convocatoria&action=add - Create new convocatoria
 *   /local/jobboard/index.php?view=vacancies               - Vacancies listing
 *   /local/jobboard/index.php?view=vacancies&convocatoriaid=X - Vacancies filtered by convocatoria
 *   /local/jobboard/index.php?view=vacancy&id=X            - Vacancy detail
 *   /local/jobboard/index.php?view=manage                  - Manage vacancies
 *   /local/jobboard/index.php?view=apply&vacancyid=X       - Apply form
 *   /local/jobboard/index.php?view=applications            - My applications
 *   /local/jobboard/index.php?view=application&id=X        - Application detail
 *   /local/jobboard/index.php?view=review                  - Review applications
 *   /local/jobboard/index.php?view=myreviews               - My reviews
 *   /local/jobboard/index.php?view=reports                 - Reports
 *   /local/jobboard/index.php?view=public                  - Public vacancies (no auth)
 *   /local/jobboard/index.php?view=public&id=X             - Public vacancy detail
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

// Main routing parameters.
$view = optional_param('view', 'dashboard', PARAM_ALPHA);
$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

// Context.
$context = context_system::instance();

// Define views that do NOT require authentication.
$publicviews = ['public', 'public_convocatoria'];

// Verify authentication based on view.
if (!in_array($view, $publicviews)) {
    require_login();
}

// Set up page context and URL.
$PAGE->set_context($context);

// Build URL parameters for current page.
$urlparams = ['view' => $view];
if ($action) {
    $urlparams['action'] = $action;
}
if ($id) {
    $urlparams['id'] = $id;
}

$PAGE->set_url(new moodle_url('/local/jobboard/index.php', $urlparams));

// Route to the appropriate view.
switch ($view) {
    case 'dashboard':
        // Default dashboard view - requires login.
        require(__DIR__ . '/views/dashboard.php');
        break;

    case 'browse_convocatorias':
        // Browse convocatorias - public entry point for applicants.
        require(__DIR__ . '/views/browse_convocatorias.php');
        break;

    case 'convocatorias':
        // List and manage convocatorias (calls) - admin view.
        require(__DIR__ . '/views/convocatorias.php');
        break;

    case 'convocatoria':
        // View/edit single convocatoria.
        require(__DIR__ . '/views/convocatoria.php');
        break;

    case 'view_convocatoria':
        // View convocatoria details with vacancies - public view.
        require(__DIR__ . '/views/view_convocatoria.php');
        break;

    case 'vacancies':
        // List all available vacancies.
        require(__DIR__ . '/views/vacancies.php');
        break;

    case 'vacancy':
        // View single vacancy detail.
        require(__DIR__ . '/views/vacancy.php');
        break;

    case 'apply':
        // Apply for a vacancy - requires apply capability.
        require(__DIR__ . '/views/apply.php');
        break;

    case 'applications':
        // View user's own applications.
        require(__DIR__ . '/views/applications.php');
        break;

    case 'application':
        // View single application detail.
        require(__DIR__ . '/views/application.php');
        break;

    case 'manage':
        // Manage vacancies - requires createvacancy capability.
        require(__DIR__ . '/views/manage.php');
        break;

    case 'review':
        // Review applications/documents - requires reviewdocuments capability.
        require(__DIR__ . '/views/review.php');
        break;

    case 'myreviews':
        // Reviewer's personal queue - requires reviewdocuments capability.
        require(__DIR__ . '/views/myreviews.php');
        break;

    case 'reports':
        // View reports - requires viewreports capability.
        require(__DIR__ . '/views/reports.php');
        break;

    case 'public':
        // Public vacancies page - no authentication required.
        require(__DIR__ . '/views/public.php');
        break;

    case 'public_convocatoria':
        // Public convocatoria view page - no authentication required.
        require(__DIR__ . '/views/public_convocatoria.php');
        break;

    default:
        // Unknown view - redirect to dashboard.
        redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'dashboard']));
        break;
}
