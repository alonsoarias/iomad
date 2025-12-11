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
 * Convocatoria create/edit view for local_jobboard.
 *
 * Uses renderer + Mustache template for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

// Require capability to manage convocatorias.
require_capability('local/jobboard:createvacancy', $context);

// Parameters.
$convocatoriaid = optional_param('id', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

// Check IOMAD installation.
$isiomad = local_jobboard_is_iomad_installed();
$companies = [];
if ($isiomad) {
    $companies = local_jobboard_get_companies();
}

// Load existing convocatoria if editing.
$convocatoria = null;
if ($convocatoriaid) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid], '*', MUST_EXIST);
    $pagetitle = get_string('editconvocatoria', 'local_jobboard') . ': ' . $convocatoria->name;
} else {
    $pagetitle = get_string('addconvocatoria', 'local_jobboard');
}

// Page setup.
$PAGE->set_pagelayout('admin');
$PAGE->set_title($pagetitle);
$PAGE->set_heading($pagetitle);

// Base URLs.
$returnurl = new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']);

// Form URL must include all necessary parameters.
$formurlparams = ['view' => 'convocatoria'];
if ($convocatoriaid) {
    $formurlparams['id'] = $convocatoriaid;
}
if ($action) {
    $formurlparams['action'] = $action;
}
$formurl = new moodle_url('/local/jobboard/index.php', $formurlparams);

// Create form with explicit URL.
$formdata = [
    'convocatoria' => $convocatoria,
    'isiomad' => $isiomad,
    'companies' => $companies,
];

$form = new \local_jobboard\forms\convocatoria_form($formurl, $formdata);

// Set form data if editing.
if ($convocatoria) {
    $form->set_data_from_convocatoria($convocatoria);
}

// Handle cancel.
if ($form->is_cancelled()) {
    redirect($returnurl);
}

// Load AMD module for IOMAD company/department selection.
if ($isiomad) {
    $PAGE->requires->js_call_amd('local_jobboard/vacancy_form', 'init', []);
}

// Handle submission.
if ($data = $form->get_data()) {
    try {
        // Process description field (editor returns array with 'text' and 'format').
        $description = '';
        if (is_array($data->description) && !empty($data->description['text'])) {
            $description = $data->description['text'];
        } else if (is_string($data->description)) {
            $description = $data->description;
        }

        // Process terms field (editor returns array with 'text' and 'format').
        $terms = '';
        if (is_array($data->terms) && !empty($data->terms['text'])) {
            $terms = $data->terms['text'];
        } else if (is_string($data->terms)) {
            $terms = $data->terms;
        }

        if ($convocatoria) {
            // Store previous values for audit.
            $previousdata = clone $convocatoria;

            // Update existing convocatoria.
            $convocatoria->code = $data->code;
            $convocatoria->name = $data->name;
            $convocatoria->description = $description;
            $convocatoria->startdate = $data->startdate;
            $convocatoria->enddate = $data->enddate;
            $convocatoria->publicationtype = $data->publicationtype ?? 'internal';
            $convocatoria->terms = $terms;
            $convocatoria->modifiedby = $USER->id;
            $convocatoria->timemodified = time();

            if ($isiomad) {
                $convocatoria->companyid = $data->companyid ?? null;
                $convocatoria->departmentid = $data->departmentid ?? null;
            }

            $DB->update_record('local_jobboard_convocatoria', $convocatoria);

            // Audit log for update.
            \local_jobboard\audit::log('convocatoria_updated', 'convocatoria', $convocatoria->id, [
                'code' => $convocatoria->code,
                'name' => $convocatoria->name,
                'previous_startdate' => $previousdata->startdate,
                'new_startdate' => $convocatoria->startdate,
                'previous_enddate' => $previousdata->enddate,
                'new_enddate' => $convocatoria->enddate,
                'status' => $convocatoria->status,
            ]);

            // Update document exemptions.
            if (isset($data->exempted_doctypes) && is_array($data->exempted_doctypes)) {
                \local_jobboard\convocatoria_exemption::set_exemptions(
                    $convocatoria->id,
                    $data->exempted_doctypes,
                    $data->exemption_reason ?? null
                );
            }

            $message = get_string('convocatoriaupdated', 'local_jobboard');

            // Redirect back to edit page to continue editing.
            redirect(
                new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'id' => $convocatoria->id]),
                $message,
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        } else {
            // Create new convocatoria.
            $newconvocatoria = new stdClass();
            $newconvocatoria->code = $data->code;
            $newconvocatoria->name = $data->name;
            $newconvocatoria->description = $description;
            $newconvocatoria->startdate = $data->startdate;
            $newconvocatoria->enddate = $data->enddate;
            $newconvocatoria->publicationtype = $data->publicationtype ?? 'internal';
            $newconvocatoria->terms = $terms;
            $newconvocatoria->status = 'draft';
            $newconvocatoria->createdby = $USER->id;
            $newconvocatoria->timecreated = time();

            if ($isiomad) {
                $newconvocatoria->companyid = $data->companyid ?? null;
                $newconvocatoria->departmentid = $data->departmentid ?? null;
            }

            $newid = $DB->insert_record('local_jobboard_convocatoria', $newconvocatoria);

            // Audit log for creation.
            \local_jobboard\audit::log('convocatoria_created', 'convocatoria', $newid, [
                'code' => $newconvocatoria->code,
                'name' => $newconvocatoria->name,
                'startdate' => $newconvocatoria->startdate,
                'enddate' => $newconvocatoria->enddate,
                'publicationtype' => $newconvocatoria->publicationtype,
                'companyid' => $newconvocatoria->companyid ?? null,
            ]);

            // Set document exemptions if any were selected.
            if (isset($data->exempted_doctypes) && is_array($data->exempted_doctypes) && !empty($data->exempted_doctypes)) {
                \local_jobboard\convocatoria_exemption::set_exemptions(
                    $newid,
                    $data->exempted_doctypes,
                    $data->exemption_reason ?? null
                );
            }

            $message = get_string('convocatoriacreated', 'local_jobboard');

            // Redirect to the new convocatoria to add vacancies.
            redirect(
                new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'id' => $newid]),
                $message,
                null,
                \core\output\notification::NOTIFY_SUCCESS
            );
        }
    } catch (Exception $e) {
        \core\notification::error($e->getMessage());
    }
}

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Get statistics and vacancies if editing.
$vacancycount = 0;
$applicationcount = 0;
$vacancies = [];

if ($convocatoria) {
    $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoria->id]);
    $applicationcount = $DB->get_field_sql(
        "SELECT COUNT(a.id)
           FROM {local_jobboard_application} a
           JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
          WHERE v.convocatoriaid = :convid",
        ['convid' => $convocatoria->id]
    );

    // Get first 5 vacancies for the list.
    if ($vacancycount > 0) {
        $vacancies = $DB->get_records_sql(
            "SELECT v.*,
                    (SELECT COUNT(*) FROM {local_jobboard_application} a WHERE a.vacancyid = v.id) as app_count
               FROM {local_jobboard_vacancy} v
              WHERE v.convocatoriaid = :convid
              ORDER BY v.code",
            ['convid' => $convocatoria->id],
            0, 5
        );
    }
}

// Capture form HTML.
ob_start();
$form->display();
$formhtml = ob_get_clean();

// Prepare template data.
$data = $renderer->prepare_convocatoria_edit_page_data(
    $convocatoria,
    $formhtml,
    $vacancycount,
    $applicationcount,
    $vacancies
);

// Output page.
echo $OUTPUT->header();
echo $renderer->render_convocatoria_page($data);
echo $OUTPUT->footer();
