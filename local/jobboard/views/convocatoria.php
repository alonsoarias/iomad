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
 * This file is included by index.php and should not be accessed directly.
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

// Add navigation.
$PAGE->navbar->add(get_string('manageconvocatorias', 'local_jobboard'),
    new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']));
$PAGE->navbar->add($convocatoria ? s($convocatoria->name) : get_string('addconvocatoria', 'local_jobboard'));

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

echo $OUTPUT->header();

// Show convocatoria info box if editing.
if ($convocatoria) {
    $statusclass = [
        'draft' => 'secondary',
        'open' => 'success',
        'closed' => 'warning',
        'archived' => 'dark',
    ];

    echo html_writer::start_div('card mb-4');
    echo html_writer::start_div('card-header d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5', s($convocatoria->name), ['class' => 'mb-0']);
    echo html_writer::tag('span',
        get_string('convocatoria_status_' . $convocatoria->status, 'local_jobboard'),
        ['class' => 'badge badge-' . ($statusclass[$convocatoria->status] ?? 'secondary')]
    );
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    // Show dates.
    echo html_writer::tag('p',
        html_writer::tag('strong', get_string('convocatoriastartdate', 'local_jobboard') . ': ') .
        userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig')) . ' - ' .
        html_writer::tag('strong', get_string('convocatoriaenddate', 'local_jobboard') . ': ') .
        userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig'))
    );

    // Show vacancy count and link.
    $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoria->id]);
    echo html_writer::tag('p',
        html_writer::tag('strong', get_string('vacancies', 'local_jobboard') . ': ') .
        html_writer::tag('span', $vacancycount, ['class' => 'badge badge-info'])
    );

    // Action buttons.
    echo html_writer::start_div('mt-3');

    // Add vacancy button (for draft convocatorias).
    if ($convocatoria->status === 'draft') {
        $addvacancyurl = new moodle_url('/local/jobboard/edit.php', ['convocatoriaid' => $convocatoria->id]);
        echo html_writer::link($addvacancyurl,
            html_writer::tag('i', '', ['class' => 'fa fa-plus me-1']) .
            get_string('addvacancy', 'local_jobboard'),
            ['class' => 'btn btn-success me-2']
        );
    }

    // View vacancies button.
    if ($vacancycount > 0) {
        $vacanciesurl = new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $convocatoria->id]);
        echo html_writer::link($vacanciesurl,
            html_writer::tag('i', '', ['class' => 'fa fa-list me-1']) .
            get_string('viewvacancies', 'local_jobboard'),
            ['class' => 'btn btn-info me-2']
        );
    }

    // Back to list button.
    echo html_writer::link($returnurl,
        html_writer::tag('i', '', ['class' => 'fa fa-arrow-left me-1']) .
        get_string('backtoconvocatorias', 'local_jobboard'),
        ['class' => 'btn btn-outline-secondary']
    );

    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::end_div();
}

// Display the form.
$form->display();

echo $OUTPUT->footer();
