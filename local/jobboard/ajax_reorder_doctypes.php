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
 * AJAX endpoint for reordering document types via drag and drop.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('AJAX_SCRIPT', true);

require_once(__DIR__ . '/../../config.php');

require_login();
require_sesskey();

$context = context_system::instance();
require_capability('local/jobboard:managedoctypes', $context);

header('Content-Type: application/json');

try {
    // Get the new order from POST data.
    $order = required_param_array('order', PARAM_INT);

    if (empty($order)) {
        throw new moodle_exception('invalidorder', 'local_jobboard');
    }

    // Update sortorder for each doctype.
    $sortorder = 0;
    foreach ($order as $doctypeid) {
        $doctypeid = (int) $doctypeid;
        if ($doctypeid > 0) {
            $DB->set_field('local_jobboard_doctype', 'sortorder', $sortorder, ['id' => $doctypeid]);
            $sortorder++;
        }
    }

    // Log the reorder action.
    \local_jobboard\audit::log(
        'reorder',
        'doctype',
        0,
        ['count' => count($order), 'order' => implode(',', $order)]
    );

    echo json_encode([
        'success' => true,
        'message' => get_string('reorder_success', 'local_jobboard'),
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
    ]);
}
