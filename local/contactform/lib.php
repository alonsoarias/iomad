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

// defined('MOODLE_INTERNAL') || die(); - Must not be called because this script is called from outside moodle
defined('MOODLE_CONTACTFORM_INTERNAL') || defined('MOODLE_INTERNAL') || die();

/**
 * Sends the email or message
 *
 * @param string $data Array with Form data
 * @param string $params Params for the message
 * @return void
 */
function local_concatform_send($data, $params) {

    // Globals
    global $SITE, $USER;

    // Plugin settings
    $local_contactform_config = get_config('local_contactform');

    $data->subject = $data->subject." - ".$local_contactform_config->contactformheading." - ".$SITE->shortname;

    // Set user details when user is logged in and parameter is set
    if ( $local_contactform_config->skip_user_contact_info=='1' && isloggedin() ) {

        $data->name = $USER->firstname." ".$USER->lastname." (".$USER->username.")";
        $data->email = $USER->email;
        $data->phone =  $USER->phone1.(empty($USER->phone2)?"":", ".$USER->phone2);

    }

    //Build message
    $message = $data->message."\n\n-----";
    $message = $message."\n".get_string('subject','local_contactform').": ".$data->subject;
    $message = $message."\n".get_string('name','local_contactform').": ".$data->name;
    $message = $message."\n".get_string('email').": ".$data->email;
    $message = $message."\n".get_string('phone').": ".$data->phone;
    $data->message=$message;

    // in the future check if send to user (moodle messaging API can be used)
    return local_concatform_send_email($data, $params);
}

/**
 * Sends a simple email
 *
 * @param string $data Array with Form data
 * @param string $params Params for the message
 * @return void
 */
