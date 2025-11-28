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

// Require login.
require_login();

// Check capability.
$context = context_system::instance();
require_capability('report/platform_usage:export', $context);

// Require sesskey.
require_sesskey();

// Get parameters.
$companyid = optional_param('companyid', 0, PARAM_INT);
$datefrom = optional_param('datefrom', strtotime('-30 days midnight'), PARAM_INT);
$dateto = optional_param('dateto', time(), PARAM_INT);
$type = optional_param('type', 'summary', PARAM_ALPHA);
$format = optional_param('format', 'excel', PARAM_ALPHA);

// Create report and exporter instances.
$report = new \report_platform_usage\report($companyid, $datefrom, $dateto);
$exporter = new \report_platform_usage\exporter($report, $type);

// Export.
$exporter->export($format);
