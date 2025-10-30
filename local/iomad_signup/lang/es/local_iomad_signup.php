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
 * Plugin language strings
 *
 * @package   local_iomad_signup
 * @copyright 2021 Derick Turner
 * @author    Derick Turner
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$string['authenticationtypes'] = 'Seleccionar tipos de autenticación';
$string['authenticationtypes_desc'] = 'Estos son los tipos de autenticación que se pueden utilizar para asignar automáticamente a un usuario a una empresa.';
$string['autoenrol'] = 'Matricular usuario automáticamente';
$string['autoenrol_help'] = 'Al seleccionarlo, se matriculará automáticamente a los usuarios nuevos en los cursos sin licencia o de auto matrícula asignados a la empresa.';
$string['autoenrol_unassigned'] = 'Matricular automáticamente cursos no asignados';
$string['autoenrol_unassigned_help'] = 'Al seleccionarlo, se matriculará automáticamente a los usuarios nuevos en los cursos sin licencia o de auto matrícula que no estén asignados a ninguna empresa.';
$string['choosepassword'] = 'Crear usuario nuevo';
$string['company'] = 'Empresa predeterminada a la que se asignan los usuarios';
$string['configcompany'] = 'Esta es la empresa a la que se asignará el usuario una vez que haya completado el proceso de registro si no se define otra empresa a través del formulario de registro o del dominio de correo.';
$string['configrole'] = 'Este es el rol que se otorgará al usuario cuando haya completado el proceso de registro.';
$string['emailasusernamehelp'] = 'Introduzca su dirección de correo electrónico. Será su nombre de usuario.';
$string['emaildomaindoesntmatch'] = 'Su dominio de correo electrónico no está en la lista de dominios aceptados para esta empresa.';
$string['enable'] = 'Habilitar';
$string['enable_help'] = 'Cuando está habilitado, se asignará una empresa a los usuarios nuevos al crearlos.';
$string['logininfo'] = 'Complete el formulario siguiente para crear un usuario nuevo. Se enviará un correo electrónico a la dirección indicada para verificar la cuenta y permitir el acceso.';
$string['pluginname'] = 'Registro de IOMAD';
$string['privacy:metadata'] = 'El plugin de registro local de IOMAD solo muestra datos almacenados en otros lugares.';
$string['role'] = 'Rol que se asignará';
$string['showinstructions'] = 'Mostrar las instrucciones de auto registro en la página de acceso';
$string['showinstructions_help'] = 'De forma predeterminada, Moodle muestra las instrucciones de auto registro en la página de acceso cuando la auto matrícula está habilitada. Esto permite ocultarlas.';
$string['useemail'] = 'Forzar que el correo sea el nombre de usuario';
$string['useemail_help'] = 'Al seleccionarlo se eliminará la opción de que el usuario seleccione su propio nombre de usuario. En su lugar se utilizará su dirección de correo electrónico.';
