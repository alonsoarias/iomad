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
 * Email templates management page - Refactored v3.0.
 *
 * Provides modern interface to customize email notifications sent by Job Board.
 * Supports multi-tenant templates per IOMAD company and template categories.
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
$id = optional_param('id', 0, PARAM_INT);
$category = optional_param('category', '', PARAM_ALPHA);
$companyid = optional_param('companyid', 0, PARAM_INT);

$context = context_system::instance();
$pageurl = new moodle_url('/local/jobboard/admin/templates.php');


// =============================================================================
// HANDLE ACTIONS
// =============================================================================

// Toggle enabled status.
if ($action === 'toggle' && $id && confirm_sesskey()) {
    $newstatus = email_template::toggle_enabled($id);
    $statusmsg = $newstatus ? get_string('template_enabled_success', 'local_jobboard')
                           : get_string('template_disabled_success', 'local_jobboard');
    redirect($pageurl, $statusmsg, null, \core\output\notification::NOTIFY_SUCCESS);
}

// Reset to default.
if ($action === 'reset' && $code && confirm_sesskey()) {
    email_template::reset_to_default($code, $companyid);
    redirect($pageurl, get_string('template_reset_success', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Delete template.
if ($action === 'delete' && $id && confirm_sesskey()) {
    $template = email_template::get_by_id($id);
    if ($template && $template->delete()) {
        redirect($pageurl, get_string('template_deleted_success', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
    }
    redirect($pageurl, get_string('template_delete_failed', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
}

// Install defaults.
if ($action === 'install' && confirm_sesskey()) {
    $count = email_template::install_defaults();
    redirect($pageurl, get_string('templates_installed', 'local_jobboard', $count), null, \core\output\notification::NOTIFY_SUCCESS);
}

// =============================================================================
// EDIT TEMPLATE
// =============================================================================

if ($action === 'edit' && $code) {
    // Get existing template or create from defaults.
    $template = email_template::get($code, $companyid);

    if (!$template) {
        redirect($pageurl, get_string('template_not_found', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
    }

    $placeholders = email_template::get_placeholders($code);

    $customdata = [
        'code' => $code,
        'companyid' => $companyid,
        'template' => $template,
        'placeholders' => $placeholders,
    ];

    $editurl = new moodle_url($pageurl, ['action' => 'edit', 'code' => $code, 'companyid' => $companyid]);
    $mform = new email_template_form($editurl, $customdata);

    // Handle cancel.
    if ($mform->is_cancelled()) {
        redirect($pageurl);
    }

    // Handle save.
    if ($data = $mform->get_data()) {
        $templateObj = $mform->get_template();
        if ($templateObj) {
            $templateObj->save();
            redirect($pageurl, get_string('template_saved_success', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
        }
    }

    // Display edit form.
    $templatename = email_template::get_template_name($code);
    $PAGE->set_title(get_string('edit_template', 'local_jobboard') . ': ' . $templatename);

    echo $OUTPUT->header();
    echo html_writer::start_div('local-jobboard-templates');

    // Page header.
    echo ui_helper::page_header(
        get_string('email_templates', 'local_jobboard'),
        [],
        [
            [
                'url' => $pageurl,
                'label' => get_string('back_to_templates', 'local_jobboard'),
                'icon' => 'arrow-left',
                'class' => 'btn btn-outline-secondary',
            ],
        ]
    );

    // Template edit card.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-primary text-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5',
        '<i class="fa fa-edit mr-2"></i>' . get_string('edit_template', 'local_jobboard') . ': ' . s($templatename),
        ['class' => 'mb-0']
    );
    echo html_writer::tag('span', s($code), ['class' => 'badge badge-light']);
    echo html_writer::end_div();

    echo html_writer::start_div('card-body');

    // Template description.
    $desckey = 'template_' . $code . '_desc';
    if (get_string_manager()->string_exists($desckey, 'local_jobboard')) {
        echo html_writer::start_div('alert alert-info d-flex align-items-start mb-4');
        echo html_writer::tag('i', '', ['class' => 'fa fa-info-circle fa-lg mr-3 mt-1']);
        echo html_writer::tag('div', get_string($desckey, 'local_jobboard'));
        echo html_writer::end_div();
    }

    // Display form.
    $mform->display();

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card

    // Navigation footer.
    echo render_navigation_footer($pageurl);

    echo html_writer::end_div(); // local-jobboard-templates
    echo $OUTPUT->footer();
    exit;
}

// =============================================================================
// TEMPLATES LIST VIEW - Uses renderer + template pattern
// =============================================================================

// Get all templates.
$templates = email_template::get_all_for_company($companyid);
$stats = email_template::get_statistics();
$categories = email_template::get_all_categories();

// Filter by category if specified.
if ($category && in_array($category, $categories)) {
    $templates = array_filter($templates, function($t) use ($category) {
        return $t->category === $category;
    });
}

echo $OUTPUT->header();

// Get renderer and prepare data.
$renderer = $PAGE->get_renderer('local_jobboard');
$data = $renderer->prepare_admin_templates_page_data(
    $templates,
    $stats,
    $categories,
    $category,
    $companyid,
    $pageurl
);

// Render the page.
echo $renderer->render_admin_templates_page($data);

echo $OUTPUT->footer();

// =============================================================================
// HELPER FUNCTIONS
// =============================================================================

/**
 * Get Bootstrap badge class for category.
 *
 * @param string $category Category code.
 * @return string Badge class.
 */
function get_category_badge_class(string $category): string {
    $classes = [
        'application' => 'badge-primary',
        'documents' => 'badge-info',
        'interview' => 'badge-warning',
        'selection' => 'badge-success',
        'system' => 'badge-secondary',
    ];
    return $classes[$category] ?? 'badge-light';
}

/**
 * Render navigation footer.
 *
 * @param moodle_url $pageurl Current page URL.
 * @return string HTML.
 */
function render_navigation_footer(moodle_url $pageurl): string {
    $html = html_writer::start_div('card mt-4 bg-light');
    $html .= html_writer::start_div('card-body d-flex flex-wrap align-items-center justify-content-center');

    $html .= html_writer::link(
        new moodle_url('/local/jobboard/index.php'),
        '<i class="fa fa-tachometer-alt mr-2"></i>' . get_string('dashboard', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary m-1']
    );

    $html .= html_writer::link(
        new moodle_url('/local/jobboard/admin/doctypes.php'),
        '<i class="fa fa-file-alt mr-2"></i>' . get_string('doctypes', 'local_jobboard'),
        ['class' => 'btn btn-outline-primary m-1']
    );

    $html .= html_writer::link(
        new moodle_url('/local/jobboard/admin/roles.php'),
        '<i class="fa fa-user-tag mr-2"></i>' . get_string('manageroles', 'local_jobboard'),
        ['class' => 'btn btn-outline-info m-1']
    );

    $html .= html_writer::link(
        new moodle_url('/admin/settings.php', ['section' => 'local_jobboard']),
        '<i class="fa fa-cog mr-2"></i>' . get_string('pluginsettings', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary m-1']
    );

    $html .= html_writer::end_div();
    $html .= html_writer::end_div();

    return $html;
}
