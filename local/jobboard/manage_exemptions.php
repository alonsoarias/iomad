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
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\exemption;

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

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('manageexemptions', 'local_jobboard'));

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
    echo $OUTPUT->heading($id ? get_string('editexemption', 'local_jobboard') :
        get_string('addexemption', 'local_jobboard'));
    $mform->display();
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
    echo $OUTPUT->heading(get_string('revokeexemption', 'local_jobboard'));

    $user = $DB->get_record('user', ['id' => $exemption->userid]);

    echo '<div class="alert alert-warning">';
    echo '<p>' . get_string('confirmrevokeexemption', 'local_jobboard',
        fullname($user)) . '</p>';
    echo '</div>';

    echo '<form method="post" action="">';
    echo '<input type="hidden" name="action" value="revoke">';
    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '<input type="hidden" name="confirm" value="1">';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';

    echo '<div class="form-group">';
    echo '<label for="reason">' . get_string('revokereason', 'local_jobboard') . '</label>';
    echo '<textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>';
    echo '</div>';

    echo '<button type="submit" class="btn btn-danger">' . get_string('confirm', 'local_jobboard') . '</button>';
    echo ' <a href="' . new moodle_url('/local/jobboard/manage_exemptions.php') .
        '" class="btn btn-secondary">' . get_string('cancel', 'local_jobboard') . '</a>';
    echo '</form>';

    echo $OUTPUT->footer();
    exit;
}

if ($action === 'view' && $id) {
    // View exemption details.
    $exemption = $DB->get_record('local_jobboard_exemption', ['id' => $id], '*', MUST_EXIST);
    $user = $DB->get_record('user', ['id' => $exemption->userid]);
    $createdby = $DB->get_record('user', ['id' => $exemption->createdby]);

    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('exemptiondetails', 'local_jobboard'));

    echo '<div class="card mb-4">';
    echo '<div class="card-header">';
    echo '<h5>' . fullname($user) . '</h5>';
    echo '</div>';
    echo '<div class="card-body">';

    echo '<dl class="row">';

    echo '<dt class="col-sm-3">' . get_string('user') . '</dt>';
    echo '<dd class="col-sm-9">' . fullname($user) . ' (' . $user->email . ')</dd>';

    echo '<dt class="col-sm-3">' . get_string('exemptiontype', 'local_jobboard') . '</dt>';
    echo '<dd class="col-sm-9">' . get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard') . '</dd>';

    echo '<dt class="col-sm-3">' . get_string('documentref', 'local_jobboard') . '</dt>';
    echo '<dd class="col-sm-9">' . ($exemption->documentref ?: '-') . '</dd>';

    echo '<dt class="col-sm-3">' . get_string('exempteddocs', 'local_jobboard') . '</dt>';
    echo '<dd class="col-sm-9">';
    $doctypes = explode(',', $exemption->exempteddoctypes);
    foreach ($doctypes as $dt) {
        $dt = trim($dt);
        if ($dt) {
            echo '<span class="badge badge-info mr-1">' .
                get_string('doctype_' . $dt, 'local_jobboard') . '</span>';
        }
    }
    echo '</dd>';

    echo '<dt class="col-sm-3">' . get_string('validfrom', 'local_jobboard') . '</dt>';
    echo '<dd class="col-sm-9">' . userdate($exemption->validfrom, get_string('strftimedate', 'langconfig')) . '</dd>';

    echo '<dt class="col-sm-3">' . get_string('validuntil', 'local_jobboard') . '</dt>';
    echo '<dd class="col-sm-9">' . ($exemption->validuntil ?
        userdate($exemption->validuntil, get_string('strftimedate', 'langconfig')) :
        get_string('noexpiry', 'local_jobboard')) . '</dd>';

    // Status.
    $isvalid = exemption::is_valid($exemption);
    echo '<dt class="col-sm-3">' . get_string('status', 'local_jobboard') . '</dt>';
    echo '<dd class="col-sm-9">';
    if ($exemption->timerevoked) {
        echo '<span class="badge badge-danger">' . get_string('revoked', 'local_jobboard') . '</span>';
    } else if ($isvalid) {
        echo '<span class="badge badge-success">' . get_string('active', 'local_jobboard') . '</span>';
    } else {
        echo '<span class="badge badge-warning">' . get_string('expired', 'local_jobboard') . '</span>';
    }
    echo '</dd>';

    echo '<dt class="col-sm-3">' . get_string('notes', 'local_jobboard') . '</dt>';
    echo '<dd class="col-sm-9">' . ($exemption->notes ?: '-') . '</dd>';

    echo '<dt class="col-sm-3">' . get_string('createdby', 'local_jobboard') . '</dt>';
    echo '<dd class="col-sm-9">' . fullname($createdby) . ' - ' .
        userdate($exemption->timecreated, get_string('strftimedatetime', 'langconfig')) . '</dd>';

    if ($exemption->timerevoked) {
        $revokedby = $DB->get_record('user', ['id' => $exemption->revokedby]);
        echo '<dt class="col-sm-3">' . get_string('revokedby', 'local_jobboard') . '</dt>';
        echo '<dd class="col-sm-9">' . fullname($revokedby) . ' - ' .
            userdate($exemption->timerevoked, get_string('strftimedatetime', 'langconfig')) . '</dd>';

        echo '<dt class="col-sm-3">' . get_string('revokereason', 'local_jobboard') . '</dt>';
        echo '<dd class="col-sm-9">' . format_text($exemption->revokereason) . '</dd>';
    }

    echo '</dl>';
    echo '</div>';
    echo '<div class="card-footer">';

    if (!$exemption->timerevoked && $isvalid) {
        echo '<a href="' . new moodle_url('/local/jobboard/manage_exemptions.php',
            ['action' => 'edit', 'id' => $id]) . '" class="btn btn-primary mr-2">' .
            get_string('edit', 'local_jobboard') . '</a>';
        echo '<a href="' . new moodle_url('/local/jobboard/manage_exemptions.php',
            ['action' => 'revoke', 'id' => $id]) . '" class="btn btn-danger mr-2">' .
            get_string('revoke', 'local_jobboard') . '</a>';
    }

    echo '<a href="' . new moodle_url('/local/jobboard/manage_exemptions.php') .
        '" class="btn btn-secondary">' . get_string('back', 'local_jobboard') . '</a>';

    echo '</div>';
    echo '</div>';

    // Show exemption usage history.
    echo '<h4>' . get_string('exemptionusagehistory', 'local_jobboard') . '</h4>';

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
        $table = new html_table();
        $table->head = [get_string('vacancy', 'local_jobboard'), get_string('date')];
        $table->attributes['class'] = 'table table-sm';

        foreach ($usages as $usage) {
            $table->data[] = [
                format_string($usage->code . ' - ' . $usage->title),
                userdate($usage->timecreated, get_string('strftimedatetime', 'langconfig')),
            ];
        }

        echo html_writer::table($table);
    } else {
        echo '<p class="text-muted">' . get_string('noexemptionusage', 'local_jobboard') . '</p>';
    }

    echo $OUTPUT->footer();
    exit;
}

