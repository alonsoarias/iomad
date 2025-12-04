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
 * Email templates management page for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

admin_externalpage_setup('local_jobboard_templates');

$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
$pageurl = new moodle_url('/local/jobboard/admin/templates.php');

// Handle edit action.
if ($action === 'edit' && $id) {
    $template = $DB->get_record('local_jobboard_email_template', ['id' => $id], '*', MUST_EXIST);

    $mform = new \MoodleQuickForm('edittemplate', 'post', $pageurl);
    $mform->addElement('hidden', 'action', 'save');
    $mform->addElement('hidden', 'id', $id);
    $mform->addElement('text', 'subject', get_string('subject', 'local_jobboard'), ['size' => 60]);
    $mform->setType('subject', PARAM_TEXT);
    $mform->setDefault('subject', $template->subject);
    $mform->addElement('textarea', 'body', get_string('body', 'local_jobboard'), ['rows' => 10, 'cols' => 60]);
    $mform->setType('body', PARAM_RAW);
    $mform->setDefault('body', $template->body);
    $mform->addElement('submit', 'submitbutton', get_string('savechanges'));

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('edittemplate', 'local_jobboard') . ': ' . $template->code);
    $mform->display();
    echo $OUTPUT->footer();
    exit;
}

// Handle save action.
if ($action === 'save' && $id && confirm_sesskey()) {
    $template = $DB->get_record('local_jobboard_email_template', ['id' => $id], '*', MUST_EXIST);
    $template->subject = required_param('subject', PARAM_TEXT);
    $template->body = required_param('body', PARAM_RAW);
    $template->timemodified = time();
    $DB->update_record('local_jobboard_email_template', $template);
    redirect($pageurl, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Get all templates.
$templates = $DB->get_records('local_jobboard_email_template', null, 'code ASC');

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('emailtemplates', 'local_jobboard'));

echo '<div class="alert alert-info">' . get_string('emailtemplateshelp', 'local_jobboard') . '</div>';

if (empty($templates)) {
    echo $OUTPUT->notification(get_string('notemplates', 'local_jobboard'), 'info');
} else {
    $table = new html_table();
    $table->head = [
        get_string('code', 'local_jobboard'),
        get_string('subject', 'local_jobboard'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable';

    foreach ($templates as $tpl) {
        $editurl = new moodle_url($pageurl, ['action' => 'edit', 'id' => $tpl->id]);
        $actions = html_writer::link($editurl, get_string('edit'), ['class' => 'btn btn-sm btn-primary']);

        $table->data[] = [
            format_string($tpl->code),
            format_string($tpl->subject),
            $actions,
        ];
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
