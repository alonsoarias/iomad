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
 * Browse convocatorias view for applicants.
 *
 * This is the primary entry point for users to browse available job calls.
 * Shows open convocatorias with their vacancy counts and allows navigation
 * to view vacancies within each convocatoria.
 * Uses Mustache templates via renderer for clean separation of concerns.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../lib.php');

// Parameters.
$page = optional_param('page', 0, PARAM_INT);
$perpage = optional_param('perpage', 12, PARAM_INT);
$status = optional_param('status', 'open', PARAM_ALPHA);

// Page setup.
$PAGE->set_pagelayout('standard');
$PAGE->set_title(get_string('convocatorias', 'local_jobboard'));
$PAGE->set_heading(get_string('convocatorias', 'local_jobboard'));

// Check IOMAD installation.
$isiomad = local_jobboard_is_iomad_installed();

// Build query.
$where = ['1=1'];
$params = [];

// Filter by status (default: open convocatorias).
if ($status) {
    $where[] = 'c.status = :status';
    $params['status'] = $status;
}

// For non-admin users, respect tenant filtering.
if ($isiomad && !has_capability('local/jobboard:viewallvacancies', $context)) {
    $usercompanyid = local_jobboard_get_user_companyid();
    if ($usercompanyid) {
        $where[] = '(c.companyid IS NULL OR c.companyid = :companyid)';
        $params['companyid'] = $usercompanyid;
    }
}

// Get total count.
$wheresql = implode(' AND ', $where);
$countsql = "SELECT COUNT(*) FROM {local_jobboard_convocatoria} c WHERE $wheresql";
$total = $DB->count_records_sql($countsql, $params);

// Get convocatorias with vacancy counts.
$sql = "SELECT c.*,
               (SELECT COUNT(*) FROM {local_jobboard_vacancy} v
                WHERE v.convocatoriaid = c.id AND v.status = 'published') as vacancy_count
          FROM {local_jobboard_convocatoria} c
         WHERE $wheresql
         ORDER BY c.startdate DESC, c.name ASC";

$convocatorias = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

// Count by status for filters.
$statusCounts = [
    'open' => $DB->count_records('local_jobboard_convocatoria', ['status' => 'open']),
    'closed' => $DB->count_records('local_jobboard_convocatoria', ['status' => 'closed']),
];

// Get the renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Prepare template data using the renderer.
$data = $renderer->prepare_browse_convocatorias_page_data(
    $convocatorias,
    $total,
    $statusCounts,
    $status,
    $page,
    $perpage
);

// Output the page.
echo $OUTPUT->header();
echo $renderer->render_browse_convocatorias_page($data);
echo $OUTPUT->footer();
