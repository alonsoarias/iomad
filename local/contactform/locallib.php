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
 * Local plugin "contactform" - Library
 *
 * @package     local
 * @subpackage  local_contactform
 * @copyright   2015 Ing. Pablo A Pico, Colombia <pabloapico@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_CONTACTFORM_INTERNAL') || defined('MOODLE_INTERNAL') || die();


// Include Forms API
require_once("$CFG->libdir/formslib.php");

class simplecontact_form extends moodleform {
    //Add elements to form
    public function definition() {
        global $CFG,$USER;
        $local_contactform_config = get_config('local_contactform');

        $mform = $this->_form; // Don't forget the underscore!

        // Add elements to your form
        $mform->addElement('html', '<div class="sc-info">'.$local_contactform_config->pre_form.'</div>');

        // Fields only for guests depending on the setting
        if ( !($local_contactform_config->skip_user_contact_info=='1' && isloggedin()) ) {

          $mform->addElement('text', 'name', get_string('name','local_contactform'), 'maxlength="30" size="25" ');
          $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="25" ');
          $mform->addElement('text', 'phone', get_string('phone'), 'maxlength="30" size="25" ');

          //set types
          $mform->setType('name', PARAM_NOTAGS);
          $mform->setType('email', PARAM_EMAIL);
          $mform->setType('phone', PARAM_NOTAGS);

          //set rules
          $mform->addRule('name', get_string('missingname'), 'required', null, 'server');
          $mform->addRule('email', get_string('missingemail'), 'required', null, 'server');
          $mform->addRule('email', get_string('invalidemail'), 'email', null, 'server');

          //set defaults
          if (isloggedin()) {
            $mform->setDefault('name',$USER->firstname." ".$USER->lastname." (".$USER->username.")");
            $mform->setDefault('email',$USER->email);
            $mform->setDefault('phone',$USER->phone1.(empty($USER->phone2)?"":", ".$USER->phone2));

          }


        } else {
          // Inform the user that contact details will be sente
          $mform->addElement('html', '<div class="sc-info"><em>'.get_string('contact_details_will_be_sent','local_contactform').'<em></div>');

        }

        $mform->addElement('text', 'subject', get_string('subject','local_contactform'), 'maxlength="100" size="25" ');
        $mform->addElement('textarea', 'message', get_string('message','local_contactform'), 'wrap="virtual" rows="4" cols="36" class="sc-resizable"');

        //set types
        $mform->setType('subject', PARAM_NOTAGS);

        //set rules
        $mform->addRule('subject', get_string('missingsubject','local_contactform'), 'required', null, 'server');
        $mform->addRule('message', get_string('missingmessage','local_contactform'), 'required', null, 'server');
        //$mform->addRule('name', get_string('invalidname','local_contactform'), 'lettersonly', null, 'server');

        // Set default value by using a passed parameter
        // try to get user's email?
        //$mform->setDefault('email',$this->_customdata['email']);

        if (!empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey)) {
            //recaptcha is enabled
            //$mform->addElement('recaptcha', 'recaptcha_field_name', $attributes);
        }

        //normally you use add_action_buttons instead of this code
        $buttonarray=array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('submit'));
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->closeHeaderBefore('buttonar');
        //$this->add_action_buttons();

    }
    //Custom validation should be added here
    function validation($data, $files) {
        return array();
    }

    public function display() {

        $local_contactform_config = get_config('local_contactform');
        if (empty($local_contactform_config->emailto)) {
            echo "<div class='contactform_error'>"."Error: Required configuration missing"."</div>";
            return false;
        }
        return parent::display();
    }
}