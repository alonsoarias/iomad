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
 * Role-based view of user applications with progress tracking.
 * Uses Mustache templates via renderer for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

use local_jobboard\application;
use local_jobboard\exemption;

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

// Get user's applications with status filter.
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

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the renderer.
$data = $renderer->prepare_applications_page_data(
    $USER->id,
    $applications,
    $total,
    $stats,
    $status,
    $page,
    $perpage,
    $exemption
);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_applications_page($data);
echo $OUTPUT->footer();
