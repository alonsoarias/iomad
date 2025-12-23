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
 * Alternative signup page for Job Board with company selection.
 *
 * This page allows new users to register with complete profile information
 * and optionally select a company when applying for vacancies. It works
 * alongside IOMAD's multi-tenant architecture and replicates Moodle's
 * email confirmation workflow.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Allow access without login.
define('NO_MOODLE_COOKIES', false);
define('ALLOW_GET_PARAMETERS', true);

require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');
require_once($CFG->dirroot . '/user/editlib.php');
require_once($CFG->dirroot . '/user/profile/lib.php');
require_once($CFG->libdir . '/authlib.php');
require_once($CFG->dirroot . '/login/lib.php');

use local_jobboard\helper\iomad_helper;

// Check if user is already logged in.
if (isloggedin() && !isguestuser()) {
    $vacancyid = optional_param('vacancyid', 0, PARAM_INT);
    if ($vacancyid) {
        redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancyid]));
    } else {
        redirect(new moodle_url('/local/jobboard/index.php'));
    }
}

// Check if plugin-specific self-registration is enabled.
// This allows registration through the jobboard even when global registration is disabled.
$pluginselfreg = get_config('local_jobboard', 'enable_self_registration');
if (empty($pluginselfreg)) {
    // Fallback to Moodle's global setting.
    if (empty($CFG->registerauth) || $CFG->registerauth === 'none') {
        throw new moodle_exception('registrationdisabled', 'local_jobboard');
    }
}

// Parameters.
$vacancyid = optional_param('vacancyid', 0, PARAM_INT);

// Get vacancy info if provided.
$vacancy = null;
if ($vacancyid) {
    $vacancy = $DB->get_record('local_jobboard_vacancy', [
        'id' => $vacancyid,
        'status' => 'published',
    ]);
}

// Set up the page.
$context = context_system::instance();
$PAGE->set_context($context);
$PAGE->set_url(new moodle_url('/local/jobboard/signup.php', ['vacancyid' => $vacancyid]));
$PAGE->set_pagelayout('base'); // Use 'base' instead of 'login' to enable help button tooltips.
$PAGE->set_title(get_string('signup_title', 'local_jobboard'));
$PAGE->set_heading(get_string('signup_title', 'local_jobboard'));

// Add custom CSS for the signup form.

// Check IOMAD installation and get companies.
$isiomad = iomad_helper::is_iomad_installed();
$companies = [];

if ($isiomad) {
    $companies = iomad_helper::get_companies();
}

// If vacancy has a company, pre-select it.
$defaultcompanyid = 0;
if ($vacancy && !empty($vacancy->companyid)) {
    $defaultcompanyid = $vacancy->companyid;
}

// Create the form.
$formdata = [
    'vacancyid' => $vacancyid,
    'companies' => $companies,
    'isiomad' => $isiomad,
];

$mform = new \local_jobboard\forms\signup_form(null, $formdata);

// Set default company if available.
if ($defaultcompanyid) {
    $mform->set_data(['companyid' => $defaultcompanyid]);
}

// Handle form cancellation.
if ($mform->is_cancelled()) {
    if ($vacancyid) {
        redirect(new moodle_url('/local/jobboard/public.php', ['id' => $vacancyid]));
    } else {
        redirect(new moodle_url('/local/jobboard/public.php'));
    }
}

// Handle form submission.
if ($data = $mform->get_data()) {
    try {
        // Create the user account.
        $user = create_user_from_form($data, $isiomad);

        if ($user) {
            // Send confirmation email using Moodle's standard function.
            $confirmationemail = send_confirmation_email_to_user($user);

            if (!$confirmationemail) {
                // Email failed but user was created - log this.
                debugging('Failed to send confirmation email to user ' . $user->id, DEBUG_NORMAL);
            }

            // Show success message using renderer + template pattern.
            $PAGE->set_title(get_string('signup_success_title', 'local_jobboard'));
            $PAGE->set_heading(get_string('signup_success_title', 'local_jobboard'));

            echo $OUTPUT->header();

            // Use renderer.
            $renderer = $PAGE->get_renderer('local_jobboard');
            $data = $renderer->prepare_signup_success_data($user->email);
            echo $renderer->render_signup_success_page($data);

            echo $OUTPUT->footer();
            exit;
        }
    } catch (Exception $e) {
        // Show error message.
        \core\notification::error(get_string('signup_error_creating', 'local_jobboard') . ': ' . $e->getMessage());
    }
}

