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
 * Email confirmation page for Job Board self-registration.
 *
 * This page handles email confirmation for users who registered through
 * the Job Board signup form, bypassing the global registration settings check.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/user/lib.php');

$data = optional_param('data', '', PARAM_RAW);
$p = optional_param('p', '', PARAM_ALPHANUM);
$s = optional_param('s', '', PARAM_RAW);
$redirect = optional_param('redirect', '', PARAM_LOCALURL);

$PAGE->set_url('/local/jobboard/confirm.php');
$PAGE->set_context(context_system::instance());
$PAGE->set_pagelayout('login');

// Check if plugin-specific self-registration is enabled.
$pluginselfreg = get_config('local_jobboard', 'enable_self_registration');
if (empty($pluginselfreg)) {
    // Fallback to standard Moodle confirmation if plugin registration is disabled.
    redirect(new moodle_url('/login/confirm.php', ['data' => $data, 'p' => $p, 's' => $s]));
}

if (!empty($data) || (!empty($p) && !empty($s))) {
    // Decode the data parameter if provided.
    if (!empty($data)) {
        $dataelements = explode('/', $data, 2);
        $usersecret = $dataelements[0];
        $username = isset($dataelements[1]) ? $dataelements[1] : '';
    } else {
        $usersecret = $s;
        $username = $p;
    }

    // Find the user.
    $confirmed = false;
    $user = $DB->get_record('user', ['username' => $username, 'mnethostid' => $CFG->mnet_localhost_id]);

    if (!$user) {
        // Try to find by secret alone (for cases where username might be different).
        $user = $DB->get_record('user', ['secret' => $usersecret, 'mnethostid' => $CFG->mnet_localhost_id]);
    }

    if ($user) {
        if ($user->confirmed) {
            // Already confirmed.
            $confirmed = true;
        } else if ($user->secret === $usersecret) {
            // Confirm the user.
            $DB->set_field('user', 'confirmed', 1, ['id' => $user->id]);
            $DB->set_field('user', 'secret', '', ['id' => $user->id]);

            // Trigger user confirmed event.
            if (class_exists('\core\event\user_confirmed')) {
                $event = \core\event\user_confirmed::create([
                    'objectid' => $user->id,
                    'context' => context_user::instance($user->id),
                    'relateduserid' => $user->id,
                ]);
                $event->trigger();
            }

            $confirmed = true;

            // Update the user record to get fresh data.
            $user = $DB->get_record('user', ['id' => $user->id]);

            // Check if user has a pending vacancy application.
            $pendingvacancy = get_user_preference('local_jobboard_pending_vacancy', null, $user->id);

            // Log the confirmation.
            $dbman = $DB->get_manager();
            if ($dbman->table_exists('local_jobboard_audit')) {
                $audit = new stdClass();
                $audit->userid = $user->id;
                $audit->action = 'email_confirmed';
                $audit->entitytype = 'user';
                $audit->entityid = $user->id;
                $audit->ipaddress = getremoteaddr();
                $audit->useragent = substr(clean_param($_SERVER['HTTP_USER_AGENT'] ?? '', PARAM_TEXT), 0, 512);
                $audit->extradata = json_encode(['pending_vacancy' => $pendingvacancy]);
                $audit->timecreated = time();
                $DB->insert_record('local_jobboard_audit', $audit);
            }
        } else {
            // Wrong secret.
            $user = null;
        }
    }

    if ($confirmed && $user) {
        // Success! Show confirmation message.
        $PAGE->set_title(get_string('confirm_success_title', 'local_jobboard'));
        $PAGE->set_heading(get_string('confirm_success_title', 'local_jobboard'));

        echo $OUTPUT->header();

        // Build login URL.
        $loginurl = new moodle_url('/login/index.php');
        $pendingvacancy = get_user_preference('local_jobboard_pending_vacancy', null, $user->id);
        if ($pendingvacancy) {
            $loginurl->param('wantsurl', (new moodle_url('/local/jobboard/index.php', [
                'view' => 'apply',
                'vacancyid' => $pendingvacancy,
            ]))->out(false));
        } else {
            $loginurl->param('wantsurl', (new moodle_url('/local/jobboard/index.php'))->out(false));
        }

        // Show success message.
        $templatedata = [
            'title' => get_string('confirm_success_title', 'local_jobboard'),
            'message' => get_string('confirm_success_message', 'local_jobboard'),
            'username' => $user->username,
            'email' => $user->email,
            'fullname' => fullname($user),
            'loginurl' => $loginurl->out(false),
            'logintext' => get_string('login'),
            'haspendingvacancy' => !empty($pendingvacancy),
            'pendingvacancytext' => get_string('confirm_pending_vacancy', 'local_jobboard'),
        ];

        // Use template if available, otherwise basic HTML.
        try {
            echo $OUTPUT->render_from_template('local_jobboard/pages/user/confirm_success', $templatedata);
        } catch (Exception $e) {
            // Fallback to basic HTML.
            echo '<div class="card shadow-sm mx-auto" style="max-width: 500px;">';
            echo '<div class="card-body text-center p-5">';
            echo '<i class="fa fa-check-circle text-success" style="font-size: 4rem;"></i>';
            echo '<h2 class="mt-4">' . $templatedata['title'] . '</h2>';
            echo '<p class="text-muted">' . $templatedata['message'] . '</p>';
            echo '<hr>';
            echo '<p><strong>' . get_string('username') . ':</strong> ' . s($templatedata['username']) . '</p>';
            echo '<p><strong>' . get_string('email') . ':</strong> ' . s($templatedata['email']) . '</p>';
            if ($templatedata['haspendingvacancy']) {
                echo '<div class="alert alert-info mt-3">' . $templatedata['pendingvacancytext'] . '</div>';
            }
            echo '<a href="' . $templatedata['loginurl'] . '" class="btn btn-primary btn-lg mt-3">';
            echo '<i class="fa fa-sign-in-alt me-2"></i>' . $templatedata['logintext'] . '</a>';
            echo '</div></div>';
        }

        echo $OUTPUT->footer();
        exit;
    } else {
        // Failed confirmation.
        $PAGE->set_title(get_string('confirm_failed_title', 'local_jobboard'));
        $PAGE->set_heading(get_string('confirm_failed_title', 'local_jobboard'));

        echo $OUTPUT->header();

        echo '<div class="card shadow-sm mx-auto" style="max-width: 500px;">';
        echo '<div class="card-body text-center p-5">';
        echo '<i class="fa fa-times-circle text-danger" style="font-size: 4rem;"></i>';
        echo '<h2 class="mt-4">' . get_string('confirm_failed_title', 'local_jobboard') . '</h2>';
        echo '<p class="text-muted">' . get_string('confirm_failed_message', 'local_jobboard') . '</p>';
        echo '<div class="mt-4">';
        echo '<a href="' . (new moodle_url('/local/jobboard/signup.php'))->out(false) . '" class="btn btn-primary">';
        echo '<i class="fa fa-user-plus me-2"></i>' . get_string('signup_title', 'local_jobboard') . '</a>';
        echo ' <a href="' . (new moodle_url('/login/index.php'))->out(false) . '" class="btn btn-outline-secondary">';
        echo '<i class="fa fa-sign-in-alt me-2"></i>' . get_string('login') . '</a>';
        echo '</div></div></div>';

        echo $OUTPUT->footer();
        exit;
    }
} else {
    // No confirmation data provided.
    redirect(new moodle_url('/local/jobboard/index.php'));
}
