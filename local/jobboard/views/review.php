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
 * Document review view for local_jobboard.
 *
 * This file is included by index.php and should not be accessed directly.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');
require_once($CFG->libdir . '/tablelib.php');

use local_jobboard\application;
use local_jobboard\document;
use local_jobboard\vacancy;

// Require review capability.
require_capability('local/jobboard:reviewdocuments', $context);

// Parameters.
$applicationid = optional_param('applicationid', 0, PARAM_INT);
$documentid = optional_param('documentid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);

// Page setup.
$PAGE->set_title(get_string('reviewdocuments', 'local_jobboard'));
$PAGE->set_heading(get_string('reviewdocuments', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

// Navigation.
$PAGE->navbar->add(get_string('jobboard', 'local_jobboard'), new moodle_url('/local/jobboard/index.php'));
$PAGE->navbar->add(get_string('reviewdocuments', 'local_jobboard'));

// Handle actions.
if ($action && confirm_sesskey()) {
    switch ($action) {
        case 'validate':
            if ($documentid) {
                $doc = new document($documentid);
                $doc->validate($USER->id);
                \core\notification::success(get_string('documentvalidated', 'local_jobboard'));
            }
            break;

        case 'reject':
            if ($documentid) {
                $reason = required_param('reason', PARAM_TEXT);
                $doc = new document($documentid);
                $doc->reject($USER->id, $reason);
                \core\notification::success(get_string('documentrejected', 'local_jobboard'));
            }
            break;

        case 'validateall':
            if ($applicationid) {
                $documents = document::get_by_application($applicationid);
                foreach ($documents as $doc) {
                    if ($doc->status === 'pending') {
                        $doc->validate($USER->id);
                    }
                }
                \core\notification::success(get_string('documentvalidated', 'local_jobboard'));
            }
            break;

        case 'markreviewed':
            if ($applicationid) {
                $app = new application($applicationid);
                $app->update_status('docs_validated', $USER->id);
                \core\notification::success(get_string('reviewsubmitted', 'local_jobboard'));
            }
            break;
    }

    // Redirect to avoid form resubmission.
    redirect(new moodle_url('/local/jobboard/index.php', [
        'view' => 'review',
        'applicationid' => $applicationid,
        'vacancyid' => $vacancyid,
    ]));
}

echo $OUTPUT->header();
echo $OUTPUT->heading(get_string('reviewdocuments', 'local_jobboard'));

// If no application selected, show list of applications pending review.
if (!$applicationid) {
    // Build filter.
    $where = "a.status IN ('submitted', 'under_review')";
    $params = [];

    if ($vacancyid) {
        $where .= " AND a.vacancyid = :vacancyid";
        $params['vacancyid'] = $vacancyid;
    }

    // Multi-tenant filter.
    if (\local_jobboard_is_iomad_installed() && !has_capability('local/jobboard:viewallvacancies', $context)) {
        $usercompanyid = \local_jobboard_get_user_companyid();
        if ($usercompanyid) {
            $where .= " AND v.companyid = :companyid";
            $params['companyid'] = $usercompanyid;
        }
    }

    // Get applications pending review.
    $sql = "SELECT a.*, v.title as vacancy_title, v.code as vacancy_code,
                   u.firstname, u.lastname, u.email,
                   (SELECT COUNT(*) FROM {local_jobboard_document} d WHERE d.applicationid = a.id AND d.issuperseded = 0) as doccount,
                   (SELECT COUNT(*) FROM {local_jobboard_document} d
                    JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                    WHERE d.applicationid = a.id AND d.issuperseded = 0 AND dv.status = 'pending'
                   ) as pendingcount
            FROM {local_jobboard_application} a
            JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
            JOIN {user} u ON u.id = a.userid
            WHERE {$where}
            ORDER BY a.timecreated ASC";

    $applications = $DB->get_records_sql($sql, $params);

    // Vacancy filter dropdown.
    $vacancies = $DB->get_records('local_jobboard_vacancy', ['status' => 'published'], 'code ASC', 'id, code, title');

    echo '<form class="form-inline mb-4" method="get" action="' . new moodle_url('/local/jobboard/index.php') . '">';
    echo '<input type="hidden" name="view" value="review">';
    echo '<label class="mr-2">' . get_string('vacancy', 'local_jobboard') . '</label>';
    echo '<select name="vacancyid" class="form-control mr-2">';
    echo '<option value="0">' . get_string('allvacancies', 'local_jobboard') . '</option>';
    foreach ($vacancies as $v) {
        $selected = ($vacancyid == $v->id) ? 'selected' : '';
        echo "<option value=\"{$v->id}\" {$selected}>" . format_string($v->code) . ' - ' . format_string($v->title) . '</option>';
    }
    echo '</select>';
    echo '<button type="submit" class="btn btn-primary">' . get_string('filter') . '</button>';
    echo '</form>';

    if (empty($applications)) {
        echo $OUTPUT->notification(get_string('nodocumentstoreview', 'local_jobboard'), 'info');
    } else {
        // Build table.
        $table = new html_table();
        $table->head = [
            get_string('vacancy', 'local_jobboard'),
            get_string('applicant', 'local_jobboard'),
            get_string('status', 'local_jobboard'),
            get_string('documents', 'local_jobboard'),
            get_string('pendingdocuments', 'local_jobboard'),
            get_string('actions'),
        ];
        $table->attributes['class'] = 'generaltable review-table';

        foreach ($applications as $app) {
            $row = [];

            // Vacancy.
            $row[] = format_string($app->vacancy_code) . '<br><small class="text-muted">' .
                     format_string($app->vacancy_title) . '</small>';

            // Applicant.
            $row[] = fullname($app) . '<br><small class="text-muted">' . $app->email . '</small>';

            // Status.
            $statusclass = ($app->status === 'submitted') ? 'badge-warning' : 'badge-info';
            $row[] = '<span class="badge ' . $statusclass . '">' .
                     get_string('status_' . $app->status, 'local_jobboard') . '</span>';

            // Document counts.
            $row[] = $app->doccount;
            $row[] = '<span class="badge badge-danger">' . $app->pendingcount . '</span>';

            // Actions.
            $reviewurl = new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'applicationid' => $app->id]);
            $row[] = '<a href="' . $reviewurl . '" class="btn btn-sm btn-primary">' .
                     get_string('reviewdocuments', 'local_jobboard') . '</a>';

            $table->data[] = $row;
        }

        echo html_writer::table($table);
    }
} else {
    // Show specific application for review.
    $application = new application($applicationid);

    if (!$application->id) {
        throw new moodle_exception('error:invalidapplication', 'local_jobboard');
    }

    $vacancy = new vacancy($application->vacancyid);
    $applicant = $DB->get_record('user', ['id' => $application->userid]);
    $documents = document::get_by_application($applicationid);

    // Back button.
    echo '<p><a href="' . new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancyid]) .
         '" class="btn btn-secondary">&laquo; ' . get_string('back') . '</a></p>';

    // Application info card.
    echo '<div class="card mb-4">';
    echo '<div class="card-header"><h5>' . get_string('applicationdetails', 'local_jobboard') . '</h5></div>';
    echo '<div class="card-body">';
    echo '<div class="row">';
    echo '<div class="col-md-6">';
    echo '<h6>' . get_string('applicantinfo', 'local_jobboard') . '</h6>';
    echo '<p><strong>' . get_string('name') . ':</strong> ' . fullname($applicant) . '</p>';
    echo '<p><strong>' . get_string('email') . ':</strong> ' . $applicant->email . '</p>';
    echo '<p><strong>' . get_string('dateapplied', 'local_jobboard') . ':</strong> ' .
         userdate($application->timecreated, get_string('strftimedatetime', 'langconfig')) . '</p>';
    echo '</div>';
    echo '<div class="col-md-6">';
    echo '<h6>' . get_string('vacancy', 'local_jobboard') . '</h6>';
    echo '<p><strong>' . get_string('code', 'local_jobboard') . ':</strong> ' . format_string($vacancy->code) . '</p>';
    echo '<p><strong>' . get_string('title', 'local_jobboard') . ':</strong> ' . format_string($vacancy->title) . '</p>';
    echo '<p><strong>' . get_string('status', 'local_jobboard') . ':</strong> ' .
         '<span class="badge badge-info">' . get_string('status_' . $application->status, 'local_jobboard') . '</span></p>';
    echo '</div>';
    echo '</div>';
    echo '</div>';
    echo '</div>';

    // Document list.
    echo '<div class="card mb-4">';
    echo '<div class="card-header d-flex justify-content-between align-items-center">';
    echo '<h5 class="mb-0">' . get_string('documentlist', 'local_jobboard') . '</h5>';

    // Bulk actions.
    $pendingcount = 0;
    foreach ($documents as $doc) {
        if ($doc->status === 'pending') {
            $pendingcount++;
        }
    }

    if ($pendingcount > 0) {
        $validateallurl = new moodle_url('/local/jobboard/index.php', [
            'view' => 'review',
            'applicationid' => $applicationid,
            'action' => 'validateall',
            'sesskey' => sesskey(),
        ]);
        echo '<a href="' . $validateallurl . '" class="btn btn-sm btn-success">' .
             get_string('validateall', 'local_jobboard') . '</a>';
    }
    echo '</div>';

    echo '<div class="card-body">';

    if (empty($documents)) {
        echo $OUTPUT->notification(get_string('nodocumentstoreview', 'local_jobboard'), 'info');
    } else {
        echo '<div class="list-group">';
        foreach ($documents as $doc) {
            $statusbadge = '';
            switch ($doc->status) {
                case 'pending':
                    $statusbadge = '<span class="badge badge-warning">' . get_string('docstatus:pending', 'local_jobboard') . '</span>';
                    break;
                case 'approved':
                    $statusbadge = '<span class="badge badge-success">' . get_string('docstatus:approved', 'local_jobboard') . '</span>';
                    break;
                case 'rejected':
                    $statusbadge = '<span class="badge badge-danger">' . get_string('docstatus:rejected', 'local_jobboard') . '</span>';
                    break;
            }

            echo '<div class="list-group-item">';
            echo '<div class="d-flex w-100 justify-content-between align-items-start">';
            echo '<div>';
            echo '<h6 class="mb-1">' . format_string($doc->get_doctype_name()) . '</h6>';
            echo '<p class="mb-1 text-muted">' . format_string($doc->filename) . '</p>';
            echo $statusbadge;
            if ($doc->reviewerid && $doc->status !== 'pending') {
                $reviewer = $DB->get_record('user', ['id' => $doc->reviewerid]);
                echo '<br><small class="text-muted">' . get_string('reviewedby', 'local_jobboard') . ': ' .
                     fullname($reviewer) . ' - ' . userdate($doc->reviewedat, get_string('strftimedatetime', 'langconfig')) . '</small>';
            }
            if ($doc->status === 'rejected' && !empty($doc->rejectreason)) {
                echo '<br><small class="text-danger">' . get_string('rejectreason', 'local_jobboard') . ': ' .
                     format_string($doc->rejectreason) . '</small>';
            }
            echo '</div>';
            echo '<div class="btn-group">';

            // Download button.
            $downloadurl = $doc->get_download_url();
            if ($downloadurl) {
                echo '<a href="' . $downloadurl . '" class="btn btn-sm btn-outline-secondary" target="_blank">' .
                     get_string('download') . '</a>';
            }

            // Action buttons for pending documents.
            if ($doc->status === 'pending') {
                $validateurl = new moodle_url('/local/jobboard/index.php', [
                    'view' => 'review',
                    'applicationid' => $applicationid,
                    'documentid' => $doc->id,
                    'action' => 'validate',
                    'sesskey' => sesskey(),
                ]);
                echo '<a href="' . $validateurl . '" class="btn btn-sm btn-success">' .
                     get_string('approve', 'local_jobboard') . '</a>';

                // Reject with modal.
                echo '<button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#rejectModal' . $doc->id . '">' .
                     get_string('reject', 'local_jobboard') . '</button>';

                // Reject modal.
                echo '<div class="modal fade" id="rejectModal' . $doc->id . '" tabindex="-1">';
                echo '<div class="modal-dialog">';
                echo '<div class="modal-content">';
                echo '<form method="post" action="' . new moodle_url('/local/jobboard/index.php') . '">';
                echo '<input type="hidden" name="view" value="review">';
                echo '<input type="hidden" name="applicationid" value="' . $applicationid . '">';
                echo '<input type="hidden" name="documentid" value="' . $doc->id . '">';
                echo '<input type="hidden" name="action" value="reject">';
                echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
                echo '<div class="modal-header">';
                echo '<h5 class="modal-title">' . get_string('rejectdocument', 'local_jobboard') . '</h5>';
                echo '<button type="button" class="close" data-dismiss="modal">&times;</button>';
                echo '</div>';
                echo '<div class="modal-body">';
                echo '<div class="form-group">';
                echo '<label for="reason">' . get_string('rejectreason', 'local_jobboard') . '</label>';
                echo '<textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>';
                echo '</div>';
                echo '</div>';
                echo '<div class="modal-footer">';
                echo '<button type="button" class="btn btn-secondary" data-dismiss="modal">' . get_string('cancel') . '</button>';
                echo '<button type="submit" class="btn btn-danger">' . get_string('reject', 'local_jobboard') . '</button>';
                echo '</div>';
                echo '</form>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            echo '</div>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
    echo '</div>';
    echo '</div>';

    // Mark as reviewed button (if all documents are reviewed).
    $allreviewed = true;
    foreach ($documents as $doc) {
        if ($doc->status === 'pending') {
            $allreviewed = false;
            break;
        }
    }

    if ($allreviewed && $application->status !== 'docs_validated' && $application->status !== 'docs_rejected') {
        echo '<div class="text-center">';
        $markurl = new moodle_url('/local/jobboard/index.php', [
            'view' => 'review',
            'applicationid' => $applicationid,
            'action' => 'markreviewed',
            'sesskey' => sesskey(),
        ]);
        echo '<a href="' . $markurl . '" class="btn btn-lg btn-success">' .
             get_string('submitreview', 'local_jobboard') . '</a>';
        echo '</div>';
    }
}

echo $OUTPUT->footer();