// Display the form.
echo $OUTPUT->header();

// Build login URL with redirect.
$loginurl = new moodle_url('/login/index.php');
if ($vacancyid) {
    $loginurl->param('wantsurl', (new moodle_url('/local/jobboard/index.php', [
        'view' => 'apply',
        'vacancyid' => $vacancyid,
    ]))->out(false));
}

// Build template context.
$templatecontext = [
    'title' => get_string('signup_title', 'local_jobboard'),
    'intro' => get_string('signup_intro', 'local_jobboard'),
    'alreadyaccounttext' => get_string('signup_already_account', 'local_jobboard'),
    'loginurl' => $loginurl->out(false),
    'logintext' => get_string('login'),
    'requiredfieldstext' => get_string('signup_required_fields', 'local_jobboard'),
    'formhtml' => $mform->render(),
];

// Add vacancy info if available.
if ($vacancy) {
    $templatecontext['vacancy'] = [
        'title' => s($vacancy->title),
        'code' => s($vacancy->code),
        'location' => !empty($vacancy->location) ? s($vacancy->location) : '',
    ];
    $templatecontext['applyingfortext'] = get_string('signup_applying_for', 'local_jobboard');
    $templatecontext['codetext'] = get_string('code', 'local_jobboard');
}

// Render using template.
echo $OUTPUT->render_from_template('local_jobboard/pages/user/signup', $templatecontext);

// JavaScript for dynamic department loading.
if ($isiomad) {
    $PAGE->requires->js_call_amd('local_jobboard/signup_form', 'init', []);
}

// Initialize progress steps indicator.
$PAGE->requires->js_call_amd('local_jobboard/progress_steps', 'init', [[
    'containerId' => 'jb-signup-progress',
    'steps' => [
        ['icon' => 'key', 'label' => get_string('signup_step_account', 'local_jobboard')],
        ['icon' => 'user', 'label' => get_string('signup_step_personal', 'local_jobboard')],
        ['icon' => 'phone', 'label' => get_string('signup_step_contact', 'local_jobboard')],
        ['icon' => 'graduation-cap', 'label' => get_string('signup_step_academic', 'local_jobboard')],
        ['icon' => 'check-circle', 'label' => get_string('signup_step_confirm', 'local_jobboard')],
    ],
    'sectionMap' => [
        'accountheader' => 0,
        'personalinfo' => 1,
        'contactinfo' => 2,
        'academicheader' => 3,
        'termsheader' => 4,
    ],
]]);

// Initialize loading states for form submission.
$PAGE->requires->js_call_amd('local_jobboard/loading_states', 'init', []);

echo $OUTPUT->footer();

/**
 * Create a new user from form data.
 *
 * @param stdClass $data Form data.
 * @param bool $isiomad Whether IOMAD is installed.
 * @return stdClass|false The created user or false on failure.
 * @throws Exception If user creation fails.
 */
