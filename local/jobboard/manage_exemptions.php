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
 * Migrated to renderer + template pattern in v3.1.23.
 * Note: Add/edit/view/revoke sub-views still use ui_helper for complex forms.
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

// Handle actions (add/edit/view/revoke use ui_helper for complex form interactions).
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

    echo html_writer::start_div('jb-exemptions');

    echo ui_helper::page_header(
        $id ? get_string('editexemption', 'local_jobboard') : get_string('addexemption', 'local_jobboard'),
        [],
        [
            [
                'url' => new moodle_url('/local/jobboard/manage_exemptions.php'),
                'label' => get_string('back'),
                'icon' => 'arrow-left',
                'class' => 'jb-btn jb-btn-outline-secondary',
            ],
        ]
    );

    echo html_writer::start_div('jb-card jb-card-shadow');
    echo html_writer::start_div('jb-card-header jb-bg-white');
    echo html_writer::tag('h5', '<i class="fa fa-' . ($id ? 'edit' : 'plus') . ' jb-text-primary jb-mr-2"></i>' .
        ($id ? get_string('editexemption', 'local_jobboard') : get_string('addexemption', 'local_jobboard')),
        ['class' => 'jb-mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('jb-card-body');
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

    echo html_writer::start_div('jb-exemptions');

    $user = $DB->get_record('user', ['id' => $exemption->userid]);

    echo html_writer::start_div('jb-card jb-card-shadow jb-border-danger');
    echo html_writer::start_div('jb-card-header jb-bg-danger jb-text-white');
    echo html_writer::tag('h5', '<i class="fa fa-ban jb-mr-2"></i>' .
        get_string('revokeexemption', 'local_jobboard'), ['class' => 'jb-mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('jb-card-body');

    echo html_writer::div(
        '<i class="fa fa-exclamation-triangle jb-mr-2"></i>' .
        get_string('confirmrevokeexemption', 'local_jobboard', fullname($user)),
        'jb-alert jb-alert-warning'
    );

    echo html_writer::start_tag('form', ['method' => 'post', 'action' => '']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'action', 'value' => 'revoke']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'id', 'value' => $id]);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'confirm', 'value' => '1']);
    echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);

    echo html_writer::start_div('jb-form-group');
    echo html_writer::tag('label', get_string('revokereason', 'local_jobboard'), ['for' => 'reason']);
    echo html_writer::tag('textarea', '', [
        'name' => 'reason',
        'id' => 'reason',
        'class' => 'jb-form-control',
        'rows' => '3',
        'required' => 'required',
    ]);
    echo html_writer::end_div();

    echo html_writer::start_div('jb-mt-4');
    echo html_writer::tag('button',
        '<i class="fa fa-ban jb-mr-2"></i>' . get_string('confirm', 'local_jobboard'),
        ['type' => 'submit', 'class' => 'jb-btn jb-btn-danger jb-mr-2']
    );
    echo html_writer::link(
        new moodle_url('/local/jobboard/manage_exemptions.php'),
        '<i class="fa fa-times jb-mr-2"></i>' . get_string('cancel', 'local_jobboard'),
        ['class' => 'jb-btn jb-btn-secondary']
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

    echo html_writer::start_div('jb-exemptions');

    echo ui_helper::page_header(
        get_string('exemptiondetails', 'local_jobboard'),
        [],
        [
            [
                'url' => new moodle_url('/local/jobboard/manage_exemptions.php'),
                'label' => get_string('back'),
                'icon' => 'arrow-left',
                'class' => 'jb-btn jb-btn-outline-secondary',
            ],
        ]
    );

    $isvalid = exemption::is_valid($exemption);

    echo html_writer::start_div('jb-card jb-card-shadow jb-mb-4');
    echo html_writer::start_div('jb-card-header jb-bg-white jb-d-flex jb-justify-content-between jb-align-items-center');
    echo html_writer::tag('h5', '<i class="fa fa-user jb-text-primary jb-mr-2"></i>' . fullname($user), ['class' => 'jb-mb-0']);

    // Status badge.
    if ($exemption->timerevoked) {
        echo html_writer::tag('span', get_string('revoked', 'local_jobboard'), ['class' => 'jb-badge jb-badge-danger']);
    } else if ($isvalid) {
        echo html_writer::tag('span', get_string('active', 'local_jobboard'), ['class' => 'jb-badge jb-badge-success']);
    } else {
        echo html_writer::tag('span', get_string('expired', 'local_jobboard'), ['class' => 'jb-badge jb-badge-warning']);
    }

    echo html_writer::end_div();
    echo html_writer::start_div('jb-card-body');

    echo html_writer::start_tag('dl', ['class' => 'jb-row']);

    echo html_writer::tag('dt', get_string('user'), ['class' => 'jb-col-sm-3']);
    echo html_writer::tag('dd', fullname($user) . ' (' . $user->email . ')', ['class' => 'jb-col-sm-9']);

    echo html_writer::tag('dt', get_string('exemptiontype', 'local_jobboard'), ['class' => 'jb-col-sm-3']);
    echo html_writer::tag('dd', get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard'), ['class' => 'jb-col-sm-9']);

    echo html_writer::tag('dt', get_string('documentref', 'local_jobboard'), ['class' => 'jb-col-sm-3']);
    echo html_writer::tag('dd', ($exemption->documentref ?: '-'), ['class' => 'jb-col-sm-9']);

    echo html_writer::tag('dt', get_string('exempteddocs', 'local_jobboard'), ['class' => 'jb-col-sm-3']);
    $doctypesbadges = '';
    $doctypes = explode(',', $exemption->exempteddoctypes);
    foreach ($doctypes as $dt) {
        $dt = trim($dt);
        if ($dt) {
            $doctypesbadges .= html_writer::tag('span',
                get_string('doctype_' . $dt, 'local_jobboard'),
                ['class' => 'jb-badge jb-badge-info jb-mr-1 jb-mb-1']
            );
        }
    }
    echo html_writer::tag('dd', $doctypesbadges, ['class' => 'jb-col-sm-9']);

    echo html_writer::tag('dt', get_string('validfrom', 'local_jobboard'), ['class' => 'jb-col-sm-3']);
    echo html_writer::tag('dd', userdate($exemption->validfrom, '%Y-%m-%d'), ['class' => 'jb-col-sm-9']);

    echo html_writer::tag('dt', get_string('validuntil', 'local_jobboard'), ['class' => 'jb-col-sm-3']);
    echo html_writer::tag('dd',
        ($exemption->validuntil ? userdate($exemption->validuntil, '%Y-%m-%d') : get_string('noexpiry', 'local_jobboard')),
        ['class' => 'jb-col-sm-9']
    );

    echo html_writer::tag('dt', get_string('notes', 'local_jobboard'), ['class' => 'jb-col-sm-3']);
    echo html_writer::tag('dd', ($exemption->notes ?: '-'), ['class' => 'jb-col-sm-9']);

    echo html_writer::tag('dt', get_string('createdby', 'local_jobboard'), ['class' => 'jb-col-sm-3']);
    echo html_writer::tag('dd',
        fullname($createdby) . ' - ' . userdate($exemption->timecreated, '%Y-%m-%d %H:%M'),
        ['class' => 'jb-col-sm-9']
    );

    if ($exemption->timerevoked) {
        $revokedby = $DB->get_record('user', ['id' => $exemption->revokedby]);
        echo html_writer::tag('dt', get_string('revokedby', 'local_jobboard'), ['class' => 'jb-col-sm-3']);
        echo html_writer::tag('dd',
            fullname($revokedby) . ' - ' . userdate($exemption->timerevoked, '%Y-%m-%d %H:%M'),
            ['class' => 'jb-col-sm-9']
        );

        echo html_writer::tag('dt', get_string('revokereason', 'local_jobboard'), ['class' => 'jb-col-sm-3']);
        echo html_writer::tag('dd', format_text($exemption->revokereason), ['class' => 'jb-col-sm-9']);
    }

    echo html_writer::end_tag('dl');

    echo html_writer::end_div();

    // Actions footer.
    if (!$exemption->timerevoked && $isvalid) {
        echo html_writer::start_div('jb-card-footer jb-bg-light');
        echo html_writer::link(
            new moodle_url('/local/jobboard/manage_exemptions.php', ['action' => 'edit', 'id' => $id]),
            '<i class="fa fa-edit jb-mr-2"></i>' . get_string('edit', 'local_jobboard'),
            ['class' => 'jb-btn jb-btn-primary jb-mr-2']
        );
        echo html_writer::link(
            new moodle_url('/local/jobboard/manage_exemptions.php', ['action' => 'revoke', 'id' => $id]),
            '<i class="fa fa-ban jb-mr-2"></i>' . get_string('revoke', 'local_jobboard'),
            ['class' => 'jb-btn jb-btn-danger']
        );
        echo html_writer::end_div();
    }

    echo html_writer::end_div();

    // Show exemption usage history.
    echo html_writer::start_div('jb-card jb-card-shadow');
    echo html_writer::start_div('jb-card-header jb-bg-white');
    echo html_writer::tag('h5', '<i class="fa fa-history jb-text-info jb-mr-2"></i>' .
        get_string('exemptionusagehistory', 'local_jobboard'), ['class' => 'jb-mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('jb-card-body');

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

// Main list view - use renderer + template pattern.
$renderer = $PAGE->get_renderer('local_jobboard');
$data = $renderer->prepare_manage_exemptions_page_data($search, $type, $status, $page, $perpage, $context);

echo $OUTPUT->header();
echo $renderer->render_manage_exemptions_page($data);
echo $OUTPUT->footer();
