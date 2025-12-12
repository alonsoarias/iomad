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
 * Vacancy create/edit page for local_jobboard.
 *
 * Modern redesign with requirement for convocatoria.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

use local_jobboard\output\ui_helper;

require_login();

$id = optional_param('id', 0, PARAM_INT);
$convocatoriaid = optional_param('convocatoriaid', 0, PARAM_INT);

$context = context_system::instance();

if ($id) {
    require_capability('local/jobboard:editvacancy', $context);
    $vacancy = \local_jobboard\vacancy::get($id);
    if (!$vacancy) {
        throw new moodle_exception('error:vacancynotfound', 'local_jobboard');
    }
    if (!$vacancy->can_edit()) {
        throw new moodle_exception('error:permissiondenied', 'local_jobboard');
    }
    $pagetitle = get_string('edit', 'local_jobboard') . ': ' . $vacancy->title;
    // Get convocatoria from vacancy if not provided.
    if (!$convocatoriaid && $vacancy->convocatoriaid) {
        $convocatoriaid = $vacancy->convocatoriaid;
    }
} else {
    require_capability('local/jobboard:createvacancy', $context);
    $vacancy = null;
    $pagetitle = get_string('newvacancy', 'local_jobboard');
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/jobboard/edit.php', ['id' => $id, 'convocatoriaid' => $convocatoriaid]));
$PAGE->set_pagelayout('admin');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

// Get convocatoria info.
$convocatoriarecord = null;
if ($convocatoriaid) {
    $convocatoriarecord = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
}

// If creating new vacancy and no convocatoria selected, show selection page.
if (!$id && !$convocatoriaid) {
    // Get available convocatorias.
    $convocatorias = local_jobboard_get_convocatorias(0, '', true); // Only draft and open convocatorias.

    echo $OUTPUT->header();

    // Use renderer + template pattern.
    $renderer = $PAGE->get_renderer('local_jobboard');
    $data = $renderer->prepare_edit_select_convocatoria_data($convocatorias);
    echo $renderer->render_edit_select_convocatoria_page($data);

    echo $OUTPUT->footer();
    exit;
}

// Create form.
$formdata = ['vacancy' => $vacancy];
if ($convocatoriaid && !$vacancy) {
    $formdata['convocatoriaid'] = $convocatoriaid;
}
$form = new \local_jobboard\forms\vacancy_form(null, $formdata);

// Handle cancel.
if ($form->is_cancelled()) {
    if ($convocatoriaid) {
        redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $convocatoriaid]));
    } else {
        redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']));
    }
}

// Handle submission.
if ($data = $form->get_data()) {
    try {
        // Note: Vacancy dates are now inherited exclusively from convocatoria.
        // The isextemporaneous and extemporaneousreason fields have been removed.

        if ($id) {
            // Update existing vacancy.
            $vacancy->update($data);
            $message = get_string('vacancyupdated', 'local_jobboard');
        } else {
            // Create new vacancy.
            $vacancy = \local_jobboard\vacancy::create($data);
            $message = get_string('vacancycreated', 'local_jobboard');
        }

        redirect(
            new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancy->id]),
            $message,
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } catch (Exception $e) {
        redirect(
            $PAGE->url,
            $e->getMessage(),
            null,
            \core\output\notification::NOTIFY_ERROR
        );
    }
}

// Set form data if editing.
if ($vacancy) {
    $form->set_data_from_vacancy($vacancy);
}

// Output using renderer + template pattern.
echo $OUTPUT->header();

// Capture form HTML.
ob_start();
$form->display();
$formhtml = ob_get_clean();

// Use renderer.
$renderer = $PAGE->get_renderer('local_jobboard');
$data = $renderer->prepare_edit_vacancy_form_data($convocatoriarecord, $formhtml);
echo $renderer->render_edit_vacancy_form_page($data);

echo $OUTPUT->footer();
