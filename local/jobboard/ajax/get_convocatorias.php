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
 * AJAX endpoint to search convocatorias for autocomplete.
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
$convocatoriaid = optional_param('id', 0, PARAM_INT);
$companyid = optional_param('companyid', 0, PARAM_INT);
$status = optional_param('status', '', PARAM_ALPHA);
$includeall = optional_param('includeall', 0, PARAM_BOOL);

// Set up context.
$context = context_system::instance();
$PAGE->set_context($context);

// Return JSON response.
header('Content-Type: application/json; charset=utf-8');

try {
    global $DB;
    $convocatorias = [];

    // If specific ID requested, return that convocatoria.
    if ($convocatoriaid > 0) {
        $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
        if ($convocatoria) {
            $convocatorias[] = [
                'id' => (int) $convocatoria->id,
                'code' => $convocatoria->code,
                'name' => format_string($convocatoria->name),
                'status' => $convocatoria->status,
                'label' => $convocatoria->code . ' - ' . format_string($convocatoria->name),
            ];
        }
    } else {
        // Build query.
        $conditions = [];
        $params = [];

        // Filter by status.
        if (!empty($status)) {
            $conditions[] = 'status = :status';
            $params['status'] = $status;
        } elseif (!$includeall) {
            // By default, only show open convocatorias.
            $conditions[] = "status IN ('draft', 'open')";
        }

        // Filter by company.
        if ($companyid > 0) {
            $conditions[] = '(companyid = :companyid OR companyid IS NULL OR companyid = 0)';
            $params['companyid'] = $companyid;
        }

        // Search filter.
        if (!empty($search)) {
            $searchterm = '%' . $DB->sql_like_escape($search) . '%';
            $conditions[] = '(' . $DB->sql_like('name', ':search1', false) .
                           ' OR ' . $DB->sql_like('code', ':search2', false) . ')';
            $params['search1'] = $searchterm;
            $params['search2'] = $searchterm;
        }

        $where = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';

        $sql = "SELECT id, code, name, status, companyid
                  FROM {local_jobboard_convocatoria}
                 $where
                 ORDER BY status ASC, startdate DESC, name ASC";

        $records = $DB->get_records_sql($sql, $params, 0, 50);

        foreach ($records as $record) {
            $statuslabel = '';
            if ($record->status !== 'open') {
                $statuslabel = ' [' . get_string('convocatoria_status_' . $record->status, 'local_jobboard') . ']';
            }
            $convocatorias[] = [
                'id' => (int) $record->id,
                'code' => $record->code,
                'name' => format_string($record->name),
                'status' => $record->status,
                'label' => $record->code . ' - ' . format_string($record->name) . $statuslabel,
            ];
        }
    }

    echo json_encode([
        'success' => true,
        'convocatorias' => $convocatorias,
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'convocatorias' => [],
    ]);
}
