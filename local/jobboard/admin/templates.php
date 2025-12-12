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

    // Display edit form using renderer + template pattern.
    $templatename = email_template::get_template_name($code);
    $PAGE->set_title(get_string('edit_template', 'local_jobboard') . ': ' . $templatename);

    echo $OUTPUT->header();

    // Capture form HTML.
    ob_start();
    $mform->display();
    $formhtml = ob_get_clean();

    // Use renderer.
    $renderer = $PAGE->get_renderer('local_jobboard');
    $data = $renderer->prepare_admin_template_edit_data($code, $templatename, $formhtml, $pageurl);
    echo $renderer->render_admin_template_edit_page($data);

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
