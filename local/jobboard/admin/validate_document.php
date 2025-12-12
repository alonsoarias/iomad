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
 * Document validation page.
 *
 * Migrated to renderer + template pattern in v3.1.20.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

use local_jobboard\document;
use local_jobboard\application;

$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:reviewdocuments', $context);

// Get document.
$document = document::get($id);
if (!$document) {
    throw new moodle_exception('documentnotfound', 'local_jobboard');
}

// Get application.
$application = application::get($document->applicationid);
if (!$application) {
    throw new moodle_exception('applicationnotfound', 'local_jobboard');
}

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/admin/validate_document.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('validatedocument', 'local_jobboard'));
$PAGE->set_heading(get_string('validatedocument', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

// Handle form submission.
if ($action === 'validate' || $action === 'reject') {
    require_sesskey();

    $isvalid = ($action === 'validate');
    $rejectreason = optional_param('rejectreason', '', PARAM_TEXT);
    $notes = optional_param('notes', '', PARAM_TEXT);

    $document->validate($isvalid, $rejectreason, $notes);

    redirect(
        new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id]),
        get_string($isvalid ? 'documentvalidated' : 'documentrejected', 'local_jobboard'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

// Initialize document preview module.
$PAGE->requires->js_call_amd('local_jobboard/document_preview', 'init');

// Render page using renderer + template pattern.
$renderer = $PAGE->get_renderer('local_jobboard');
$data = $renderer->prepare_validate_document_page_data($document, $application);

echo $OUTPUT->header();
echo $renderer->render_validate_document_page($data);
echo $OUTPUT->footer();
