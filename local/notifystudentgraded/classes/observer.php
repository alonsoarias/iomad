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

/**
 * Graded student notification event observers.
 */
class local_notifystudentgraded_observer {
    /**
     * Event processor - user created
     *
     * @param \core\event\user_graded $event
     * @return bool
     */
    public static function activity_graded(\core\event\user_graded $event) {
        global $DB, $CFG;
		
		$reflection = new ReflectionClass($event);
		$property = $reflection->getProperty('grade');
		$property->setAccessible(true);
		$objet = $property->getValue($event);
		$iteminstance = $objet->grade_item->iteminstance;
		
		// We dont want this for all activities so we validate if it is a quiz or we bypass
		if ($objet->grade_item->itemmodule !== 'quiz') {
            return true;
        }
		
		// Now detect if it was manual graded or bypass this
		$params1 = array('quizid' => $iteminstance);
		$typeofgradesel = "SELECT qa.behaviour 
						FROM {quiz_attempts} quiza JOIN im3l_question_usages qu ON qu.id = quiza.uniqueid 
						JOIN {question_attempts} qa ON qa.questionusageid = qu.id 
						JOIN {question_attempt_steps} qas ON qas.questionattemptid = qa.id 
						LEFT JOIN {question_attempt_step_data} qasd ON qasd.attemptstepid = qas.id 
						WHERE quiza.quiz = :quizid ORDER BY qa.id limit 1";
						
		$tg = $DB->get_record_sql($typeofgradesel,$params1);
		
		if( $tg->behaviour != 'manualgraded' ) {
			return true;
		}
		
		$data = array();
		$data['evaluacion'] = $objet->grade_item->itemname;
		
		$site = get_site();
		
		$user = $DB->get_record('user', array('id' => $event->relateduserid));
		$data['studentname'] = $user->firstname;
		
		// Build quiz link here ****
		$courseid = $objet->grade_item->courseid;
		
		$params = array('userid' => $user->id, 'iteminstance' => $iteminstance);
		$selectid = "SELECT id FROM {quiz_attempts} WHERE quiz = :iteminstance and userid= :userid";
		
		$idCourseModule = $DB->get_record_sql($selectid,$params);
		
		$data['sitename'] = format_string($site->fullname);
		$data['signoff'] = generate_email_signoff();
		$data['platurl'] = '<br /><br /><a href="'.$CFG->wwwroot.'" target="_blank">'.$CFG->wwwroot.'</a>';
		$data['quizurl'] = $CFG->wwwroot.'/mod/quiz/review.php?attempt='.$idCourseModule->id;
		
		
		
		$studentusr = $user;
		$noreplyuser = core_user::get_noreply_user();
		
        $subject = get_string('notifystudentgradedsubjetc', 'local_notifystudentgraded', format_string($site->fullname));
        $message  = get_string('notifystudentgradedbody', 'local_notifystudentgraded', $data);
        $messagehtml = text_to_html($message, false, false, true);

        $studentusr->mailformat = 1; // Always send HTML version as well.

        // Directly email rather than using the messaging system to ensure its not routed to a popup or jabber.
        return email_to_user($studentusr, $noreplyuser, $subject, $message, $messagehtml);

        return true;
    }
}
