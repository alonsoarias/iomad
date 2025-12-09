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
 * AJAX endpoint to search companies for autocomplete.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');

// Get parameters.
$search = optional_param('search', '', PARAM_TEXT);
$companyid = optional_param('id', 0, PARAM_INT);

// Set up context (no login required for signup).
$context = context_system::instance();
$PAGE->set_context($context);

// Return JSON response.
header('Content-Type: application/json; charset=utf-8');

try {
    // Check if IOMAD is installed.
    if (!local_jobboard_is_iomad_installed()) {
        echo json_encode([
            'success' => false,
            'error' => 'IOMAD not installed',
            'companies' => [],
        ]);
        exit;
    }

    $companies = [];

    // If specific ID requested, return that company.
    if ($companyid > 0) {
        global $DB;
        $company = $DB->get_record('company', ['id' => $companyid, 'suspended' => 0]);
        if ($company) {
            $companies[] = [
                'id' => (int) $company->id,
                'name' => format_string($company->name),
                'shortname' => format_string($company->shortname),
            ];
        }
    } else {
        // Get all companies or search.
        $allcompanies = local_jobboard_get_companies();

        // Filter by search term if provided.
        foreach ($allcompanies as $id => $name) {
            if (empty($search) || stripos($name, $search) !== false) {
                $companies[] = [
                    'id' => (int) $id,
                    'name' => $name,
                ];
            }
        }

        // Limit results for performance.
        if (count($companies) > 50) {
            $companies = array_slice($companies, 0, 50);
        }
    }

    echo json_encode([
        'success' => true,
        'companies' => $companies,
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'companies' => [],
    ]);
}
