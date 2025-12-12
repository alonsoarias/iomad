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
 * Document types management page for local_jobboard.
 *
 * Modern redesign with consistent UX pattern using ui_helper.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_jobboard\forms\doctype_form;

admin_externalpage_setup('local_jobboard_doctypes');

$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
$pageurl = new moodle_url('/local/jobboard/admin/doctypes.php');

// Handle toggle action.
if ($action === 'toggle' && $id && confirm_sesskey()) {
    $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $id], '*', MUST_EXIST);
    $oldstatus = $doctype->enabled;
    $doctype->enabled = $doctype->enabled ? 0 : 1;
    $doctype->timemodified = time();
    $DB->update_record('local_jobboard_doctype', $doctype);

    // Audit log.
    \local_jobboard\audit::log_transition(
        \local_jobboard\audit::ENTITY_CONFIG,
        $id,
        'enabled',
        $oldstatus ? 'enabled' : 'disabled',
        $doctype->enabled ? 'enabled' : 'disabled',
        ['code' => $doctype->code, 'name' => $doctype->name, 'entity' => 'doctype']
    );

    redirect($pageurl, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle delete action.
if ($action === 'delete' && $id && confirm_sesskey()) {
    $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $id], '*', MUST_EXIST);

    // Check if doctype is in use.
    $inuse = $DB->count_records('local_jobboard_document', ['documenttype' => $doctype->code]);
    if ($inuse > 0) {
        redirect($pageurl, get_string('error:doctypeinuse', 'local_jobboard', $inuse),
            null, \core\output\notification::NOTIFY_ERROR);
    }

    $DB->delete_records('local_jobboard_doctype', ['id' => $id]);

    // Audit log.
    \local_jobboard\audit::log(
        \local_jobboard\audit::ACTION_DELETE,
        \local_jobboard\audit::ENTITY_CONFIG,
        $id,
        ['code' => $doctype->code, 'name' => $doctype->name, 'entity' => 'doctype']
    );

    redirect($pageurl, get_string('doctypedeleted', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle move up/down actions.
if (($action === 'moveup' || $action === 'movedown') && $id && confirm_sesskey()) {
    $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $id], '*', MUST_EXIST);
    $currentorder = (int) $doctype->sortorder;

    if ($action === 'moveup') {
        // Find the item above.
        $swap = $DB->get_record_sql(
            'SELECT * FROM {local_jobboard_doctype} WHERE sortorder < ? ORDER BY sortorder DESC LIMIT 1',
            [$currentorder]
        );
    } else {
        // Find the item below.
        $swap = $DB->get_record_sql(
            'SELECT * FROM {local_jobboard_doctype} WHERE sortorder > ? ORDER BY sortorder ASC LIMIT 1',
            [$currentorder]
        );
    }

    if ($swap) {
        // Swap the sort orders.
        $DB->set_field('local_jobboard_doctype', 'sortorder', $swap->sortorder, ['id' => $doctype->id]);
        $DB->set_field('local_jobboard_doctype', 'sortorder', $currentorder, ['id' => $swap->id]);
    }

    redirect($pageurl);
}

