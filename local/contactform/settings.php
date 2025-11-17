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
 * Local plugin "contactform" - Settings
 *
 * @package     local
 * @subpackage  local_contactform
 * @copyright   2015 Ing. Pablo A Pico, Colombia <pabloapico@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

// Include lib.php
require_once(dirname(__FILE__) . '/lib.php');

global $CFG, $PAGE;

if ($hassiteconfig) {

    // New settings page
    $page = new admin_settingpage('contactform', get_string('pluginname', 'local_contactform'));

    // Link to the form
    $link = rtrim($CFG->wwwroot, '/').'/local/contactform';
    $html = '<a href="'.$link.'" target="_blank">'.$link.'</a>';
    $page->add(new admin_setting_heading('local_contactform/help_info', '', get_string('help_info', 'local_contactform').$html));

    $page->add(new admin_setting_configcheckbox('local_contactform/is_public', get_string('is_public', 'local_contactform'), get_string('is_public_desc', 'local_contactform'), '1'));
    $page->add(new admin_setting_configtext('local_contactform/contactformheading', get_string('contactformheading', 'local_contactform'), get_string('contactformheading_desc', 'local_contactform'), get_string('contactformheading_default', 'local_contactform'),PARAM_TEXT));
    $page->add(new admin_setting_configtext('local_contactform/contactformpagetitle', get_string('contactformpagetitle', 'local_contactform'), get_string('contactformpagetitle_desc', 'local_contactform'),get_string('contactformheading_default', 'local_contactform'),PARAM_TEXT));
    $page->add(new admin_setting_configcheckbox('local_contactform/skip_user_contact_info', get_string('skip_user_contact_info', 'local_contactform'), get_string('skip_user_contact_info_desc', 'local_contactform'), '1'));

    // EmailTo
    $page->add(new admin_setting_configtext('local_contactform/emailto', get_string('emailto', 'local_contactform'), get_string('emailto_desc', 'local_contactform'),$CFG->supportemail,PARAM_EMAIL));
    $page->add(new admin_setting_configtext('local_contactform/contactform_thanks', get_string('contactform_thanks', 'local_contactform'), get_string('contactform_thanks_desc', 'local_contactform'),get_string('contactform_thanks_default', 'local_contactform'),PARAM_TEXT ));
    $page->add(new admin_setting_confightmleditor('local_contactform/contactform_thanks_html', get_string('contactform_thanks_html', 'local_contactform'), get_string('contactform_thanks_html_desc', 'local_contactform'),"" ));

    // Additional optional params
    $page->add(new admin_setting_confightmleditor('local_contactform/pre_form', get_string('pre_form', 'local_contactform'), get_string('pre_form_desc', 'local_contactform'), get_string('pre_form_default', 'local_contactform') ));
    $page->add(new admin_setting_confightmleditor('local_contactform/post_form', get_string('post_form', 'local_contactform'), get_string('post_form_desc', 'local_contactform'),"" ));


    // Get previously configured plugin config
    $local_contactform_config = get_config('local_contactform');


    // Add settings page to navigation tree
    $ADMIN->add('localplugins', $page);
}
