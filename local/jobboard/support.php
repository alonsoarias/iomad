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
 * Technical support form for Job Board plugin.
 *
 * This page allows users to report technical issues with the Job Board plugin.
 * It is NOT for questions about selection processes or general inquiries.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

// Set up the page (no login required - guests can report issues too).
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/jobboard/support.php'));
$PAGE->set_pagelayout('standard');
$PAGE->activityheader->disable();
$PAGE->set_title(get_string('support_page_title', 'local_jobboard'));
$PAGE->set_heading(get_string('support_page_title', 'local_jobboard'));

// Set up breadcrumbs.
$PAGE->navbar->add(get_string('jobboard', 'local_jobboard'),
    new moodle_url('/local/jobboard/index.php', ['view' => 'public']));
$PAGE->navbar->add(get_string('support_page_title', 'local_jobboard'));

// Create the support form.
$mform = new \local_jobboard\forms\support_form();

// Handle form submission.
if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'public']));
}

if ($data = $mform->get_data()) {
    // Get support emails from plugin settings (comma or newline separated).
    $supportemailssetting = get_config('local_jobboard', 'support_emails');
    if (empty($supportemailssetting)) {
        // Default emails if setting is not configured.
        $supportemailssetting = 'mtic@iser.edu.co, soporteplataformas@iser.edu.co';
    }

    // Parse email addresses (support both comma and newline separation).
    $supportemailssetting = str_replace(["\r\n", "\r", "\n"], ',', $supportemailssetting);
    $supportemails = array_filter(array_map('trim', explode(',', $supportemailssetting)));

    // Extract text from editor fields (editor returns array with 'text' key).
    $description = '';
    if (!empty($data->description_editor['text'])) {
        $description = strip_tags($data->description_editor['text']);
    }

    $stepstoreproduce = '';
    if (!empty($data->steps_to_reproduce_editor['text'])) {
        $stepstoreproduce = strip_tags($data->steps_to_reproduce_editor['text']);
    }

    $expectedbehavior = '';
    if (!empty($data->expected_behavior_editor['text'])) {
        $expectedbehavior = strip_tags($data->expected_behavior_editor['text']);
    }

    // Build email subject.
    $subject = get_string('support_email_subject', 'local_jobboard') . ': ' . $data->error_type;

    // Get error type label.
    $errortypelabel = get_string('support_type_' . $data->error_type, 'local_jobboard');

    // Build plain text email body.
    $body = get_string('support_email_header', 'local_jobboard') . "\n\n";
    $body .= "TIPO DE ERROR: " . $errortypelabel . "\n\n";
    $body .= "DESCRIPCIÓN:\n" . $description . "\n\n";

    if (!empty($stepstoreproduce)) {
        $body .= "PASOS PARA REPRODUCIR:\n" . $stepstoreproduce . "\n\n";
    }

    if (!empty($expectedbehavior)) {
        $body .= "COMPORTAMIENTO ESPERADO:\n" . $expectedbehavior . "\n\n";
    }

    $body .= "INFORMACIÓN DEL USUARIO\n";
    $body .= "Nombre: " . $data->reporter_name . "\n";
    $body .= "Correo: " . $data->reporter_email . "\n";

    if (!empty($data->page_url)) {
        $body .= "URL: " . $data->page_url . "\n";
    }

    $body .= "Navegador: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido') . "\n";
    $body .= "Fecha/Hora: " . userdate(time(), get_string('strftimedatetime', 'langconfig')) . "\n";

    // Build HTML email body for better formatting.
    $htmlbody = '
    <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
        <div style="background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 100%); color: white; padding: 20px; border-radius: 8px 8px 0 0;">
            <h2 style="margin: 0; font-size: 18px;">🎫 ' . get_string('support_email_header', 'local_jobboard') . '</h2>
        </div>

        <div style="background: #f8f9fa; padding: 20px; border: 1px solid #dee2e6;">
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 12px; background: #e9ecef; font-weight: bold; width: 150px; border: 1px solid #dee2e6;">' . get_string('support_error_type', 'local_jobboard') . '</td>
                    <td style="padding: 8px 12px; background: white; border: 1px solid #dee2e6;">
                        <span style="background: #dc3545; color: white; padding: 4px 8px; border-radius: 4px; font-size: 12px;">' . htmlspecialchars($errortypelabel) . '</span>
                    </td>
                </tr>
            </table>
        </div>

        <div style="background: white; padding: 20px; border: 1px solid #dee2e6; border-top: none;">
            <h3 style="color: #1e3a5f; margin-top: 0; border-bottom: 2px solid #1e3a5f; padding-bottom: 8px;">
                📝 ' . get_string('support_error_description', 'local_jobboard') . '
            </h3>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; border-left: 4px solid #1e3a5f;">
                ' . nl2br(htmlspecialchars($description)) . '
            </div>';

    if (!empty($stepstoreproduce)) {
        $htmlbody .= '
            <h3 style="color: #1e3a5f; margin-top: 20px; border-bottom: 2px solid #17a2b8; padding-bottom: 8px;">
                🔄 ' . get_string('support_steps_to_reproduce', 'local_jobboard') . '
            </h3>
            <div style="background: #e7f5ff; padding: 15px; border-radius: 4px; border-left: 4px solid #17a2b8;">
                ' . nl2br(htmlspecialchars($stepstoreproduce)) . '
            </div>';
    }

    if (!empty($expectedbehavior)) {
        $htmlbody .= '
            <h3 style="color: #1e3a5f; margin-top: 20px; border-bottom: 2px solid #28a745; padding-bottom: 8px;">
                ✅ ' . get_string('support_expected_behavior', 'local_jobboard') . '
            </h3>
            <div style="background: #d4edda; padding: 15px; border-radius: 4px; border-left: 4px solid #28a745;">
                ' . nl2br(htmlspecialchars($expectedbehavior)) . '
            </div>';
    }

    $htmlbody .= '
        </div>

        <div style="background: #1e3a5f; color: white; padding: 20px; border-radius: 0 0 8px 8px;">
            <h3 style="margin-top: 0; font-size: 14px; border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 8px;">
                👤 ' . get_string('support_user_info', 'local_jobboard') . '
            </h3>
            <table style="width: 100%; color: white; font-size: 13px;">
                <tr>
                    <td style="padding: 4px 0;"><strong>' . get_string('support_reporter_name', 'local_jobboard') . ':</strong></td>
                    <td style="padding: 4px 0;">' . htmlspecialchars($data->reporter_name) . '</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0;"><strong>' . get_string('support_reporter_email', 'local_jobboard') . ':</strong></td>
                    <td style="padding: 4px 0;"><a href="mailto:' . htmlspecialchars($data->reporter_email) . '" style="color: #87ceeb;">' . htmlspecialchars($data->reporter_email) . '</a></td>
                </tr>';

    if (!empty($data->page_url)) {
        $htmlbody .= '
                <tr>
                    <td style="padding: 4px 0;"><strong>' . get_string('support_page_url', 'local_jobboard') . ':</strong></td>
                    <td style="padding: 4px 0;"><a href="' . htmlspecialchars($data->page_url) . '" style="color: #87ceeb;">' . htmlspecialchars($data->page_url) . '</a></td>
                </tr>';
    }

    $htmlbody .= '
                <tr>
                    <td style="padding: 4px 0;"><strong>' . get_string('support_browser', 'local_jobboard') . ':</strong></td>
                    <td style="padding: 4px 0; font-size: 11px;">' . htmlspecialchars($_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido') . '</td>
                </tr>
                <tr>
                    <td style="padding: 4px 0;"><strong>' . get_string('support_timestamp', 'local_jobboard') . ':</strong></td>
                    <td style="padding: 4px 0;">' . userdate(time(), get_string('strftimedatetime', 'langconfig')) . '</td>
                </tr>
            </table>
        </div>
    </div>';


    // Create a fake user for the reporter if not logged in.
    if (isloggedin() && !isguestuser()) {
        global $USER;
        $fromuser = $USER;
    } else {
        $fromuser = new stdClass();
        $fromuser->id = -1;
        $fromuser->email = $data->reporter_email;
        $fromuser->firstname = $data->reporter_name;
        $fromuser->lastname = '';
        $fromuser->maildisplay = 1;
        $fromuser->mailformat = 1;
    }

    // Send email to all configured support email addresses.
    $emailsent = false;
    foreach ($supportemails as $supportemail) {
        if (!validate_email($supportemail)) {
            continue;
        }

        $supportuser = \core_user::get_support_user();
        $supportuser->email = $supportemail;
        $supportuser->firstname = 'Soporte';
        $supportuser->lastname = 'Técnico';

        if (email_to_user($supportuser, $fromuser, $subject, $body, $htmlbody)) {
            $emailsent = true;
        }
    }

    if ($emailsent) {
        // Log the support request.
        if (class_exists('\local_jobboard\audit')) {
            \local_jobboard\audit::log('support_request', 'support', 0, [
                'error_type' => $data->error_type,
                'reporter_email' => $data->reporter_email,
            ]);
        }

        \core\notification::success(get_string('support_success_message', 'local_jobboard'));
    } else {
        \core\notification::warning(get_string('support_email_failed', 'local_jobboard'));
    }

    redirect(new moodle_url('/local/jobboard/support.php'));
}

