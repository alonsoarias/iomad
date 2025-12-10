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
 * Redesigned with ui_helper for consistent UX pattern.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_jobboard\email_template;
use local_jobboard\forms\email_template_form;
use local_jobboard\output\ui_helper;

admin_externalpage_setup('local_jobboard_templates');

$action = optional_param('action', '', PARAM_ALPHA);
$code = optional_param('code', '', PARAM_ALPHANUMEXT);

$context = context_system::instance();
$pageurl = new moodle_url('/local/jobboard/admin/templates.php');

$PAGE->requires->css('/local/jobboard/styles.css');

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

    if (!$template) {
        redirect($pageurl, get_string('templatenotfound', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
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

    // Display the edit form.
    $templatename = get_string_manager()->string_exists('template_' . $code, 'local_jobboard')
        ? get_string('template_' . $code, 'local_jobboard')
        : $code;

    $PAGE->set_title(get_string('emailtemplate', 'local_jobboard') . ': ' . $templatename);

    echo $OUTPUT->header();

    echo html_writer::start_div('local-jobboard-templates');

    // Page header with back button.
    echo ui_helper::page_header(
        get_string('emailtemplates', 'local_jobboard'),
        [],
        [
            [
                'url' => $pageurl,
                'label' => get_string('backtotemplates', 'local_jobboard'),
                'icon' => 'arrow-left',
                'class' => 'btn btn-outline-secondary',
            ],
        ]
    );

    // Template info card.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-primary text-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5',
        '<i class="fa fa-edit mr-2"></i>' . get_string('edittemplate', 'local_jobboard') . ': ' . $templatename,
        ['class' => 'mb-0']
    );
    echo html_writer::tag('code', $code, ['class' => 'badge badge-light']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    // Show description for the template type.
    $desckey = 'template_' . $code . '_desc';
    if (get_string_manager()->string_exists($desckey, 'local_jobboard')) {
        echo html_writer::start_div('alert alert-info d-flex align-items-start mb-4');
        echo html_writer::tag('i', '', ['class' => 'fa fa-info-circle fa-lg mr-3 mt-1']);
        echo html_writer::tag('div', get_string($desckey, 'local_jobboard'));
        echo html_writer::end_div();
    }

    // Available placeholders.
    if (!empty($placeholders)) {
        echo html_writer::start_div('card bg-light mb-4');
        echo html_writer::start_div('card-header');
        echo html_writer::tag('h6',
            '<i class="fa fa-code mr-2"></i>' . get_string('availableplaceholders', 'local_jobboard'),
            ['class' => 'mb-0']
        );
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');
        echo html_writer::start_div('row');
        foreach ($placeholders as $placeholder => $description) {
            echo html_writer::start_div('col-md-6 col-lg-4 mb-2');
            echo html_writer::tag('code', $placeholder, ['class' => 'text-primary']);
            echo html_writer::tag('br');
            echo html_writer::tag('small', $description, ['class' => 'text-muted']);
            echo html_writer::end_div();
        }
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    // Display the form.
    $mform->display();

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card

    // Live preview section.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-success text-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-eye mr-2"></i>' . get_string('livepreview', 'local_jobboard'),
        ['class' => 'mb-0']
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    echo html_writer::div('', '', ['id' => 'template-preview']);
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Navigation footer.
    echo html_writer::start_div('card mt-4 bg-light');
    echo html_writer::start_div('card-body d-flex flex-wrap align-items-center justify-content-center');
    echo html_writer::link(
        $pageurl,
        '<i class="fa fa-envelope mr-2"></i>' . get_string('emailtemplates', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary m-1']
    );
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php'),
        '<i class="fa fa-tachometer-alt mr-2"></i>' . get_string('dashboard', 'local_jobboard'),
        ['class' => 'btn btn-outline-primary m-1']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div(); // local-jobboard-templates

    // Initialize live preview JavaScript (only if needed).
    // Note: The email_template_preview module provides live preview functionality.
    // If module is not available, preview will update on form submission.

    echo $OUTPUT->footer();
    exit;
}

// ============================================================================
// TEMPLATES LIST VIEW
// ============================================================================

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

// Calculate statistics.
$totalTemplates = count($templates) + count($missingcodes);
$customizedTemplates = 0;
$defaultTemplates = 0;

foreach ($templates as $tpl) {
    $customizedTemplates++;
}
$defaultTemplates = count($missingcodes);

// Display the templates list.
echo $OUTPUT->header();

echo html_writer::start_div('local-jobboard-templates');

// ============================================================================
// PAGE HEADER WITH ACTIONS
// ============================================================================
echo ui_helper::page_header(
    get_string('emailtemplates', 'local_jobboard'),
    [],
    [
        [
            'url' => new moodle_url('/local/jobboard/index.php'),
            'label' => get_string('dashboard', 'local_jobboard'),
            'icon' => 'tachometer-alt',
            'class' => 'btn btn-outline-secondary',
        ],
    ]
);

// ============================================================================
// STATISTICS CARDS
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card((string) $totalTemplates, get_string('totaltemplates', 'local_jobboard'), 'primary', 'envelope');
echo ui_helper::stat_card((string) $customizedTemplates, get_string('customizedtemplates', 'local_jobboard'), 'success', 'edit');
echo ui_helper::stat_card((string) $defaultTemplates, get_string('defaulttemplates', 'local_jobboard'), 'secondary', 'file-alt');
echo html_writer::end_div();

// ============================================================================
// INFO CARD
// ============================================================================
echo html_writer::start_div('card shadow-sm mb-4 border-info');
echo html_writer::start_div('card-body d-flex align-items-start');
echo html_writer::tag('i', '', ['class' => 'fa fa-info-circle fa-2x text-info mr-3']);
echo html_writer::start_div();
echo html_writer::tag('h6', get_string('aboutemailtemplates', 'local_jobboard'), ['class' => 'mb-1']);
echo html_writer::tag('p', get_string('emailtemplates_help', 'local_jobboard'), ['class' => 'text-muted mb-0']);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

// ============================================================================
// TEMPLATES TABLE
// ============================================================================
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
echo html_writer::tag('h5',
    '<i class="fa fa-envelope text-primary mr-2"></i>' . get_string('templatelist', 'local_jobboard'),
    ['class' => 'mb-0']
);
echo html_writer::tag('span', $totalTemplates . ' ' . get_string('templates', 'local_jobboard'), ['class' => 'badge badge-primary']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

if (empty($templates) && empty($missingcodes)) {
    echo ui_helper::empty_state(get_string('notemplates', 'local_jobboard'), 'envelope');
} else {
    $headers = [
        get_string('templatename', 'local_jobboard'),
        get_string('templatecode', 'local_jobboard'),
        get_string('subject'),
        get_string('status'),
        get_string('actions'),
    ];

    $rows = [];

    // Database templates (customized).
    foreach ($templates as $tpl) {
        $tplcode = $tpl->code ?? $tpl->templatekey ?? 'unknown';
        $templatename = get_string_manager()->string_exists('template_' . $tplcode, 'local_jobboard')
            ? get_string('template_' . $tplcode, 'local_jobboard')
            : $tplcode;

        $editurl = new moodle_url($pageurl, ['action' => 'edit', 'code' => $tplcode]);
        $reseturl = new moodle_url($pageurl, ['action' => 'reset', 'code' => $tplcode, 'sesskey' => sesskey()]);

        $nameCell = html_writer::tag('strong', format_string($templatename));

        $codeCell = html_writer::tag('code', $tplcode, ['class' => 'text-muted']);

        $subjectCell = html_writer::tag('span', format_string($tpl->subject), ['class' => 'text-truncate d-inline-block', 'style' => 'max-width: 250px;']);

        $statusCell = html_writer::tag('span',
            '<i class="fa fa-check-circle mr-1"></i>' . get_string('customized', 'local_jobboard'),
            ['class' => 'badge badge-success']
        );

        $actions = html_writer::link($editurl,
            '<i class="fa fa-edit"></i>',
            ['class' => 'btn btn-sm btn-outline-primary mr-1', 'title' => get_string('edit')]
        );
        $actions .= html_writer::link($reseturl,
            '<i class="fa fa-undo"></i>',
            [
                'class' => 'btn btn-sm btn-outline-warning',
                'title' => get_string('resettodefault', 'local_jobboard'),
                'onclick' => "return confirm('" . get_string('confirmreset', 'local_jobboard') . "');",
            ]
        );

        $rows[] = [$nameCell, $codeCell, $subjectCell, $statusCell, $actions];
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

        $editurl = new moodle_url($pageurl, ['action' => 'edit', 'code' => $missingcode]);

        $nameCell = html_writer::tag('strong', format_string($templatename));

        $codeCell = html_writer::tag('code', $missingcode, ['class' => 'text-muted']);

        $subjectCell = html_writer::tag('span', format_string($defaultTemplate->subject), ['class' => 'text-truncate d-inline-block', 'style' => 'max-width: 250px;']);

        $statusCell = html_writer::tag('span',
            '<i class="fa fa-file-alt mr-1"></i>' . get_string('default', 'local_jobboard'),
            ['class' => 'badge badge-secondary']
        );

        $actions = html_writer::link($editurl,
            '<i class="fa fa-edit"></i>',
            ['class' => 'btn btn-sm btn-outline-primary', 'title' => get_string('customize', 'local_jobboard')]
        );

        $rows[] = [$nameCell, $codeCell, $subjectCell, $statusCell, $actions];
    }

    echo ui_helper::data_table($headers, $rows);
}

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// ============================================================================
// QUICK HELP SECTION
// ============================================================================
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-secondary text-white');
echo html_writer::tag('h6',
    '<i class="fa fa-question-circle mr-2"></i>' . get_string('quickhelp', 'local_jobboard'),
    ['class' => 'mb-0']
);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

echo html_writer::start_div('row');

// Placeholders help.
echo html_writer::start_div('col-md-4 mb-3 mb-md-0');
echo html_writer::start_div('d-flex align-items-start');
echo html_writer::tag('i', '', ['class' => 'fa fa-code text-primary fa-lg mr-3 mt-1']);
echo html_writer::start_div();
echo html_writer::tag('h6', get_string('placeholders', 'local_jobboard'), ['class' => 'mb-1']);
echo html_writer::tag('small', get_string('templatehelp_placeholders', 'local_jobboard'), ['class' => 'text-muted']);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

// HTML help.
echo html_writer::start_div('col-md-4 mb-3 mb-md-0');
echo html_writer::start_div('d-flex align-items-start');
echo html_writer::tag('i', '', ['class' => 'fa fa-html5 text-warning fa-lg mr-3 mt-1']);
echo html_writer::start_div();
echo html_writer::tag('h6', get_string('htmlsupport', 'local_jobboard'), ['class' => 'mb-1']);
echo html_writer::tag('small', get_string('templatehelp_html', 'local_jobboard'), ['class' => 'text-muted']);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

// Reset help.
echo html_writer::start_div('col-md-4');
echo html_writer::start_div('d-flex align-items-start');
echo html_writer::tag('i', '', ['class' => 'fa fa-undo text-info fa-lg mr-3 mt-1']);
echo html_writer::start_div();
echo html_writer::tag('h6', get_string('resettodefault', 'local_jobboard'), ['class' => 'mb-1']);
echo html_writer::tag('small', get_string('templatehelp_reset', 'local_jobboard'), ['class' => 'text-muted']);
echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // row
echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

// ============================================================================
// NAVIGATION FOOTER
// ============================================================================
echo html_writer::start_div('card mt-4 bg-light');
echo html_writer::start_div('card-body d-flex flex-wrap align-items-center justify-content-center');

echo html_writer::link(
    new moodle_url('/local/jobboard/index.php'),
    '<i class="fa fa-tachometer-alt mr-2"></i>' . get_string('dashboard', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary m-1']
);

echo html_writer::link(
    new moodle_url('/local/jobboard/admin/doctypes.php'),
    '<i class="fa fa-file-alt mr-2"></i>' . get_string('doctypes', 'local_jobboard'),
    ['class' => 'btn btn-outline-primary m-1']
);

echo html_writer::link(
    new moodle_url('/local/jobboard/admin/roles.php'),
    '<i class="fa fa-user-tag mr-2"></i>' . get_string('manageroles', 'local_jobboard'),
    ['class' => 'btn btn-outline-info m-1']
);

echo html_writer::link(
    new moodle_url('/admin/settings.php', ['section' => 'local_jobboard']),
    '<i class="fa fa-cog mr-2"></i>' . get_string('pluginsettings', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary m-1']
);

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-templates

echo $OUTPUT->footer();
