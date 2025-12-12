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
 * Manage applications for a vacancy.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/tablelib.php');

use local_jobboard\vacancy;
use local_jobboard\application;

$vacancyid = required_param('vacancyid', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);
$status = optional_param('status', '', PARAM_ALPHA);
$search = optional_param('search', '', PARAM_TEXT);
$sort = optional_param('sort', 'timecreated', PARAM_ALPHA);
$order = optional_param('order', 'DESC', PARAM_ALPHA);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:reviewdocuments', $context);

// Get vacancy.
$vacancy = vacancy::get($vacancyid);
if (!$vacancy) {
    throw new moodle_exception('vacancynotfound', 'local_jobboard');
}

// Set up page.
$baseurl = new moodle_url('/local/jobboard/admin/manage_applications.php', [
    'vacancyid' => $vacancyid,
    'status' => $status,
    'search' => $search,
    'sort' => $sort,
    'order' => $order,
]);

$PAGE->set_url($baseurl);
$PAGE->set_context($context);
$PAGE->set_title(get_string('manageapplications', 'local_jobboard'));
$PAGE->set_heading(get_string('manageapplications', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Get applications.
$filters = ['vacancyid' => $vacancyid];
if (!empty($status)) {
    $filters['status'] = $status;
}
if (!empty($search)) {
    $filters['search'] = $search;
}

$result = application::get_list($filters, $sort, $order, $page, $perpage);
$applications = $result['applications'];
$total = $result['total'];

// Get application stats.
$stats = application::get_stats_for_vacancy($vacancyid);

// Use renderer + template pattern.
echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('local_jobboard');
$data = $renderer->prepare_manage_applications_page_data(
    $vacancy,
    $applications,
    $stats,
    $total,
    $page,
    $perpage,
    $status,
    $search,
    $sort,
    $order,
    $baseurl
);
echo $renderer->render_manage_applications_page($data);

echo $OUTPUT->footer();
