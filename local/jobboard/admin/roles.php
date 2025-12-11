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
 * Role management page for local_jobboard.
 *
 * Uses renderer + Mustache template for clean separation of concerns.
 *
 * Allows administrators to assign users to the plugin's custom roles:
 * - jobboard_reviewer: Document reviewers
 * - jobboard_coordinator: Selection coordinators
 * - jobboard_committee: Selection committee members
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

require_login();

$context = context_system::instance();
require_capability('local/jobboard:configure', $context);

// Parameters.
$action = optional_param('action', '', PARAM_ALPHA);
$roleshortname = optional_param('role', '', PARAM_ALPHANUMEXT);
$userid = optional_param('userid', 0, PARAM_INT);

// Page setup.
$PAGE->set_url(new moodle_url('/local/jobboard/admin/roles.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('manageroles', 'local_jobboard'));
$PAGE->set_heading(get_string('manageroles', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Define the plugin roles for action handling.
$pluginroles = ['jobboard_reviewer', 'jobboard_coordinator', 'jobboard_committee'];

// Handle actions.
if ($action === 'assign' && confirm_sesskey()) {
    $userids = required_param_array('userids', PARAM_INT);

    if (!empty($roleshortname) && in_array($roleshortname, $pluginroles)) {
        $role = $DB->get_record('role', ['shortname' => $roleshortname]);
        if ($role) {
            $assigned = 0;
            foreach ($userids as $uid) {
                if (!$DB->record_exists('role_assignments', [
                    'roleid' => $role->id,
                    'contextid' => $context->id,
                    'userid' => $uid,
                ])) {
                    role_assign($role->id, $uid, $context->id);
                    $assigned++;
                }
            }
            if ($assigned > 0) {
                \core\notification::success(get_string('usersassigned', 'local_jobboard', $assigned));
            }
        }
    }
    redirect(new moodle_url('/local/jobboard/admin/roles.php', ['role' => $roleshortname]));
}

if ($action === 'unassign' && confirm_sesskey() && $userid) {
    if (!empty($roleshortname) && in_array($roleshortname, $pluginroles)) {
        $role = $DB->get_record('role', ['shortname' => $roleshortname]);
        if ($role) {
            role_unassign($role->id, $userid, $context->id);
            \core\notification::success(get_string('userunassigned', 'local_jobboard'));
        }
    }
    redirect(new moodle_url('/local/jobboard/admin/roles.php', ['role' => $roleshortname]));
}

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data.
$data = $renderer->prepare_admin_roles_page_data(
    $roleshortname ?: null,
    $context
);

// Output page.
echo $OUTPUT->header();
echo $renderer->render_admin_roles_page($data);

// Add JavaScript for user search filtering when a role is selected.
if (!empty($roleshortname)) {
    $PAGE->requires->js_init_code("
        document.getElementById('usersearch').addEventListener('keyup', function() {
            var filter = this.value.toLowerCase();
            var options = document.querySelectorAll('#userselect option');
            options.forEach(function(option) {
                var text = option.textContent.toLowerCase();
                option.style.display = text.includes(filter) ? '' : 'none';
            });
        });
    ");
}

echo $OUTPUT->footer();
