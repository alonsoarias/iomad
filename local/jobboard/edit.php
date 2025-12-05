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
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

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
} else {
    require_capability('local/jobboard:createvacancy', $context);
    $vacancy = null;
    $pagetitle = get_string('newvacancy', 'local_jobboard');
}

$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/jobboard/edit.php', ['id' => $id]));
$PAGE->set_pagelayout('admin');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

// Build navigation based on context.
if ($convocatoriaid) {
    $convocatoriarecord = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
    if ($convocatoriarecord) {
        $PAGE->navbar->add(get_string('manageconvocatorias', 'local_jobboard'),
            new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']));
        $PAGE->navbar->add(s($convocatoriarecord->name),
            new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'id' => $convocatoriaid]));
    }
} else {
    $PAGE->navbar->add(get_string('managevacancies', 'local_jobboard'),
        new moodle_url('/local/jobboard/index.php', ['view' => 'manage']));
}
$PAGE->navbar->add($pagetitle);

// Create form.
$formdata = ['vacancy' => $vacancy];
if ($convocatoriaid && !$vacancy) {
    $formdata['convocatoriaid'] = $convocatoriaid;
}
$form = new \local_jobboard\forms\vacancy_form(null, $formdata);

// Handle cancel.
if ($form->is_cancelled()) {
    if ($convocatoriaid) {
        redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'id' => $convocatoriaid]));
    } else {
        redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'manage']));
    }
}

// Handle submission.
if ($data = $form->get_data()) {
    try {
        // Handle extemporaneous logic: use convocatoria dates if not extemporaneous.
        if (!empty($data->convocatoriaid)) {
            $convocatoria = local_jobboard_get_convocatoria($data->convocatoriaid);
            if ($convocatoria) {
                if (empty($data->isextemporaneous)) {
                    // Use convocatoria dates.
                    $data->opendate = $convocatoria->startdate;
                    $data->closedate = $convocatoria->enddate;
                    $data->isextemporaneous = 0;
                    $data->extemporaneousreason = '';
                } else {
                    $data->isextemporaneous = 1;
                }
            }
        } else {
            // No convocatoria - clear extemporaneous fields.
            $data->isextemporaneous = 0;
            $data->extemporaneousreason = '';
        }

        if ($id) {
            // Update existing vacancy.
            $vacancy->update($data);
            $message = get_string('vacancyupdated', 'local_jobboard');

            // Audit log for update.
            \local_jobboard\audit::log('vacancy_updated', 'vacancy', $vacancy->id, [
                'isextemporaneous' => $data->isextemporaneous,
                'extemporaneousreason' => $data->extemporaneousreason ?? '',
            ]);
        } else {
            // Create new vacancy.
            $vacancy = \local_jobboard\vacancy::create($data);
            $message = get_string('vacancycreated', 'local_jobboard');

            // Audit log for creation.
            \local_jobboard\audit::log('vacancy_created', 'vacancy', $vacancy->id, [
                'convocatoriaid' => $data->convocatoriaid ?? 0,
                'isextemporaneous' => $data->isextemporaneous,
                'extemporaneousreason' => $data->extemporaneousreason ?? '',
            ]);
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

// Output.
echo $OUTPUT->header();

echo html_writer::tag('h2', $pagetitle);

$form->display();

echo $OUTPUT->footer();
