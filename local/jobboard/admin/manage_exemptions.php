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
 * ISER Exemption management page.
 *
 * Migrated to renderer + template pattern in v3.1.23.
 * Note: Add/edit/view/revoke sub-views still use ui_helper for complex forms.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');

use local_jobboard\exemption;
use local_jobboard\output\ui_helper;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageexemptions', $context);

// Parameters.
$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$search = optional_param('search', '', PARAM_TEXT);
$type = optional_param('type', '', PARAM_ALPHA);
$status = optional_param('status', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/admin/manage_exemptions.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('manageexemptions', 'local_jobboard'));
$PAGE->set_heading(get_string('manageexemptions', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Handle actions (add/edit/view/revoke use ui_helper for complex form interactions).
if ($action === 'add' || ($action === 'edit' && $id)) {
    // Add/Edit exemption form.
    require_once($CFG->libdir . '/formslib.php');

    $exemption = null;
    if ($id) {
        $exemption = $DB->get_record('local_jobboard_exemption', ['id' => $id], '*', MUST_EXIST);
    }

    $mform = new \local_jobboard\forms\exemption_form(null, ['exemption' => $exemption]);

    if ($mform->is_cancelled()) {
        redirect(new moodle_url('/local/jobboard/admin/manage_exemptions.php'));
    } else if ($data = $mform->get_data()) {
        if ($id) {
            // Update existing.
            $record = new stdClass();
            $record->id = $id;
            $record->userid = $data->userid;
            $record->exemptiontype = $data->exemptiontype;
            $record->documentref = $data->documentref ?? '';
            $record->exempteddoctypes = is_array($data->exempteddoctypes) ?
                implode(',', $data->exempteddoctypes) : $data->exempteddoctypes;
            $record->validfrom = $data->validfrom;
            $record->validuntil = $data->validuntil ?? null;
            $record->notes = $data->notes ?? '';
            $record->timemodified = time();
            $record->modifiedby = $USER->id;

            $DB->update_record('local_jobboard_exemption', $record);

            // Log event.
            \local_jobboard\event\exemption_updated::create([
                'context' => $context,
                'objectid' => $id,
                'relateduserid' => $data->userid,
            ])->trigger();

            redirect(new moodle_url('/local/jobboard/admin/manage_exemptions.php'),
                get_string('exemptionupdated', 'local_jobboard'), null,
                \core\output\notification::NOTIFY_SUCCESS);
        } else {
            // Create new.
            $result = exemption::create(
                $data->userid,
                $data->exemptiontype,
                is_array($data->exempteddoctypes) ?
                    $data->exempteddoctypes : explode(',', $data->exempteddoctypes),
                $data->validfrom,
                $data->validuntil ?? null,
                $data->documentref ?? '',
                $data->notes ?? ''
            );

            if ($result) {
                redirect(new moodle_url('/local/jobboard/admin/manage_exemptions.php'),
                    get_string('exemptioncreated', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } else {
                redirect(new moodle_url('/local/jobboard/admin/manage_exemptions.php'),
                    get_string('exemptionerror', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_ERROR);
            }
        }
    }

    echo $OUTPUT->header();

    // Use renderer + template pattern.
    ob_start();
    $mform->display();
    $formhtml = ob_get_clean();

    $renderer = $PAGE->get_renderer('local_jobboard');
    $data = $renderer->prepare_exemption_form_data((bool)$id, $formhtml);
    echo $renderer->render_exemption_form_page($data);

    echo $OUTPUT->footer();
    exit;
}

if ($action === 'revoke' && $id) {
    $exemption = $DB->get_record('local_jobboard_exemption', ['id' => $id], '*', MUST_EXIST);

    if ($confirm) {
        require_sesskey();

        $reason = required_param('reason', PARAM_TEXT);

        $result = exemption::revoke($id, $reason);

        if ($result) {
            redirect(new moodle_url('/local/jobboard/admin/manage_exemptions.php'),
                get_string('exemptionrevoked', 'local_jobboard'), null,
                \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect(new moodle_url('/local/jobboard/admin/manage_exemptions.php'),
                get_string('exemptionrevokeerror', 'local_jobboard'), null,
                \core\output\notification::NOTIFY_ERROR);
        }
    }

    // Show confirmation form using renderer + template pattern.
    $user = $DB->get_record('user', ['id' => $exemption->userid]);

    echo $OUTPUT->header();

    $renderer = $PAGE->get_renderer('local_jobboard');
    $data = $renderer->prepare_exemption_revoke_data($id, $user);
    echo $renderer->render_exemption_revoke_page($data);

    echo $OUTPUT->footer();
    exit;
}

if ($action === 'view' && $id) {
    // View exemption details using renderer + template pattern.
    $exemption = $DB->get_record('local_jobboard_exemption', ['id' => $id], '*', MUST_EXIST);
    $user = $DB->get_record('user', ['id' => $exemption->userid]);
    $createdby = $DB->get_record('user', ['id' => $exemption->createdby]);
    $isvalid = exemption::is_valid($exemption);

    // Get usage history.
    $usages = $DB->get_records_sql("
        SELECT a.id, a.timecreated, v.code, v.title
          FROM {local_jobboard_application} a
          JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
         WHERE a.userid = :userid
           AND a.timecreated >= :validfrom
           AND (a.timecreated <= :validuntil OR :hasexpiry = 0)
         ORDER BY a.timecreated DESC
    ", [
        'userid' => $exemption->userid,
        'validfrom' => $exemption->validfrom,
        'validuntil' => $exemption->validuntil ?: time(),
        'hasexpiry' => $exemption->validuntil ? 1 : 0,
    ]);

    echo $OUTPUT->header();

    $renderer = $PAGE->get_renderer('local_jobboard');
    $data = $renderer->prepare_exemption_view_data($exemption, $user, $createdby, $isvalid, $usages);
    echo $renderer->render_exemption_view_page($data);

    echo $OUTPUT->footer();
    exit;
}

// Main list view - use renderer + template pattern.
$renderer = $PAGE->get_renderer('local_jobboard');
$data = $renderer->prepare_manage_exemptions_page_data($search, $type, $status, $page, $perpage, $context);

echo $OUTPUT->header();
echo $renderer->render_manage_exemptions_page($data);
echo $OUTPUT->footer();
