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
 * Upgrade script for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the local_jobboard plugin.
 *
 * @param int $oldversion The old version of the plugin.
 * @return bool True on success.
 */
function xmldb_local_jobboard_upgrade($oldversion) {
    global $DB;

    // Version 2.2.0 - Create custom roles for the plugin.
    if ($oldversion < 2025121100) {
        // Create the plugin custom roles if they don't exist.
        local_jobboard_upgrade_create_roles();

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121100, 'local', 'jobboard');
    }

    return true;
}

/**
 * Create custom roles for the Job Board plugin during upgrade.
 *
 * This function is called during upgrade to create the three specialized roles
 * for existing installations that didn't have them created during initial install.
 *
 * @return void
 */
function local_jobboard_upgrade_create_roles(): void {
    global $DB;

    // Ensure capabilities are loaded.
    update_capabilities('local_jobboard');

    $systemcontext = context_system::instance();

    // Role: Document Reviewer.
    $reviewerrole = $DB->get_record('role', ['shortname' => 'jobboard_reviewer']);
    if (!$reviewerrole) {
        $reviewerroleid = create_role(
            get_string('role_reviewer', 'local_jobboard'),
            'jobboard_reviewer',
            get_string('role_reviewer_desc', 'local_jobboard'),
            'teacher'
        );

        // Assign capabilities for reviewer role.
        $reviewercaps = [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:review',
            'local/jobboard:validatedocuments',
            'local/jobboard:reviewdocuments',
            'local/jobboard:downloadanydocument',
        ];

        foreach ($reviewercaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $reviewerroleid, $systemcontext->id);
        }

        // Set contexts where this role can be assigned.
        set_role_contextlevels($reviewerroleid, [CONTEXT_SYSTEM]);
    }

    // Role: Selection Coordinator.
    $coordinatorrole = $DB->get_record('role', ['shortname' => 'jobboard_coordinator']);
    if (!$coordinatorrole) {
        $coordinatorroleid = create_role(
            get_string('role_coordinator', 'local_jobboard'),
            'jobboard_coordinator',
            get_string('role_coordinator_desc', 'local_jobboard'),
            'editingteacher'
        );

        // Assign capabilities for coordinator role.
        $coordinatorcaps = [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:manage',
            'local/jobboard:createvacancy',
            'local/jobboard:editvacancy',
            'local/jobboard:publishvacancy',
            'local/jobboard:viewallvacancies',
            'local/jobboard:viewallapplications',
            'local/jobboard:changeapplicationstatus',
            'local/jobboard:assignreviewers',
            'local/jobboard:viewreports',
            'local/jobboard:viewevaluations',
        ];

        foreach ($coordinatorcaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $coordinatorroleid, $systemcontext->id);
        }

        set_role_contextlevels($coordinatorroleid, [CONTEXT_SYSTEM]);
    }

    // Role: Selection Committee Member.
    $committeerole = $DB->get_record('role', ['shortname' => 'jobboard_committee']);
    if (!$committeerole) {
        $committeeroleid = create_role(
            get_string('role_committee', 'local_jobboard'),
            'jobboard_committee',
            get_string('role_committee_desc', 'local_jobboard'),
            'teacher'
        );

        // Assign capabilities for committee role.
        $committeecaps = [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:evaluate',
            'local/jobboard:viewevaluations',
            'local/jobboard:downloadanydocument',
        ];

        foreach ($committeecaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $committeeroleid, $systemcontext->id);
        }

        set_role_contextlevels($committeeroleid, [CONTEXT_SYSTEM]);
    }
}
