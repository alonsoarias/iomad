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
 * Version details
 *
 * @package    local_notifystudentgraded
 * @author     Promwebsoft
 * @copyright  2019 Promwebsoft
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */


defined('MOODLE_INTERNAL') || die();

$string['notifystudentgradedsubjetc'] = 'Has recibido una calificación.';
$string['notifystudentgradedbody'] = 'Hola <strong>{$a->studentname}</strong>, has recibido una calificación en la evaluación <strong>{$a->evaluacion}</strong>.  <br /><br />Puede revisar el resultado en el siguiente enlace: {$a->quizurl}<br /><br />

Enviado desde \'{$a->sitename}\'.
{$a->platurl}';
$string['pluginname'] = 'Notificar por email al estudiante cuando recibe una calificación manual en un cuestionario.';