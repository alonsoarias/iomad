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
 * Document types management page for local_jobboard.
 *
 * Modern redesign with consistent UX pattern using ui_helper.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');

use local_jobboard\forms\doctype_form;
use local_jobboard\output\ui_helper;

admin_externalpage_setup('local_jobboard_doctypes');

$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
$pageurl = new moodle_url('/local/jobboard/admin/doctypes.php');
$PAGE->requires->css('/local/jobboard/styles.css');

// Handle toggle action.
if ($action === 'toggle' && $id && confirm_sesskey()) {
    $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $id], '*', MUST_EXIST);
    $oldstatus = $doctype->enabled;
    $doctype->enabled = $doctype->enabled ? 0 : 1;
    $doctype->timemodified = time();
    $DB->update_record('local_jobboard_doctype', $doctype);

    // Audit log.
    \local_jobboard\audit::log_transition(
        \local_jobboard\audit::ENTITY_CONFIG,
        $id,
        'enabled',
        $oldstatus ? 'enabled' : 'disabled',
        $doctype->enabled ? 'enabled' : 'disabled',
        ['code' => $doctype->code, 'name' => $doctype->name, 'entity' => 'doctype']
    );

    redirect($pageurl, get_string('changessaved'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle delete action.
if ($action === 'delete' && $id && confirm_sesskey()) {
    $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $id], '*', MUST_EXIST);

    // Check if doctype is in use.
    $inuse = $DB->count_records('local_jobboard_document', ['documenttype' => $doctype->code]);
    if ($inuse > 0) {
        redirect($pageurl, get_string('error:doctypeinuse', 'local_jobboard', $inuse),
            null, \core\output\notification::NOTIFY_ERROR);
    }

    $DB->delete_records('local_jobboard_doctype', ['id' => $id]);

    // Audit log.
    \local_jobboard\audit::log(
        \local_jobboard\audit::ACTION_DELETE,
        \local_jobboard\audit::ENTITY_CONFIG,
        $id,
        ['code' => $doctype->code, 'name' => $doctype->name, 'entity' => 'doctype']
    );

    redirect($pageurl, get_string('doctypedeleted', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle move up/down actions.
if (($action === 'moveup' || $action === 'movedown') && $id && confirm_sesskey()) {
    $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $id], '*', MUST_EXIST);
    $currentorder = (int) $doctype->sortorder;

    if ($action === 'moveup') {
        // Find the item above.
        $swap = $DB->get_record_sql(
            'SELECT * FROM {local_jobboard_doctype} WHERE sortorder < ? ORDER BY sortorder DESC LIMIT 1',
            [$currentorder]
        );
    } else {
        // Find the item below.
        $swap = $DB->get_record_sql(
            'SELECT * FROM {local_jobboard_doctype} WHERE sortorder > ? ORDER BY sortorder ASC LIMIT 1',
            [$currentorder]
        );
    }

    if ($swap) {
        // Swap the sort orders.
        $DB->set_field('local_jobboard_doctype', 'sortorder', $swap->sortorder, ['id' => $doctype->id]);
        $DB->set_field('local_jobboard_doctype', 'sortorder', $currentorder, ['id' => $swap->id]);
    }

    redirect($pageurl);
}

// Handle add/edit actions.
if ($action === 'add' || ($action === 'edit' && $id)) {
    $doctype = null;
    if ($action === 'edit') {
        $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $id], '*', MUST_EXIST);
    }

    $customdata = ['doctype' => $doctype];
    $mform = new doctype_form($pageurl, $customdata);

    if ($mform->is_cancelled()) {
        redirect($pageurl);
    }

    if ($data = $mform->get_data()) {
        $now = time();

        if ($doctype) {
            // Update existing.
            $record = new stdClass();
            $record->id = $doctype->id;
            $record->name = $data->name;
            $record->description = $data->description ?? '';
            $record->category = $data->category ?? '';
            $record->isrequired = $data->isrequired ?? 0;
            $record->externalurl = $data->externalurl ?? '';
            $record->requirements = $data->requirements ?? '';
            $record->defaultmaxagedays = $data->defaultmaxagedays ?? null;
            $record->iserexempted = $data->iserexempted ?? 0;
            $record->gender_condition = $data->gender_condition ?? null;
            $record->age_exemption_threshold = $data->age_exemption_threshold ?: null;
            $record->profession_exempt = $data->profession_exempt ?? null;
            $record->conditional_note = $data->conditional_note ?? '';
            $record->checklistitems = $data->checklistitems ?? '';
            $record->sortorder = $data->sortorder ?? 0;
            $record->enabled = $data->enabled ?? 1;
            $record->timemodified = $now;

            $DB->update_record('local_jobboard_doctype', $record);

            // Audit log.
            \local_jobboard\audit::log(
                \local_jobboard\audit::ACTION_UPDATE,
                \local_jobboard\audit::ENTITY_CONFIG,
                $doctype->id,
                ['code' => $doctype->code, 'name' => $data->name, 'entity' => 'doctype'],
                (array) $doctype,
                (array) $record
            );

            redirect($pageurl, get_string('doctypeupdated', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
        } else {
            // Create new.
            // Validate unique code.
            if ($DB->record_exists('local_jobboard_doctype', ['code' => $data->code])) {
                redirect($pageurl, get_string('error:codealreadyexists', 'local_jobboard'),
                    null, \core\output\notification::NOTIFY_ERROR);
            }

            $record = new stdClass();
            $record->code = $data->code;
            $record->name = $data->name;
            $record->description = $data->description ?? '';
            $record->category = $data->category ?? '';
            $record->isrequired = $data->isrequired ?? 0;
            $record->externalurl = $data->externalurl ?? '';
            $record->requirements = $data->requirements ?? '';
            $record->defaultmaxagedays = $data->defaultmaxagedays ?? null;
            $record->iserexempted = $data->iserexempted ?? 0;
            $record->gender_condition = $data->gender_condition ?? null;
            $record->age_exemption_threshold = $data->age_exemption_threshold ?: null;
            $record->profession_exempt = $data->profession_exempt ?? null;
            $record->conditional_note = $data->conditional_note ?? '';
            $record->checklistitems = $data->checklistitems ?? '';
            $record->sortorder = $data->sortorder ?? 0;
            $record->enabled = $data->enabled ?? 1;
            $record->timecreated = $now;
            $record->timemodified = $now;

            $newid = $DB->insert_record('local_jobboard_doctype', $record);

            // Audit log.
            \local_jobboard\audit::log(
                \local_jobboard\audit::ACTION_CREATE,
                \local_jobboard\audit::ENTITY_CONFIG,
                $newid,
                ['code' => $record->code, 'name' => $record->name, 'entity' => 'doctype']
            );

            redirect($pageurl, get_string('doctypecreated', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
        }
    }

    // Display the form.
    $title = $doctype ? get_string('editdoctype', 'local_jobboard') : get_string('adddoctype', 'local_jobboard');
    $PAGE->set_title($title);

    echo $OUTPUT->header();

    echo html_writer::start_div('local-jobboard-doctypes');

    // Back button and title.
    echo ui_helper::page_header($title, [], [
        [
            'url' => $pageurl,
            'label' => get_string('back'),
            'icon' => 'arrow-left',
            'class' => 'btn btn-outline-secondary',
        ],
    ]);

    // Form card.
    echo html_writer::start_div('card shadow-sm');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-' . ($doctype ? 'edit' : 'plus') . ' text-primary mr-2"></i>' . $title,
        ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');
    $mform->display();
    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();

    echo $OUTPUT->footer();
    exit;
}

// Handle confirm delete action.
if ($action === 'confirmdelete' && $id) {
    $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $id], '*', MUST_EXIST);

    echo $OUTPUT->header();

    echo html_writer::start_div('local-jobboard-doctypes');

    // Check usage.
    $inuse = $DB->count_records('local_jobboard_document', ['documenttype' => $doctype->code]);

    echo html_writer::start_div('card shadow-sm');
    echo html_writer::start_div('card-header bg-warning');
    echo html_writer::tag('h5',
        '<i class="fa fa-exclamation-triangle mr-2"></i>' . get_string('confirmdeletedoctype', 'local_jobboard'),
        ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if ($inuse > 0) {
        echo html_writer::div(
            '<i class="fa fa-times-circle mr-2"></i>' . get_string('error:doctypeinuse', 'local_jobboard', $inuse),
            'alert alert-danger'
        );
        echo html_writer::link($pageurl, '<i class="fa fa-arrow-left mr-2"></i>' . get_string('back'),
            ['class' => 'btn btn-secondary']);
    } else {
        $name = get_string_manager()->string_exists('doctype_' . $doctype->code, 'local_jobboard')
            ? get_string('doctype_' . $doctype->code, 'local_jobboard')
            : $doctype->name;

        echo html_writer::tag('p', get_string('confirmdeletedoctype_msg', 'local_jobboard', $name));
        echo html_writer::tag('p',
            '<strong>' . get_string('code', 'local_jobboard') . ':</strong> <code>' . s($doctype->code) . '</code>',
            ['class' => 'text-muted']);

        $deleteurl = new moodle_url($pageurl, ['action' => 'delete', 'id' => $id, 'sesskey' => sesskey()]);
        echo html_writer::start_div('mt-4');
        echo html_writer::link($deleteurl, '<i class="fa fa-trash mr-2"></i>' . get_string('delete'),
            ['class' => 'btn btn-danger mr-2']);
        echo html_writer::link($pageurl, '<i class="fa fa-times mr-2"></i>' . get_string('cancel'),
            ['class' => 'btn btn-secondary']);
        echo html_writer::end_div();
    }

    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();

    echo $OUTPUT->footer();
    exit;
}

// Default: List all document types.
$doctypes = $DB->get_records('local_jobboard_doctype', null, 'sortorder ASC, code ASC');

// Calculate stats.
$totalDoctypes = count($doctypes);
$enabledDoctypes = 0;
$requiredDoctypes = 0;
$conditionalDoctypes = 0;
foreach ($doctypes as $dt) {
    if ($dt->enabled) {
        $enabledDoctypes++;
    }
    if (!empty($dt->isrequired)) {
        $requiredDoctypes++;
    }
    if (!empty($dt->gender_condition) || !empty($dt->profession_exempt) || !empty($dt->age_exemption_threshold)) {
        $conditionalDoctypes++;
    }
}

echo $OUTPUT->header();

echo html_writer::start_div('local-jobboard-doctypes');

// ============================================================================
// PAGE HEADER WITH ACTIONS
// ============================================================================
$addurl = new moodle_url($pageurl, ['action' => 'add']);
echo ui_helper::page_header(
    get_string('managedoctypes', 'local_jobboard'),
    [],
    [
        [
            'url' => $addurl,
            'label' => get_string('adddoctype', 'local_jobboard'),
            'icon' => 'plus',
            'class' => 'btn btn-primary',
        ],
        [
            'url' => new moodle_url('/local/jobboard/index.php'),
            'label' => get_string('dashboard', 'local_jobboard'),
            'icon' => 'tachometer-alt',
            'class' => 'btn btn-outline-secondary',
        ],
    ]
);

// ============================================================================
// STATS CARDS
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card((string)$totalDoctypes, get_string('totaldoctypes', 'local_jobboard'), 'primary', 'folder');
echo ui_helper::stat_card((string)$enabledDoctypes, get_string('enableddoctypes', 'local_jobboard'), 'success', 'check-circle');
echo ui_helper::stat_card((string)$requiredDoctypes, get_string('requireddoctypes', 'local_jobboard'), 'info', 'star');
echo ui_helper::stat_card((string)$conditionalDoctypes, get_string('conditionaldoctypes', 'local_jobboard'), 'warning', 'filter');
echo html_writer::end_div();

// ============================================================================
// HELP INFO
// ============================================================================
echo ui_helper::info_card(
    get_string('aboutdoctypes', 'local_jobboard'),
    get_string('doctypeshelp', 'local_jobboard'),
    'info',
    'info-circle'
);

// ============================================================================
// DOCUMENT TYPES TABLE
// ============================================================================
if (empty($doctypes)) {
    echo ui_helper::empty_state(
        get_string('nodoctypes', 'local_jobboard'),
        'folder-open',
        [
            'url' => $addurl,
            'label' => get_string('adddoctype', 'local_jobboard'),
            'class' => 'btn btn-primary',
        ]
    );
} else {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5', '<i class="fa fa-list text-primary mr-2"></i>' .
        get_string('doctypelist', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::tag('span', $totalDoctypes . ' ' . get_string('items', 'local_jobboard'),
        ['class' => 'badge badge-secondary']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body p-0');

    $headers = [
        '<span class="text-muted">#</span>',
        get_string('code', 'local_jobboard'),
        get_string('name'),
        get_string('category', 'local_jobboard'),
        get_string('required', 'local_jobboard'),
        get_string('conditions', 'local_jobboard'),
        get_string('status'),
        get_string('actions'),
    ];

    $rows = [];
    $totalcount = count($doctypes);
    $index = 0;

    foreach ($doctypes as $dt) {
        $index++;

        // Status badge.
        $statusbadge = $dt->enabled
            ? '<span class="badge badge-success">' . get_string('enabled', 'local_jobboard') . '</span>'
            : '<span class="badge badge-secondary">' . get_string('disabled', 'local_jobboard') . '</span>';

        // Required badge.
        $isrequired = $dt->isrequired ?? 0;
        $requiredbadge = $isrequired
            ? '<span class="badge badge-primary">' . get_string('yes') . '</span>'
            : '<span class="badge badge-light">' . get_string('no') . '</span>';

        // Build conditions display.
        $conditions = [];

        // Gender condition.
        if (!empty($dt->gender_condition)) {
            if ($dt->gender_condition === 'M') {
                $conditions[] = '<span class="badge badge-info">' . get_string('doc_condition_men_only', 'local_jobboard') . '</span>';
            } else if ($dt->gender_condition === 'F') {
                $conditions[] = '<span class="badge badge-info">' . get_string('doc_condition_women_only', 'local_jobboard') . '</span>';
            }
        }

        // Profession exemption.
        if (!empty($dt->profession_exempt)) {
            $exemptlist = json_decode($dt->profession_exempt, true);
            if (is_array($exemptlist) && !empty($exemptlist)) {
                $exemptnames = [];
                foreach ($exemptlist as $edu) {
                    $stringkey = 'signup_edu_' . $edu;
                    if (get_string_manager()->string_exists($stringkey, 'local_jobboard')) {
                        $exemptnames[] = get_string($stringkey, 'local_jobboard');
                    } else {
                        $exemptnames[] = ucfirst($edu);
                    }
                }
                $conditions[] = '<span class="badge badge-warning">' .
                    get_string('doc_condition_profession_exempt', 'local_jobboard', implode(', ', $exemptnames)) . '</span>';
            }
        }

        // ISER exemption.
        if (!empty($dt->iserexempted)) {
            $conditions[] = '<span class="badge badge-secondary">' . get_string('doc_condition_iser_exempt', 'local_jobboard') . '</span>';
        }

        // Age exemption threshold.
        if (!empty($dt->age_exemption_threshold)) {
            $conditions[] = '<span class="badge badge-success">' .
                get_string('age_exempt_notice', 'local_jobboard', (int) $dt->age_exemption_threshold) . '</span>';
        }

        // Conditional note.
        if (!empty($dt->conditional_note)) {
            $conditions[] = '<span class="badge badge-light border" title="' . s($dt->conditional_note) . '">' .
                '<i class="fa fa-info-circle"></i> ' . get_string('hasnote', 'local_jobboard') . '</span>';
        }

        $conditionshtml = !empty($conditions) ? implode('<br>', $conditions) : '<span class="text-muted">-</span>';

        // Build actions.
        $actions = [];

        // Move up/down.
        if ($index > 1) {
            $moveupurl = new moodle_url($pageurl, ['action' => 'moveup', 'id' => $dt->id, 'sesskey' => sesskey()]);
            $actions[] = html_writer::link($moveupurl, '<i class="fa fa-arrow-up"></i>',
                ['class' => 'btn btn-sm btn-outline-secondary', 'title' => get_string('moveup')]);
        } else {
            $actions[] = '<span class="btn btn-sm btn-outline-secondary disabled"><i class="fa fa-arrow-up"></i></span>';
        }

        if ($index < $totalcount) {
            $movedownurl = new moodle_url($pageurl, ['action' => 'movedown', 'id' => $dt->id, 'sesskey' => sesskey()]);
            $actions[] = html_writer::link($movedownurl, '<i class="fa fa-arrow-down"></i>',
                ['class' => 'btn btn-sm btn-outline-secondary', 'title' => get_string('movedown')]);
        } else {
            $actions[] = '<span class="btn btn-sm btn-outline-secondary disabled"><i class="fa fa-arrow-down"></i></span>';
        }

        // Edit.
        $editurl = new moodle_url($pageurl, ['action' => 'edit', 'id' => $dt->id]);
        $actions[] = html_writer::link($editurl, '<i class="fa fa-edit"></i>',
            ['class' => 'btn btn-sm btn-outline-primary', 'title' => get_string('edit')]);

        // Toggle enable/disable.
        $toggleurl = new moodle_url($pageurl, ['action' => 'toggle', 'id' => $dt->id, 'sesskey' => sesskey()]);
        $toggleicon = $dt->enabled ? 'fa-eye-slash' : 'fa-eye';
        $toggletitle = $dt->enabled ? get_string('disable') : get_string('enable');
        $actions[] = html_writer::link($toggleurl, '<i class="fa ' . $toggleicon . '"></i>',
            ['class' => 'btn btn-sm btn-outline-secondary', 'title' => $toggletitle]);

        // Delete.
        $deleteurl = new moodle_url($pageurl, ['action' => 'confirmdelete', 'id' => $dt->id]);
        $actions[] = html_writer::link($deleteurl, '<i class="fa fa-trash"></i>',
            ['class' => 'btn btn-sm btn-outline-danger', 'title' => get_string('delete')]);

        $actionshtml = html_writer::start_div('btn-group btn-group-sm', ['role' => 'group']);
        $actionshtml .= implode('', $actions);
        $actionshtml .= html_writer::end_div();

        // Name with translation.
        $name = get_string_manager()->string_exists('doctype_' . $dt->code, 'local_jobboard')
            ? get_string('doctype_' . $dt->code, 'local_jobboard')
            : $dt->name;

        // Category with translation.
        $category = !empty($dt->category) && get_string_manager()->string_exists('doccategory_' . $dt->category, 'local_jobboard')
            ? get_string('doccategory_' . $dt->category, 'local_jobboard')
            : ($dt->category ?? '-');

        $rows[] = [
            html_writer::tag('span', $dt->sortorder, ['class' => 'text-muted font-weight-light']),
            html_writer::tag('code', format_string($dt->code)),
            html_writer::tag('strong', $name),
            $category,
            $requiredbadge,
            $conditionshtml,
            $statusbadge,
            $actionshtml,
        ];
    }

    echo ui_helper::data_table($headers, $rows, ['class' => 'mb-0']);

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card
}

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
    new moodle_url('/local/jobboard/admin/templates.php'),
    '<i class="fa fa-envelope mr-2"></i>' . get_string('emailtemplates', 'local_jobboard'),
    ['class' => 'btn btn-outline-primary m-1']
);

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-doctypes

echo $OUTPUT->footer();
