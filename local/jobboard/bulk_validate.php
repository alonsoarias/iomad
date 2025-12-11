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
 * Modern redesign with consistent UX pattern using ui_helper.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\bulk_validator;
use local_jobboard\output\ui_helper;

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

// Calculate stats.
$totalPending = 0;
$typeCount = count($pendingbytype);
foreach ($pendingbytype as $dt) {
    $totalPending += $dt->count;
}

echo $OUTPUT->header();

echo html_writer::start_div('local-jobboard-bulk-validate');

// ============================================================================
// PAGE HEADER WITH ACTIONS
// ============================================================================
echo ui_helper::page_header(
    get_string('bulkvalidation', 'local_jobboard'),
    [],
    [
        [
            'url' => new moodle_url('/local/jobboard/index.php'),
            'label' => get_string('dashboard', 'local_jobboard'),
            'icon' => 'tachometer-alt',
            'class' => 'btn btn-outline-secondary',
        ],
        [
            'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'review']),
            'label' => get_string('reviewapplications', 'local_jobboard'),
            'icon' => 'clipboard-check',
            'class' => 'btn btn-outline-primary',
        ],
    ]
);

// ============================================================================
// STATS CARDS
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card((string)$totalPending, get_string('pendingvalidation', 'local_jobboard'), 'warning', 'clock');
echo ui_helper::stat_card((string)$typeCount, get_string('documenttypes', 'local_jobboard'), 'primary', 'folder');
echo html_writer::end_div();

// ============================================================================
// FILTERS
// ============================================================================
$vacancyOptions = [0 => get_string('allvacancies', 'local_jobboard')];
foreach ($vacancies as $v) {
    $vacancyOptions[$v->id] = format_string($v->code . ' - ' . $v->title);
}

$typeOptions = ['' => get_string('selecttype', 'local_jobboard')];
foreach ($pendingbytype as $dt) {
    $typename = get_string('doctype_' . $dt->documenttype, 'local_jobboard');
    $typeOptions[$dt->documenttype] = $typename . ' (' . $dt->count . ')';
}

$filterDefinitions = [
    [
        'type' => 'select',
        'name' => 'vacancyid',
        'label' => get_string('vacancy', 'local_jobboard'),
        'options' => $vacancyOptions,
        'col' => 'col-md-4',
    ],
    [
        'type' => 'select',
        'name' => 'documenttype',
        'label' => get_string('documenttype', 'local_jobboard'),
        'options' => $typeOptions,
        'col' => 'col-md-4',
    ],
];

echo ui_helper::filter_form(
    (new moodle_url('/local/jobboard/bulk_validate.php'))->out(false),
    $filterDefinitions,
    ['vacancyid' => $vacancyid, 'documenttype' => $documenttype],
    []
);

// ============================================================================
// PENDING BY TYPE SUMMARY
// ============================================================================
if (!empty($pendingbytype)) {
    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5', '<i class="fa fa-folder-open text-warning mr-2"></i>' .
        get_string('pendingbytype', 'local_jobboard'), ['class' => 'mb-0']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    echo html_writer::start_div('row');
    foreach ($pendingbytype as $dt) {
        $typename = get_string('doctype_' . $dt->documenttype, 'local_jobboard');
        $isSelected = ($documenttype === $dt->documenttype);

        echo html_writer::start_div('col-lg-3 col-md-4 col-sm-6 mb-3');
        echo html_writer::start_tag('a', [
            'href' => (new moodle_url('/local/jobboard/bulk_validate.php', [
                'vacancyid' => $vacancyid,
                'documenttype' => $dt->documenttype,
            ]))->out(false),
            'class' => 'text-decoration-none',
        ]);
        echo html_writer::start_div('card h-100 ' . ($isSelected ? 'border-primary bg-light' : ''));
        echo html_writer::start_div('card-body text-center');
        echo html_writer::tag('h3', $dt->count, ['class' => 'mb-1 ' . ($isSelected ? 'text-primary' : 'text-warning')]);
        echo html_writer::tag('p', $typename, ['class' => 'mb-0 small text-muted']);
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_tag('a');
        echo html_writer::end_div();
    }
    echo html_writer::end_div();

    echo html_writer::end_div();
    echo html_writer::end_div();
}

