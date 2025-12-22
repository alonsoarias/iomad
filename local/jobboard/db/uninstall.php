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
 * Uninstallation script for local_jobboard.
 *
 * This script is executed when the plugin is uninstalled.
 * It cleans up resources that are not automatically removed by Moodle:
 * - Custom roles (jobboard_reviewer, jobboard_coordinator, jobboard_committee)
 * - User Tours created by the plugin
 * - Files stored by the plugin
 * - Plugin configuration settings
 *
 * Note: Database tables defined in install.xml are automatically dropped by Moodle.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

/**
 * Custom uninstallation procedure for local_jobboard.
 *
 * @return bool True on success.
 */
function xmldb_local_jobboard_uninstall() {
    global $DB;

    // 1. Remove custom roles created by the plugin.
    local_jobboard_uninstall_remove_roles();

    // 2. Remove User Tours created by the plugin.
    local_jobboard_uninstall_remove_tours();

    // 3. Remove files stored by the plugin.
    local_jobboard_uninstall_remove_files();

    // 4. Remove plugin configuration settings.
    local_jobboard_uninstall_remove_config();

    return true;
}

/**
 * Remove custom roles created by the Job Board plugin.
 *
 * Removes the following roles:
 * - jobboard_reviewer: Document reviewer role
 * - jobboard_coordinator: Selection coordinator role
 * - jobboard_dean: Dean role (profile approval)
 * - jobboard_hr: HR role (document validation)
 *
 * @return void
 */
function local_jobboard_uninstall_remove_roles(): void {
    global $DB;

    $roleshortnames = [
        'jobboard_reviewer',
        'jobboard_coordinator',
        'jobboard_dean',
        'jobboard_hr',
    ];

    foreach ($roleshortnames as $shortname) {
        $role = $DB->get_record('role', ['shortname' => $shortname]);
        if ($role) {
            // Remove all role assignments for this role.
            role_unassign_all(['roleid' => $role->id]);

            // Delete the role itself.
            delete_role($role->id);
        }
    }
}

/**
 * Remove User Tours created by the Job Board plugin.
 *
 * Tours are identified by the 'shipped_tour' config flag set during installation.
 * We query the database directly since the tour API doesn't provide a get_all method.
 *
 * @return void
 */
function local_jobboard_uninstall_remove_tours(): void {
    global $DB;

    // Check if User Tours tool is available.
    if (!class_exists('\tool_usertours\tour')) {
        return;
    }

    // Check if the tours table exists.
    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('tool_usertours_tours')) {
        return;
    }

    try {
        // Get all tour IDs from the database.
        $tourrecords = $DB->get_records('tool_usertours_tours', [], 'id ASC', 'id, configdata');

        foreach ($tourrecords as $tourrecord) {
            // Decode the config data to check if this is our tour.
            $configdata = [];
            if (!empty($tourrecord->configdata)) {
                $configdata = json_decode($tourrecord->configdata, true);
                if ($configdata === null) {
                    // Try unserialize for older Moodle versions.
                    $configdata = @unserialize($tourrecord->configdata);
                }
            }

            if (!is_array($configdata)) {
                continue;
            }

            // Check if this tour was created by our plugin.
            $shippedtour = $configdata['shipped_tour'] ?? false;
            $shippedfilename = $configdata['shipped_filename'] ?? '';

            // Our tours have 'shipped_tour' = true and filenames starting with 'tour_'.
            if ($shippedtour && !empty($shippedfilename) && strpos($shippedfilename, 'tour_') === 0) {
                // Load the tour object and remove it properly.
                $tour = \tool_usertours\tour::instance($tourrecord->id);
                if ($tour) {
                    $tour->remove();
                }
            }
        }
    } catch (\Exception $e) {
        // Log error but don't fail uninstallation.
        debugging('Error removing User Tours during uninstallation: ' . $e->getMessage(), DEBUG_DEVELOPER);
    }
}

/**
 * Remove files stored by the Job Board plugin.
 *
 * This includes:
 * - Application documents uploaded by users
 * - Convocatoria PDF files
 * - Any other files stored under the 'local_jobboard' component
 *
 * @return void
 */
function local_jobboard_uninstall_remove_files(): void {
    $fs = get_file_storage();

    // File areas used by the plugin.
    $fileareas = [
        'document',           // Application documents.
        'convocatoria_pdf',   // Convocatoria PDF files.
        'vacancy_attachment', // Vacancy attachments.
    ];

    // Get all contexts where our files might be stored.
    $systemcontext = context_system::instance();

    foreach ($fileareas as $filearea) {
        // Delete all files in this file area for the system context.
        $fs->delete_area_files($systemcontext->id, 'local_jobboard', $filearea);
    }

    // Also try to delete any files that might be stored in user contexts.
    // This is a more thorough cleanup.
    $files = $fs->get_area_files(
        $systemcontext->id,
        'local_jobboard',
        false, // All file areas.
        false  // Include directories.
    );

    foreach ($files as $file) {
        $file->delete();
    }
}

/**
 * Remove plugin configuration settings.
 *
 * Cleans up settings stored in the config_plugins table for local_jobboard.
 *
 * @return void
 */
function local_jobboard_uninstall_remove_config(): void {
    // Remove all plugin settings.
    // This is typically handled by Moodle during uninstallation,
    // but we explicitly clean up to ensure nothing is left behind.
    unset_all_config_for_plugin('local_jobboard');
}
