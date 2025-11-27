<?php
// This file is part of Moodle - https://moodle.org/
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
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Plugin version and other meta-data are defined here.
 *
 * @package     theme_inteb
 * @copyright   2025 Vicerrectoría Académica ISER <vicerrectoria@iser.edu.co>
 * @author      Alonso Arias <soporteplataformas@iser.edu.co>
 * @license     https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$plugin->component = 'theme_inteb';
$plugin->version = 2025112716; // Simplified courseindex styles with ISER colors.
$plugin->requires = 2022041200; // Versión mínima de Moodle requerida.
$plugin->release   = '4.6.2';
$plugin->dependencies = [
    'theme_remui' => 2024102300, // Dependencia del tema padre primario (RemUI).
    'theme_iomad' => 2024100745, // Dependencia del tema padre secundario (IOMAD).
    'local_iomad' => 2024090401, // Dependencia del plugin local IOMAD (requerido por theme_iomad).
];
