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
 * Modern redesign with card-based layout and vacancy management.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

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
$PAGE->requires->css('/local/jobboard/styles.css');

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
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-convocatoria');

// ============================================================================
// PAGE HEADER
// ============================================================================
$breadcrumbs = [
    get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
    get_string('manageconvocatorias', 'local_jobboard') => new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
    ($convocatoria ? format_string($convocatoria->name) : get_string('addconvocatoria', 'local_jobboard')) => null,
];

$headerActions = [[
    'url' => $returnurl,
    'label' => get_string('backtoconvocatorias', 'local_jobboard'),
    'icon' => 'arrow-left',
    'class' => 'btn btn-outline-secondary',
]];

echo ui_helper::page_header(
    $convocatoria ? get_string('editconvocatoria', 'local_jobboard') : get_string('addconvocatoria', 'local_jobboard'),
    $breadcrumbs,
    $headerActions
);

// ============================================================================
// CONVOCATORIA INFO CARD (when editing)
// ============================================================================
if ($convocatoria) {
    // Get statistics.
    $vacancyCount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoria->id]);
    $applicationCount = $DB->get_field_sql(
        "SELECT COUNT(a.id)
           FROM {local_jobboard_application} a
           JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
          WHERE v.convocatoriaid = :convid",
        ['convid' => $convocatoria->id]
    );

    // Status config.
    $statusColors = [
        'draft' => 'secondary',
        'open' => 'success',
        'closed' => 'warning',
        'archived' => 'dark',
    ];
    $statusColor = $statusColors[$convocatoria->status] ?? 'secondary';

    // Statistics row.
    echo html_writer::start_div('row mb-4');
    echo ui_helper::stat_card(
        (string)$vacancyCount,
        get_string('vacancies', 'local_jobboard'),
        'primary', 'briefcase'
    );
    echo ui_helper::stat_card(
        (string)$applicationCount,
        get_string('applications', 'local_jobboard'),
        'info', 'file-alt'
    );
    echo ui_helper::stat_card(
        get_string('convocatoria_status_' . $convocatoria->status, 'local_jobboard'),
        get_string('status', 'local_jobboard'),
        $statusColor, 'flag'
    );
    echo html_writer::end_div();

    // Info card with dates and actions.
    echo html_writer::start_div('card shadow-sm mb-4 border-' . $statusColor);
    echo html_writer::start_div('card-header d-flex justify-content-between align-items-center bg-' . $statusColor . ' text-white');
    echo html_writer::tag('h5', format_string($convocatoria->name), ['class' => 'mb-0']);
    echo html_writer::tag('span',
        get_string('convocatoria_status_' . $convocatoria->status, 'local_jobboard'),
        ['class' => 'badge badge-light']
    );
    echo html_writer::end_div();

    echo html_writer::start_div('card-body');

    // Dates.
    echo html_writer::start_div('row mb-3');
    echo html_writer::start_div('col-md-6');
    echo html_writer::tag('small', get_string('convocatoriastartdate', 'local_jobboard'), ['class' => 'd-block text-muted']);
    echo html_writer::tag('strong',
        '<i class="fa fa-calendar-alt text-success mr-2"></i>' .
        userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig'))
    );
    echo html_writer::end_div();
    echo html_writer::start_div('col-md-6');
    echo html_writer::tag('small', get_string('convocatoriaenddate', 'local_jobboard'), ['class' => 'd-block text-muted']);
    echo html_writer::tag('strong',
        '<i class="fa fa-calendar-times text-danger mr-2"></i>' .
        userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig'))
    );
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Action buttons.
    echo html_writer::start_div('d-flex flex-wrap');

    // Add vacancy button (for draft/open convocatorias).
    if (in_array($convocatoria->status, ['draft', 'open'])) {
        $addVacancyUrl = new moodle_url('/local/jobboard/edit.php', ['convocatoriaid' => $convocatoria->id]);
        echo html_writer::link($addVacancyUrl,
            '<i class="fa fa-plus mr-2"></i>' . get_string('addvacancy', 'local_jobboard'),
            ['class' => 'btn btn-success mr-2 mb-2']
        );
    }

    // View vacancies button.
    if ($vacancyCount > 0) {
        $vacanciesUrl = new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $convocatoria->id]);
        echo html_writer::link($vacanciesUrl,
            '<i class="fa fa-list mr-2"></i>' . get_string('viewvacancies', 'local_jobboard') . ' (' . $vacancyCount . ')',
            ['class' => 'btn btn-info mr-2 mb-2']
        );
    }

    // View applications button.
    if ($applicationCount > 0) {
        $appsUrl = new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $convocatoria->id]);
        echo html_writer::link($appsUrl,
            '<i class="fa fa-file-alt mr-2"></i>' . get_string('applications', 'local_jobboard') . ' (' . $applicationCount . ')',
            ['class' => 'btn btn-outline-info mr-2 mb-2']
        );
    }

    echo html_writer::end_div();

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card

    // ============================================================================
    // VACANCIES LIST (if any)
    // ============================================================================
    if ($vacancyCount > 0) {
        $vacancies = $DB->get_records_sql(
            "SELECT v.*,
                    (SELECT COUNT(*) FROM {local_jobboard_application} a WHERE a.vacancyid = v.id) as app_count
               FROM {local_jobboard_vacancy} v
              WHERE v.convocatoriaid = :convid
              ORDER BY v.code",
            ['convid' => $convocatoria->id],
            0, 5
        );

        echo html_writer::start_div('card shadow-sm mb-4');
        echo html_writer::tag('div',
            '<i class="fa fa-briefcase mr-2"></i>' . get_string('vacancies', 'local_jobboard') .
            html_writer::tag('span', $vacancyCount, ['class' => 'badge badge-primary ml-2']),
            ['class' => 'card-header bg-white font-weight-bold']
        );
        echo html_writer::start_div('card-body p-0');
        echo html_writer::start_div('list-group list-group-flush');

        foreach ($vacancies as $v) {
            $statusClass = 'badge-secondary';
            if ($v->status === 'published') {
                $statusClass = 'badge-success';
            } else if ($v->status === 'closed') {
                $statusClass = 'badge-warning';
            }

            echo html_writer::start_div('list-group-item d-flex justify-content-between align-items-center');
            echo html_writer::start_div();
            echo html_writer::tag('span', $v->code, ['class' => 'badge badge-secondary mr-2']);
            echo html_writer::tag('strong', format_string($v->title));
            echo html_writer::tag('span',
                get_string('status:' . $v->status, 'local_jobboard'),
                ['class' => 'badge ' . $statusClass . ' ml-2']
            );
            echo html_writer::end_div();
            echo html_writer::start_div();
            echo html_writer::tag('span',
                '<i class="fa fa-file-alt mr-1"></i>' . $v->app_count,
                ['class' => 'badge badge-info mr-2', 'title' => get_string('applications', 'local_jobboard')]
            );
            $editUrl = new moodle_url('/local/jobboard/edit.php', ['id' => $v->id]);
            echo html_writer::link($editUrl,
                '<i class="fa fa-edit"></i>',
                ['class' => 'btn btn-sm btn-outline-primary']
            );
            echo html_writer::end_div();
            echo html_writer::end_div();
        }

        if ($vacancyCount > 5) {
            echo html_writer::start_div('list-group-item text-center');
            $allUrl = new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $convocatoria->id]);
            echo html_writer::link($allUrl,
                get_string('viewall', 'local_jobboard') . ' (' . $vacancyCount . ')',
                ['class' => 'btn btn-sm btn-outline-secondary']
            );
            echo html_writer::end_div();
        }

        echo html_writer::end_div(); // list-group
        echo html_writer::end_div(); // card-body
        echo html_writer::end_div(); // card
    }
}

// ============================================================================
// FORM CARD
// ============================================================================
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::tag('div',
    '<i class="fa fa-edit mr-2"></i>' .
    ($convocatoria ? get_string('editconvocatoria', 'local_jobboard') : get_string('addconvocatoria', 'local_jobboard')),
    ['class' => 'card-header bg-primary text-white font-weight-bold']
);
echo html_writer::start_div('card-body');

// Display the form.
$form->display();

echo html_writer::end_div(); // card-body
echo html_writer::end_div(); // card

echo html_writer::end_div(); // local-jobboard-convocatoria

// Additional styles.
echo html_writer::tag('style', '
.local-jobboard-convocatoria .mform {
    max-width: 100%;
}
.local-jobboard-convocatoria .fitem {
    margin-bottom: 1rem;
}
.local-jobboard-convocatoria .list-group-item:hover {
    background-color: #f8f9fa;
}
');

echo $OUTPUT->footer();
