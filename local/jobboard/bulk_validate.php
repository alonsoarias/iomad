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
 * Bulk document validation page.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\bulk_validator;

$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$documenttype = optional_param('documenttype', '', PARAM_ALPHANUMEXT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:reviewdocuments', $context);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/bulk_validate.php', [
    'vacancyid' => $vacancyid,
    'documenttype' => $documenttype,
]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('bulkvalidation', 'local_jobboard'));
$PAGE->set_heading(get_string('bulkvalidation', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Handle bulk actions.
if ($action === 'validate' || $action === 'reject') {
    require_sesskey();

    $documentids = required_param_array('documents', PARAM_INT);
    $notes = optional_param('notes', '', PARAM_TEXT);
    $rejectreason = optional_param('rejectreason', '', PARAM_ALPHA);

    $isvalid = ($action === 'validate');

    $results = bulk_validator::validate_documents($documentids, $isvalid,
        $isvalid ? null : $rejectreason, $notes);

    $message = get_string('bulkvalidationcomplete', 'local_jobboard', $results);
    $type = $results['failed'] > 0 ? \core\output\notification::NOTIFY_WARNING : \core\output\notification::NOTIFY_SUCCESS;

    redirect(
        new moodle_url('/local/jobboard/bulk_validate.php', [
            'vacancyid' => $vacancyid,
            'documenttype' => $documenttype,
        ]),
        $message,
        null,
        $type
    );
}

// Get vacancy list for filter.
$vacancies = $DB->get_records_select('local_jobboard_vacancy',
    "status IN ('published', 'closed')", null, 'code ASC', 'id, code, title');

// Get pending documents by type.
$pendingbytype = bulk_validator::get_pending_by_type($vacancyid ?: null);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('bulkvalidation', 'local_jobboard'));

// Filters.
echo '<form class="form-inline mb-4" method="get">';

echo '<div class="form-group mr-3">';
echo '<label class="mr-2">' . get_string('vacancy', 'local_jobboard') . '</label>';
echo '<select name="vacancyid" class="form-control">';
echo '<option value="0">' . get_string('allvacancies', 'local_jobboard') . '</option>';
foreach ($vacancies as $v) {
    $selected = ($vacancyid == $v->id) ? 'selected' : '';
    echo "<option value=\"{$v->id}\" {$selected}>" . format_string($v->code . ' - ' . $v->title) . '</option>';
}
echo '</select>';
echo '</div>';

echo '<div class="form-group mr-3">';
echo '<label class="mr-2">' . get_string('documenttype', 'local_jobboard') . '</label>';
echo '<select name="documenttype" class="form-control">';
echo '<option value="">' . get_string('selecttype', 'local_jobboard') . '</option>';
foreach ($pendingbytype as $dt) {
    $selected = ($documenttype === $dt->documenttype) ? 'selected' : '';
    $typename = get_string('doctype_' . $dt->documenttype, 'local_jobboard');
    echo "<option value=\"{$dt->documenttype}\" {$selected}>{$typename} ({$dt->count})</option>";
}
echo '</select>';
echo '</div>';

echo '<button type="submit" class="btn btn-primary">' . get_string('filter') . '</button>';
echo '</form>';

// Summary cards.
echo '<div class="row mb-4">';
foreach ($pendingbytype as $dt) {
    $typename = get_string('doctype_' . $dt->documenttype, 'local_jobboard');
    echo '<div class="col-md-3 col-sm-6 mb-2">';
    echo '<div class="card">';
    echo '<div class="card-body text-center">';
    echo '<h4>' . $dt->count . '</h4>';
    echo '<p class="mb-0 small">' . $typename . '</p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
}
echo '</div>';

// Document list if type selected.
if (!empty($documenttype)) {
    $documents = bulk_validator::get_pending_documents_by_type($documenttype, $vacancyid ?: null);

    if (empty($documents)) {
        echo $OUTPUT->notification(get_string('nodocumentspending', 'local_jobboard'), 'info');
    } else {
        echo '<form method="post" action="' . $PAGE->url . '">';
        echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
        echo '<input type="hidden" name="vacancyid" value="' . $vacancyid . '">';
        echo '<input type="hidden" name="documenttype" value="' . $documenttype . '">';

        // Select all checkbox.
        echo '<div class="mb-3">';
        echo '<div class="form-check">';
        echo '<input type="checkbox" class="form-check-input" id="selectall">';
        echo '<label class="form-check-label" for="selectall">' . get_string('selectall') . '</label>';
        echo '</div>';
        echo '</div>';

        // Documents table.
        echo '<table class="table table-striped">';
        echo '<thead><tr>';
        echo '<th width="40"></th>';
        echo '<th>' . get_string('applicant', 'local_jobboard') . '</th>';
        echo '<th>' . get_string('vacancy', 'local_jobboard') . '</th>';
        echo '<th>' . get_string('filename', 'local_jobboard') . '</th>';
        echo '<th>' . get_string('uploaded', 'local_jobboard') . '</th>';
        echo '<th>' . get_string('actions') . '</th>';
        echo '</tr></thead>';
        echo '<tbody>';

        foreach ($documents as $doc) {
            echo '<tr>';
            echo '<td><input type="checkbox" name="documents[]" value="' . $doc->id . '" class="doc-checkbox"></td>';
            echo '<td>' . format_string($doc->firstname . ' ' . $doc->lastname) . '<br>';
            echo '<small class="text-muted">' . $doc->email . '</small></td>';
            echo '<td>' . format_string($doc->vacancy_code) . '</td>';
            echo '<td>' . format_string($doc->filename) . '</td>';
            echo '<td>' . userdate($doc->timecreated, get_string('strftimedatetime', 'langconfig')) . '</td>';
            echo '<td>';

            // View document link.
            $docobj = \local_jobboard\document::get($doc->id);
            $downloadurl = $docobj ? $docobj->get_download_url() : null;
            if ($downloadurl) {
                echo '<a href="' . $downloadurl . '" class="btn btn-sm btn-outline-primary" target="_blank">' .
                    get_string('view') . '</a> ';
            }

            // Individual validate link.
            echo '<a href="' . new moodle_url('/local/jobboard/validate_document.php', ['id' => $doc->id]) .
                '" class="btn btn-sm btn-outline-success">' . get_string('validate', 'local_jobboard') . '</a>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody></table>';

        // Bulk action buttons.
        echo '<div class="card mt-3">';
        echo '<div class="card-header">' . get_string('bulkactions', 'local_jobboard') . '</div>';
        echo '<div class="card-body">';

        echo '<div class="row">';

        // Approve selected.
        echo '<div class="col-md-6">';
        echo '<h6>' . get_string('approveselected', 'local_jobboard') . '</h6>';
        echo '<div class="form-group">';
        echo '<textarea name="notes" class="form-control" rows="2" placeholder="' .
            get_string('optionalnotes', 'local_jobboard') . '"></textarea>';
        echo '</div>';
        echo '<button type="submit" name="action" value="validate" class="btn btn-success">';
        echo '<i class="fa fa-check"></i> ' . get_string('approveselected', 'local_jobboard');
        echo '</button>';
        echo '</div>';

        // Reject selected.
        echo '<div class="col-md-6">';
        echo '<h6>' . get_string('rejectselected', 'local_jobboard') . '</h6>';
        echo '<div class="form-group">';
        echo '<select name="rejectreason" class="form-control mb-2">';
        echo '<option value="">' . get_string('selectreason', 'local_jobboard') . '</option>';
        $reasons = ['illegible', 'expired', 'incomplete', 'wrongtype', 'mismatch'];
        foreach ($reasons as $reason) {
            echo '<option value="' . $reason . '">' . get_string('rejectreason_' . $reason, 'local_jobboard') . '</option>';
        }
        echo '</select>';
        echo '</div>';
        echo '<button type="submit" name="action" value="reject" class="btn btn-danger">';
        echo '<i class="fa fa-times"></i> ' . get_string('rejectselected', 'local_jobboard');
        echo '</button>';
        echo '</div>';

        echo '</div>'; // row.
        echo '</div>'; // card-body.
        echo '</div>'; // card.

        echo '</form>';

        // JavaScript for select all.
        echo '<script>
        document.getElementById("selectall").addEventListener("change", function() {
            var checkboxes = document.querySelectorAll(".doc-checkbox");
            checkboxes.forEach(function(cb) {
                cb.checked = this.checked;
            }.bind(this));
        });
        </script>';
    }
}

echo $OUTPUT->footer();
