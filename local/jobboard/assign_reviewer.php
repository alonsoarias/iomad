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
 * Assign reviewers to applications.
 *
 * Migrated to renderer + template pattern in v3.1.21.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\reviewer;

$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageworkflow', $context);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/assign_reviewer.php', ['vacancyid' => $vacancyid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('assignreviewer', 'local_jobboard'));
$PAGE->set_heading(get_string('assignreviewer', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Handle actions.
if ($action === 'assign') {
    require_sesskey();

    $applicationids = required_param_array('applications', PARAM_INT);
    $reviewerid = required_param('reviewerid', PARAM_INT);

    $results = reviewer::bulk_assign($applicationids, $reviewerid);

    $message = get_string('reviewerassigned', 'local_jobboard', $results);
    redirect($PAGE->url, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($action === 'autoassign') {
    require_sesskey();

    $maxperreviewer = optional_param('maxperreviewer', 20, PARAM_INT);
    $assigned = reviewer::auto_assign($vacancyid ?: null, $maxperreviewer);

    $message = get_string('autoassigncomplete', 'local_jobboard', $assigned);
    redirect($PAGE->url, $message, null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($action === 'unassign') {
    require_sesskey();

    $applicationid = required_param('applicationid', PARAM_INT);
    reviewer::unassign($applicationid);

    redirect($PAGE->url, get_string('reviewerunassigned', 'local_jobboard'), null,
        \core\output\notification::NOTIFY_SUCCESS);
}

// Render page using renderer + template pattern.
$renderer = $PAGE->get_renderer('local_jobboard');
$data = $renderer->prepare_assign_reviewer_page_data($vacancyid);

echo $OUTPUT->header();
echo $renderer->render_assign_reviewer_page($data);
echo $OUTPUT->footer();