function local_concatform_send_email($data, $params) {

    // Set defaults
    $wordwrapwidth = 79;

    global $CFG;
    extract(get_object_vars($data));
    extract($params);

    if (empty($emailto)) {
        debugging('Email to not set', DEBUG_DEVELOPER);
        return false;
    }

    if (empty($email) ) {
        debugging('No email to reply to', DEBUG_DEVELOPER);
        return false;
    }

    if (defined('BEHAT_SITE_RUNNING')) {
        // Fake email sending in behat.
        return true;
    }

    if (!empty($CFG->noemailever)) {
        // Hidden setting for development sites, set in config.php if needed.
        debugging('Not sending email due to $CFG->noemailever config setting', DEBUG_NORMAL);
        return true;
    }


    if (!validate_email($emailto)) {
        // We can not send emails to invalid addresses - it might create security issue or confuse the mailer.
        debugging("local_contact_form_send-email: Email set in 'Send to' is invalid! Not sending.");
        return false;
    }


    $mail = get_mailer();

    if (!empty($mail->SMTPDebug)) {
        echo '<pre>' . "\n";
    }

    $temprecipients = array();
    $tempreplyto = array();

    $supportuser = core_user::get_support_user();

    // Make up an email address for handling bounces.
    if (!empty($CFG->handlebounces)) {
        $modargs = 'B'.base64_encode(pack('V', $user->id)).substr(md5($user->email), 0, 16);
        $mail->Sender = generate_email_processing_address(0, $modargs);
    } else {
        $mail->Sender = $supportuser->email;
    }

    $replyto = $email;
    $replytoname = $name;
    $tempreplyto[] = array($replyto, $replytoname);

    $mail->From     = $CFG->noreplyaddress;
    $mail->FromName = $name;

    $mail->Subject = substr($subject, 0, 900);

    $temprecipients[] = array($emailto, $name);

    // Set word wrap.
    $mail->WordWrap = $wordwrapwidth;

    //if (!empty($from->customheaders)) {
//        // Add custom headers.
//        if (is_array($from->customheaders)) {
//            foreach ($from->customheaders as $customheader) {
//                $mail->addCustomHeader($customheader);
//            }
//        } else {
//            $mail->addCustomHeader($from->customheaders);
//        }
//    }

    //if (!empty($from->priority)) {
//        $mail->Priority = $from->priority;
//    }


    $message_html=nl2br($message);
    $message_text=$message;

    // We always use html email.
    $mail->isHTML(true);
    $mail->Encoding = 'quoted-printable';
    $mail->Body    =  $message_html;
    $mail->AltBody =  "\n$message_text\n";

    // Check if the email should be sent in an other charset then the default UTF-8.
    if ((!empty($CFG->sitemailcharset) || !empty($CFG->allowusermailcharset))) {

        // Use the defined site mail charset or eventually the one preferred by the recipient.
        $charset = $CFG->sitemailcharset;
        //if (!empty($CFG->allowusermailcharset)) {
//            if ($useremailcharset = get_user_preferences('mailcharset', '0', $user->id)) {
//                $charset = $useremailcharset;
//            }
//        }

        // Convert all the necessary strings if the charset is supported.
        $charsets = get_list_of_charsets();
        unset($charsets['UTF-8']);
        if (in_array($charset, $charsets)) {
            $mail->CharSet  = $charset;
            $mail->FromName = core_text::convert($mail->FromName, 'utf-8', strtolower($charset));
            $mail->Subject  = core_text::convert($mail->Subject, 'utf-8', strtolower($charset));
            $mail->Body     = core_text::convert($mail->Body, 'utf-8', strtolower($charset));
            $mail->AltBody  = core_text::convert($mail->AltBody, 'utf-8', strtolower($charset));

            foreach ($temprecipients as $key => $values) {
                $temprecipients[$key][1] = core_text::convert($values[1], 'utf-8', strtolower($charset));
            }
            foreach ($tempreplyto as $key => $values) {
                $tempreplyto[$key][1] = core_text::convert($values[1], 'utf-8', strtolower($charset));
            }
        }
    }

    foreach ($temprecipients as $values) {
        $mail->addAddress($values[0], $values[1]);
    }
    foreach ($tempreplyto as $values) {
        $mail->addReplyTo($values[0], $values[1]);
    }

    if ($mail->send()) {
        //set_send_count($user);
        if (!empty($mail->SMTPDebug)) {
            echo '</pre>';
        }
        return true;
    } else {
        // Trigger event for failing to send email.
        //$event = \core\event\email_failed::create(array(
//            'context' => context_system::instance(),
//            'userid' => $from->id,
//            'relateduserid' => $user->id,
//            'other' => array(
//                'subject' => $subject,
//                'message' => $messagetext,
//                'errorinfo' => $mail->ErrorInfo
//            )
//        ));
//        $event->trigger();
        if (CLI_SCRIPT) {
            mtrace('Error: local_contactform_send_email(): '.$mail->ErrorInfo);
        }
        if (!empty($mail->SMTPDebug)) {
            echo '</pre>';
        }
        local_concatform_show_thanks();
        return false;
    }
}

/**
 * Sends email to user
 *
 * @param string $data Array with Form data
 * @param string $params Params for the message
 * @return void
 */
function local_concatform_send_to_user($data, $userto) {

    // Get plugin config
    $local_contactform_config = get_config('local_contactform');

    $message = new \core\message\message();
    $message->component = 'moodle';
    $message->name = 'instantmessage';
    //$message->userfrom = $USER;
    $message->userto = $userto;
    $message->email=$local_contactform_config->emailto;
    $message->subject = 'message subject 1';
    $message->fullmessage = 'message body';
    $message->fullmessageformat = FORMAT_MARKDOWN;
    $message->fullmessagehtml = '<p>message body</p>';
    $message->smallmessage = 'small message';
    $message->notification = '0';
    $message->contexturl = 'http://GalaxyFarFarAway.com';
    $message->contexturlname = 'Context name';
    $message->replyto = $data->email;
    $content = array('*' => array('header' => ' test ', 'footer' => ' test ')); // Extra content for specific processor
    $message->set_additional_content('email', $content);

    $messageid = message_send($message);
}

/**
 * Shows the thank you page after form is sent
 *
 * @return void
 */
function local_concatform_show_thanks() {
  $local_contactform_config = get_config('local_contactform');
  echo "<div class='contactform_thankyou'><div class='thanks-message'>".$local_contactform_config->contactform_thanks."</div><div class='thanks-content'>".$local_contactform_config->contactform_thanks_html."</div></div>";
}

/**
 * Displays error message
 *
 * @return void
 */
function local_concatform_show_error() {
  echo "<div class='contactform_error'>"."Error..."."</div>";
}