// Handle add/edit actions.
if ($action === 'add' || ($action === 'edit' && $id)) {
    $doctype = null;
    if ($action === 'edit') {
        $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $id], '*', MUST_EXIST);
    }

    $customdata = ['doctype' => $doctype];
    $mform = new doctype_form($pageurl, $customdata);

    if ($mform->is_cancelled()) {
        redirect($pageurl);
    }

    if ($data = $mform->get_data()) {
        $now = time();

        if ($doctype) {
            // Update existing.
            $record = new stdClass();
            $record->id = $doctype->id;
            $record->name = $data->name;
            $record->description = $data->description ?? '';
            $record->category = $data->category ?? '';
            $record->isrequired = $data->isrequired ?? 0;
            $record->externalurl = $data->externalurl ?? '';
            $record->requirements = $data->requirements ?? '';
            $record->defaultmaxagedays = $data->defaultmaxagedays ?? null;
            $record->iserexempted = $data->iserexempted ?? 0;
            $record->gender_condition = $data->gender_condition ?? null;
            $record->age_exemption_threshold = $data->age_exemption_threshold ?: null;
            $record->profession_exempt = $data->profession_exempt ?? null;
            $record->conditional_note = $data->conditional_note ?? '';
            $record->checklistitems = $data->checklistitems ?? '';
            $record->sortorder = $data->sortorder ?? 0;
            $record->enabled = $data->enabled ?? 1;
            $record->timemodified = $now;

            $DB->update_record('local_jobboard_doctype', $record);

            // Audit log.
            \local_jobboard\audit::log(
                \local_jobboard\audit::ACTION_UPDATE,
                \local_jobboard\audit::ENTITY_CONFIG,
                $doctype->id,
                ['code' => $doctype->code, 'name' => $data->name, 'entity' => 'doctype'],
                (array) $doctype,
                (array) $record
            );

            redirect($pageurl, get_string('doctypeupdated', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            // Create new.
            // Validate unique code.
            if ($DB->record_exists('local_jobboard_doctype', ['code' => $data->code])) {
                redirect($pageurl, get_string('error:codealreadyexists', 'local_jobboard'),
                    null, \core\output\notification::NOTIFY_ERROR);
            }

            $record = new stdClass();
            $record->code = $data->code;
            $record->name = $data->name;
            $record->description = $data->description ?? '';
            $record->category = $data->category ?? '';
            $record->isrequired = $data->isrequired ?? 0;
            $record->externalurl = $data->externalurl ?? '';
            $record->requirements = $data->requirements ?? '';
            $record->defaultmaxagedays = $data->defaultmaxagedays ?? null;
            $record->iserexempted = $data->iserexempted ?? 0;
            $record->gender_condition = $data->gender_condition ?? null;
            $record->age_exemption_threshold = $data->age_exemption_threshold ?: null;
            $record->profession_exempt = $data->profession_exempt ?? null;
            $record->conditional_note = $data->conditional_note ?? '';
            $record->checklistitems = $data->checklistitems ?? '';
            $record->sortorder = $data->sortorder ?? 0;
            $record->enabled = $data->enabled ?? 1;
            $record->timecreated = $now;
            $record->timemodified = $now;

            $newid = $DB->insert_record('local_jobboard_doctype', $record);

            // Audit log.
            \local_jobboard\audit::log(
                \local_jobboard\audit::ACTION_CREATE,
                \local_jobboard\audit::ENTITY_CONFIG,
                $newid,
                ['code' => $record->code, 'name' => $record->name, 'entity' => 'doctype']
            );

            redirect($pageurl, get_string('doctypecreated', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
        }
    }

    // Display the form using renderer + template pattern.
    $title = $doctype ? get_string('editdoctype', 'local_jobboard') : get_string('adddoctype', 'local_jobboard');
    $PAGE->set_title($title);

    echo $OUTPUT->header();

    // Capture form HTML.
    ob_start();
    $mform->display();
    $formhtml = ob_get_clean();

    // Use renderer.
    $renderer = $PAGE->get_renderer('local_jobboard');
    $data = $renderer->prepare_admin_doctype_form_data((bool) $doctype, $formhtml, $pageurl);
    echo $renderer->render_admin_doctype_form_page($data);

    echo $OUTPUT->footer();
    exit;
}

// Handle confirm delete action using renderer + template pattern.
if ($action === 'confirmdelete' && $id) {
    $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $id], '*', MUST_EXIST);

    // Check usage.
    $inuse = $DB->count_records('local_jobboard_document', ['documenttype' => $doctype->code]);

    echo $OUTPUT->header();

    // Use renderer.
    $renderer = $PAGE->get_renderer('local_jobboard');
    $data = $renderer->prepare_admin_doctype_confirm_delete_data($doctype, (int) $inuse, $pageurl);
    echo $renderer->render_admin_doctype_confirm_delete_page($data);

    echo $OUTPUT->footer();
    exit;
}

// Default: List all document types using renderer + template pattern.
$doctypes = $DB->get_records('local_jobboard_doctype', null, 'sortorder ASC, code ASC');

echo $OUTPUT->header();

// Get renderer and prepare data.
$renderer = $PAGE->get_renderer('local_jobboard');
$data = $renderer->prepare_admin_doctypes_page_data($doctypes, $pageurl);

// Render the page.
echo $renderer->render_admin_doctypes_page($data);

echo $OUTPUT->footer();
