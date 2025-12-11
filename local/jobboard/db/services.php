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
 * This file defines all external web services according to Moodle 4.1+ standards.
 * All functions use the consolidated local_jobboard\external\api class.
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
        'classname' => 'local_jobboard\external\api',
        'methodname' => 'get_vacancies',
        'description' => 'Get list of vacancies with optional filtering',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
        'capabilities' => 'local/jobboard:viewpublicvacancies',
    ],

    'local_jobboard_get_vacancy' => [
        'classname' => 'local_jobboard\external\api',
        'methodname' => 'get_vacancy',
        'description' => 'Get a single vacancy by ID with document requirements',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
        'capabilities' => 'local/jobboard:viewpublicvacancies',
    ],

    'local_jobboard_filter_vacancies' => [
        'classname' => 'local_jobboard\external\api',
        'methodname' => 'filter_vacancies',
        'description' => 'Filter and search vacancies (AJAX)',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
    ],

    // =========================================================================
    // APPLICATION FUNCTIONS
    // =========================================================================

    'local_jobboard_get_applications' => [
        'classname' => 'local_jobboard\external\api',
        'methodname' => 'get_applications',
        'description' => 'Get list of applications (own applications or all if manager)',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/jobboard:viewownapplications',
    ],

    'local_jobboard_get_application' => [
        'classname' => 'local_jobboard\external\api',
        'methodname' => 'get_application',
        'description' => 'Get a single application with documents and history',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/jobboard:viewownapplications',
    ],

    'local_jobboard_check_application_limit' => [
        'classname' => 'local_jobboard\external\api',
        'methodname' => 'check_application_limit',
        'description' => 'Check if user can apply (based on application limits)',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
    ],

    // =========================================================================
    // IOMAD FUNCTIONS
    // =========================================================================

    'local_jobboard_get_departments' => [
        'classname' => 'local_jobboard\external\api',
        'methodname' => 'get_departments',
        'description' => 'Get list of departments for a company (IOMAD)',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => true,
        'capabilities' => 'local/jobboard:createvacancy',
    ],
];

// Define the web service.
$services = [
    'Job Board Web Services' => [
        'functions' => [
            // Vacancy functions.
            'local_jobboard_get_vacancies',
            'local_jobboard_get_vacancy',
            'local_jobboard_filter_vacancies',
            // Application functions.
            'local_jobboard_get_applications',
            'local_jobboard_get_application',
            'local_jobboard_check_application_limit',
            // IOMAD functions.
            'local_jobboard_get_departments',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'local_jobboard_ws',
        'downloadfiles' => 0,
        'uploadfiles' => 0,
    ],
];
