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
 * Provides interface to customize email notifications sent to applicants.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_jobboard\email_template;
use local_jobboard\forms\email_template_form;

admin_externalpage_setup('local_jobboard_templates');

$action = optional_param('action', '', PARAM_ALPHA);
$code = optional_param('code', '', PARAM_ALPHANUMEXT);

$context = context_system::instance();
$pageurl = new moodle_url('/local/jobboard/admin/templates.php');

/**
 * Check if companyid column exists in email_template table.
 *
 * @return bool True if column exists.
 */
function local_jobboard_email_template_has_companyid(): bool {
    global $DB;
    $dbman = $DB->get_manager();
    $table = new xmldb_table('local_jobboard_email_template');
    $field = new xmldb_field('companyid');
    return $dbman->field_exists($table, $field);
}

/**
 * Get templates from database safely.
 *
 * @param int $companyid Company ID (0 for global).
 * @return array Array of template records.
 */
function local_jobboard_get_templates(int $companyid = 0): array {
    global $DB;

    $hascompanyid = local_jobboard_email_template_has_companyid();

    if ($hascompanyid) {
        return $DB->get_records('local_jobboard_email_template', ['companyid' => $companyid], 'code ASC');
    } else {
        return $DB->get_records('local_jobboard_email_template', [], 'code ASC');
    }
}

/**
 * Count templates in database safely.
 *
 * @param int $companyid Company ID (0 for global).
 * @return int Count of templates.
 */
function local_jobboard_count_templates(int $companyid = 0): int {
    global $DB;

    $hascompanyid = local_jobboard_email_template_has_companyid();

    if ($hascompanyid) {
        return $DB->count_records('local_jobboard_email_template', ['companyid' => $companyid]);
    } else {
        return $DB->count_records('local_jobboard_email_template');
    }
}

