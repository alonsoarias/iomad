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
 * Version information for local_jobboard.
 *
 * Job Board plugin for managing academic vacancies and teacher applications.
 * Designed for adjunct professor recruitment in higher education institutions.
 * Compatible with Moodle 4.1+ and IOMAD multi-tenant architecture.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'local_jobboard';
$plugin->version = 2025121240;
$plugin->requires = 2022112800; // Moodle 4.1 LTS minimum.
$plugin->supported = [401, 405]; // Moodle 4.1 to 4.5.
$plugin->maturity = MATURITY_STABLE;
$plugin->release = '3.2.0';
