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
 * AJAX endpoint to get departments for a company.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');

// Get the company ID parameter.
$companyid = required_param('companyid', PARAM_INT);

// Set up context (no login required for signup).
$context = context_system::instance();
$PAGE->set_context($context);

// Return JSON response.
header('Content-Type: application/json; charset=utf-8');

try {
    // Get departments for the company.
    $departments = local_jobboard_get_departments($companyid);

    // Format for JSON response.
    $result = [];
    foreach ($departments as $id => $name) {
        $result[] = [
            'id' => $id,
            'name' => $name,
        ];
    }

    echo json_encode([
        'success' => true,
        'departments' => $result,
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