// ============================================================================
// DOCUMENT LIST
// ============================================================================
if (!empty($documenttype)) {
    $documents = bulk_validator::get_pending_documents_by_type($documenttype, $vacancyid ?: null);
    $typename = get_string('doctype_' . $documenttype, 'local_jobboard');

    echo html_writer::start_div('card shadow-sm mb-4');
    echo html_writer::start_div('card-header bg-white d-flex justify-content-between align-items-center');
    echo html_writer::tag('h5', '<i class="fa fa-file-alt text-primary mr-2"></i>' . $typename, ['class' => 'mb-0']);
    echo html_writer::tag('span', count($documents) . ' ' . get_string('documents', 'local_jobboard'),
        ['class' => 'badge badge-warning']);
    echo html_writer::end_div();
    echo html_writer::start_div('card-body');

    if (empty($documents)) {
        echo ui_helper::empty_state(get_string('nodocumentspending', 'local_jobboard'), 'check-circle');
    } else {
        echo html_writer::start_tag('form', ['method' => 'post', 'action' => $PAGE->url]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'sesskey', 'value' => sesskey()]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'vacancyid', 'value' => $vacancyid]);
        echo html_writer::empty_tag('input', ['type' => 'hidden', 'name' => 'documenttype', 'value' => $documenttype]);

        // Select all checkbox.
        echo html_writer::start_div('custom-control custom-checkbox mb-3');
        echo html_writer::empty_tag('input', [
            'type' => 'checkbox',
            'class' => 'custom-control-input',
            'id' => 'selectall',
        ]);
        echo html_writer::tag('label',
            get_string('selectall'),
            ['class' => 'custom-control-label font-weight-bold', 'for' => 'selectall']
        );
        echo html_writer::end_div();

        // Documents table.
        $headers = [
            '',
            get_string('applicant', 'local_jobboard'),
            get_string('vacancy', 'local_jobboard'),
            get_string('filename', 'local_jobboard'),
            get_string('uploaded', 'local_jobboard'),
            get_string('actions'),
        ];

        $rows = [];
        foreach ($documents as $doc) {
            $checkbox = html_writer::empty_tag('input', [
                'type' => 'checkbox',
                'name' => 'documents[]',
                'value' => $doc->id,
                'class' => 'doc-checkbox',
            ]);

            $applicantInfo = html_writer::tag('strong', format_string($doc->firstname . ' ' . $doc->lastname)) .
                html_writer::tag('br') .
                html_writer::tag('small', $doc->email, ['class' => 'text-muted']);

            // View document link.
            $docobj = \local_jobboard\document::get($doc->id);
            $downloadurl = $docobj ? $docobj->get_download_url() : null;

            $actions = '';
            if ($downloadurl) {
                $actions .= html_writer::link($downloadurl,
                    '<i class="fa fa-eye"></i>',
                    ['class' => 'btn btn-sm btn-outline-primary mr-1', 'target' => '_blank', 'title' => get_string('view')]
                );
            }
            $actions .= html_writer::link(
                new moodle_url('/local/jobboard/validate_document.php', ['id' => $doc->id]),
                '<i class="fa fa-check"></i>',
                ['class' => 'btn btn-sm btn-outline-success', 'title' => get_string('validate', 'local_jobboard')]
            );

            $rows[] = [
                $checkbox,
                $applicantInfo,
                format_string($doc->vacancy_code),
                html_writer::tag('code', format_string($doc->filename)),
                userdate($doc->timecreated, '%Y-%m-%d %H:%M'),
                $actions,
            ];
        }

        echo ui_helper::data_table($headers, $rows);

        // Bulk action panel.
        echo html_writer::start_div('card mt-4 bg-light');
        echo html_writer::start_div('card-header');
        echo html_writer::tag('h6', '<i class="fa fa-tasks mr-2"></i>' .
            get_string('bulkactions', 'local_jobboard'), ['class' => 'mb-0']);
        echo html_writer::end_div();
        echo html_writer::start_div('card-body');

        echo html_writer::start_div('row');

        // Approve selected.
        echo html_writer::start_div('col-md-6 mb-3 mb-md-0');
        echo html_writer::start_div('card border-success h-100');
        echo html_writer::start_div('card-body');
        echo html_writer::tag('h6', '<i class="fa fa-check text-success mr-2"></i>' .
            get_string('approveselected', 'local_jobboard'), ['class' => 'card-title']);
        echo html_writer::start_div('form-group');
        echo html_writer::tag('textarea', '', [
            'name' => 'notes',
            'class' => 'form-control',
            'rows' => '2',
            'placeholder' => get_string('optionalnotes', 'local_jobboard'),
        ]);
        echo html_writer::end_div();
        echo html_writer::tag('button',
            '<i class="fa fa-check mr-2"></i>' . get_string('approveselected', 'local_jobboard'),
            ['type' => 'submit', 'name' => 'action', 'value' => 'validate', 'class' => 'btn btn-success']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();

        // Reject selected.
        echo html_writer::start_div('col-md-6');
        echo html_writer::start_div('card border-danger h-100');
        echo html_writer::start_div('card-body');
        echo html_writer::tag('h6', '<i class="fa fa-times text-danger mr-2"></i>' .
            get_string('rejectselected', 'local_jobboard'), ['class' => 'card-title']);
        echo html_writer::start_div('form-group');
        echo html_writer::start_tag('select', ['name' => 'rejectreason', 'class' => 'form-control']);
        echo html_writer::tag('option', get_string('selectreason', 'local_jobboard'), ['value' => '']);
        $reasons = ['illegible', 'expired', 'incomplete', 'wrongtype', 'mismatch'];
        foreach ($reasons as $reason) {
            echo html_writer::tag('option',
                get_string('rejectreason_' . $reason, 'local_jobboard'),
                ['value' => $reason]
            );
        }
        echo html_writer::end_tag('select');
        echo html_writer::end_div();
        echo html_writer::tag('button',
            '<i class="fa fa-times mr-2"></i>' . get_string('rejectselected', 'local_jobboard'),
            ['type' => 'submit', 'name' => 'action', 'value' => 'reject', 'class' => 'btn btn-danger']
        );
        echo html_writer::end_div();
        echo html_writer::end_div();
        echo html_writer::end_div();

        echo html_writer::end_div(); // row
        echo html_writer::end_div(); // card-body
        echo html_writer::end_div(); // card

        echo html_writer::end_tag('form');

        // JavaScript for select all.
        echo html_writer::script('
            document.getElementById("selectall").addEventListener("change", function() {
                var checkboxes = document.querySelectorAll(".doc-checkbox");
                var self = this;
                checkboxes.forEach(function(cb) {
                    cb.checked = self.checked;
                });
            });
        ');
    }

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
    new moodle_url('/local/jobboard/assign_reviewer.php'),
    '<i class="fa fa-user-plus mr-2"></i>' . get_string('assignreviewer', 'local_jobboard'),
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

echo html_writer::end_div(); // local-jobboard-bulk-validate

echo $OUTPUT->footer();
