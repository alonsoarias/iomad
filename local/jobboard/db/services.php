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
 * Web service definitions for local_jobboard.
 *
 * This file defines all external web services according to Moodle standards.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    // =========================================================================
    // VACANCY FUNCTIONS
    // =========================================================================

    'local_jobboard_get_vacancies' => [
        'classname' => 'local_jobboard_external',
        'methodname' => 'get_vacancies',
        'classpath' => 'local/jobboard/externallib.php',
        'description' => 'Get list of vacancies with optional filtering',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false, // Public vacancies accessible without login.
        'capabilities' => 'local/jobboard:viewpublicvacancies',
    ],

    'local_jobboard_get_vacancy' => [
        'classname' => 'local_jobboard_external',
        'methodname' => 'get_vacancy',
        'classpath' => 'local/jobboard/externallib.php',
        'description' => 'Get a single vacancy by ID with document requirements',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false, // Public vacancies accessible without login.
        'capabilities' => 'local/jobboard:viewpublicvacancies',
    ],

    // =========================================================================
    // APPLICATION FUNCTIONS
    // =========================================================================

    'local_jobboard_get_applications' => [
        'classname' => 'local_jobboard_external',
        'methodname' => 'get_applications',
        'classpath' => 'local/jobboard/externallib.php',
        'description' => 'Get list of applications (own applications or all if manager)',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/jobboard:viewownapplications',
    ],

    'local_jobboard_get_application' => [
        'classname' => 'local_jobboard_external',
        'methodname' => 'get_application',
        'classpath' => 'local/jobboard/externallib.php',
        'description' => 'Get a single application with documents and history',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/jobboard:viewownapplications',
    ],

    'local_jobboard_check_application_limit' => [
        'classname' => 'local_jobboard_external',
        'methodname' => 'check_application_limit',
        'classpath' => 'local/jobboard/externallib.php',
        'description' => 'Check if user can apply (based on application limits)',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false, // Will return appropriate message if not logged in.
    ],

    // =========================================================================
    // AJAX TOKEN MANAGEMENT FUNCTIONS
    // =========================================================================

    'local_jobboard_revoke_token' => [
        'classname' => 'local_jobboard\external\api_functions',
        'methodname' => 'revoke_token',
        'classpath' => 'local/jobboard/classes/external/api_functions.php',
        'description' => 'Revoke an API token',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/jobboard:manageapitokens',
    ],

    'local_jobboard_enable_token' => [
        'classname' => 'local_jobboard\external\api_functions',
        'methodname' => 'enable_token',
        'classpath' => 'local/jobboard/classes/external/api_functions.php',
        'description' => 'Enable an API token',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/jobboard:manageapitokens',
    ],

    'local_jobboard_delete_token' => [
        'classname' => 'local_jobboard\external\api_functions',
        'methodname' => 'delete_token',
        'classpath' => 'local/jobboard/classes/external/api_functions.php',
        'description' => 'Delete an API token',
        'type' => 'write',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/jobboard:manageapitokens',
    ],

    // =========================================================================
    // AJAX FILTER FUNCTIONS
    // =========================================================================

    'local_jobboard_filter_vacancies' => [
        'classname' => 'local_jobboard\external\api_functions',
        'methodname' => 'filter_vacancies',
        'classpath' => 'local/jobboard/classes/external/api_functions.php',
        'description' => 'Filter and search vacancies (AJAX)',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false, // Public vacancies accessible without login.
    ],
];

// Define the web service.
$services = [
    'Job Board Web Services' => [
        'functions' => [
            // Main API functions.
            'local_jobboard_get_vacancies',
            'local_jobboard_get_vacancy',
            'local_jobboard_get_applications',
            'local_jobboard_get_application',
            'local_jobboard_check_application_limit',
            // Token management.
            'local_jobboard_revoke_token',
            'local_jobboard_enable_token',
            'local_jobboard_delete_token',
            // AJAX functions.
            'local_jobboard_filter_vacancies',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'local_jobboard_ws',
        'downloadfiles' => 0,
        'uploadfiles' => 0,
    ],
];
