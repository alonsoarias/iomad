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
    // Create a category for Job Board.
    $ADMIN->add('localplugins', new admin_category('local_jobboard_category',
        get_string('pluginname', 'local_jobboard')));

    // Create new settings page.
    $settings = new admin_settingpage('local_jobboard', get_string('generalsettings', 'local_jobboard'));

    // Add settings page to the category.
    $ADMIN->add('local_jobboard_category', $settings);

    // Register external admin pages.
    $ADMIN->add('local_jobboard_category', new admin_externalpage(
        'local_jobboard_doctypes',
        get_string('managedoctypes', 'local_jobboard'),
        new moodle_url('/local/jobboard/admin/doctypes.php'),
        'local/jobboard:configure'
    ));

    $ADMIN->add('local_jobboard_category', new admin_externalpage(
        'local_jobboard_templates',
        get_string('emailtemplates', 'local_jobboard'),
        new moodle_url('/local/jobboard/admin/templates.php'),
        'local/jobboard:configure'
    ));

    $ADMIN->add('local_jobboard_category', new admin_externalpage(
        'local_jobboard_exemptions',
        get_string('manageexemptions', 'local_jobboard'),
        new moodle_url('/local/jobboard/admin/exemptions.php'),
        'local/jobboard:configure'
    ));

    $ADMIN->add('local_jobboard_category', new admin_externalpage(
        'local_jobboard_tokens',
        get_string('apitokens', 'local_jobboard'),
        new moodle_url('/local/jobboard/admin/tokens.php'),
        'local/jobboard:manageapitokens'
    ));

    $ADMIN->add('local_jobboard_category', new admin_externalpage(
        'local_jobboard_convocatorias',
        get_string('manageconvocatorias', 'local_jobboard'),
        new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']),
        'local/jobboard:createvacancy'
    ));

    // General settings header.
    $settings->add(new admin_setting_heading(
        'local_jobboard/generalheading',
        get_string('generalsettings', 'local_jobboard'),
        ''
    ));

    // Enable plugin-specific self-registration.
    $settings->add(new admin_setting_configcheckbox(
        'local_jobboard/enable_self_registration',
        get_string('enableselfregistration', 'local_jobboard'),
        get_string('enableselfregistration_desc', 'local_jobboard'),
        1
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

    // ==========================================================================
    // Public page settings.
    // ==========================================================================
    $settings->add(new admin_setting_heading(
        'local_jobboard/publicpageheading',
        get_string('publicpagesettings', 'local_jobboard'),
        get_string('publicpagesettings_desc', 'local_jobboard')
    ));

    // Enable public page.
    $settings->add(new admin_setting_configcheckbox(
        'local_jobboard/enable_public_page',
        get_string('enablepublicpage', 'local_jobboard'),
        get_string('enablepublicpage_desc', 'local_jobboard'),
        1
    ));

    // Public page title.
    $settings->add(new admin_setting_configtext(
        'local_jobboard/public_page_title',
        get_string('publicpagetitle', 'local_jobboard'),
        get_string('publicpagetitle_desc', 'local_jobboard'),
        '',
        PARAM_TEXT
    ));

    // Public page description.
    $settings->add(new admin_setting_configtextarea(
        'local_jobboard/public_page_description',
        get_string('publicpagedescription', 'local_jobboard'),
        get_string('publicpagedescription_desc', 'local_jobboard'),
        ''
    ));

    // Show public link in navigation.
    $settings->add(new admin_setting_configcheckbox(
        'local_jobboard/show_public_nav_link',
        get_string('showpublicnavlink', 'local_jobboard'),
        get_string('showpublicnavlink_desc', 'local_jobboard'),
        1
    ));

    // ==========================================================================
    // Navigation settings.
    // ==========================================================================
    $settings->add(new admin_setting_heading(
        'local_jobboard/navigationheading',
        get_string('navigationsettings', 'local_jobboard'),
        get_string('navigationsettings_desc', 'local_jobboard')
    ));

    // Show in main navigation menu (custom menu items).
    $settings->add(new admin_setting_configcheckbox(
        'local_jobboard/show_in_main_menu',
        get_string('showinmainmenu', 'local_jobboard'),
        get_string('showinmainmenu_desc', 'local_jobboard'),
        1
    ));

    // Main menu item title (can be customized).
    $settings->add(new admin_setting_configtext(
        'local_jobboard/main_menu_title',
        get_string('mainmenutitle', 'local_jobboard'),
        get_string('mainmenutitle_desc', 'local_jobboard'),
        '',
        PARAM_TEXT
    ));

    // ==========================================================================
    // Application limits settings.
    // ==========================================================================
    $settings->add(new admin_setting_heading(
        'local_jobboard/applicationlimitsheading',
        get_string('applicationlimits', 'local_jobboard'),
        get_string('applicationlimits_desc', 'local_jobboard')
    ));

    // Allow multiple applications.
    $settings->add(new admin_setting_configcheckbox(
        'local_jobboard/allow_multiple_applications',
        get_string('allowmultipleapplications', 'local_jobboard'),
        get_string('allowmultipleapplications_desc', 'local_jobboard'),
        1
    ));

    // Maximum active applications.
    $settings->add(new admin_setting_configtext(
        'local_jobboard/max_active_applications',
        get_string('maxactiveapplications', 'local_jobboard'),
        get_string('maxactiveapplications_desc', 'local_jobboard'),
        0,
        PARAM_INT
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

    // ==========================================================================
    // reCAPTCHA settings.
    // ==========================================================================
    $settings->add(new admin_setting_heading(
        'local_jobboard/recaptchaheading',
        get_string('recaptchasettings', 'local_jobboard'),
        get_string('recaptchasettings_desc', 'local_jobboard')
    ));

    // Enable reCAPTCHA.
    $settings->add(new admin_setting_configcheckbox(
        'local_jobboard/recaptcha_enabled',
        get_string('recaptcha_enabled', 'local_jobboard'),
        get_string('recaptcha_enabled_desc', 'local_jobboard'),
        0
    ));

    // reCAPTCHA version.
    $recaptchaversions = [
        'v2' => get_string('recaptcha_v2', 'local_jobboard'),
        'v3' => get_string('recaptcha_v3', 'local_jobboard'),
    ];
    $settings->add(new admin_setting_configselect(
        'local_jobboard/recaptcha_version',
        get_string('recaptcha_version', 'local_jobboard'),
        get_string('recaptcha_version_desc', 'local_jobboard'),
        'v2',
        $recaptchaversions
    ));

    // reCAPTCHA site key.
    $settings->add(new admin_setting_configtext(
        'local_jobboard/recaptcha_sitekey',
        get_string('recaptcha_sitekey', 'local_jobboard'),
        get_string('recaptcha_sitekey_desc', 'local_jobboard'),
        '',
        PARAM_TEXT
    ));

    // reCAPTCHA secret key.
    $settings->add(new admin_setting_configpasswordunmask(
        'local_jobboard/recaptcha_secretkey',
        get_string('recaptcha_secretkey', 'local_jobboard'),
        get_string('recaptcha_secretkey_desc', 'local_jobboard'),
        ''
    ));

    // reCAPTCHA v3 threshold (0.0 - 1.0).
    $settings->add(new admin_setting_configtext(
        'local_jobboard/recaptcha_v3_threshold',
        get_string('recaptcha_v3_threshold', 'local_jobboard'),
        get_string('recaptcha_v3_threshold_desc', 'local_jobboard'),
        '0.5',
        PARAM_FLOAT
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
