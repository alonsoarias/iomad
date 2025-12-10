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
 * ISER Exemption management page.
 *
 * Modern redesign with consistent UX pattern using ui_helper.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\exemption;
use local_jobboard\output\ui_helper;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageexemptions', $context);

// Parameters.
$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);
$userid = optional_param('userid', 0, PARAM_INT);
$confirm = optional_param('confirm', 0, PARAM_BOOL);
$search = optional_param('search', '', PARAM_TEXT);
$type = optional_param('type', '', PARAM_ALPHA);
$status = optional_param('status', '', PARAM_ALPHA);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/manage_exemptions.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('manageexemptions', 'local_jobboard'));
$PAGE->set_heading(get_string('manageexemptions', 'local_jobboard'));
$PAGE->set_pagelayout('admin');
$PAGE->requires->css('/local/jobboard/styles.css');

// Handle actions.
if ($action === 'add' || ($action === 'edit' && $id)) {
    // Add/Edit exemption form.
    require_once($CFG->libdir . '/formslib.php');

    $exemption = null;
    if ($id) {
        $exemption = $DB->get_record('local_jobboard_exemption', ['id' => $id], '*', MUST_EXIST);
    }

    $mform = new \local_jobboard\forms\exemption_form(null, ['exemption' => $exemption]);

    if ($mform->is_cancelled()) {
        redirect(new moodle_url('/local/jobboard/manage_exemptions.php'));
    } else if ($data = $mform->get_data()) {
        if ($id) {
            // Update existing.
            $record = new stdClass();
            $record->id = $id;
            $record->userid = $data->userid;
            $record->exemptiontype = $data->exemptiontype;
            $record->documentref = $data->documentref ?? '';
            $record->exempteddoctypes = is_array($data->exempteddoctypes) ?
                implode(',', $data->exempteddoctypes) : $data->exempteddoctypes;
            $record->validfrom = $data->validfrom;
            $record->validuntil = $data->validuntil ?? null;
            $record->notes = $data->notes ?? '';
            $record->timemodified = time();
            $record->modifiedby = $USER->id;

            $DB->update_record('local_jobboard_exemption', $record);

            // Log event.
            \local_jobboard\event\exemption_updated::create([
                'context' => $context,
                'objectid' => $id,
                'relateduserid' => $data->userid,
            ])->trigger();

            redirect(new moodle_url('/local/jobboard/manage_exemptions.php'),
                get_string('exemptionupdated', 'local_jobboard'), null,
                \core\output\notification::NOTIFY_SUCCESS);
        } else {
            // Create new.
            $result = exemption::create(
                $data->userid,
                $data->exemptiontype,
                is_array($data->exempteddoctypes) ?
                    $data->exempteddoctypes : explode(',', $data->exempteddoctypes),
                $data->validfrom,
                $data->validuntil ?? null,
                $data->documentref ?? '',
                $data->notes ?? ''
            );

            if ($result) {
                redirect(new moodle_url('/local/jobboard/manage_exemptions.php'),
                    get_string('exemptioncreated', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_SUCCESS);
            } else {
                redirect(new moodle_url('/local/jobboard/manage_exemptions.php'),
                    get_string('exemptionerror', 'local_jobboard'), null,
                    \core\output\notification::NOTIFY_ERROR);
            }
        }
    }

    echo $OUTPUT->header();

    echo html_writer::start_div('local-jobboard-exemptions');

    echo ui_helper::page_header(
        $id ? get_string('editexemption', 'local_jobboard') : get_string('addexemption', 'local_jobboard'),
        [],
        [
            [
                'url' => new moodle_url('/local/jobboard/manage_exemptions.php'),
                'label' => get_string('back'),
                'icon' => 'arrow-left',
                'class' => 'btn btn-outline-secondary',
            ],
        ]
    );

    echo html_writer::start_div('card shadow-sm');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5', '<i class="fa fa-' . ($id ? 'edit' : 'plus') . ' text-primary mr-2"></i>' .
        ($id ? get_string('editexemption', 'local_jobboard') : get_string('addexemption', 'local_jobboard')),
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

if ($action === 'revoke' && $id) {
    $exemption = $DB->get_record('local_jobboard_exemption', ['id' => $id], '*', MUST_EXIST);

    if ($confirm) {
        require_sesskey();

        $reason = required_param('reason', PARAM_TEXT);

        $result = exemption::revoke($id, $reason);

        if ($result) {
            redirect(new moodle_url('/local/jobboard/manage_exemptions.php'),
                get_string('exemptionrevoked', 'local_jobboard'), null,
                \core\output\notification::NOTIFY_SUCCESS);
        } else {
            redirect(new moodle_url('/local/jobboard/manage_exemptions.php'),
                get_string('exemptionrevokeerror', 'local_jobboard'), null,
                \core\output\notification::NOTIFY_ERROR);
        }
    }

    // Show confirmation form.
    echo $OUTPUT->header();

    echo html_writer::start_div('local-jobboard-exemptions');

    $user = $DB->get_record('user', ['id' => $exemption->userid]);

    echo html_writer::start_div('card shadow-sm border-danger');
    echo html_writer::start_div('card-header bg-danger text-white');
    echo html_writer::tag('h5', '<i class="fa fa-ban mr-2"></i>' .
        get_string('revokeexemption', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    echo html_writer::div(
        '<i class="fa fa-exclamation-triangle mr-2"></i>' .
        get_string('confirmrevokeexemption', 'local_jobboard', fullname($user)),
        'alert alert-warning'
    );

    echo html_writer::start_tag('form', ['method' => 'post', 'action' => '']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'revoke']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $id]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'confirm', 'value' => '1']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

    echo html_writer::start_div('form-group');
    echo html_writer::tag('label', get_string('revokereason', 'local_jobboard'), ['for' => 'reason']);
    echo html_writer::tag('textarea', '', [
        'name' => 'reason',
        'id' => 'reason',
        'class' => 'form-control',
        'rows' => '3',
        'required' => 'required',
    ]);
    echo html_writer::end_div();

    echo html_writer::start_div('mt-4');
    echo html_writer::tag('button',
        '<i class="fa fa-ban mr-2"></i>' . get_string('confirm', 'local_jobboard'),
        ['type' => 'submit', 'class' => 'btn btn-danger mr-2']
    );
    echo html_writer::link(
        new moodle_url('/local/jobboard/manage_exemptions.php'),
        '<i class="fa fa-times mr-2"></i>' . get_string('cancel', 'local_jobboard'),
        ['class' => 'btn btn-secondary']
    );
    echo html_writer::end_div();

    echo html_writer::end_tag('form');

    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();

    echo $OUTPUT->footer();
    exit;
}

if ($action === 'view' && $id) {
    // View exemption details.
    $exemption = $DB->get_record('local_jobboard_exemption', ['id' => $id], '*', MUST_EXIST);
    $user = $DB->get_record('user', ['id' => $exemption->userid]);
    $createdby = $DB->get_record('user', ['id' => $exemption->createdby]);

    echo $OUTPUT->header();

    echo html_writer::start_div('local-jobboard-exemptions');

    echo ui_helper::page_header(
        get_string('exemptiondetails', 'local_jobboard'),
        [],
        [
            [
                'url' => new moodle_url('/local/jobboard/manage_exemptions.php'),
                'label' => get_string('back'),
                'icon' => 'arrow-left',
                'class' => 'btn btn-outline-secondary',
            ],
        ]
    );

    $isvalid = exemption::is_valid($exemption);

    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5', '<i class="fa fa-user text-primary mr-2"></i>' . fullname($user), ['class' => 'mb-0']);

    // Status badge.
    if ($exemption->timerevoked) {
        echo html_writer::tag('span', get_string('revoked', 'local_jobboard'), ['class' => 'badge badge-danger']);
    } else if ($isvalid) {
        echo html_writer::tag('span', get_string('active', 'local_jobboard'), ['class' => 'badge badge-success']);
    } else {
        echo html_writer::tag('span', get_string('expired', 'local_jobboard'), ['class' => 'badge badge-warning']);
    }

    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    echo html_writer::start_tag('dl', ['class' => 'row']);

    echo html_writer::tag('dt', get_string('user'), ['class' => 'col-sm-3']);
    echo html_writer::tag('dd', fullname($user) . ' (' . $user->email . ')', ['class' => 'col-sm-9']);

    echo html_writer::tag('dt', get_string('exemptiontype', 'local_jobboard'), ['class' => 'col-sm-3']);
    echo html_writer::tag('dd', get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard'), ['class' => 'col-sm-9']);

    echo html_writer::tag('dt', get_string('documentref', 'local_jobboard'), ['class' => 'col-sm-3']);
    echo html_writer::tag('dd', ($exemption->documentref ?: '-'), ['class' => 'col-sm-9']);

    echo html_writer::tag('dt', get_string('exempteddocs', 'local_jobboard'), ['class' => 'col-sm-3']);
    $doctypesbadges = '';
    $doctypes = explode(',', $exemption->exempteddoctypes);
    foreach ($doctypes as $dt) {
        $dt = trim($dt);
        if ($dt) {
            $doctypesbadges .= html_writer::tag('span',
                get_string('doctype_' . $dt, 'local_jobboard'),
                ['class' => 'badge badge-info mr-1 mb-1']
            );
        }
    }
    echo html_writer::tag('dd', $doctypesbadges, ['class' => 'col-sm-9']);

    echo html_writer::tag('dt', get_string('validfrom', 'local_jobboard'), ['class' => 'col-sm-3']);
    echo html_writer::tag('dd', userdate($exemption->validfrom, '%Y-%m-%d'), ['class' => 'col-sm-9']);

    echo html_writer::tag('dt', get_string('validuntil', 'local_jobboard'), ['class' => 'col-sm-3']);
    echo html_writer::tag('dd',
        ($exemption->validuntil ? userdate($exemption->validuntil, '%Y-%m-%d') : get_string('noexpiry', 'local_jobboard')),
        ['class' => 'col-sm-9']
    );

    echo html_writer::tag('dt', get_string('notes', 'local_jobboard'), ['class' => 'col-sm-3']);
    echo html_writer::tag('dd', ($exemption->notes ?: '-'), ['class' => 'col-sm-9']);

    echo html_writer::tag('dt', get_string('createdby', 'local_jobboard'), ['class' => 'col-sm-3']);
    echo html_writer::tag('dd',
        fullname($createdby) . ' - ' . userdate($exemption->timecreated, '%Y-%m-%d %H:%M'),
        ['class' => 'col-sm-9']
    );

    if ($exemption->timerevoked) {
        $revokedby = $DB->get_record('user', ['id' => $exemption->revokedby]);
        echo html_writer::tag('dt', get_string('revokedby', 'local_jobboard'), ['class' => 'col-sm-3']);
        echo html_writer::tag('dd',
            fullname($revokedby) . ' - ' . userdate($exemption->timerevoked, '%Y-%m-%d %H:%M'),
            ['class' => 'col-sm-9']
        );

        echo html_writer::tag('dt', get_string('revokereason', 'local_jobboard'), ['class' => 'col-sm-3']);
        echo html_writer::tag('dd', format_text($exemption->revokereason), ['class' => 'col-sm-9']);
    }

    echo html_writer::end_tag('dl');

    echo html_writer::end_div();

    // Actions footer.
    if (!$exemption->timerevoked && $isvalid) {
        echo html_writer::start_div('card-footer bg-light');
        echo html_writer::link(
            new moodle_url('/local/jobboard/manage_exemptions.php', ['action' => 'edit', 'id' => $id]),
            '<i class="fa fa-edit mr-2"></i>' . get_string('edit', 'local_jobboard'),
            ['class' => 'btn btn-primary mr-2']
        );
        echo html_writer::link(
            new moodle_url('/local/jobboard/manage_exemptions.php', ['action' => 'revoke', 'id' => $id]),
            '<i class="fa fa-ban mr-2"></i>' . get_string('revoke', 'local_jobboard'),
            ['class' => 'btn btn-danger']
        );
        echo html_writer::end_div();
    }

    echo html_writer::end_div();

    // Show exemption usage history.
    echo html_writer::start_div('card shadow-sm');
    echo html_writer::start_div('card-header bg-white');
    echo html_writer::tag('h5', '<i class="fa fa-history text-info mr-2"></i>' .
        get_string('exemptionusagehistory', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    $usages = $DB->get_records_sql("
        SELECT a.id, a.timecreated, v.code, v.title
          FROM {local_jobboard_application} a
          JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
         WHERE a.userid = :userid
           AND a.timecreated >= :validfrom
           AND (a.timecreated <= :validuntil OR :hasexpiry = 0)
         ORDER BY a.timecreated DESC
    ", [
        'userid' => $exemption->userid,
        'validfrom' => $exemption->validfrom,
        'validuntil' => $exemption->validuntil ?: time(),
        'hasexpiry' => $exemption->validuntil ? 1 : 0,
    ]);

    if ($usages) {
        $headers = [get_string('vacancy', 'local_jobboard'), get_string('date')];
        $rows = [];
        foreach ($usages as $usage) {
            $rows[] = [
                format_string($usage->code . ' - ' . $usage->title),
                userdate($usage->timecreated, '%Y-%m-%d %H:%M'),
            ];
        }
        echo ui_helper::data_table($headers, $rows);
    } else {
        echo ui_helper::empty_state(get_string('noexemptionusage', 'local_jobboard'), 'history');
    }

    echo html_writer::end_div();
    echo html_writer::end_div();

    echo html_writer::end_div();

    echo $OUTPUT->footer();
    exit;
}

// Main list view.
echo $OUTPUT->header();

echo html_writer::start_div('local-jobboard-exemptions');

// ============================================================================
// PAGE HEADER WITH ACTIONS
// ============================================================================
echo ui_helper::page_header(
    get_string('manageexemptions', 'local_jobboard'),
    [],
    [
        [
            'url' => new moodle_url('/local/jobboard/manage_exemptions.php', ['action' => 'add']),
            'label' => get_string('addexemption', 'local_jobboard'),
            'icon' => 'plus',
            'class' => 'btn btn-primary',
        ],
        [
            'url' => new moodle_url('/local/jobboard/import_exemptions.php'),
            'label' => get_string('importexemptions', 'local_jobboard'),
            'icon' => 'upload',
            'class' => 'btn btn-outline-secondary',
        ],
        [
            'url' => new moodle_url('/local/jobboard/index.php'),
            'label' => get_string('dashboard', 'local_jobboard'),
            'icon' => 'tachometer-alt',
            'class' => 'btn btn-outline-secondary',
        ],
    ]
);

// Statistics.
$now = time();
$activecnt = $DB->count_records_sql("
    SELECT COUNT(*) FROM {local_jobboard_exemption}
     WHERE timerevoked IS NULL
       AND validfrom <= :now1
       AND (validuntil IS NULL OR validuntil > :now2)
", ['now1' => $now, 'now2' => $now]);

$expiredcnt = $DB->count_records_sql("
    SELECT COUNT(*) FROM {local_jobboard_exemption}
     WHERE timerevoked IS NULL
       AND validuntil IS NOT NULL AND validuntil < :now
", ['now' => $now]);

$revokedcnt = $DB->count_records_select('local_jobboard_exemption', 'timerevoked IS NOT NULL');

$totalcnt = $activecnt + $expiredcnt + $revokedcnt;

// ============================================================================
// STATS CARDS
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card((string)$totalcnt, get_string('totalexemptions', 'local_jobboard'), 'primary', 'shield-alt');
echo ui_helper::stat_card((string)$activecnt, get_string('activeexemptions', 'local_jobboard'), 'success', 'check-circle');
echo ui_helper::stat_card((string)$expiredcnt, get_string('expiredexemptions', 'local_jobboard'), 'warning', 'clock');
echo ui_helper::stat_card((string)$revokedcnt, get_string('revokedexemptions', 'local_jobboard'), 'danger', 'ban');
echo html_writer::end_div();

// ============================================================================
// FILTERS
// ============================================================================
$typeOptions = ['' => get_string('all', 'local_jobboard')];
$types = ['historico_iser', 'documentos_recientes', 'traslado_interno', 'recontratacion'];
foreach ($types as $t) {
    $typeOptions[$t] = get_string('exemptiontype_' . $t, 'local_jobboard');
}

$statusOptions = [
    '' => get_string('all', 'local_jobboard'),
    'active' => get_string('active', 'local_jobboard'),
    'expired' => get_string('expired', 'local_jobboard'),
    'revoked' => get_string('revoked', 'local_jobboard'),
];

$filterDefinitions = [
    [
        'type' => 'text',
        'name' => 'search',
        'label' => get_string('search'),
        'placeholder' => get_string('searchuser', 'local_jobboard'),
        'col' => 'col-md-4',
    ],
    [
        'type' => 'select',
        'name' => 'type',
        'label' => get_string('type', 'local_jobboard'),
        'options' => $typeOptions,
        'col' => 'col-md-3',
    ],
    [
        'type' => 'select',
        'name' => 'status',
        'label' => get_string('status', 'local_jobboard'),
        'options' => $statusOptions,
        'col' => 'col-md-3',
    ],
];

echo ui_helper::filter_form(
    (new moodle_url('/local/jobboard/manage_exemptions.php'))->out(false),
    $filterDefinitions,
    ['search' => $search, 'type' => $type, 'status' => $status],
    []
);

// Build query.
$params = [];
$whereclauses = ['1=1'];

if ($search) {
    $whereclauses[] = $DB->sql_like("CONCAT(u.firstname, ' ', u.lastname)", ':search', false);
    $params['search'] = '%' . $DB->sql_like_escape($search) . '%';
}

if ($type) {
    $whereclauses[] = 'e.exemptiontype = :type';
    $params['type'] = $type;
}

if ($status === 'active') {
    $whereclauses[] = 'e.timerevoked IS NULL';
    $whereclauses[] = '(e.validuntil IS NULL OR e.validuntil > :now1)';
    $whereclauses[] = 'e.validfrom <= :now2';
    $params['now1'] = time();
    $params['now2'] = time();
} else if ($status === 'expired') {
    $whereclauses[] = 'e.timerevoked IS NULL';
    $whereclauses[] = 'e.validuntil IS NOT NULL AND e.validuntil < :now';
    $params['now'] = time();
} else if ($status === 'revoked') {
    $whereclauses[] = 'e.timerevoked IS NOT NULL';
}

$whereclause = implode(' AND ', $whereclauses);

// Count total.
$countsql = "SELECT COUNT(*)
               FROM {local_jobboard_exemption} e
               JOIN {user} u ON u.id = e.userid
              WHERE $whereclause";
$totalcount = $DB->count_records_sql($countsql, $params);

// Get exemptions.
$sql = "SELECT e.*, u.firstname, u.lastname, u.email
          FROM {local_jobboard_exemption} e
          JOIN {user} u ON u.id = e.userid
         WHERE $whereclause
         ORDER BY e.timecreated DESC";

$exemptions = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

// ============================================================================
// EXEMPTIONS TABLE
// ============================================================================
echo html_writer::start_div('card shadow-sm mb-4');
echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
echo html_writer::tag('h5', '<i class="fa fa-list text-primary mr-2"></i>' .
    get_string('exemptionlist', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::tag('span', $totalcount . ' ' . get_string('items', 'local_jobboard'),
    ['class' => 'badge badge-secondary']);
echo html_writer::end_div();
echo html_writer::start_div('card-body p-0');

if (empty($exemptions)) {
    echo html_writer::start_div('p-4');
    echo ui_helper::empty_state(get_string('noexemptions', 'local_jobboard'), 'shield-alt');
    echo html_writer::end_div();
} else {
    $headers = [
        get_string('user'),
        get_string('exemptiontype', 'local_jobboard'),
        get_string('exempteddocs', 'local_jobboard'),
        get_string('validfrom', 'local_jobboard'),
        get_string('validuntil', 'local_jobboard'),
        get_string('status', 'local_jobboard'),
        get_string('actions'),
    ];

    $rows = [];
    foreach ($exemptions as $ex) {
        // Status badge.
        $isvalid = !$ex->timerevoked &&
            $ex->validfrom <= time() &&
            (!$ex->validuntil || $ex->validuntil > time());

        if ($ex->timerevoked) {
            $statusbadge = html_writer::tag('span', get_string('revoked', 'local_jobboard'),
                ['class' => 'badge badge-danger']);
        } else if ($isvalid) {
            $statusbadge = html_writer::tag('span', get_string('active', 'local_jobboard'),
                ['class' => 'badge badge-success']);
        } else {
            $statusbadge = html_writer::tag('span', get_string('expired', 'local_jobboard'),
                ['class' => 'badge badge-warning']);
        }

        // Exempted docs (abbreviated).
        $doctypes = explode(',', $ex->exempteddoctypes);
        $doctypeshtml = html_writer::tag('span', count($doctypes) . ' ' . get_string('doctypes', 'local_jobboard'),
            ['class' => 'badge badge-secondary']);

        // Actions.
        $actions = html_writer::start_div('btn-group btn-group-sm', ['role' => 'group']);
        $actions .= html_writer::link(
            new moodle_url('/local/jobboard/manage_exemptions.php', ['action' => 'view', 'id' => $ex->id]),
            '<i class="fa fa-eye"></i>',
            ['class' => 'btn btn-outline-primary', 'title' => get_string('view', 'local_jobboard')]
        );

        if ($isvalid) {
            $actions .= html_writer::link(
                new moodle_url('/local/jobboard/manage_exemptions.php', ['action' => 'edit', 'id' => $ex->id]),
                '<i class="fa fa-edit"></i>',
                ['class' => 'btn btn-outline-secondary', 'title' => get_string('edit', 'local_jobboard')]
            );
            $actions .= html_writer::link(
                new moodle_url('/local/jobboard/manage_exemptions.php', ['action' => 'revoke', 'id' => $ex->id]),
                '<i class="fa fa-ban"></i>',
                ['class' => 'btn btn-outline-danger', 'title' => get_string('revoke', 'local_jobboard')]
            );
        }
        $actions .= html_writer::end_div();

        $rows[] = [
            html_writer::tag('strong', format_string($ex->firstname . ' ' . $ex->lastname)) .
                html_writer::tag('br') .
                html_writer::tag('small', $ex->email, ['class' => 'text-muted']),
            get_string('exemptiontype_' . $ex->exemptiontype, 'local_jobboard'),
            $doctypeshtml,
            userdate($ex->validfrom, '%Y-%m-%d'),
            $ex->validuntil ? userdate($ex->validuntil, '%Y-%m-%d') :
                html_writer::tag('em', get_string('noexpiry', 'local_jobboard')),
            $statusbadge,
            $actions,
        ];
    }

    echo ui_helper::data_table($headers, $rows, ['class' => 'mb-0']);

    // Pagination.
    echo html_writer::start_div('card-footer bg-white');
    $baseurl = new moodle_url('/local/jobboard/manage_exemptions.php', [
        'search' => $search,
        'type' => $type,
        'status' => $status,
    ]);
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
    echo html_writer::end_div();
}

echo html_writer::end_div();
echo html_writer::end_div();

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
    '<i class="fa fa-folder mr-2"></i>' . get_string('managedoctypes', 'local_jobboard'),
    ['class' => 'btn btn-outline-primary m-1']
);

if (has_capability('local/jobboard:viewreports', $context)) {
    echo html_writer::link(
        new moodle_url('/local/jobboard/index.php', ['view' => 'reports']),
        '<i class="fa fa-chart-bar mr-2"></i>' . get_string('reports', 'local_jobboard'),
        ['class' => 'btn btn-outline-info m-1']
    );
}

echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div();

echo $OUTPUT->footer();
