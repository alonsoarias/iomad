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
 * Platform Usage Report export handler.
 *
 * @package   report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

// Get parameters.
$courseid = optional_param('courseid', 0, PARAM_INT);
$companyid = optional_param('companyid', 0, PARAM_INT);
$datefrom = optional_param('datefrom', strtotime('-30 days midnight'), PARAM_INT);
$dateto = optional_param('dateto', time(), PARAM_INT);
$type = optional_param('type', 'summary', PARAM_ALPHA);
$format = optional_param('format', 'excel', PARAM_ALPHA);

// Determine context based on course ID.
if ($courseid > 0) {
    $course = get_course($courseid);
    require_login($course);
    $context = context_course::instance($courseid);
} else {
    require_login();
    $context = context_system::instance();
}

// Check capability.
require_capability('report/platform_usage:export', $context);

// Require sesskey.
require_sesskey();

// Reset company filter if IOMAD is not installed.
if (!\report_platform_usage\report::is_iomad_installed()) {
    $companyid = 0;
}

// Create report instance (disable cache for export to get fresh data).
$report = new \report_platform_usage\report($companyid, $datefrom, $dateto, false, $courseid);

// Export based on format.
if ($format === 'excel' && $type === 'summary') {
    // Use enhanced Excel exporter with charts for summary.
    $exporter = new \report_platform_usage\excel_exporter($report);
    $exporter->export();
} else {
    // Use standard exporter for CSV or specific data types.
    $exporter = new \report_platform_usage\exporter($report, $type);
    $exporter->export($format);
}
