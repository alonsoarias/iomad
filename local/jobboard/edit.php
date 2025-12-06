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
$PAGE->requires->css('/local/jobboard/styles.css');

// Build navigation.
$PAGE->navbar->add(get_string('jobboard', 'local_jobboard'), new moodle_url('/local/jobboard/index.php'));
$PAGE->navbar->add(get_string('manageconvocatorias', 'local_jobboard'),
    new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']));

// Get convocatoria info.
$convocatoriarecord = null;
if ($convocatoriaid) {
    $convocatoriarecord = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
    if ($convocatoriarecord) {
        $PAGE->navbar->add(s($convocatoriarecord->name),
            new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $convocatoriaid]));
    }
}
$PAGE->navbar->add($pagetitle);

// If creating new vacancy and no convocatoria selected, show selection page.
if (!$id && !$convocatoriaid) {
    echo $OUTPUT->header();
    echo ui_helper::get_inline_styles();

    // Breadcrumbs.
    $breadcrumbs = [
        get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
        get_string('manageconvocatorias', 'local_jobboard') => new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
        get_string('newvacancy', 'local_jobboard') => null,
    ];

    echo ui_helper::page_header(
        get_string('newvacancy', 'local_jobboard'),
        $breadcrumbs
    );

    // Info message about requirement.
    echo html_writer::start_div('alert alert-info border-left-info d-flex align-items-center mb-4');
    echo html_writer::tag('i', '', ['class' => 'fa fa-info-circle fa-2x mr-3 text-info']);
    echo html_writer::start_div();
    echo html_writer::tag('h5', get_string('selectconvocatoriafirst', 'local_jobboard'), ['class' => 'mb-1']);
    echo html_writer::tag('p', get_string('createvacancyinconvocatoriadesc', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::end_div();

    // Get available convocatorias.
    $convocatorias = local_jobboard_get_convocatorias(0, '', true); // Only draft and open convocatorias.

    if (empty($convocatorias)) {
        // No convocatorias available.
        echo ui_helper::empty_state(
            get_string('noconvocatoriasavailable', 'local_jobboard'),
            'calendar-alt',
            [
                'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'action' => 'add']),
                'label' => get_string('gotocreateconvocatoria', 'local_jobboard'),
                'class' => 'btn btn-primary',
            ]
        );
    } else {
        // Show convocatoria cards.
        echo html_writer::tag('h4', get_string('selectconvocatoria', 'local_jobboard'), ['class' => 'mb-3']);
        echo html_writer::start_div('row');

        foreach ($convocatorias as $cid => $cname) {
            $conv = $DB->get_record('local_jobboard_convocatoria', ['id' => $cid]);
            if (!$conv) {
                continue;
            }

            echo html_writer::start_div('col-md-6 col-lg-4 mb-4');
            echo html_writer::start_div('card h-100 shadow-sm hover-shadow');
            echo html_writer::start_div('card-body');

            // Status badge.
            $statusColors = ['draft' => 'secondary', 'open' => 'success', 'closed' => 'warning'];
            $statusColor = $statusColors[$conv->status] ?? 'secondary';
            echo html_writer::div(
                html_writer::tag('span',
                    get_string('convocatoria_status_' . $conv->status, 'local_jobboard'),
                    ['class' => 'badge badge-' . $statusColor]
                ),
                'mb-2'
            );

            // Name.
            echo html_writer::tag('h5', s($conv->name), ['class' => 'card-title']);

            // Code.
            echo html_writer::tag('p',
                html_writer::tag('code', s($conv->code)),
                ['class' => 'small text-muted mb-2']
            );

            // Dates.
            echo html_writer::div(
                '<i class="fa fa-calendar-alt text-muted mr-1"></i>' .
                userdate($conv->startdate, get_string('strftimedate', 'langconfig')) . ' - ' .
                userdate($conv->enddate, get_string('strftimedate', 'langconfig')),
                'small mb-3'
            );

            // Vacancy count.
            $vacancyCount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $cid]);
            echo html_writer::div(
                '<i class="fa fa-briefcase text-muted mr-1"></i>' .
                get_string('vacancies', 'local_jobboard') . ': ' .
                html_writer::tag('strong', $vacancyCount),
                'small mb-3'
            );

            echo html_writer::end_div(); // card-body

            echo html_writer::start_div('card-footer bg-white');
            echo html_writer::link(
                new moodle_url('/local/jobboard/edit.php', ['convocatoriaid' => $cid]),
                '<i class="fa fa-plus mr-1"></i>' . get_string('addvacancy', 'local_jobboard'),
                ['class' => 'btn btn-primary btn-block']
            );
            echo html_writer::end_div(); // card-footer

            echo html_writer::end_div(); // card
            echo html_writer::end_div(); // col
        }

        echo html_writer::end_div(); // row

        // Option to create new convocatoria.
        echo html_writer::div('', 'border-top my-4');
        echo html_writer::start_div('text-center');
        echo html_writer::tag('p', get_string('or', 'moodle'), ['class' => 'text-muted']);
        echo html_writer::link(
            new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'action' => 'add']),
            '<i class="fa fa-calendar-plus mr-1"></i>' . get_string('addconvocatoria', 'local_jobboard'),
            ['class' => 'btn btn-outline-primary']
        );
        echo html_writer::end_div();
    }

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
echo ui_helper::get_inline_styles();

// Breadcrumbs and header.
$breadcrumbs = [
    get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
    get_string('manageconvocatorias', 'local_jobboard') => new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
];
if ($convocatoriarecord) {
    $breadcrumbs[s($convocatoriarecord->name)] = new moodle_url('/local/jobboard/index.php',
        ['view' => 'manage', 'convocatoriaid' => $convocatoriaid]);
}
$breadcrumbs[$pagetitle] = null;

echo ui_helper::page_header($pagetitle, $breadcrumbs);

// Convocatoria info banner.
if ($convocatoriarecord) {
    $statusColors = ['draft' => 'secondary', 'open' => 'success', 'closed' => 'warning', 'archived' => 'dark'];
    $convColor = $statusColors[$convocatoriarecord->status] ?? 'secondary';

    echo html_writer::start_div('alert alert-info border-left-info d-flex justify-content-between align-items-center mb-4');
    echo html_writer::start_div('d-flex align-items-center');
    echo html_writer::tag('i', '', ['class' => 'fa fa-calendar-alt mr-3 fa-lg']);
    echo html_writer::start_div();
    echo html_writer::tag('strong',
        get_string('convocatoria', 'local_jobboard') . ': ' . s($convocatoriarecord->name),
        ['class' => 'd-block']
    );
    echo html_writer::tag('small',
        userdate($convocatoriarecord->startdate, get_string('strftimedate', 'langconfig')) . ' - ' .
        userdate($convocatoriarecord->enddate, get_string('strftimedate', 'langconfig')),
        ['class' => 'text-muted']
    );
    echo html_writer::end_div();
    echo html_writer::end_div();
    echo html_writer::tag('span',
        get_string('convocatoria_status_' . $convocatoriarecord->status, 'local_jobboard'),
        ['class' => 'badge badge-' . $convColor]
    );
    echo html_writer::end_div();
}

// Display form in a card.
echo html_writer::start_div('card shadow-sm');
echo html_writer::start_div('card-body');
$form->display();
echo html_writer::end_div();
echo html_writer::end_div();

echo $OUTPUT->footer();