// Handle reset action.
if ($action === 'reset' && $code && confirm_sesskey()) {
    email_template::reset_to_default($code, 0);
    redirect($pageurl, get_string('emailtemplate_restored', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle edit action - show form.
if ($action === 'edit' && $code) {
    $template = email_template::get($code, 0);
    if (!$template) {
        // Get default template if not customized yet.
        $template = email_template::get_default($code);
    }

    $placeholders = email_template::get_placeholders($code);

    $customdata = [
        'code' => $code,
        'template' => $template,
        'placeholders' => $placeholders,
    ];

    $mform = new email_template_form($pageurl, $customdata);

    // Handle form submission.
    if ($mform->is_cancelled()) {
        redirect($pageurl);
    }

    if ($data = $mform->get_data()) {
        $subject = $data->subject;
        $body = $data->body_editor['text'] ?? '';

        email_template::save($code, $subject, $body, 0);
        redirect($pageurl, get_string('emailtemplate_saved', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
    }

    // Display the form.
    $templatename = get_string_manager()->string_exists('template_' . $code, 'local_jobboard')
        ? get_string('template_' . $code, 'local_jobboard')
        : $code;

    $PAGE->set_title(get_string('emailtemplate', 'local_jobboard') . ': ' . $templatename);

    echo $OUTPUT->header();

    // Breadcrumb-style navigation.
    echo html_writer::start_div('mb-4');
    echo html_writer::link($pageurl, '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtotemplates', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary']);
    echo html_writer::end_div();

    echo $OUTPUT->heading(get_string('emailtemplate', 'local_jobboard') . ': ' . $templatename);

    // Show description for the template type.
    $desckey = 'template_' . $code . '_desc';
    if (get_string_manager()->string_exists($desckey, 'local_jobboard')) {
        echo html_writer::div(get_string($desckey, 'local_jobboard'), 'alert alert-info');
    }

    // Display the form.
    $mform->display();

    // Initialize live preview JavaScript.
    $PAGE->requires->js_call_amd('local_jobboard/email_template_preview', 'init', [[]]);

    echo $OUTPUT->footer();
    exit;
}

// Install default templates if needed.
$existingcount = local_jobboard_count_templates(0);
if ($existingcount === 0) {
    email_template::install_defaults();
}

// Get all templates from database.
$templates = local_jobboard_get_templates(0);

// Also check for missing templates that have language string defaults.
$allcodes = email_template::get_all_codes();
$dbcodes = array_map(function($tpl) {
    return $tpl->code ?? $tpl->templatekey ?? '';
}, $templates);
$missingcodes = array_diff($allcodes, $dbcodes);

// Display the templates list.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('emailtemplates', 'local_jobboard'));

// Help text.
echo html_writer::start_div('alert alert-info d-flex align-items-start');
echo html_writer::tag('i', '', ['class' => 'fa fa-info-circle fa-2x mr-3 mt-1']);
echo html_writer::start_div();
echo html_writer::tag('strong', get_string('emailtemplates', 'local_jobboard'));
echo html_writer::tag('p', get_string('emailtemplates_help', 'local_jobboard'), ['class' => 'mb-0 mt-1']);
echo html_writer::end_div();
echo html_writer::end_div();

if (empty($templates) && empty($missingcodes)) {
    echo $OUTPUT->notification(get_string('notemplates', 'local_jobboard'), 'info');
} else {
    // Card-based layout for templates.
    echo html_writer::start_div('row');

    // Database templates (customized).
    foreach ($templates as $tpl) {
        $tplcode = $tpl->code ?? $tpl->templatekey ?? 'unknown';
        $templatename = get_string_manager()->string_exists('template_' . $tplcode, 'local_jobboard')
            ? get_string('template_' . $tplcode, 'local_jobboard')
            : $tplcode;

        echo render_template_card($tplcode, $templatename, $tpl->subject, true, $pageurl);
    }

    // Missing templates (using defaults).
    foreach ($missingcodes as $missingcode) {
        $defaultTemplate = email_template::get_default($missingcode);
        if (!$defaultTemplate) {
            continue;
        }

        $templatename = get_string_manager()->string_exists('template_' . $missingcode, 'local_jobboard')
            ? get_string('template_' . $missingcode, 'local_jobboard')
            : $missingcode;

        echo render_template_card($missingcode, $templatename, $defaultTemplate->subject, false, $pageurl);
    }

    echo html_writer::end_div(); // .row
}

// Quick help section.
echo html_writer::start_div('card mt-4 shadow-sm');
echo html_writer::start_div('card-header bg-secondary text-white');
echo '<i class="fa fa-question-circle mr-2"></i>' . get_string('quickhelp', 'local_jobboard');
echo html_writer::end_div();
echo html_writer::start_div('card-body');
echo html_writer::start_tag('ul', ['class' => 'mb-0']);
echo html_writer::tag('li', get_string('templatehelp_placeholders', 'local_jobboard'));
echo html_writer::tag('li', get_string('templatehelp_html', 'local_jobboard'));
echo html_writer::tag('li', get_string('templatehelp_reset', 'local_jobboard'));
echo html_writer::end_tag('ul');
echo html_writer::end_div();
echo html_writer::end_div();

echo $OUTPUT->footer();

/**
 * Render a template card.
 *
 * @param string $code Template code.
 * @param string $name Template display name.
 * @param string $subject Template subject.
 * @param bool $iscustomized Whether template is customized.
 * @param moodle_url $pageurl Base page URL.
 * @return string HTML output.
 */
function render_template_card(string $code, string $name, string $subject, bool $iscustomized, moodle_url $pageurl): string {
    $editurl = new moodle_url($pageurl, ['action' => 'edit', 'code' => $code]);
    $reseturl = new moodle_url($pageurl, ['action' => 'reset', 'code' => $code, 'sesskey' => sesskey()]);

    $borderclass = $iscustomized ? 'border-success' : 'border-secondary';
    $statusclass = $iscustomized ? 'badge-success' : 'badge-secondary';
    $statustext = $iscustomized ? get_string('customized', 'local_jobboard') : get_string('default', 'local_jobboard');

    $html = html_writer::start_div('col-lg-6 col-xl-4 mb-4');
    $html .= html_writer::start_div('card h-100 shadow-sm ' . $borderclass);

    // Card header.
    $html .= html_writer::start_div('card-header d-flex justify-content-between align-items-center');
    $html .= html_writer::tag('span', $name, ['class' => 'font-weight-bold']);
    $html .= html_writer::tag('span', $statustext, ['class' => 'badge ' . $statusclass]);
    $html .= html_writer::end_div();

    // Card body.
    $html .= html_writer::start_div('card-body');
    $html .= html_writer::tag('code', s($code), ['class' => 'small text-muted d-block mb-2']);
    $html .= html_writer::tag('p', '<strong>' . get_string('subject') . ':</strong>', ['class' => 'mb-1 small']);
    $html .= html_writer::tag('p', format_string($subject), ['class' => 'text-muted small mb-0 text-truncate']);
    $html .= html_writer::end_div();

    // Card footer with actions.
    $html .= html_writer::start_div('card-footer bg-transparent');

    // Edit button.
    $html .= html_writer::link($editurl,
        '<i class="fa fa-edit mr-1"></i>' . get_string('edit'),
        ['class' => 'btn btn-sm btn-primary mr-2']
    );

    // Reset button (only for customized templates).
    if ($iscustomized) {
        $html .= html_writer::link($reseturl,
            '<i class="fa fa-undo mr-1"></i>' . get_string('reset'),
            [
                'class' => 'btn btn-sm btn-outline-secondary',
                'onclick' => "return confirm('" . get_string('confirmreset', 'local_jobboard') . "');",
            ]
        );
    }

    $html .= html_writer::end_div();
    $html .= html_writer::end_div(); // .card
    $html .= html_writer::end_div(); // .col

    return $html;
}