// Display the page.
echo $OUTPUT->header();

// Page content.
echo '<div class="jb-support-page" data-region="jobboard-support-form">';

// Warning banner - This is ONLY for technical support.
echo '<div class="alert alert-warning mb-4">';
echo '<h5 class="alert-heading"><i class="fa fa-exclamation-triangle me-2"></i>' . get_string('support_warning_title', 'local_jobboard') . '</h5>';
echo '<p class="mb-0">' . get_string('support_warning_message', 'local_jobboard') . '</p>';
echo '</div>';

// Information card.
echo '<div class="card shadow-sm mb-4">';
echo '<div class="card-header bg-primary text-white">';
echo '<h5 class="mb-0"><i class="fa fa-bug me-2"></i>' . get_string('support_info_title', 'local_jobboard') . '</h5>';
echo '</div>';
echo '<div class="card-body">';
echo '<p>' . get_string('support_info_description', 'local_jobboard') . '</p>';
echo '<h6 class="mt-3"><i class="fa fa-check-circle text-success me-2"></i>' . get_string('support_examples_title', 'local_jobboard') . '</h6>';
echo '<ul class="mb-0">';
echo '<li>' . get_string('support_example_1', 'local_jobboard') . '</li>';
echo '<li>' . get_string('support_example_2', 'local_jobboard') . '</li>';
echo '<li>' . get_string('support_example_3', 'local_jobboard') . '</li>';
echo '<li>' . get_string('support_example_4', 'local_jobboard') . '</li>';
echo '</ul>';
echo '</div>';
echo '</div>';

// Display the form.
$mform->display();

echo '</div>';

echo $OUTPUT->footer();
