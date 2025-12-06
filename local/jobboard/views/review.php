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
 * Modern redesign with card-based layout and document validation workflow.
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
use local_jobboard\output\ui_helper;

// Require review capability.
require_capability('local/jobboard:reviewdocuments', $context);

// Parameters.
$applicationid = optional_param('applicationid', 0, PARAM_INT);
$documentid = optional_param('documentid', 0, PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 20, PARAM_INT);

// Page setup.
$PAGE->set_title(get_string('reviewdocuments', 'local_jobboard'));
$PAGE->set_heading(get_string('reviewdocuments', 'local_jobboard'));
$PAGE->set_pagelayout('standard');
$PAGE->requires->css('/local/jobboard/styles.css');

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
                $validated = 0;
                foreach ($documents as $doc) {
                    if ($doc->status === 'pending') {
                        $doc->validate($USER->id);
                        $validated++;
                    }
                }
                \core\notification::success(get_string('documentvalidated', 'local_jobboard') . " ({$validated})");
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
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-review');

// If no application selected, show list of applications pending review.
if (!$applicationid) {
    // ============================================================================
    // PAGE HEADER
    // ============================================================================
    $breadcrumbs = [
        get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
        get_string('reviewdocuments', 'local_jobboard') => null,
    ];

    echo ui_helper::page_header(
        get_string('reviewdocuments', 'local_jobboard'),
        $breadcrumbs,
        [[
            'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'myreviews']),
            'label' => get_string('myreviews', 'local_jobboard'),
            'icon' => 'clipboard-list',
            'class' => 'btn btn-outline-primary',
        ]]
    );

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
    // Note: Document status is in local_jobboard_doc_validation table, not in document table.
    // A document is "pending" if it has no validation record or validation status = 'pending'.
    $sql = "SELECT a.*, v.title as vacancy_title, v.code as vacancy_code, v.closedate,
                   u.firstname, u.lastname, u.email,
                   (SELECT COUNT(*) FROM {local_jobboard_document} d WHERE d.applicationid = a.id AND d.issuperseded = 0) as doccount,
                   (SELECT COUNT(*) FROM {local_jobboard_document} d
                    LEFT JOIN {local_jobboard_doc_validation} dv ON dv.documentid = d.id
                    WHERE d.applicationid = a.id AND d.issuperseded = 0
                    AND (dv.id IS NULL OR dv.status = 'pending')
                   ) as pendingcount
            FROM {local_jobboard_application} a
            JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
            JOIN {user} u ON u.id = a.userid
            WHERE {$where}
            ORDER BY a.timecreated ASC";

    $applications = $DB->get_records_sql($sql, $params);
    $total = count($applications);

    // Calculate stats.
    $stats = [
        'total' => 0,
        'pending' => 0,
        'urgent' => 0,
    ];
    foreach ($applications as $app) {
        $stats['total']++;
        $stats['pending'] += (int)$app->pendingcount;
        if ($app->closedate && ($app->closedate - time()) <= 7 * 86400) {
            $stats['urgent']++;
        }
    }

    // ============================================================================
    // STATISTICS ROW
    // ============================================================================
    echo html_writer::start_div('row mb-4');
    echo ui_helper::stat_card(
        (string)$stats['total'],
        get_string('pendingreview', 'local_jobboard'),
        'primary', 'file-alt'
    );
    echo ui_helper::stat_card(
        (string)$stats['pending'],
        get_string('pendingdocuments', 'local_jobboard'),
        'warning', 'clock'
    );
    echo ui_helper::stat_card(
        (string)$stats['urgent'],
        get_string('urgent', 'local_jobboard'),
        'danger', 'exclamation-triangle'
    );
    echo html_writer::end_div();

    // ============================================================================
    // FILTER FORM
    // ============================================================================
    $vacancies = $DB->get_records('local_jobboard_vacancy', ['status' => 'published'], 'code ASC', 'id, code, title');
    $vacancyOptions = [0 => get_string('allvacancies', 'local_jobboard')];
    foreach ($vacancies as $v) {
        $vacancyOptions[$v->id] = format_string($v->code) . ' - ' . format_string($v->title);
    }

    $filterDefinitions = [
        [
            'type' => 'select',
            'name' => 'vacancyid',
            'options' => $vacancyOptions,
            'col' => 'col-md-6',
        ],
    ];

    echo ui_helper::filter_form(
        (new moodle_url('/local/jobboard/index.php'))->out(false),
        $filterDefinitions,
        ['vacancyid' => $vacancyid],
        ['view' => 'review']
    );

    // ============================================================================
    // APPLICATIONS LIST
    // ============================================================================
    if (empty($applications)) {
        echo ui_helper::empty_state(
            get_string('nodocumentstoreview', 'local_jobboard'),
            'check-circle'
        );
    } else {
        echo html_writer::start_div('row');

        foreach ($applications as $app) {
            $isUrgent = $app->closedate && ($app->closedate - time()) <= 7 * 86400;
            $hasPendingDocs = (int)$app->pendingcount > 0;

            echo html_writer::start_div('col-lg-6 mb-4');
            echo html_writer::start_div('card h-100 shadow-sm' . ($isUrgent ? ' border-danger' : ''));

            // Card header.
            $headerClass = $hasPendingDocs ? 'bg-warning' : 'bg-success';
            echo html_writer::start_div('card-header d-flex justify-content-between align-items-center ' . $headerClass);

            echo html_writer::start_div();
            echo html_writer::tag('span',
                format_string($app->vacancy_code),
                ['class' => 'badge badge-light mr-2']
            );
            echo html_writer::tag('span',
                get_string('status_' . $app->status, 'local_jobboard'),
                ['class' => 'text-white small']
            );
            echo html_writer::end_div();

            if ($isUrgent) {
                echo html_writer::tag('span',
                    '<i class="fa fa-exclamation-triangle mr-1"></i>' . get_string('urgent', 'local_jobboard'),
                    ['class' => 'badge badge-danger']
                );
            }

            echo html_writer::end_div();

            // Card body.
            echo html_writer::start_div('card-body');

            // Applicant info.
            echo html_writer::tag('h5',
                '<i class="fa fa-user text-muted mr-2"></i>' . fullname($app),
                ['class' => 'card-title']
            );
            echo html_writer::tag('p',
                '<i class="fa fa-envelope text-muted mr-2"></i>' .
                html_writer::tag('a', $app->email, ['href' => 'mailto:' . $app->email]),
                ['class' => 'small mb-2']
            );

            // Vacancy title.
            echo html_writer::div(
                '<i class="fa fa-briefcase text-muted mr-2"></i>' . format_string($app->vacancy_title),
                'small text-muted mb-3'
            );

            // Document counts.
            echo html_writer::start_div('d-flex align-items-center');
            echo html_writer::tag('span',
                '<i class="fa fa-file-alt mr-1"></i>' . get_string('documents', 'local_jobboard') . ': ',
                ['class' => 'mr-2']
            );
            echo html_writer::tag('span', $app->doccount, ['class' => 'badge badge-secondary mr-2']);
            if ($app->pendingcount > 0) {
                echo html_writer::tag('span',
                    $app->pendingcount . ' ' . get_string('pending', 'local_jobboard'),
                    ['class' => 'badge badge-warning']
                );
            } else {
                echo html_writer::tag('span',
                    get_string('allvalidated', 'local_jobboard'),
                    ['class' => 'badge badge-success']
                );
            }
            echo html_writer::end_div();

            // Date submitted.
            echo html_writer::div(
                '<i class="fa fa-calendar-alt text-muted mr-2"></i>' .
                get_string('datesubmitted', 'local_jobboard') . ': ' .
                userdate($app->timecreated, get_string('strftimedate', 'langconfig')),
                'small text-muted mt-2'
            );

            echo html_writer::end_div(); // card-body

            // Card footer.
            echo html_writer::start_div('card-footer bg-white d-flex justify-content-between');

            $reviewUrl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'review',
                'applicationid' => $app->id,
            ]);
            echo html_writer::link($reviewUrl,
                '<i class="fa fa-search mr-1"></i>' . get_string('reviewdocuments', 'local_jobboard'),
                ['class' => 'btn btn-primary btn-sm']
            );

            // Quick view application link.
            $appUrl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'application',
                'id' => $app->id,
            ]);
            echo html_writer::link($appUrl,
                '<i class="fa fa-eye"></i>',
                ['class' => 'btn btn-outline-secondary btn-sm', 'title' => get_string('viewapplication', 'local_jobboard')]
            );

            echo html_writer::end_div(); // card-footer

            echo html_writer::end_div(); // card
            echo html_writer::end_div(); // col
        }

        echo html_writer::end_div(); // row
    }
} else {
    // ============================================================================
    // SINGLE APPLICATION REVIEW
    // ============================================================================
    $application = new application($applicationid);

    if (!$application->id) {
        throw new moodle_exception('error:invalidapplication', 'local_jobboard');
    }

    $vacancy = new vacancy($application->vacancyid);
    $applicant = $DB->get_record('user', ['id' => $application->userid]);
    $documents = document::get_by_application($applicationid);

    // Count document statuses.
    $docStats = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
    foreach ($documents as $doc) {
        if (isset($docStats[$doc->status])) {
            $docStats[$doc->status]++;
        }
    }
    $totalDocs = count($documents);

    // Page header.
    $breadcrumbs = [
        get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
        get_string('reviewdocuments', 'local_jobboard') => new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
        fullname($applicant) => null,
    ];

    echo ui_helper::page_header(
        get_string('reviewapplication', 'local_jobboard'),
        $breadcrumbs,
        [[
            'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancyid]),
            'label' => get_string('back'),
            'icon' => 'arrow-left',
            'class' => 'btn btn-outline-secondary',
        ]]
    );

    // ============================================================================
    // STATISTICS ROW
    // ============================================================================
    echo html_writer::start_div('row mb-4');
    echo ui_helper::stat_card((string)$totalDocs, get_string('documents', 'local_jobboard'), 'primary', 'file-alt');
    echo ui_helper::stat_card((string)$docStats['approved'], get_string('docstatus:approved', 'local_jobboard'), 'success', 'check-circle');
    echo ui_helper::stat_card((string)$docStats['rejected'], get_string('docstatus:rejected', 'local_jobboard'), 'danger', 'times-circle');
    echo ui_helper::stat_card((string)$docStats['pending'], get_string('docstatus:pending', 'local_jobboard'), 'warning', 'clock');
    echo html_writer::end_div();

    // ============================================================================
    // TWO COLUMN LAYOUT
    // ============================================================================
    echo html_writer::start_div('row');

    // LEFT COLUMN - Documents.
    echo html_writer::start_div('col-lg-8');

    // Documents card.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header d-flex justify-content-between align-items-center bg-primary text-white');
    echo html_writer::tag('h5',
        '<i class="fa fa-folder-open mr-2"></i>' . get_string('documentlist', 'local_jobboard'),
        ['class' => 'mb-0']
    );

    // Bulk actions.
    if ($docStats['pending'] > 0) {
        $validateAllUrl = new moodle_url('/local/jobboard/index.php', [
            'view' => 'review',
            'applicationid' => $applicationid,
            'action' => 'validateall',
            'sesskey' => sesskey(),
        ]);
        echo html_writer::link($validateAllUrl,
            '<i class="fa fa-check-double mr-1"></i>' . get_string('validateall', 'local_jobboard'),
            ['class' => 'btn btn-success btn-sm']
        );
    }
    echo html_writer::end_div();

    echo html_writer::start_div('card-body p-0');

    if (empty($documents)) {
        echo html_writer::div(
            ui_helper::empty_state(get_string('nodocumentstoreview', 'local_jobboard'), 'file'),
            'p-4'
        );
    } else {
        echo html_writer::start_div('list-group list-group-flush');

        foreach ($documents as $doc) {
            // Status styling.
            $statusConfig = [
                'pending' => ['bg' => 'bg-warning', 'icon' => 'clock', 'text' => 'text-warning'],
                'approved' => ['bg' => 'bg-success', 'icon' => 'check-circle', 'text' => 'text-success'],
                'rejected' => ['bg' => 'bg-danger', 'icon' => 'times-circle', 'text' => 'text-danger'],
            ];
            $config = $statusConfig[$doc->status] ?? $statusConfig['pending'];

            echo html_writer::start_div('list-group-item');
            echo html_writer::start_div('d-flex w-100 justify-content-between align-items-start');

            // Document info.
            echo html_writer::start_div('flex-grow-1');

            echo html_writer::start_div('d-flex align-items-center mb-2');
            echo html_writer::tag('i', '', ['class' => 'fa fa-' . $config['icon'] . ' fa-lg ' . $config['text'] . ' mr-3']);
            echo html_writer::start_div();
            echo html_writer::tag('h6', format_string($doc->get_doctype_name()), ['class' => 'mb-0']);
            echo html_writer::tag('small', format_string($doc->filename), ['class' => 'text-muted']);
            echo html_writer::end_div();
            echo html_writer::end_div();

            // Status badge.
            echo html_writer::tag('span',
                get_string('docstatus:' . $doc->status, 'local_jobboard'),
                ['class' => 'badge ' . str_replace('bg-', 'badge-', $config['bg']) . ' mb-2']
            );

            // Reviewer info.
            if ($doc->reviewerid && $doc->status !== 'pending') {
                $reviewer = $DB->get_record('user', ['id' => $doc->reviewerid]);
                echo html_writer::div(
                    '<i class="fa fa-user-check text-muted mr-1"></i>' .
                    get_string('reviewedby', 'local_jobboard') . ': ' .
                    fullname($reviewer) . ' - ' .
                    userdate($doc->reviewedat, get_string('strftimedatetime', 'langconfig')),
                    'small text-muted'
                );
            }

            // Rejection reason.
            if ($doc->status === 'rejected' && !empty($doc->rejectreason)) {
                echo html_writer::div(
                    '<i class="fa fa-exclamation-circle text-danger mr-1"></i>' .
                    get_string('rejectreason', 'local_jobboard') . ': ' .
                    format_string($doc->rejectreason),
                    'small text-danger mt-1'
                );
            }

            echo html_writer::end_div();

            // Actions.
            echo html_writer::start_div('btn-group-vertical ml-3');

            // Download button.
            $downloadUrl = $doc->get_download_url();
            if ($downloadUrl) {
                echo html_writer::link($downloadUrl,
                    '<i class="fa fa-download"></i>',
                    ['class' => 'btn btn-sm btn-outline-primary', 'target' => '_blank', 'title' => get_string('download')]
                );
            }

            // Action buttons for pending documents.
            if ($doc->status === 'pending') {
                $validateUrl = new moodle_url('/local/jobboard/index.php', [
                    'view' => 'review',
                    'applicationid' => $applicationid,
                    'documentid' => $doc->id,
                    'action' => 'validate',
                    'sesskey' => sesskey(),
                ]);
                echo html_writer::link($validateUrl,
                    '<i class="fa fa-check"></i>',
                    ['class' => 'btn btn-sm btn-success', 'title' => get_string('approve', 'local_jobboard')]
                );

                // Reject button with modal trigger.
                echo html_writer::tag('button',
                    '<i class="fa fa-times"></i>',
                    [
                        'class' => 'btn btn-sm btn-danger',
                        'data-toggle' => 'modal',
                        'data-target' => '#rejectModal' . $doc->id,
                        'title' => get_string('reject', 'local_jobboard'),
                    ]
                );
            }

            echo html_writer::end_div(); // btn-group

            echo html_writer::end_div(); // d-flex
            echo html_writer::end_div(); // list-group-item

            // Reject modal.
            if ($doc->status === 'pending') {
                echo '
                <div class="modal fade" id="rejectModal' . $doc->id . '" tabindex="-1">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <form method="post" action="' . new moodle_url('/local/jobboard/index.php') . '">
                                <input type="hidden" name="view" value="review">
                                <input type="hidden" name="applicationid" value="' . $applicationid . '">
                                <input type="hidden" name="documentid" value="' . $doc->id . '">
                                <input type="hidden" name="action" value="reject">
                                <input type="hidden" name="sesskey" value="' . sesskey() . '">
                                <div class="modal-header bg-danger text-white">
                                    <h5 class="modal-title">
                                        <i class="fa fa-exclamation-triangle mr-2"></i>' .
                                        get_string('rejectdocument', 'local_jobboard') . '
                                    </h5>
                                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                                </div>
                                <div class="modal-body">
                                    <p class="text-muted">' . format_string($doc->get_doctype_name()) . '</p>
                                    <div class="form-group">
                                        <label for="reason' . $doc->id . '" class="font-weight-bold">' .
                                            get_string('rejectreason', 'local_jobboard') . ' *
                                        </label>
                                        <textarea name="reason" id="reason' . $doc->id . '" class="form-control" rows="3" required
                                            placeholder="' . get_string('rejectreason_placeholder', 'local_jobboard') . '"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">' .
                                        get_string('cancel') . '
                                    </button>
                                    <button type="submit" class="btn btn-danger">
                                        <i class="fa fa-times mr-1"></i>' . get_string('reject', 'local_jobboard') . '
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>';
            }
        }

        echo html_writer::end_div(); // list-group
    }

    echo html_writer::end_div(); // card-body
    echo html_writer::end_div(); // card

    echo html_writer::end_div(); // col-lg-8

    // RIGHT COLUMN - Application info.
    echo html_writer::start_div('col-lg-4');

    // Applicant info card.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::tag('div',
        '<i class="fa fa-user mr-2"></i>' . get_string('applicantinfo', 'local_jobboard'),
        ['class' => 'card-header bg-secondary text-white']
    );
    echo html_writer::start_div('card-body');

    echo html_writer::tag('h5', fullname($applicant), ['class' => 'card-title']);
    echo html_writer::tag('p',
        '<i class="fa fa-envelope text-muted mr-2"></i>' .
        html_writer::tag('a', $applicant->email, ['href' => 'mailto:' . $applicant->email]),
        ['class' => 'mb-2']
    );
    echo html_writer::div(
        '<i class="fa fa-calendar-alt text-muted mr-2"></i>' .
        get_string('dateapplied', 'local_jobboard') . ': ' .
        userdate($application->timecreated, get_string('strftimedate', 'langconfig')),
        'small text-muted'
    );

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Vacancy info card.
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::tag('div',
        '<i class="fa fa-briefcase mr-2"></i>' . get_string('vacancy', 'local_jobboard'),
        ['class' => 'card-header bg-info text-white']
    );
    echo html_writer::start_div('card-body');

    echo html_writer::tag('span', $vacancy->code, ['class' => 'badge badge-secondary mb-2']);
    echo html_writer::tag('h5', format_string($vacancy->title), ['class' => 'card-title']);

    // Application status.
    $statusClass = 'badge-secondary';
    if (in_array($application->status, ['docs_validated', 'selected'])) {
        $statusClass = 'badge-success';
    } else if (in_array($application->status, ['docs_rejected', 'rejected'])) {
        $statusClass = 'badge-danger';
    } else if ($application->status === 'under_review') {
        $statusClass = 'badge-warning';
    } else if ($application->status === 'submitted') {
        $statusClass = 'badge-info';
    }

    echo html_writer::div(
        get_string('status', 'local_jobboard') . ': ' .
        html_writer::tag('span',
            get_string('status_' . $application->status, 'local_jobboard'),
            ['class' => 'badge ' . $statusClass]
        ),
        'mb-2'
    );

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Review progress card.
    echo html_writer::start_div('card shadow-sm mb-4 border-primary');
    echo html_writer::tag('div',
        '<i class="fa fa-tasks mr-2"></i>' . get_string('reviewprogress', 'local_jobboard'),
        ['class' => 'card-header bg-primary text-white']
    );
    echo html_writer::start_div('card-body');

    // Progress bar.
    $progressPct = $totalDocs > 0 ? round(($docStats['approved'] / $totalDocs) * 100) : 0;
    $rejectPct = $totalDocs > 0 ? round(($docStats['rejected'] / $totalDocs) * 100) : 0;

    echo html_writer::start_div('progress mb-3', ['style' => 'height: 25px;']);
    echo html_writer::div($docStats['approved'], 'progress-bar bg-success', [
        'role' => 'progressbar',
        'style' => 'width: ' . $progressPct . '%',
    ]);
    echo html_writer::div($docStats['rejected'], 'progress-bar bg-danger', [
        'role' => 'progressbar',
        'style' => 'width: ' . $rejectPct . '%',
    ]);
    echo html_writer::end_div();

    echo html_writer::start_div('small text-muted');
    echo html_writer::tag('span', $docStats['approved'] . ' ' . get_string('docstatus:approved', 'local_jobboard'),
        ['class' => 'text-success mr-3']);
    echo html_writer::tag('span', $docStats['rejected'] . ' ' . get_string('docstatus:rejected', 'local_jobboard'),
        ['class' => 'text-danger mr-3']);
    echo html_writer::tag('span', $docStats['pending'] . ' ' . get_string('docstatus:pending', 'local_jobboard'),
        ['class' => 'text-warning']);
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();

    // Complete review button.
    $allReviewed = ($docStats['pending'] === 0);
    if ($allReviewed && !in_array($application->status, ['docs_validated', 'docs_rejected'])) {
        echo html_writer::start_div('card shadow-sm mb-4 border-success');
        echo html_writer::start_div('card-body text-center');

        echo html_writer::tag('p',
            '<i class="fa fa-check-circle fa-2x text-success mb-2"></i><br>' .
            get_string('alldocsreviewed', 'local_jobboard'),
            ['class' => 'text-success font-weight-bold']
        );

        $markUrl = new moodle_url('/local/jobboard/index.php', [
            'view' => 'review',
            'applicationid' => $applicationid,
            'action' => 'markreviewed',
            'sesskey' => sesskey(),
        ]);
        echo html_writer::link($markUrl,
            '<i class="fa fa-clipboard-check mr-2"></i>' . get_string('submitreview', 'local_jobboard'),
            ['class' => 'btn btn-success btn-lg btn-block']
        );

        echo html_writer::end_div();
        echo html_writer::end_div();
    }

    echo html_writer::end_div(); // col-lg-4

    echo html_writer::end_div(); // row
}

echo html_writer::end_div(); // local-jobboard-review

// Additional styles.
echo html_writer::tag('style', '
.local-jobboard-review .list-group-item {
    transition: background-color 0.2s ease;
}
.local-jobboard-review .list-group-item:hover {
    background-color: #f8f9fa;
}
.local-jobboard-review .card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.local-jobboard-review .card:hover {
    transform: translateY(-2px);
}
');

echo $OUTPUT->footer();
