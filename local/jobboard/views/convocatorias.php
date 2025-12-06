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
 * Convocatorias (Calls) listing and management view for local_jobboard.
 *
 * Modern redesign using ui_helper for consistent styling.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

// Require capability to manage convocatorias.
require_capability('local/jobboard:createvacancy', $context);

// Parameters.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 25, PARAM_INT);
$status = optional_param('status', '', PARAM_ALPHA);
$action = optional_param('action', '', PARAM_ALPHA);
$convocatoriaid = optional_param('id', 0, PARAM_INT);

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('manageconvocatorias', 'local_jobboard'));
$PAGE->set_heading(get_string('manageconvocatorias', 'local_jobboard'));
$PAGE->requires->css('/local/jobboard/styles.css');

// Base URL for this page.
$baseurl = new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']);

// Handle actions that modify data (require sesskey).
if ($action && $convocatoriaid && confirm_sesskey()) {
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid], '*', MUST_EXIST);

    switch ($action) {
        case 'delete':
            // Only allow delete for draft or archived convocatorias.
            if (in_array($convocatoria->status, ['draft', 'archived'])) {
                // Count vacancies to log.
                $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoriaid]);

                // Unlink vacancies from this convocatoria (don't delete them).
                $DB->set_field('local_jobboard_vacancy', 'convocatoriaid', null, ['convocatoriaid' => $convocatoriaid]);

                // Delete the convocatoria.
                $DB->delete_records('local_jobboard_convocatoria', ['id' => $convocatoriaid]);

                // Audit log.
                \local_jobboard\audit::log('convocatoria_deleted', 'convocatoria', $convocatoriaid, [
                    'code' => $convocatoria->code,
                    'name' => $convocatoria->name,
                    'previous_status' => $convocatoria->status,
                    'vacancies_unlinked' => $vacancycount,
                ]);

                redirect($baseurl, get_string('convocatoriadeleted', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
            } else {
                redirect($baseurl, get_string('error:cannotdeleteconvocatoria', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
            }
            break;

        case 'open':
            // Check if it has vacancies.
            $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoriaid]);
            if ($vacancycount == 0) {
                redirect($baseurl, get_string('error:convocatoriahasnovacancies', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
            }

            $previousstatus = $convocatoria->status;

            // Update status and publish all vacancies.
            $convocatoria->status = 'open';
            $convocatoria->timemodified = time();
            $convocatoria->modifiedby = $USER->id;
            $DB->update_record('local_jobboard_convocatoria', $convocatoria);

            // Publish all draft vacancies in this convocatoria and sync dates.
            $DB->execute("UPDATE {local_jobboard_vacancy}
                          SET status = 'published', opendate = ?, closedate = ?, timemodified = ?
                          WHERE convocatoriaid = ? AND status = 'draft' AND isextemporaneous = 0",
                [$convocatoria->startdate, $convocatoria->enddate, time(), $convocatoriaid]);

            // For extemporaneous vacancies, only change status (keep their custom dates).
            $DB->execute("UPDATE {local_jobboard_vacancy}
                          SET status = 'published', timemodified = ?
                          WHERE convocatoriaid = ? AND status = 'draft' AND isextemporaneous = 1",
                [time(), $convocatoriaid]);

            // Audit log.
            \local_jobboard\audit::log('convocatoria_opened', 'convocatoria', $convocatoriaid, [
                'code' => $convocatoria->code,
                'name' => $convocatoria->name,
                'previous_status' => $previousstatus,
                'vacancies_published' => $vacancycount,
                'startdate' => $convocatoria->startdate,
                'enddate' => $convocatoria->enddate,
            ]);

            redirect($baseurl, get_string('convocatoriaopened', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
            break;

        case 'close':
            $previousstatus = $convocatoria->status;

            $convocatoria->status = 'closed';
            $convocatoria->timemodified = time();
            $convocatoria->modifiedby = $USER->id;
            $DB->update_record('local_jobboard_convocatoria', $convocatoria);

            // Close all vacancies in this convocatoria.
            $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoriaid]);
            $DB->set_field('local_jobboard_vacancy', 'status', 'closed', ['convocatoriaid' => $convocatoriaid]);

            // Audit log.
            \local_jobboard\audit::log('convocatoria_closed', 'convocatoria', $convocatoriaid, [
                'code' => $convocatoria->code,
                'name' => $convocatoria->name,
                'previous_status' => $previousstatus,
                'vacancies_closed' => $vacancycount,
            ]);

            redirect($baseurl, get_string('convocatoriaclosedmsg', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
            break;

        case 'archive':
            $previousstatus = $convocatoria->status;

            $convocatoria->status = 'archived';
            $convocatoria->timemodified = time();
            $convocatoria->modifiedby = $USER->id;
            $DB->update_record('local_jobboard_convocatoria', $convocatoria);

            // Audit log.
            \local_jobboard\audit::log('convocatoria_archived', 'convocatoria', $convocatoriaid, [
                'code' => $convocatoria->code,
                'name' => $convocatoria->name,
                'previous_status' => $previousstatus,
            ]);

            redirect($baseurl, get_string('convocatoriaarchived', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
            break;

        case 'reopen':
            // Can only reopen closed convocatorias.
            if ($convocatoria->status !== 'closed') {
                redirect($baseurl, get_string('error:cannotreopenconvocatoria', 'local_jobboard'), null, \core\output\notification::NOTIFY_ERROR);
            }

            $previousstatus = $convocatoria->status;

            $convocatoria->status = 'open';
            $convocatoria->timemodified = time();
            $convocatoria->modifiedby = $USER->id;
            $DB->update_record('local_jobboard_convocatoria', $convocatoria);

            // Reopen all closed vacancies in this convocatoria.
            $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoriaid, 'status' => 'closed']);
            $DB->execute("UPDATE {local_jobboard_vacancy}
                          SET status = 'published', timemodified = ?
                          WHERE convocatoriaid = ? AND status = 'closed'",
                [time(), $convocatoriaid]);

            // Audit log.
            \local_jobboard\audit::log('convocatoria_reopened', 'convocatoria', $convocatoriaid, [
                'code' => $convocatoria->code,
                'name' => $convocatoria->name,
                'previous_status' => $previousstatus,
                'vacancies_reopened' => $vacancycount,
            ]);

            redirect($baseurl, get_string('convocatoriareopened', 'local_jobboard'), null, \core\output\notification::NOTIFY_SUCCESS);
            break;
    }
}

// Check IOMAD installation.
$isiomad = local_jobboard_is_iomad_installed();

// Build filters.
$where = ['1=1'];
$params = [];

if ($status) {
    $where[] = 'status = :status';
    $params['status'] = $status;
}

// For non-admin users, filter by their company.
if ($isiomad && !has_capability('local/jobboard:viewallvacancies', $context)) {
    $usercompanyid = local_jobboard_get_user_companyid();
    if ($usercompanyid) {
        $where[] = '(companyid IS NULL OR companyid = :companyid)';
        $params['companyid'] = $usercompanyid;
    }
}

// Get total count.
$wheresql = implode(' AND ', $where);
$total = $DB->count_records_select('local_jobboard_convocatoria', $wheresql, $params);

// Get convocatorias.
$convocatorias = $DB->get_records_select(
    'local_jobboard_convocatoria',
    $wheresql,
    $params,
    'status ASC, startdate DESC',
    '*',
    $page * $perpage,
    $perpage
);

// Get statistics.
$statsQuery = "SELECT status, COUNT(*) as count
               FROM {local_jobboard_convocatoria}
               WHERE " . $wheresql . "
               GROUP BY status";
$statsResults = $DB->get_records_sql($statsQuery, $params);
$statsCounts = ['draft' => 0, 'open' => 0, 'closed' => 0, 'archived' => 0];
foreach ($statsResults as $row) {
    $statsCounts[$row->status] = (int) $row->count;
}

echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-convocatorias');

// ============================================================================
// PAGE HEADER
// ============================================================================
$breadcrumbs = [
    get_string('dashboard', 'local_jobboard') => new moodle_url('/local/jobboard/index.php'),
    get_string('manageconvocatorias', 'local_jobboard') => null,
];

$addurl = new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'action' => 'add']);

echo ui_helper::page_header(
    get_string('manageconvocatorias', 'local_jobboard'),
    $breadcrumbs,
    [[
        'url' => $addurl,
        'label' => get_string('addconvocatoria', 'local_jobboard'),
        'icon' => 'plus',
        'class' => 'btn btn-primary',
    ]]
);

// ============================================================================
// HELP TEXT
// ============================================================================
echo html_writer::start_div('alert alert-info border-left-info d-flex align-items-center mb-4');
echo html_writer::tag('i', '', ['class' => 'fa fa-info-circle mr-3 fa-lg text-info']);
echo html_writer::tag('div', get_string('convocatoriahelp', 'local_jobboard'));
echo html_writer::end_div();

// ============================================================================
// STATISTICS ROW
// ============================================================================
echo html_writer::start_div('row mb-4');
echo ui_helper::stat_card(
    (string) $total,
    get_string('totalconvocatorias', 'local_jobboard'),
    'primary', 'calendar-alt'
);
echo ui_helper::stat_card(
    (string) $statsCounts['draft'],
    get_string('convocatoria_status_draft', 'local_jobboard'),
    'secondary', 'edit'
);
echo ui_helper::stat_card(
    (string) $statsCounts['open'],
    get_string('convocatoria_status_open', 'local_jobboard'),
    'success', 'check-circle'
);
echo ui_helper::stat_card(
    (string) $statsCounts['closed'],
    get_string('convocatoria_status_closed', 'local_jobboard'),
    'warning', 'lock'
);
echo html_writer::end_div();

// ============================================================================
// FILTER FORM
// ============================================================================
$filterDefinitions = [
    [
        'type' => 'select',
        'name' => 'status',
        'options' => [
            '' => get_string('allstatuses', 'local_jobboard'),
            'draft' => get_string('convocatoria_status_draft', 'local_jobboard'),
            'open' => get_string('convocatoria_status_open', 'local_jobboard'),
            'closed' => get_string('convocatoria_status_closed', 'local_jobboard'),
            'archived' => get_string('convocatoria_status_archived', 'local_jobboard'),
        ],
        'col' => 'col-md-4',
    ],
];

echo ui_helper::filter_form(
    (new moodle_url('/local/jobboard/index.php'))->out(false),
    $filterDefinitions,
    ['status' => $status],
    ['view' => 'convocatorias']
);

// ============================================================================
// RESULTS INFO
// ============================================================================
$showing = new stdClass();
$showing->from = $total > 0 ? ($page * $perpage) + 1 : 0;
$showing->to = min(($page + 1) * $perpage, $total);
$showing->total = $total;
echo html_writer::tag('p', get_string('showing', 'local_jobboard', $showing),
    ['class' => 'text-muted small mb-3']);

// ============================================================================
// CONVOCATORIAS TABLE / CARDS
// ============================================================================
if (empty($convocatorias)) {
    echo ui_helper::empty_state(
        get_string('noconvocatorias', 'local_jobboard'),
        'calendar-alt',
        [
            'url' => $addurl,
            'label' => get_string('addconvocatoria', 'local_jobboard'),
            'class' => 'btn btn-primary',
        ]
    );
} else {
    $headers = [
        get_string('convocatoriacode', 'local_jobboard'),
        get_string('convocatorianame', 'local_jobboard'),
        get_string('period', 'local_jobboard'),
        get_string('vacancies', 'local_jobboard'),
        get_string('convocatoriastatus', 'local_jobboard'),
        get_string('actions'),
    ];

    $rows = [];
    foreach ($convocatorias as $c) {
        $row = [];

        // Code.
        $row[] = html_writer::tag('code', s($c->code), ['class' => 'font-weight-bold']);

        // Name with edit link.
        $editurl = new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'id' => $c->id]);
        $row[] = html_writer::link($editurl, s($c->name), ['class' => 'font-weight-medium']);

        // Period (dates).
        $period = html_writer::start_div('small');
        $period .= html_writer::tag('i', '', ['class' => 'fa fa-calendar-alt text-muted mr-1']);
        $period .= userdate($c->startdate, get_string('strftimedate', 'langconfig'));
        $period .= html_writer::tag('span', ' - ', ['class' => 'mx-1']);
        $period .= userdate($c->enddate, get_string('strftimedate', 'langconfig'));
        $period .= html_writer::end_div();
        $row[] = $period;

        // Vacancy count.
        $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $c->id]);
        if ($vacancycount > 0) {
            $vacanciesurl = new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $c->id]);
            $row[] = html_writer::link($vacanciesurl, $vacancycount, ['class' => 'badge badge-info']);
        } else {
            $row[] = html_writer::tag('span', '0', ['class' => 'badge badge-secondary']);
        }

        // Status badge.
        $row[] = ui_helper::status_badge($c->status, 'convocatoria');

        // Actions.
        $actions = [];

        // Edit.
        $actions[] = [
            'url' => $editurl,
            'icon' => 't/edit',
            'title' => get_string('edit'),
            'class' => 'btn-outline-primary',
        ];

        // Add vacancy (for draft convocatorias).
        if ($c->status === 'draft') {
            $addvacancyurl = new moodle_url('/local/jobboard/edit.php', ['convocatoriaid' => $c->id]);
            $actions[] = [
                'url' => $addvacancyurl,
                'icon' => 't/add',
                'title' => get_string('addvacancy', 'local_jobboard'),
                'class' => 'btn-outline-success',
            ];
        }

        // View vacancies (if has any).
        if ($vacancycount > 0) {
            $actions[] = [
                'url' => new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $c->id]),
                'icon' => 'i/viewsection',
                'title' => get_string('viewvacancies', 'local_jobboard'),
                'class' => 'btn-outline-info',
            ];
        }

        // Status change actions based on current status.
        if ($c->status === 'draft') {
            $openurl = new moodle_url($baseurl, ['action' => 'open', 'id' => $c->id, 'sesskey' => sesskey()]);
            $actions[] = [
                'url' => $openurl,
                'label' => get_string('openconvocatoria', 'local_jobboard'),
                'class' => 'btn-success',
                'confirm' => get_string('confirmopenconvocatoria', 'local_jobboard'),
            ];
        } elseif ($c->status === 'open') {
            $closeurl = new moodle_url($baseurl, ['action' => 'close', 'id' => $c->id, 'sesskey' => sesskey()]);
            $actions[] = [
                'url' => $closeurl,
                'label' => get_string('closeconvocatoria', 'local_jobboard'),
                'class' => 'btn-warning',
                'confirm' => get_string('confirmcloseconvocatoria', 'local_jobboard'),
            ];
        } elseif ($c->status === 'closed') {
            // Reopen button.
            $reopenurl = new moodle_url($baseurl, ['action' => 'reopen', 'id' => $c->id, 'sesskey' => sesskey()]);
            $actions[] = [
                'url' => $reopenurl,
                'label' => get_string('reopenconvocatoria', 'local_jobboard'),
                'class' => 'btn-success',
                'confirm' => get_string('confirmreopenconvocatoria', 'local_jobboard'),
            ];
            // Archive button.
            $archiveurl = new moodle_url($baseurl, ['action' => 'archive', 'id' => $c->id, 'sesskey' => sesskey()]);
            $actions[] = [
                'url' => $archiveurl,
                'label' => get_string('archiveconvocatoria', 'local_jobboard'),
                'class' => 'btn-dark',
                'confirm' => get_string('confirmarchiveconvocatoria', 'local_jobboard'),
            ];
        }

        // Delete (only for draft or archived).
        if (in_array($c->status, ['draft', 'archived'])) {
            $deleteurl = new moodle_url($baseurl, ['action' => 'delete', 'id' => $c->id, 'sesskey' => sesskey()]);
            $actions[] = [
                'url' => $deleteurl,
                'icon' => 't/delete',
                'title' => get_string('delete'),
                'class' => 'btn-outline-danger',
                'confirm' => get_string('confirmdeletevconvocatoria', 'local_jobboard'),
            ];
        }

        $row[] = ui_helper::action_buttons($actions);

        $rows[] = $row;
    }

    echo ui_helper::data_table($headers, $rows);
}

// ============================================================================
// PAGINATION
// ============================================================================
if ($total > $perpage) {
    $paginationurl = new moodle_url('/local/jobboard/index.php', [
        'view' => 'convocatorias',
        'status' => $status,
        'perpage' => $perpage,
    ]);
    echo $OUTPUT->paging_bar($total, $page, $perpage, $paginationurl);
}

echo html_writer::end_div(); // local-jobboard-convocatorias

echo $OUTPUT->footer();
