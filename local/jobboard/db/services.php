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
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    // Token management functions (AJAX).
    'local_jobboard_revoke_token' => [
        'classname' => 'local_jobboard\external\api_functions',
        'methodname' => 'revoke_token',
        'description' => 'Revoke an API token',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/jobboard:manageapitokens',
    ],
    'local_jobboard_enable_token' => [
        'classname' => 'local_jobboard\external\api_functions',
        'methodname' => 'enable_token',
        'description' => 'Enable an API token',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/jobboard:manageapitokens',
    ],
    'local_jobboard_delete_token' => [
        'classname' => 'local_jobboard\external\api_functions',
        'methodname' => 'delete_token',
        'description' => 'Delete an API token',
        'type' => 'write',
        'ajax' => true,
        'capabilities' => 'local/jobboard:manageapitokens',
    ],

    // Vacancy functions (AJAX).
    'local_jobboard_filter_vacancies' => [
        'classname' => 'local_jobboard\external\api_functions',
        'methodname' => 'filter_vacancies',
        'description' => 'Filter and search vacancies',
        'type' => 'read',
        'ajax' => true,
        'loginrequired' => false,
    ],
];

$services = [
    'Job Board Web Services' => [
        'functions' => [
            'local_jobboard_revoke_token',
            'local_jobboard_enable_token',
            'local_jobboard_delete_token',
            'local_jobboard_filter_vacancies',
        ],
        'restrictedusers' => 0,
        'enabled' => 1,
        'shortname' => 'local_jobboard_ws',
    ],
];
