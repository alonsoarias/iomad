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
 * Role-based view with organized sections per user capabilities.
 * Uses Mustache templates via renderer for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

// Require capability to manage convocatorias.
require_capability('local/jobboard:createvacancy', $context);

// Parameters.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 25, PARAM_INT);
$status = optional_param('status', '', PARAM_ALPHA);
$action = optional_param('action', '', PARAM_ALPHA);
$convocatoriaid = optional_param('id', 0, PARAM_INT);

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

            // Publish all draft vacancies in this convocatoria.
            $DB->execute("UPDATE {local_jobboard_vacancy}
                          SET status = 'published', timemodified = ?
                          WHERE convocatoriaid = ? AND status = 'draft'",
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

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('manageconvocatorias', 'local_jobboard'));
$PAGE->set_heading(get_string('manageconvocatorias', 'local_jobboard'));

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

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the renderer.
$data = $renderer->prepare_convocatorias_page_data(
    $convocatorias,
    $total,
    $statsCounts,
    $status,
    $page,
    $perpage,
    sesskey()
);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_convocatorias_page($data);
echo $OUTPUT->footer();
