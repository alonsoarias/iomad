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
 * Convocatorias (Calls) management page for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/adminlib.php');
require_once(__DIR__ . '/../lib.php');

admin_externalpage_setup('local_jobboard_convocatorias');

$action = optional_param('action', '', PARAM_ALPHA);
$id = optional_param('id', 0, PARAM_INT);

$context = context_system::instance();
$pageurl = new moodle_url('/local/jobboard/admin/convocatorias.php');

// Check IOMAD installation.
$isiomad = local_jobboard_is_iomad_installed();

// Handle actions.
if ($action === 'delete' && $id && confirm_sesskey()) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $id], '*', MUST_EXIST);

    // Unlink vacancies from this convocatoria (don't delete them).
    $DB->set_field('local_jobboard_vacancy', 'convocatoriaid', null, ['convocatoriaid' => $id]);

    // Delete the convocatoria.
    $DB->delete_records('local_jobboard_convocatoria', ['id' => $id]);

    redirect($pageurl, get_string('convocatoriadeleted', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($action === 'open' && $id && confirm_sesskey()) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $id], '*', MUST_EXIST);

    // Check if it has vacancies.
    $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $id]);
    if ($vacancycount == 0) {
        redirect($pageurl, get_string('error:convocatoriahasnovacancies', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
    }

    // Update status and publish all vacancies.
    $convocatoria->status = 'open';
    $convocatoria->timemodified = time();
    $convocatoria->modifiedby = $USER->id;
    $DB->update_record('local_jobboard_convocatoria', $convocatoria);

    // Publish all draft vacancies in this convocatoria.
    $DB->execute("UPDATE {local_jobboard_vacancy}
                  SET status = 'published', opendate = ?, closedate = ?, timemodified = ?
                  WHERE convocatoriaid = ? AND status = 'draft'",
        [$convocatoria->startdate, $convocatoria->enddate, time(), $id]);

    redirect($pageurl, get_string('convocatoriaopened', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($action === 'close' && $id && confirm_sesskey()) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $id], '*', MUST_EXIST);

    $convocatoria->status = 'closed';
    $convocatoria->timemodified = time();
    $convocatoria->modifiedby = $USER->id;
    $DB->update_record('local_jobboard_convocatoria', $convocatoria);

    // Close all vacancies in this convocatoria.
    $DB->set_field('local_jobboard_vacancy', 'status', 'closed', ['convocatoriaid' => $id]);

    redirect($pageurl, get_string('convocatoriaclosedmsg', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
}

if ($action === 'archive' && $id && confirm_sesskey()) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $id], '*', MUST_EXIST);

    $convocatoria->status = 'archived';
    $convocatoria->timemodified = time();
    $convocatoria->modifiedby = $USER->id;
    $DB->update_record('local_jobboard_convocatoria', $convocatoria);

    redirect($pageurl, get_string('convocatoriaarchived', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
}

// Handle form submission for add/edit.
if ($action === 'edit' || $action === 'add') {
    $convocatoria = null;
    if ($action === 'edit' && $id) {
        $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $id], '*', MUST_EXIST);
    }

    // Process form submission.
    if (data_submitted() && confirm_sesskey()) {
        $data = new stdClass();
        $data->code = required_param('code', PARAM_ALPHANUMEXT);
        $data->name = required_param('name', PARAM_TEXT);
        $data->description = optional_param('description', '', PARAM_RAW);
        $data->startdate = required_param('startdate', PARAM_INT);
        $data->enddate = required_param('enddate', PARAM_INT);
        $data->publicationtype = optional_param('publicationtype', 'internal', PARAM_ALPHA);
        $data->terms = optional_param('terms', '', PARAM_RAW);

        if ($isiomad) {
            $data->companyid = optional_param('companyid', null, PARAM_INT);
            $data->departmentid = optional_param('departmentid', null, PARAM_INT);
        }

        // Validation.
        $errors = [];
        if ($data->enddate <= $data->startdate) {
            $errors[] = get_string('error:convocatoriadatesinvalid', 'local_jobboard');
        }

        // Check if code exists (for new or different id).
        $existingcode = $DB->get_record('local_jobboard_convocatoria', ['code' => $data->code]);
        if ($existingcode && (!$convocatoria || $existingcode->id != $convocatoria->id)) {
            $errors[] = get_string('error:convocatoriacodeexists', 'local_jobboard');
        }

        if (empty($errors)) {
            if ($convocatoria) {
                // Update existing.
                $data->id = $convocatoria->id;
                $data->status = $convocatoria->status;
                $data->createdby = $convocatoria->createdby;
                $data->timecreated = $convocatoria->timecreated;
                $data->modifiedby = $USER->id;
                $data->timemodified = time();
                $DB->update_record('local_jobboard_convocatoria', $data);
                redirect($pageurl, get_string('convocatoriaupdated', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
            } else {
                // Insert new.
                $data->status = 'draft';
                $data->createdby = $USER->id;
                $data->timecreated = time();
                $DB->insert_record('local_jobboard_convocatoria', $data);
                redirect($pageurl, get_string('convocatoriacreated', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
            }
        }
    }

    // Display form.
    echo $OUTPUT->header();

    $formtitle = $convocatoria
        ? get_string('editconvocatoria', 'local_jobboard')
        : get_string('addconvocatoria', 'local_jobboard');
    echo $OUTPUT->heading($formtitle);

    // Show errors if any.
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo $OUTPUT->notification($error, 'error');
        }
    }

    // Show help text.
    echo '<div class="alert alert-info">' . get_string('convocatoriahelp', 'local_jobboard') . '</div>';

    // Build form.
    $formurl = new moodle_url($pageurl, ['action' => $action, 'id' => $id]);
    echo '<form method="post" action="' . $formurl . '" class="mform">';
    echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';

    echo '<div class="form-group row mb-3">';
    echo '<label class="col-md-3 col-form-label">' . get_string('convocatoriacode', 'local_jobboard') . ' *</label>';
    echo '<div class="col-md-9">';
    echo '<input type="text" name="code" class="form-control" required maxlength="50" value="' .
        s($convocatoria->code ?? '') . '">';
    echo '</div></div>';

    echo '<div class="form-group row mb-3">';
    echo '<label class="col-md-3 col-form-label">' . get_string('convocatorianame', 'local_jobboard') . ' *</label>';
    echo '<div class="col-md-9">';
    echo '<input type="text" name="name" class="form-control" required maxlength="255" value="' .
        s($convocatoria->name ?? '') . '">';
    echo '</div></div>';

    echo '<div class="form-group row mb-3">';
    echo '<label class="col-md-3 col-form-label">' . get_string('convocatoriadescription', 'local_jobboard') . '</label>';
    echo '<div class="col-md-9">';
    echo '<textarea name="description" class="form-control" rows="4">' .
        s($convocatoria->description ?? '') . '</textarea>';
    echo '</div></div>';

    echo '<div class="form-group row mb-3">';
    echo '<label class="col-md-3 col-form-label">' . get_string('convocatoriastartdate', 'local_jobboard') . ' *</label>';
    echo '<div class="col-md-9">';
    $startdate = $convocatoria->startdate ?? time();
    echo '<input type="date" name="startdate" class="form-control" required value="' .
        date('Y-m-d', $startdate) . '" onchange="this.value = Math.floor(new Date(this.value).getTime() / 1000); document.querySelector(\'input[name=startdate]\').value = Math.floor(new Date(document.querySelector(\'input[name=startdate]\').value).getTime() / 1000)">';
    echo '<input type="hidden" name="startdate" value="' . $startdate . '">';
    echo '</div></div>';

    echo '<div class="form-group row mb-3">';
    echo '<label class="col-md-3 col-form-label">' . get_string('convocatoriaenddate', 'local_jobboard') . ' *</label>';
    echo '<div class="col-md-9">';
    $enddate = $convocatoria->enddate ?? strtotime('+30 days');
    echo '<input type="date" name="enddate" class="form-control" required value="' .
        date('Y-m-d', $enddate) . '">';
    echo '<input type="hidden" name="enddate" value="' . $enddate . '">';
    echo '</div></div>';

    echo '<div class="form-group row mb-3">';
    echo '<label class="col-md-3 col-form-label">' . get_string('publicationtype', 'local_jobboard') . '</label>';
    echo '<div class="col-md-9">';
    $pubtype = $convocatoria->publicationtype ?? 'internal';
    echo '<select name="publicationtype" class="form-control">';
    echo '<option value="internal"' . ($pubtype === 'internal' ? ' selected' : '') . '>' .
        get_string('publicationtype:internal', 'local_jobboard') . '</option>';
    echo '<option value="public"' . ($pubtype === 'public' ? ' selected' : '') . '>' .
        get_string('publicationtype:public', 'local_jobboard') . '</option>';
    echo '</select>';
    echo '</div></div>';

    // Company selector (IOMAD only).
    if ($isiomad) {
        $companies = local_jobboard_get_companies();
        echo '<div class="form-group row mb-3">';
        echo '<label class="col-md-3 col-form-label">' . get_string('company', 'local_jobboard') . '</label>';
        echo '<div class="col-md-9">';
        echo '<select name="companyid" class="form-control">';
        echo '<option value="">' . get_string('allcompanies', 'local_jobboard') . '</option>';
        foreach ($companies as $cid => $cname) {
            $selected = (isset($convocatoria->companyid) && $convocatoria->companyid == $cid) ? ' selected' : '';
            echo '<option value="' . $cid . '"' . $selected . '>' . s($cname) . '</option>';
        }
        echo '</select>';
        echo '</div></div>';
    }

    echo '<div class="form-group row mb-3">';
    echo '<label class="col-md-3 col-form-label">' . get_string('convocatoriaterms', 'local_jobboard') . '</label>';
    echo '<div class="col-md-9">';
    echo '<textarea name="terms" class="form-control" rows="6">' .
        s($convocatoria->terms ?? '') . '</textarea>';
    echo '</div></div>';

    echo '<div class="form-group row mb-3">';
    echo '<div class="col-md-9 offset-md-3">';
    echo '<button type="submit" class="btn btn-primary">' . get_string('savechanges') . '</button>';
    echo ' <a href="' . $pageurl . '" class="btn btn-secondary">' . get_string('cancel') . '</a>';
    echo '</div></div>';

    echo '</form>';

    // JavaScript for date handling.
    echo '<script>
    document.querySelectorAll("input[type=date]").forEach(function(el) {
        el.addEventListener("change", function() {
            var hidden = el.nextElementSibling;
            if (hidden && hidden.type === "hidden" && hidden.name === el.name) {
                hidden.value = Math.floor(new Date(el.value + "T00:00:00").getTime() / 1000);
            }
        });
    });
    </script>';

    echo $OUTPUT->footer();
    exit;
}

// Default view: list all convocatorias.
echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('manageconvocatorias', 'local_jobboard'));

echo '<div class="alert alert-info">' . get_string('convocatoriahelp', 'local_jobboard') . '</div>';

// Add button.
$addurl = new moodle_url($pageurl, ['action' => 'add']);
echo '<p><a href="' . $addurl . '" class="btn btn-primary">' . get_string('addconvocatoria', 'local_jobboard') . '</a></p>';

// Get all convocatorias.
$convocatorias = $DB->get_records('local_jobboard_convocatoria', null, 'status ASC, startdate DESC');

if (empty($convocatorias)) {
    echo $OUTPUT->notification(get_string('noconvocatorias', 'local_jobboard'), 'info');
} else {
    $table = new html_table();
    $table->head = [
        get_string('convocatoriacode', 'local_jobboard'),
        get_string('convocatorianame', 'local_jobboard'),
        get_string('convocatoriastartdate', 'local_jobboard'),
        get_string('convocatoriaenddate', 'local_jobboard'),
        get_string('vacancies', 'local_jobboard'),
        get_string('convocatoriastatus', 'local_jobboard'),
        get_string('actions'),
    ];
    $table->attributes['class'] = 'generaltable';

    foreach ($convocatorias as $c) {
        // Count vacancies.
        $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $c->id]);

        // Status badge.
        $statusclass = [
            'draft' => 'secondary',
            'open' => 'success',
            'closed' => 'warning',
            'archived' => 'dark',
        ];
        $statusbadge = '<span class="badge badge-' . ($statusclass[$c->status] ?? 'secondary') . '">' .
            get_string('convocatoria_status_' . $c->status, 'local_jobboard') . '</span>';

        // Actions.
        $actions = [];

        // Edit.
        $editurl = new moodle_url($pageurl, ['action' => 'edit', 'id' => $c->id]);
        $actions[] = html_writer::link($editurl, get_string('edit'), ['class' => 'btn btn-sm btn-outline-primary']);

        // Status change actions based on current status.
        if ($c->status === 'draft') {
            $openurl = new moodle_url($pageurl, ['action' => 'open', 'id' => $c->id, 'sesskey' => sesskey()]);
            $actions[] = html_writer::link($openurl, get_string('openconvocatoria', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-success']);
        } elseif ($c->status === 'open') {
            $closeurl = new moodle_url($pageurl, ['action' => 'close', 'id' => $c->id, 'sesskey' => sesskey()]);
            $actions[] = html_writer::link($closeurl, get_string('closeconvocatoria', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-warning']);
        } elseif ($c->status === 'closed') {
            $archiveurl = new moodle_url($pageurl, ['action' => 'archive', 'id' => $c->id, 'sesskey' => sesskey()]);
            $actions[] = html_writer::link($archiveurl, get_string('archiveconvocatoria', 'local_jobboard'),
                ['class' => 'btn btn-sm btn-outline-dark']);
        }

        // Delete (only for draft or archived).
        if (in_array($c->status, ['draft', 'archived'])) {
            $deleteurl = new moodle_url($pageurl, ['action' => 'delete', 'id' => $c->id, 'sesskey' => sesskey()]);
            $actions[] = html_writer::link($deleteurl, get_string('delete'),
                ['class' => 'btn btn-sm btn-outline-danger',
                 'onclick' => "return confirm('" . get_string('confirmdeletevconvocatoria', 'local_jobboard') . "')"]);
        }

        // View vacancies link.
        if ($vacancycount > 0) {
            $vacanciesurl = new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $c->id]);
            $vacancylink = html_writer::link($vacanciesurl, $vacancycount, ['class' => 'badge badge-info']);
        } else {
            $vacancylink = '<span class="badge badge-secondary">0</span>';
        }

        $table->data[] = [
            s($c->code),
            s($c->name),
            userdate($c->startdate, get_string('strftimedate', 'langconfig')),
            userdate($c->enddate, get_string('strftimedate', 'langconfig')),
            $vacancylink,
            $statusbadge,
            implode(' ', $actions),
        ];
    }

    echo html_writer::table($table);
}

echo $OUTPUT->footer();