function create_user_from_form($data, $isiomad) {
    global $CFG, $DB;

    // Build user object with all Moodle standard fields.
    $user = new stdClass();
    // Use Moodle's registerauth if set, otherwise default to 'email' for plugin-specific registration.
    $user->auth = (!empty($CFG->registerauth) && $CFG->registerauth !== 'none') ? $CFG->registerauth : 'email';
    $user->confirmed = 0; // Requires email confirmation.
    $user->mnethostid = $CFG->mnet_localhost_id;

    // Username is the ID number (cleaned for use as username).
    $idnumber = trim($data->idnumber ?? '');
    $user->username = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($idnumber));
    $user->idnumber = $idnumber;

    $user->password = hash_internal_user_password($data->password);
    $user->email = trim($data->email);
    $user->firstname = trim($data->firstname);
    $user->lastname = trim($data->lastname);
    $user->phone1 = trim($data->phone1 ?? '');
    $user->phone2 = trim($data->phone2 ?? '');
    $user->address = trim($data->address ?? '');
    $user->city = trim($data->city ?? '');
    $user->country = $data->country ?? '';
    $user->institution = trim($data->institution ?? '');
    $user->department = trim($data->department_region ?? '');
    // Handle editor data format (array with 'text' and 'format' keys).
    if (is_array($data->description ?? null)) {
        $user->description = $data->description['text'] ?? '';
        $user->descriptionformat = $data->description['format'] ?? FORMAT_HTML;
    } else {
        $user->description = trim($data->description ?? '');
        $user->descriptionformat = FORMAT_HTML;
    }
    $user->lang = current_language();
    $user->calendartype = $CFG->calendartype;
    $user->timezone = $CFG->timezone ?? '99';
    $user->timecreated = time();
    $user->timemodified = $user->timecreated;
    $user->policyagreed = 1;
    $user->secret = random_string(15);

    // Start transaction for data integrity.
    $transaction = $DB->start_delegated_transaction();

    try {
        // Create the user in the database.
        $user->id = $DB->insert_record('user', $user);

        if (!$user->id) {
            throw new Exception('Failed to insert user record');
        }

        // Update user context.
        $usercontext = context_user::instance($user->id);

        // Trigger user created event.
        $event = \core\event\user_created::create([
            'objectid' => $user->id,
            'context' => context_system::instance(),
            'relateduserid' => $user->id,
            'other' => [
                'auth' => $user->auth,
            ],
        ]);
        $event->trigger();

        // Store extended profile data in jobboard profile table.
        store_extended_profile($user->id, $data);

        // Store consent records.
        store_user_consents($user->id, $data);

        // Assign to company in IOMAD if selected.
        if ($isiomad && !empty($data->companyid)) {
            iomad_helper::assign_user_to_company($user->id, (int)$data->companyid, (int)($data->departmentid ?? 0));
        }

        // Store the vacancy they were applying for as user preference.
        if (!empty($data->vacancyid)) {
            set_user_preference('local_jobboard_pending_vacancy', $data->vacancyid, $user->id);
        }

        // Commit transaction.
        $transaction->allow_commit();

        // Log the signup in the audit table.
        log_signup_audit($user->id, $data);

        return $user;

    } catch (Exception $e) {
        $transaction->rollback($e);
        throw $e;
    }
}

/**
 * Store extended profile data for the applicant.
 *
 * @param int $userid The user ID.
 * @param stdClass $data The form data.
 */
function store_extended_profile($userid, $data) {
    global $DB;

    // Create applicant profile record.
    $profile = new stdClass();
    $profile->userid = $userid;
    $profile->doctype = $data->doctype ?? '';
    $profile->birthdate = $data->birthdate ?? 0;
    $profile->gender = $data->gender ?? '';
    $profile->education_level = $data->education_level ?? '';
    $profile->degree_title = trim($data->degree_title ?? '');
    $profile->expertise_area = trim($data->expertise_area ?? '');
    $profile->experience_years = $data->experience_years ?? '';
    $profile->profile_complete = 1; // Mark as complete since they filled full signup form.
    $profile->timecreated = time();
    $profile->timemodified = time();

    // Check if our profile table exists.
    $dbman = $DB->get_manager();
    if ($dbman->table_exists('local_jobboard_applicant_profile')) {
        $DB->insert_record('local_jobboard_applicant_profile', $profile);
    } else {
        // Fallback: Store in user preferences.
        set_user_preference('local_jobboard_doctype', $profile->doctype, $userid);
        set_user_preference('local_jobboard_birthdate', $profile->birthdate, $userid);
        set_user_preference('local_jobboard_gender', $profile->gender, $userid);
        set_user_preference('local_jobboard_education_level', $profile->education_level, $userid);
        set_user_preference('local_jobboard_degree_title', $profile->degree_title, $userid);
        set_user_preference('local_jobboard_expertise_area', $profile->expertise_area, $userid);
        set_user_preference('local_jobboard_experience_years', $profile->experience_years, $userid);
        set_user_preference('local_jobboard_profile_complete', '1', $userid);
    }
}

/**
 * Store user consent records.
 *
 * @param int $userid The user ID.
 * @param stdClass $data The form data.
 */
