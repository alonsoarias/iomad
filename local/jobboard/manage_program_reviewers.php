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
 * Manage program reviewers page.
 *
 * Migrated to renderer + template pattern in v3.1.17.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_jobboard\program_reviewer;

require_login();
$context = context_system::instance();
require_capability('local/jobboard:manageworkflow', $context);

$categoryid = optional_param('categoryid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$userid = optional_param('userid', 0, PARAM_INT);

$pageurl = new moodle_url('/local/jobboard/manage_program_reviewers.php');
$PAGE->set_context($context);
$PAGE->set_url($pageurl);
$PAGE->set_pagelayout('admin');
$PAGE->set_title(get_string('programreviewers', 'local_jobboard'));
$PAGE->set_heading(get_string('programreviewers', 'local_jobboard'));

// Handle actions.
if ($action && confirm_sesskey()) {
    switch ($action) {
        case 'add':
            if ($categoryid && $userid) {
                $role = optional_param('role', program_reviewer::ROLE_REVIEWER, PARAM_ALPHA);
                $result = program_reviewer::add($categoryid, $userid, $role);
                if ($result) {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('revieweradded', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('revieweradderror', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
                }
            }
            break;

        case 'remove':
            if ($categoryid && $userid) {
                $result = program_reviewer::remove($categoryid, $userid);
                if ($result) {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('reviewerremoved', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('reviewerremoveerror', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
                }
            }
            break;

        case 'changerole':
            if ($categoryid && $userid) {
                $newrole = optional_param('newrole', '', PARAM_ALPHA);
                $result = program_reviewer::update_role($categoryid, $userid, $newrole);
                if ($result) {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('rolechanged', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('rolechangeerror', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
                }
            }
            break;

        case 'togglestatus':
            if ($categoryid && $userid) {
                $result = program_reviewer::toggle_status($categoryid, $userid);
                if ($result) {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('statuschanged', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
                } else {
                    redirect(new moodle_url($pageurl, ['categoryid' => $categoryid]),
                        get_string('statuschangeerror', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
                }
            }
            break;
    }
}

// Render page using renderer + template pattern.
$renderer = $PAGE->get_renderer('local_jobboard');
$data = $renderer->prepare_program_reviewers_page_data($categoryid);

echo $OUTPUT->header();
echo $renderer->render_program_reviewers_page($data);
echo $OUTPUT->footer();
