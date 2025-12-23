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

/**
 * Convert draft area file URLs in HTML to base64 data URIs for email embedding.
 *
 * This function finds all draftfile.php URLs in the HTML content and replaces
 * them with base64-encoded data URIs so images display correctly in emails.
 *
 * @param string $html The HTML content with draftfile.php URLs.
 * @param int $draftitemid The draft area item ID.
 * @return string The HTML with images converted to base64 data URIs.
 */
function local_jobboard_embed_draft_images($html, $draftitemid) {
    global $USER;

    if (empty($html) || empty($draftitemid)) {
        return $html;
    }

    // Guests cannot upload files, so just return the HTML as-is.
    if (!isloggedin() || isguestuser()) {
        return $html;
    }

    $fs = get_file_storage();
    $usercontext = context_user::instance($USER->id);

    // Get all files from the draft area.
    $files = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id', false);

    foreach ($files as $file) {
        $filename = $file->get_filename();
        $mimetype = $file->get_mimetype();

        // Only process image files.
        if (strpos($mimetype, 'image/') !== 0) {
            continue;
        }

        // Get file content and encode as base64.
        $content = $file->get_content();
        $base64 = base64_encode($content);
        $datauri = 'data:' . $mimetype . ';base64,' . $base64;

        // Replace draftfile.php URLs with the base64 data URI.
        // Pattern matches: @@PLUGINFILE@@/filename or /draftfile.php/.../filename
        $patterns = [
            '~@@PLUGINFILE@@/' . preg_quote($filename, '~') . '~',
            '~/draftfile\.php/[^"\']+/' . preg_quote($filename, '~') . '~',
        ];

        foreach ($patterns as $pattern) {
            $html = preg_replace($pattern, $datauri, $html);
        }
    }

    return $html;
}

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

    // Extract text from editor fields (editor returns array with 'text' and 'itemid' keys).
    // We need both plain text (for text email) and HTML (for HTML email with images).
    // Convert draft area images to base64 data URIs for email embedding.
    $descriptiontext = '';
    $descriptionhtml = '';
    if (!empty($data->description_editor['text'])) {
        $descriptionhtml = $data->description_editor['text'];
        // Convert any draft images to base64 for email embedding.
        if (!empty($data->description_editor['itemid'])) {
            $descriptionhtml = local_jobboard_embed_draft_images($descriptionhtml, $data->description_editor['itemid']);
        }
        $descriptiontext = strip_tags($descriptionhtml);
    }

    $stepstoreproducetext = '';
    $stepstoreproducehtml = '';
    if (!empty($data->steps_to_reproduce_editor['text'])) {
        $stepstoreproducehtml = $data->steps_to_reproduce_editor['text'];
        // Convert any draft images to base64 for email embedding.
        if (!empty($data->steps_to_reproduce_editor['itemid'])) {
            $stepstoreproducehtml = local_jobboard_embed_draft_images($stepstoreproducehtml, $data->steps_to_reproduce_editor['itemid']);
        }
        $stepstoreproducetext = strip_tags($stepstoreproducehtml);
    }

    $expectedbehaviortext = '';
    $expectedbehaviorhtml = '';
    if (!empty($data->expected_behavior_editor['text'])) {
        $expectedbehaviorhtml = $data->expected_behavior_editor['text'];
        // Convert any draft images to base64 for email embedding.
        if (!empty($data->expected_behavior_editor['itemid'])) {
            $expectedbehaviorhtml = local_jobboard_embed_draft_images($expectedbehaviorhtml, $data->expected_behavior_editor['itemid']);
        }
        $expectedbehaviortext = strip_tags($expectedbehaviorhtml);
    }

    // Build email subject.
    $subject = get_string('support_email_subject', 'local_jobboard') . ': ' . $data->error_type;

    // Get error type label.
    $errortypelabel = get_string('support_type_' . $data->error_type, 'local_jobboard');

    // Build plain text email body.
    $body = get_string('support_email_header', 'local_jobboard') . "\n\n";
    $body .= "TIPO DE ERROR: " . $errortypelabel . "\n\n";
    $body .= "DESCRIPCIÓN:\n" . $descriptiontext . "\n\n";

    if (!empty($stepstoreproducetext)) {
        $body .= "PASOS PARA REPRODUCIR:\n" . $stepstoreproducetext . "\n\n";
    }

    if (!empty($expectedbehaviortext)) {
        $body .= "COMPORTAMIENTO ESPERADO:\n" . $expectedbehaviortext . "\n\n";
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
                ' . $descriptionhtml . '
            </div>';

    if (!empty($stepstoreproducehtml)) {
        $htmlbody .= '
            <h3 style="color: #1e3a5f; margin-top: 20px; border-bottom: 2px solid #17a2b8; padding-bottom: 8px;">
                🔄 ' . get_string('support_steps_to_reproduce', 'local_jobboard') . '
            </h3>
            <div style="background: #e7f5ff; padding: 15px; border-radius: 4px; border-left: 4px solid #17a2b8;">
                ' . $stepstoreproducehtml . '
            </div>';
    }

    if (!empty($expectedbehaviorhtml)) {
        $htmlbody .= '
            <h3 style="color: #1e3a5f; margin-top: 20px; border-bottom: 2px solid #28a745; padding-bottom: 8px;">
                ✅ ' . get_string('support_expected_behavior', 'local_jobboard') . '
            </h3>
            <div style="background: #d4edda; padding: 15px; border-radius: 4px; border-left: 4px solid #28a745;">
                ' . $expectedbehaviorhtml . '
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

// Page content with jb-* design pattern.
echo '<div class="jb-support" id="jobboard-support-form">';

// Header section.
echo '<div class="jb-support-header">';
echo '<div class="jb-support-header__nav">';
echo '<a href="' . (new moodle_url('/local/jobboard/index.php', ['view' => 'public']))->out() . '" class="jb-support-header__back">';
echo '<i class="fa fa-arrow-left"></i> ' . get_string('back', 'local_jobboard');
echo '</a>';
echo '</div>';
echo '<h1 class="jb-support-header__title">';
echo '<i class="fa fa-headset"></i> ' . get_string('support_page_title', 'local_jobboard');
echo '</h1>';
echo '<p class="jb-support-header__subtitle">' . get_string('support_info_description', 'local_jobboard') . '</p>';
echo '</div>';

// Warning banner - This is ONLY for technical support.
echo '<div class="jb-alert jb-alert--warning">';
echo '<i class="fa fa-exclamation-triangle"></i>';
echo '<div class="jb-alert__content">';
echo '<strong>' . get_string('support_warning_title', 'local_jobboard') . '</strong>';
echo '<span>' . get_string('support_warning_message', 'local_jobboard') . '</span>';
echo '</div>';
echo '</div>';

// Two column layout: Form (left col-8) + FAQ (right col-4).
echo '<div class="jb-support-layout">';

// Left column - Form section.
echo '<div class="jb-support-layout__main">';

// Information card.
echo '<div class="jb-card jb-support-info">';
echo '<div class="jb-card__header jb-card__header--primary">';
echo '<i class="fa fa-bug"></i> ' . get_string('support_info_title', 'local_jobboard');
echo '</div>';
echo '<div class="jb-card__body">';
echo '<h4 class="jb-support-info__title">';
echo '<i class="fa fa-check-circle"></i> ' . get_string('support_examples_title', 'local_jobboard');
echo '</h4>';
echo '<ul class="jb-support-info__list">';
echo '<li><i class="fa fa-check"></i> ' . get_string('support_example_1', 'local_jobboard') . '</li>';
echo '<li><i class="fa fa-check"></i> ' . get_string('support_example_2', 'local_jobboard') . '</li>';
echo '<li><i class="fa fa-check"></i> ' . get_string('support_example_3', 'local_jobboard') . '</li>';
echo '<li><i class="fa fa-check"></i> ' . get_string('support_example_4', 'local_jobboard') . '</li>';
echo '</ul>';
echo '</div>';
echo '</div>';

// Form section.
echo '<div class="jb-card jb-support-form">';
echo '<div class="jb-card__header">';
echo '<i class="fa fa-edit"></i> ' . get_string('support_form_title', 'local_jobboard');
echo '</div>';
echo '<div class="jb-card__body">';

// Display the form.
$mform->display();

echo '</div>';
echo '</div>';

echo '</div>'; // End left column.

// Right column - FAQ section.
echo '<div class="jb-support-layout__sidebar">';

echo '<div class="jb-card jb-faq">';
echo '<div class="jb-card__header jb-card__header--info">';
echo '<i class="fa fa-question-circle"></i> ' . get_string('faq_title', 'local_jobboard');
echo '</div>';
echo '<div class="jb-card__body">';
echo '<p class="jb-faq__subtitle">' . get_string('faq_subtitle', 'local_jobboard') . '</p>';
echo '<div class="jb-faq__list">';

// FAQ items - using accordion pattern.
for ($i = 1; $i <= 9; $i++) {
    $question = get_string("faq_q{$i}", 'local_jobboard');
    $answer = get_string("faq_a{$i}", 'local_jobboard');
    echo '<div class="jb-faq__item">';
    echo '<button class="jb-faq__question" type="button" aria-expanded="false" onclick="this.classList.toggle(\'jb-faq__question--active\'); this.setAttribute(\'aria-expanded\', this.classList.contains(\'jb-faq__question--active\'));">';
    echo '<span>' . $question . '</span>';
    echo '<i class="fa fa-chevron-down jb-faq__icon"></i>';
    echo '</button>';
    echo '<div class="jb-faq__answer">';
    echo '<p>' . $answer . '</p>';
    echo '</div>';
    echo '</div>';
}

echo '</div>';
echo '</div>';
echo '</div>';

echo '</div>'; // End right column.

echo '</div>'; // End two column layout.

// Footer navigation.
echo '<div class="jb-footer-nav">';
echo '<a href="' . (new moodle_url('/local/jobboard/index.php', ['view' => 'public']))->out() . '" class="jb-btn jb-btn--outline">';
echo '<i class="fa fa-arrow-left"></i> ' . get_string('back', 'local_jobboard');
echo '</a>';
echo '</div>';

echo '</div>';

// Custom styles for support page.
echo '<style>
/* ================================================================
   SUPPORT PAGE - CLEAN MINIMAL DESIGN
   Following jb-* pattern
   ================================================================ */
.jb-support {
    max-width: 1400px;
    padding: 1rem;
}

/* Two column layout */
.jb-support-layout {
    display: grid;
    grid-template-columns: 1fr 380px;
    gap: 1.5rem;
    align-items: start;
}

.jb-support-layout__main {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.jb-support-layout__sidebar {
    position: sticky;
    top: 1rem;
}

/* Responsive: Stack on mobile */
@media (max-width: 992px) {
    .jb-support-layout {
        grid-template-columns: 1fr;
    }

    .jb-support-layout__sidebar {
        position: static;
        order: -1;
    }
}

/* Header */
.jb-support-header {
    margin-bottom: 1.5rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e5e5e5;
}

.jb-support-header__nav {
    margin-bottom: 1rem;
}

.jb-support-header__back {
    color: var(--iser-verde, #1b9e88);
    text-decoration: none;
    font-size: 0.875rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}

.jb-support-header__back:hover {
    color: var(--iser-verde-dark, #157a6a);
}

.jb-support-header__title {
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0 0 0.5rem 0;
    color: var(--iser-negro, #1a1a1a);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.jb-support-header__title i {
    color: var(--iser-verde, #1b9e88);
}

.jb-support-header__subtitle {
    font-size: 1rem;
    color: var(--iser-gris-medio, #666);
    margin: 0;
}

/* Alert */
.jb-alert {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1rem;
}

.jb-alert > i {
    font-size: 1.25rem;
    margin-top: 0.125rem;
}

.jb-alert__content {
    flex: 1;
}

.jb-alert__content strong {
    display: block;
    margin-bottom: 0.25rem;
}

.jb-alert__content span {
    font-size: 0.875rem;
    opacity: 0.9;
}

.jb-alert--warning {
    background: rgba(255, 193, 7, 0.15);
    border: 1px solid rgba(255, 193, 7, 0.3);
    color: #856404;
}

/* Cards */
.jb-card {
    background: #fff;
    border: 1px solid #e5e5e5;
    border-radius: 8px;
    overflow: hidden;
    margin-bottom: 1rem;
}

.jb-card__header {
    padding: 0.875rem 1rem;
    font-size: 0.9375rem;
    font-weight: 600;
    color: var(--iser-gris-oscuro, #333);
    background: #f9fafb;
    border-bottom: 1px solid #e5e5e5;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.jb-card__header i {
    color: var(--iser-verde, #1b9e88);
}

.jb-card__header--primary {
    background: var(--iser-verde, #1b9e88);
    color: #fff;
}

.jb-card__header--primary i {
    color: #fff;
}

.jb-card__body {
    padding: 1.25rem;
}

/* Support Info */
.jb-support-info__title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--iser-gris-oscuro, #333);
    margin: 0 0 0.75rem 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.jb-support-info__title i {
    color: #28a745;
}

.jb-support-info__list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.jb-support-info__list li {
    padding: 0.5rem 0;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--iser-gris-oscuro, #333);
}

.jb-support-info__list li i {
    color: var(--iser-verde, #1b9e88);
    margin-top: 0.125rem;
}

/* FAQ Section */
.jb-card__header--info {
    background: #17a2b8;
    color: #fff;
}

.jb-card__header--info i {
    color: #fff;
}

.jb-faq__subtitle {
    color: var(--iser-gris-medio, #666);
    font-size: 0.875rem;
    margin: 0 0 1rem 0;
}

.jb-faq__list {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.jb-faq__item {
    border: 1px solid #e5e5e5;
    border-radius: 6px;
    overflow: hidden;
}

.jb-faq__question {
    width: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.875rem 1rem;
    background: #f9fafb;
    border: none;
    cursor: pointer;
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--iser-gris-oscuro, #333);
    text-align: left;
    transition: all 0.2s;
}

.jb-faq__question:hover {
    background: #f0f0f0;
}

.jb-faq__question span {
    flex: 1;
    padding-right: 1rem;
}

.jb-faq__icon {
    color: var(--iser-verde, #1b9e88);
    transition: transform 0.2s;
    flex-shrink: 0;
}

.jb-faq__question--active .jb-faq__icon {
    transform: rotate(180deg);
}

.jb-faq__answer {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-out;
    background: #fff;
}

.jb-faq__question--active + .jb-faq__answer {
    max-height: 500px;
}

.jb-faq__answer p {
    padding: 1rem;
    margin: 0;
    font-size: 0.875rem;
    color: var(--iser-gris-oscuro, #333);
    line-height: 1.6;
    border-top: 1px solid #e5e5e5;
}

/* Footer Nav */
.jb-footer-nav {
    display: flex;
    justify-content: flex-start;
    padding: 1.5rem 0;
    margin-top: 1rem;
    border-top: 1px solid #e5e5e5;
}

/* Buttons */
.jb-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.375rem;
    padding: 0.5rem 1rem;
    border: 1px solid transparent;
    border-radius: 6px;
    font-size: 0.8125rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    transition: all 0.15s;
}

.jb-btn--outline {
    background: transparent;
    border-color: #d1d5db;
    color: var(--iser-gris-oscuro, #333);
}

.jb-btn--outline:hover {
    border-color: var(--iser-verde, #1b9e88);
    color: var(--iser-verde, #1b9e88);
}

/* Form styling */
.jb-support-form .mform fieldset {
    margin-bottom: 1rem;
}

.jb-support-form .mform legend {
    font-size: 1rem;
    font-weight: 600;
    color: var(--iser-gris-oscuro, #333);
    border-bottom: 2px solid var(--iser-verde, #1b9e88);
    padding-bottom: 0.5rem;
    margin-bottom: 1rem;
}

.jb-support-form .mform .form-group {
    margin-bottom: 1rem;
}

.jb-support-form .mform .form-control {
    border-radius: 6px;
    border-color: #d1d5db;
}

.jb-support-form .mform .form-control:focus {
    border-color: var(--iser-verde, #1b9e88);
    box-shadow: 0 0 0 2px rgba(27, 158, 136, 0.15);
}

.jb-support-form .mform .btn-primary {
    background: var(--iser-verde, #1b9e88);
    border-color: var(--iser-verde, #1b9e88);
    border-radius: 6px;
}

.jb-support-form .mform .btn-primary:hover {
    background: var(--iser-verde-dark, #157a6a);
    border-color: var(--iser-verde-dark, #157a6a);
}

.jb-support-form .mform .btn-secondary {
    border-radius: 6px;
}

/* Mobile */
@media (max-width: 768px) {
    .jb-support-header__title {
        font-size: 1.25rem;
    }
}
</style>';

echo $OUTPUT->footer();