function store_user_consents($userid, $data) {
    global $DB;

    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('local_jobboard_consent')) {
        return;
    }

    $now = time();
    $ipaddress = getremoteaddr();
    // Sanitize user agent to prevent XSS in logs.
    $useragent = substr(clean_param($_SERVER['HTTP_USER_AGENT'] ?? '', PARAM_TEXT), 0, 512);

    // Terms consent.
    if (!empty($data->policyagreed)) {
        $consent = new stdClass();
        $consent->userid = $userid;
        $consent->consenttype = 'terms';
        $consent->consentgiven = 1;
        $consent->consentversion = get_config('local_jobboard', 'terms_version') ?: '1.0';
        $consent->ipaddress = $ipaddress;
        $consent->useragent = $useragent;
        $consent->timecreated = $now;
        $DB->insert_record('local_jobboard_consent', $consent);
    }

    // Data treatment consent.
    if (!empty($data->datatreatmentagreed)) {
        $consent = new stdClass();
        $consent->userid = $userid;
        $consent->consenttype = 'datatreatment';
        $consent->consentgiven = 1;
        $consent->consentversion = get_config('local_jobboard', 'datatreatment_version') ?: '1.0';
        $consent->ipaddress = $ipaddress;
        $consent->useragent = $useragent;
        $consent->timecreated = $now;
        $DB->insert_record('local_jobboard_consent', $consent);
    }

    // Data accuracy consent.
    if (!empty($data->dataaccuracy)) {
        $consent = new stdClass();
        $consent->userid = $userid;
        $consent->consenttype = 'dataaccuracy';
        $consent->consentgiven = 1;
        $consent->consentversion = '1.0';
        $consent->ipaddress = $ipaddress;
        $consent->useragent = $useragent;
        $consent->timecreated = $now;
        $DB->insert_record('local_jobboard_consent', $consent);
    }
}

/**
 * Assign user to an IOMAD company.
 *
 * @deprecated Use iomad_helper::assign_user_to_company() instead.
 * @param int $userid The user ID.
 * @param int $companyid The company ID.
 * @param int $departmentid The department ID (optional).
 * @return bool True on success, false on failure.
 */
function assign_user_to_company($userid, $companyid, $departmentid = 0) {
    // Use the centralized helper function that handles company changes properly.
    return iomad_helper::assign_user_to_company((int)$userid, (int)$companyid, (int)$departmentid);
}

/**
 * Send confirmation email to the new user.
 *
 * This function sends a custom confirmation email with the jobboard
 * confirmation URL instead of the standard Moodle URL. This allows
 * email confirmation to work even when global registration is disabled.
 *
 * @param stdClass $user The user object.
 * @return bool True if email was sent successfully.
 */
