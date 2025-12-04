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
 * Settings page for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig) {
    // Create new settings page.
    $settings = new admin_settingpage('local_jobboard', get_string('pluginname', 'local_jobboard'));

    // Add to admin menu.
    $ADMIN->add('localplugins', $settings);

    // General settings header.
    $settings->add(new admin_setting_heading(
        'local_jobboard/generalheading',
        get_string('generalsettings', 'local_jobboard'),
        ''
    ));

    // Institution name.
    $settings->add(new admin_setting_configtext(
        'local_jobboard/institutionname',
        get_string('institutionname', 'local_jobboard'),
        '',
        '',
        PARAM_TEXT
    ));

    // Contact email.
    $settings->add(new admin_setting_configtext(
        'local_jobboard/contactemail',
        get_string('contactemail', 'local_jobboard'),
        '',
        '',
        PARAM_EMAIL
    ));

    // Document settings header.
    $settings->add(new admin_setting_heading(
        'local_jobboard/documentsheading',
        get_string('documentsettings', 'local_jobboard'),
        ''
    ));

    // Max file size (MB).
    $settings->add(new admin_setting_configtext(
        'local_jobboard/maxfilesize',
        get_string('maxfilesize', 'local_jobboard'),
        '',
        '10',
        PARAM_INT
    ));

    // Allowed formats.
    $settings->add(new admin_setting_configtext(
        'local_jobboard/allowedformats',
        get_string('allowedformats', 'local_jobboard'),
        '',
        'pdf,jpg,jpeg,png',
        PARAM_TEXT
    ));

    // EPS max days.
    $settings->add(new admin_setting_configtext(
        'local_jobboard/epsmaxdays',
        get_string('epsmaxdays', 'local_jobboard'),
        '',
        '30',
        PARAM_INT
    ));

    // Pension max days.
    $settings->add(new admin_setting_configtext(
        'local_jobboard/pensionmaxdays',
        get_string('pensionmaxdays', 'local_jobboard'),
        '',
        '30',
        PARAM_INT
    ));

    // Antecedentes max days.
    $settings->add(new admin_setting_configtext(
        'local_jobboard/antecedentesmaxdays',
        get_string('antecedentesmaxdays', 'local_jobboard'),
        '',
        '30',
        PARAM_INT
    ));

    // Security settings header.
    $settings->add(new admin_setting_heading(
        'local_jobboard/securityheading',
        get_string('securitysettings', 'local_jobboard'),
        ''
    ));

    // Enable encryption.
    $settings->add(new admin_setting_configcheckbox(
        'local_jobboard/enableencryption',
        get_string('enableencryption', 'local_jobboard'),
        '',
        0
    ));

    // Data retention days.
    $settings->add(new admin_setting_configtext(
        'local_jobboard/dataretentiondays',
        get_string('dataretentiondays', 'local_jobboard'),
        '',
        '1825',
        PARAM_INT
    ));

    // Enable API.
    $settings->add(new admin_setting_configcheckbox(
        'local_jobboard/enableapi',
        get_string('enableapi', 'local_jobboard'),
        '',
        0
    ));
}