// Main list view.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('manageexemptions', 'local_jobboard'));

// Action buttons.
echo '<div class="mb-4">';
echo '<a href="' . new moodle_url('/local/jobboard/manage_exemptions.php', ['action' => 'add']) .
    '" class="btn btn-primary mr-2">' .
    '<i class="fa fa-plus mr-1"></i>' . get_string('addexemption', 'local_jobboard') . '</a>';
echo '<a href="' . new moodle_url('/local/jobboard/import_exemptions.php') .
    '" class="btn btn-outline-secondary">' .
    '<i class="fa fa-upload mr-1"></i>' . get_string('importexemptions', 'local_jobboard') . '</a>';
echo '</div>';

// Filters.
echo '<div class="card mb-4">';
echo '<div class="card-body">';
echo '<form method="get" action="" class="form-inline">';

echo '<div class="form-group mr-3 mb-2">';
echo '<label for="search" class="sr-only">' . get_string('search', 'local_jobboard') . '</label>';
echo '<input type="text" name="search" id="search" class="form-control" placeholder="' .
    get_string('searchuser', 'local_jobboard') . '" value="' . s($search) . '">';
echo '</div>';

echo '<div class="form-group mr-3 mb-2">';
echo '<label for="type" class="mr-2">' . get_string('type', 'local_jobboard') . ':</label>';
echo '<select name="type" id="type" class="form-control">';
echo '<option value="">' . get_string('all', 'local_jobboard') . '</option>';
$types = ['historico_iser', 'documentos_recientes', 'traslado_interno', 'recontratacion'];
foreach ($types as $t) {
    $selected = ($type === $t) ? 'selected' : '';
    echo '<option value="' . $t . '" ' . $selected . '>' .
        get_string('exemptiontype_' . $t, 'local_jobboard') . '</option>';
}
echo '</select>';
echo '</div>';

echo '<div class="form-group mr-3 mb-2">';
echo '<label for="status" class="mr-2">' . get_string('status', 'local_jobboard') . ':</label>';
echo '<select name="status" id="status" class="form-control">';
echo '<option value="">' . get_string('all', 'local_jobboard') . '</option>';
echo '<option value="active"' . ($status === 'active' ? ' selected' : '') . '>' .
    get_string('active', 'local_jobboard') . '</option>';
echo '<option value="expired"' . ($status === 'expired' ? ' selected' : '') . '>' .
    get_string('expired', 'local_jobboard') . '</option>';
echo '<option value="revoked"' . ($status === 'revoked' ? ' selected' : '') . '>' .
    get_string('revoked', 'local_jobboard') . '</option>';