function send_confirmation_email_to_user($user) {
    global $CFG, $DB;

    // Check if plugin self-registration is enabled.
    $pluginselfreg = get_config('local_jobboard', 'enable_self_registration');

    // If plugin registration is not enabled, use standard Moodle confirmation.
    if (empty($pluginselfreg)) {
        return send_confirmation_email($user);
    }

    // Build the custom jobboard confirmation URL.
    $data = $user->secret . '/' . urlencode($user->username);
    $confirmurl = new moodle_url('/local/jobboard/confirm.php', ['data' => $data]);

    // Get the support user for the 'from' field.
    $supportuser = core_user::get_support_user();

    // Build the email subject.
    $site = get_site();
    $subject = get_string('confirm_email_subject', 'local_jobboard') . ' - ' . format_string($site->fullname);

    // Build support URL for the jobboard.
    $supporturl = new moodle_url('/local/jobboard/support.php');

    // Build plain text version.
    $username = fullname($user, true);
    $messagetext = get_string('confirm_email_greeting', 'local_jobboard', $username) . "\n\n";
    $messagetext .= get_string('confirm_email_intro', 'local_jobboard') . "\n\n";
    $messagetext .= get_string('confirm_email_action', 'local_jobboard') . "\n\n";
    $messagetext .= $confirmurl->out(false) . "\n\n";
    $messagetext .= get_string('confirm_email_expires', 'local_jobboard') . "\n\n";
    $messagetext .= get_string('confirm_email_next_steps_title', 'local_jobboard') . "\n";
    $messagetext .= "1. " . get_string('confirm_email_step1', 'local_jobboard') . "\n";
    $messagetext .= "2. " . get_string('confirm_email_step2', 'local_jobboard') . "\n";
    $messagetext .= "3. " . get_string('confirm_email_step3', 'local_jobboard') . "\n";
    $messagetext .= "4. " . get_string('confirm_email_step4', 'local_jobboard') . "\n\n";
    $messagetext .= get_string('confirm_email_help', 'local_jobboard') . "\n";
    $messagetext .= $supporturl->out(false) . "\n\n";
    $messagetext .= get_string('confirm_email_ignore', 'local_jobboard') . "\n\n";
    $messagetext .= "---\n" . format_string($site->fullname) . "\n";
    $messagetext .= get_string('confirm_email_footer', 'local_jobboard');

    // Build HTML version with professional styling.
    $messagehtml = '
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    </head>
    <body style="margin: 0; padding: 0; font-family: Arial, sans-serif; background-color: #f4f4f4;">
        <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f4f4f4; padding: 20px 0;">
            <tr>
                <td align="center">
                    <table width="600" cellpadding="0" cellspacing="0" style="background-color: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <!-- Header -->
                        <tr>
                            <td style="background: linear-gradient(135deg, #1b9e88 0%, #157a6a 100%); padding: 30px 40px; text-align: center;">
                                <h1 style="margin: 0; color: #ffffff; font-size: 24px; font-weight: 600;">
                                    📋 ' . get_string('jobboard', 'local_jobboard') . '
                                </h1>
                                <p style="margin: 8px 0 0 0; color: rgba(255,255,255,0.9); font-size: 14px;">
                                    ' . htmlspecialchars(format_string($site->fullname)) . '
                                </p>
                            </td>
                        </tr>

                        <!-- Main Content -->
                        <tr>
                            <td style="padding: 40px;">
                                <h2 style="margin: 0 0 20px 0; color: #1a1a1a; font-size: 22px;">
                                    ' . get_string('confirm_email_greeting', 'local_jobboard', htmlspecialchars($username)) . '
                                </h2>

                                <p style="margin: 0 0 20px 0; color: #555555; font-size: 16px; line-height: 1.6;">
                                    ' . get_string('confirm_email_intro', 'local_jobboard') . '
                                </p>

                                <p style="margin: 0 0 25px 0; color: #555555; font-size: 16px; line-height: 1.6;">
                                    ' . get_string('confirm_email_action', 'local_jobboard') . '
                                </p>

                                <!-- CTA Button -->
                                <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 25px;">
                                    <tr>
                                        <td align="center">
                                            <a href="' . htmlspecialchars($confirmurl->out(false)) . '"
                                               style="display: inline-block; background: #1b9e88; color: #ffffff; text-decoration: none;
                                                      padding: 14px 40px; border-radius: 6px; font-size: 16px; font-weight: 600;
                                                      box-shadow: 0 2px 4px rgba(27, 158, 136, 0.3);">
                                                ✓ ' . get_string('confirm_email_button', 'local_jobboard') . '
                                            </a>
                                        </td>
                                    </tr>
                                </table>

                                <!-- Alternative Link -->
                                <div style="background: #f8f9fa; border-radius: 6px; padding: 15px; margin-bottom: 25px;">
                                    <p style="margin: 0 0 8px 0; color: #666666; font-size: 13px;">
                                        ' . get_string('confirm_email_link_alt', 'local_jobboard') . '
                                    </p>
                                    <p style="margin: 0; word-break: break-all;">
                                        <a href="' . htmlspecialchars($confirmurl->out(false)) . '"
                                           style="color: #1b9e88; font-size: 12px; text-decoration: underline;">
                                            ' . htmlspecialchars($confirmurl->out(false)) . '
                                        </a>
                                    </p>
                                </div>

                                <!-- Expiry Notice -->
                                <p style="margin: 0 0 25px 0; color: #856404; background: #fff3cd; padding: 12px 15px;
                                          border-radius: 6px; font-size: 14px; border-left: 4px solid #ffc107;">
                                    ⏰ ' . get_string('confirm_email_expires', 'local_jobboard') . '
                                </p>

                                <!-- Next Steps -->
                                <div style="border-top: 1px solid #e9ecef; padding-top: 25px;">
                                    <h3 style="margin: 0 0 15px 0; color: #1a1a1a; font-size: 16px;">
                                        ' . get_string('confirm_email_next_steps_title', 'local_jobboard') . '
                                    </h3>
                                    <table width="100%" cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding: 8px 0; color: #555555; font-size: 14px;">
                                                <span style="display: inline-block; width: 24px; height: 24px; background: #1b9e88;
                                                             color: white; border-radius: 50%; text-align: center; line-height: 24px;
                                                             font-size: 12px; margin-right: 10px;">1</span>
                                                ' . get_string('confirm_email_step1', 'local_jobboard') . '
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; color: #555555; font-size: 14px;">
                                                <span style="display: inline-block; width: 24px; height: 24px; background: #1b9e88;
                                                             color: white; border-radius: 50%; text-align: center; line-height: 24px;
                                                             font-size: 12px; margin-right: 10px;">2</span>
                                                ' . get_string('confirm_email_step2', 'local_jobboard') . '
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; color: #555555; font-size: 14px;">
                                                <span style="display: inline-block; width: 24px; height: 24px; background: #1b9e88;
                                                             color: white; border-radius: 50%; text-align: center; line-height: 24px;
                                                             font-size: 12px; margin-right: 10px;">3</span>
                                                ' . get_string('confirm_email_step3', 'local_jobboard') . '
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding: 8px 0; color: #555555; font-size: 14px;">
                                                <span style="display: inline-block; width: 24px; height: 24px; background: #1b9e88;
                                                             color: white; border-radius: 50%; text-align: center; line-height: 24px;
                                                             font-size: 12px; margin-right: 10px;">4</span>
                                                ' . get_string('confirm_email_step4', 'local_jobboard') . '
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </td>
                        </tr>

                        <!-- Footer -->
                        <tr>
                            <td style="background: #f8f9fa; padding: 20px 40px; border-top: 1px solid #e9ecef;">
                                <p style="margin: 0 0 12px 0; color: #555555; font-size: 13px; text-align: center;">
                                    ' . get_string('confirm_email_help', 'local_jobboard') . '
                                    <a href="' . htmlspecialchars($supporturl->out(false)) . '"
                                       style="color: #1b9e88; text-decoration: underline;">
                                       ' . get_string('support_page_title', 'local_jobboard') . '
                                    </a>
                                </p>
                                <p style="margin: 0 0 10px 0; color: #666666; font-size: 13px; text-align: center;">
                                    ' . get_string('confirm_email_ignore', 'local_jobboard') . '
                                </p>
                                <p style="margin: 0; color: #999999; font-size: 12px; text-align: center;">
                                    ' . get_string('confirm_email_footer', 'local_jobboard') . '
                                </p>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </body>
    </html>';

    // Send the email.
    $result = email_to_user(
        $user,
        $supportuser,
        $subject,
        $messagetext,
        $messagehtml
    );

    return $result;
}

/**
 * Log the signup in the audit table.
 *
 * @param int $userid The user ID.
 * @param stdClass $data The form data.
 */
function log_signup_audit($userid, $data) {
    global $DB;

    $dbman = $DB->get_manager();
    if (!$dbman->table_exists('local_jobboard_audit')) {
        return;
    }

    $audit = new stdClass();
    $audit->userid = $userid;
    $audit->action = 'user_signup';
    $audit->entitytype = 'user';
    $audit->entityid = $userid;
    $audit->ipaddress = getremoteaddr();
    // Sanitize user agent to prevent XSS in logs.
    $audit->useragent = substr(clean_param($_SERVER['HTTP_USER_AGENT'] ?? '', PARAM_TEXT), 0, 512);
    $audit->extradata = json_encode([
        'vacancyid' => $data->vacancyid ?? 0,
        'companyid' => $data->companyid ?? 0,
        'source' => 'jobboard_signup',
    ]);
    $audit->timecreated = time();

    $DB->insert_record('local_jobboard_audit', $audit);
}
