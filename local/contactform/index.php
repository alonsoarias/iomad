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
 * Local plugin "contactform" - View page
 *
 * @package     local
 * @subpackage  local_contactform
 * @copyright   2015 Ing. Pablo A Pico, Colombia <pabloapico@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// defined('MOODLE_INTERNAL') || die(); - Must not be called because this script is called from outside moodle

define('MOODLE_CONTACTFORM_INTERNAL', 1);

// Include config.php
require_once(dirname(__FILE__) .'/../../config.php');

// Include lib.php
require_once(dirname(__FILE__) . '/lib.php');
require_once(dirname(__FILE__).'/locallib.php');     // we extend this library here

// Globals
global $PAGE;

//load our CSS
$PAGE->requires->css('/local/contactform/styles.css');

// Get plugin config
$local_contactform_config = get_config('local_contactform');

// Require login if form is not public
if (!$local_contactform_config->is_public) {
    require_login();
}

// Put together absolute document paths based on requested page and current language
$lang = current_language();

// Set page context
$PAGE->set_context(context_system::instance());

// Set page layout
$PAGE->set_pagelayout('standard');

// Set page title
$PAGE->set_title($local_contactform_config->contactformpagetitle);

// Set page heading
$PAGE->set_heading($local_contactform_config->contactformheading);

// Set url
$PAGE->set_url('/local/contactform');

// Set page navbar
$PAGE->navbar->add(get_string('contact_breadcrumb','local_contactform'));

echo $OUTPUT->header();

// Print html code
echo "<section class='simple-contact'>";
echo "<h1>".$local_contactform_config->contactformheading."</h1>";

//Instantiate simplehtml_form
$mform = new simplecontact_form( null );

//Form processing and displaying is done here
if ($mform->is_cancelled()) {
    //Handle form cancel operation, if cancel button is present on form
} else if ($formdata = $mform->get_data()) {
  //In this case you process validated data. $mform->get_data() returns data posted in form.
  if (local_concatform_send($formdata,array('emailto'=>$local_contactform_config->emailto, 'emailtoname'=>'emailtoname'))) {
     local_concatform_show_thanks();
  } else {
     local_concatform_show_error();
  }
} else {
  // this branch is executed if the form is submitted but the data doesn't validate and the form should be redisplayed
  // or on the first display of the form.

  //Set default data (if any)
  //$mform->set_data($toform);

  //displays the form
  $mform->display();
}

echo '<div class="sc-info">'.$local_contactform_config->post_form.'</div>';
echo "</section>";
echo $OUTPUT->footer();
