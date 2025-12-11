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
 * Bulk document validation page.
 *
 * Migrated to renderer + template pattern in v3.1.19.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\bulk_validator;

$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$documenttype = optional_param('documenttype', '', PARAM_ALPHANUMEXT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:reviewdocuments', $context);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/bulk_validate.php', [
    'vacancyid' => $vacancyid,
    'documenttype' => $documenttype,
]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('bulkvalidation', 'local_jobboard'));
$PAGE->set_heading(get_string('bulkvalidation', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Handle bulk actions.
if ($action === 'validate' || $action === 'reject') {
    require_sesskey();

    $documentids = required_param_array('documents', PARAM_INT);
    $notes = optional_param('notes', '', PARAM_TEXT);
    $rejectreason = optional_param('rejectreason', '', PARAM_ALPHA);

    $isvalid = ($action === 'validate');

    $results = bulk_validator::validate_documents($documentids, $isvalid,
        $isvalid ? null : $rejectreason, $notes);

    $message = get_string('bulkvalidationcomplete', 'local_jobboard', $results);
    $type = $results['failed'] > 0 ? \core\output\notification::NOTIFY_WARNING : \core\output\notification::NOTIFY_SUCCESS;

    redirect(
        new moodle_url('/local/jobboard/bulk_validate.php', [
            'vacancyid' => $vacancyid,
            'documenttype' => $documenttype,
        ]),
        $message,
        null,
        $type
    );
}

// Render page using renderer + template pattern.
$renderer = $PAGE->get_renderer('local_jobboard');
$data = $renderer->prepare_bulk_validate_page_data($vacancyid, $documenttype, $context);

echo $OUTPUT->header();
echo $renderer->render_bulk_validate_page($data);
echo $OUTPUT->footer();
