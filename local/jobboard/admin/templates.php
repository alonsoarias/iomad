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

use local_jobboard\email_template;

admin_externalpage_setup('local_jobboard_templates');

$action = optional_param('action', '', PARAM_ALPHA);
$code = optional_param('code', '', PARAM_ALPHANUMEXT);
$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
$pageurl = new moodle_url('/local/jobboard/admin/templates.php');

// Handle reset action.
if ($action === 'reset' && $code && confirm_sesskey()) {
    email_template::reset_to_default($code, 0);
    redirect($pageurl, get_string('emailtemplate_restored', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle edit action.
if ($action === 'edit' && $code) {
    $template = email_template::get($code, 0);
    $placeholders = email_template::get_placeholders($code);

    $mform = new \MoodleQuickForm('edittemplate', 'post', $pageurl);
    $mform->addElement('hidden', 'action', 'save');
    $mform->setType('action', PARAM_ALPHA);
    $mform->addElement('hidden', 'code', $code);
    $mform->setType('code', PARAM_ALPHANUMEXT);

    // Subject.
    $mform->addElement('text', 'subject', get_string('templatesubject', 'local_jobboard'), ['size' => 80]);
    $mform->setType('subject', PARAM_TEXT);
    $mform->setDefault('subject', $template ? $template->subject : '');
    $mform->addRule('subject', get_string('required'), 'required', null, 'client');

    // Body.
    $mform->addElement('textarea', 'body', get_string('templatebody', 'local_jobboard'), ['rows' => 15, 'cols' => 80]);
    $mform->setType('body', PARAM_RAW);
    $mform->setDefault('body', $template ? $template->body : '');
    $mform->addRule('body', get_string('required'), 'required', null, 'client');

    // Submit buttons.
    $buttonarray = [];
    $buttonarray[] = $mform->createElement('submit', 'submitbutton', get_string('savechanges'));
    $buttonarray[] = $mform->createElement('cancel');
    $mform->addGroup($buttonarray, 'buttonar', '', ' ', false);

    echo $OUTPUT->header();

    // Title.
    $templatename = get_string_manager()->string_exists('template_' . $code, 'local_jobboard')
        ? get_string('template_' . $code, 'local_jobboard')
        : $code;
    echo $OUTPUT->heading(get_string('emailtemplate', 'local_jobboard') . ': ' . $templatename);

    // Back link.
    echo html_writer::link($pageurl, '<i class="fa fa-arrow-left mr-2"></i>' . get_string('back'),
        ['class' => 'btn btn-outline-secondary mb-3']);

    echo html_writer::start_div('row');

    // Form column.
    echo html_writer::start_div('col-lg-8');
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-body');
    $mform->display();
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Placeholders column.
    echo html_writer::start_div('col-lg-4');
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-info text-white');
    echo '<i class="fa fa-tags mr-2"></i>' . get_string('availableplaceholders', 'local_jobboard');
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo '<p class="small text-muted">' . get_string('placeholders_help', 'local_jobboard') . '</p>';
    echo '<ul class="list-unstyled mb-0">';
    foreach ($placeholders as $placeholder => $desc) {
        echo '<li class="mb-2">';
        echo '<code class="bg-light px-2 py-1">' . s($placeholder) . '</code>';
        echo '<br><small class="text-muted">' . s($desc) . '</small>';
        echo '</li>';
    }
    echo '</ul>';
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // row.

    echo $OUTPUT->footer();
    exit;
}

// Handle save action.
if ($action === 'save' && $code && confirm_sesskey()) {
    $subject = required_param('subject', PARAM_TEXT);
    $body = required_param('body', PARAM_RAW);

    email_template::save($code, $subject, $body, 0);
    redirect($pageurl, get_string('emailtemplate_saved', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Install default templates if needed.
$existingcount = $DB->count_records('local_jobboard_email_template', ['companyid' => 0]);
if ($existingcount === 0) {
    email_template::install_defaults();
}

// Get all templates.
$templates = $DB->get_records('local_jobboard_email_template', ['companyid' => 0], 'code ASC');

// Also check for missing templates that have language string defaults.
$allcodes = email_template::get_all_codes();
$dbcodes = array_column($templates, 'code');
$missingcodes = array_diff($allcodes, $dbcodes);

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('emailtemplates', 'local_jobboard'));

echo '<div class="alert alert-info">';
echo '<i class="fa fa-info-circle mr-2"></i>';
echo get_string('emailtemplates_help', 'local_jobboard');
echo '</div>';

if (empty($templates) && empty($missingcodes)) {
    echo $OUTPUT->notification(get_string('notemplates', 'local_jobboard'), 'info');
} else {
    $table = new html_table();
    $table->head = [
        get_string('templatekey', 'local_jobboard'),
        get_string('templatesubject', 'local_jobboard'),
        get_string('status'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable table-hover';

    // Add database templates.
    foreach ($templates as $tpl) {
        $templatename = get_string_manager()->string_exists('template_' . $tpl->code, 'local_jobboard')
            ? get_string('template_' . $tpl->code, 'local_jobboard')
            : $tpl->code;

        $editurl = new moodle_url($pageurl, ['action' => 'edit', 'code' => $tpl->code]);
        $reseturl = new moodle_url($pageurl, ['action' => 'reset', 'code' => $tpl->code, 'sesskey' => sesskey()]);

        $actions = html_writer::link($editurl, '<i class="fa fa-edit mr-1"></i>' . get_string('edit'),
            ['class' => 'btn btn-sm btn-primary mr-1']);
        $actions .= html_writer::link($reseturl, '<i class="fa fa-undo mr-1"></i>' . get_string('restoreddefault', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-secondary', 'onclick' => "return confirm('" .
                get_string('confirm') . "');"]);

        $statusbadge = '<span class="badge badge-success">' . get_string('customized', 'local_jobboard') . '</span>';

        $table->data[] = [
            html_writer::tag('strong', s($templatename)) . '<br><code class="small text-muted">' . s($tpl->code) . '</code>',
            format_string($tpl->subject),
            $statusbadge,
            $actions,
        ];
    }

    // Add missing templates (using defaults).
    foreach ($missingcodes as $missingcode) {
        $defaultTemplate = email_template::get_default($missingcode);
        if (!$defaultTemplate) {
            continue;
        }

        $templatename = get_string_manager()->string_exists('template_' . $missingcode, 'local_jobboard')
            ? get_string('template_' . $missingcode, 'local_jobboard')
            : $missingcode;

        $editurl = new moodle_url($pageurl, ['action' => 'edit', 'code' => $missingcode]);
        $actions = html_writer::link($editurl, '<i class="fa fa-edit mr-1"></i>' . get_string('customize', 'local_jobboard'),
            ['class' => 'btn btn-sm btn-outline-primary']);

        $statusbadge = '<span class="badge badge-secondary">' . get_string('default', 'local_jobboard') . '</span>';

        $table->data[] = [
            html_writer::tag('strong', s($templatename)) . '<br><code class="small text-muted">' . s($missingcode) . '</code>',
            format_string($defaultTemplate->subject),
            $statusbadge,
            $actions,
        ];
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