echo '</select>';
echo '</div>';

echo '<button type="submit" class="btn btn-primary mb-2 mr-2">' . get_string('filter', 'local_jobboard') . '</button>';
echo '<a href="' . new moodle_url('/local/jobboard/manage_exemptions.php') .
    '" class="btn btn-outline-secondary mb-2">' . get_string('clearfilters', 'local_jobboard') . '</a>';
echo '</form>';
echo '</div>';
echo '</div>';

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

echo '<div class="row mb-4">';

echo '<div class="col-md-4 mb-3">';
echo '<div class="card bg-success text-white h-100">';
echo '<div class="card-body text-center">';
echo '<h2>' . $activecnt . '</h2>';
echo '<p class="mb-0">' . get_string('activeexemptions', 'local_jobboard') . '</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="col-md-4 mb-3">';
echo '<div class="card bg-warning text-dark h-100">';
echo '<div class="card-body text-center">';
echo '<h2>' . $expiredcnt . '</h2>';
echo '<p class="mb-0">' . get_string('expiredexemptions', 'local_jobboard') . '</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '<div class="col-md-4 mb-3">';
echo '<div class="card bg-danger text-white h-100">';
echo '<div class="card-body text-center">';
echo '<h2>' . $revokedcnt . '</h2>';
echo '<p class="mb-0">' . get_string('revokedexemptions', 'local_jobboard') . '</p>';
echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>';

// Exemptions table.
if (empty($exemptions)) {
    echo $OUTPUT->notification(get_string('noexemptions', 'local_jobboard'), 'info');
} else {
    $table = new html_table();
    $table->head = [
        get_string('user'),
        get_string('exemptiontype', 'local_jobboard'),
        get_string('exempteddocs', 'local_jobboard'),
        get_string('validfrom', 'local_jobboard'),
        get_string('validuntil', 'local_jobboard'),
        get_string('status', 'local_jobboard'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'table table-striped table-hover';

    foreach ($exemptions as $ex) {
        // Status badge.
        $isvalid = !$ex->timerevoked &&
            $ex->validfrom <= time() &&
            (!$ex->validuntil || $ex->validuntil > time());

        if ($ex->timerevoked) {
            $statusbadge = '<span class="badge badge-danger">' . get_string('revoked', 'local_jobboard') . '</span>';
        } else if ($isvalid) {
            $statusbadge = '<span class="badge badge-success">' . get_string('active', 'local_jobboard') . '</span>';
        } else {
            $statusbadge = '<span class="badge badge-warning">' . get_string('expired', 'local_jobboard') . '</span>';
        }

        // Exempted docs (abbreviated).
        $doctypes = explode(',', $ex->exempteddoctypes);
        $doctypeshtml = '<span class="badge badge-secondary">' . count($doctypes) . ' ' .
            get_string('doctypes', 'local_jobboard') . '</span>';

        // Actions.
        $actions = '<div class="btn-group btn-group-sm">';
        $actions .= '<a href="' . new moodle_url('/local/jobboard/manage_exemptions.php',
            ['action' => 'view', 'id' => $ex->id]) . '" class="btn btn-outline-primary" title="' .
            get_string('view', 'local_jobboard') . '"><i class="fa fa-eye"></i></a>';

        if ($isvalid) {
            $actions .= '<a href="' . new moodle_url('/local/jobboard/manage_exemptions.php',
                ['action' => 'edit', 'id' => $ex->id]) . '" class="btn btn-outline-secondary" title="' .
                get_string('edit', 'local_jobboard') . '"><i class="fa fa-edit"></i></a>';
            $actions .= '<a href="' . new moodle_url('/local/jobboard/manage_exemptions.php',
                ['action' => 'revoke', 'id' => $ex->id]) . '" class="btn btn-outline-danger" title="' .
                get_string('revoke', 'local_jobboard') . '"><i class="fa fa-ban"></i></a>';
        }
        $actions .= '</div>';

        $row = [
            '<strong>' . format_string($ex->firstname . ' ' . $ex->lastname) . '</strong>' .
                '<br><small class="text-muted">' . $ex->email . '</small>',
            get_string('exemptiontype_' . $ex->exemptiontype, 'local_jobboard'),
            $doctypeshtml,
            userdate($ex->validfrom, get_string('strftimedate', 'langconfig')),
            $ex->validuntil ?
                userdate($ex->validuntil, get_string('strftimedate', 'langconfig')) :
                '<em>' . get_string('noexpiry', 'local_jobboard') . '</em>',
            $statusbadge,
            $actions,
        ];

        $table->data[] = $row;
    }

    echo html_writer::table($table);

    // Pagination.
    $baseurl = new moodle_url('/local/jobboard/manage_exemptions.php', [
        'search' => $search,
        'type' => $type,
        'status' => $status,
    ]);
    echo $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
}

echo $OUTPUT->footer();
