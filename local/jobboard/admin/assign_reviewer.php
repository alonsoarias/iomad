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
 * Assign reviewers to applications and faculties.
 *
 * Supports two modes:
 * - applications: Assign reviewers to specific applications (default)
 * - faculties: Assign deans/reviewers to faculties for dean review workflow
 *
 * Migrated to renderer + template pattern in v3.1.21.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

use local_jobboard\reviewer;
use local_jobboard\faculty_reviewer;

$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$mode = optional_param('mode', 'applications', PARAM_ALPHA);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageworkflow', $context);

// Set up page.
$urlparams = ['mode' => $mode];
if ($vacancyid) {
    $urlparams['vacancyid'] = $vacancyid;
}
$PAGE->set_url(new moodle_url('/local/jobboard/admin/assign_reviewer.php', $urlparams));
$PAGE->set_context($context);

if ($mode === 'faculties') {
    $PAGE->set_title(get_string('managefacultyreviewers', 'local_jobboard'));
    $PAGE->set_heading(get_string('managefacultyreviewers', 'local_jobboard'));
} else {
    $PAGE->set_title(get_string('assignreviewer', 'local_jobboard'));
    $PAGE->set_heading(get_string('assignreviewer', 'local_jobboard'));
}
$PAGE->set_pagelayout('admin');

// Add breadcrumbs for navigation.
$PAGE->navbar->add(get_string('dashboard', 'local_jobboard'),
    new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('review', 'local_jobboard'),
    new moodle_url('/local/jobboard/views/myreviews.php'));
if ($mode === 'faculties') {
    $PAGE->navbar->add(get_string('managefacultyreviewers', 'local_jobboard'));
} else {
    $PAGE->navbar->add(get_string('assignreviewer', 'local_jobboard'));
}

// Handle actions (before output).
// === APPLICATION REVIEWER ACTIONS ===
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

// === FACULTY REVIEWER ACTIONS ===
if ($action === 'assignfaculty') {
    require_sesskey();

    $facultyid = required_param('facultyid', PARAM_INT);
    $selecteduserids = required_param_array('userids', PARAM_INT);
    $facultyrole = optional_param('facultyrole', 'dean', PARAM_ALPHA);

    $assigned = 0;
    foreach ($selecteduserids as $uid) {
        try {
            faculty_reviewer::assign($facultyid, $uid, $facultyrole);
            $assigned++;
        } catch (\moodle_exception $e) {
            // User already assigned, skip.
        }
    }

    if ($assigned > 0) {
        $message = get_string('facultyreviewersassigned', 'local_jobboard', $assigned);
        redirect($PAGE->url, $message, null, \core\output\notification::NOTIFY_SUCCESS);
    }
    redirect($PAGE->url);
}

if ($action === 'unassignfaculty') {
    require_sesskey();

    $assignmentid = required_param('id', PARAM_INT);
    $assignment = faculty_reviewer::get($assignmentid);
    if ($assignment) {
        $assignment->deactivate();
        redirect($PAGE->url, get_string('facultyreviewerunassigned', 'local_jobboard'), null,
            \core\output\notification::NOTIFY_SUCCESS);
    }
    redirect($PAGE->url);
}

// Render page using renderer + template pattern.
// Output header first, then get renderer (Moodle standard pattern).
echo $OUTPUT->header();

$renderer = $PAGE->get_renderer('local_jobboard');
if ($mode === 'faculties') {
    $data = $renderer->prepare_faculty_assignment_page_data();
    echo $renderer->render_faculty_assignment_page($data);
} else {
    $data = $renderer->prepare_assign_reviewer_page_data($vacancyid);
    echo $renderer->render_assign_reviewer_page($data);
}

echo $OUTPUT->footer();
