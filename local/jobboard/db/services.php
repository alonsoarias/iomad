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
 * External services definitions for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$functions = [
    // Validate (approve) a document.
    'local_jobboard_validate_document' => [
        'classname'     => 'local_jobboard\external\validate_document',
        'methodname'    => 'execute',
        'description'   => 'Validate (approve) a document in an application',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => 'local/jobboard:reviewdocuments',
        'loginrequired' => true,
    ],

    // Reject a document.
    'local_jobboard_reject_document' => [
        'classname'     => 'local_jobboard\external\reject_document',
        'methodname'    => 'execute',
        'description'   => 'Reject a document in an application with a reason',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => 'local/jobboard:reviewdocuments',
        'loginrequired' => true,
    ],

    // Save document observations.
    'local_jobboard_save_document_observations' => [
        'classname'     => 'local_jobboard\external\save_document_observations',
        'methodname'    => 'execute',
        'description'   => 'Save observations for documents in an application',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => 'local/jobboard:reviewdocuments',
        'loginrequired' => true,
    ],

    // Send observations email.
    'local_jobboard_send_observations_email' => [
        'classname'     => 'local_jobboard\external\send_observations_email',
        'methodname'    => 'execute',
        'description'   => 'Send email with document observations to applicant',
        'type'          => 'write',
        'ajax'          => true,
        'capabilities'  => 'local/jobboard:reviewdocuments',
        'loginrequired' => true,
    ],

    // Get next pending document for an application.
    'local_jobboard_get_next_document' => [
        'classname'     => 'local_jobboard\external\get_next_document',
        'methodname'    => 'execute',
        'description'   => 'Get the next pending document for review in an application',
        'type'          => 'read',
        'ajax'          => true,
        'capabilities'  => 'local/jobboard:reviewdocuments',
        'loginrequired' => true,
    ],
];
