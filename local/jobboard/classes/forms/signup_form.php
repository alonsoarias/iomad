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
 * Alternative signup form with company selection for Job Board.
 *
 * This form allows new users to register and select a company/department
 * when applying for vacancies in IOMAD environments.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');
require_once($CFG->dirroot . '/user/editlib.php');

use moodleform;
use context_system;

/**
 * Alternative signup form for job applicants.
 */
class signup_form extends moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        global $CFG;

        $mform = $this->_form;
        $vacancyid = $this->_customdata['vacancyid'] ?? 0;
        $companies = $this->_customdata['companies'] ?? [];
        $isiomad = $this->_customdata['isiomad'] ?? false;

        // Hidden field for vacancy redirect.
        $mform->addElement('hidden', 'vacancyid', $vacancyid);
        $mform->setType('vacancyid', PARAM_INT);

        // Personal information header.
        $mform->addElement('header', 'personalinfo', get_string('signup_personalinfo', 'local_jobboard'));
        $mform->setExpanded('personalinfo', true);

        // Username.
        $mform->addElement('text', 'username', get_string('username'), 'maxlength="100" size="25"');
        $mform->setType('username', PARAM_RAW);
        $mform->addRule('username', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('username', 'signup_username', 'local_jobboard');

        // Password with strength requirements.
        $mform->addElement('passwordunmask', 'password', get_string('password'), 'maxlength="32" size="25"');
        $mform->setType('password', PARAM_RAW);
        $mform->addRule('password', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('password', 'signup_password', 'local_jobboard');

        // Email.
        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="40"');
        $mform->setType('email', PARAM_RAW_TRIMMED);
        $mform->addRule('email', get_string('required'), 'required', null, 'client');

        // Email confirmation.
        $mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" size="40"');
        $mform->setType('email2', PARAM_RAW_TRIMMED);
        $mform->addRule('email2', get_string('required'), 'required', null, 'client');

        // First name.
        $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('required'), 'required', null, 'client');

        // Last name.
        $mform->addElement('text', 'lastname', get_string('lastname'), 'maxlength="100" size="30"');
        $mform->setType('lastname', PARAM_TEXT);
        $mform->addRule('lastname', get_string('required'), 'required', null, 'client');

        // Additional contact information header.
        $mform->addElement('header', 'contactinfo', get_string('signup_contactinfo', 'local_jobboard'));
        $mform->setExpanded('contactinfo', true);

        // Phone.
        $mform->addElement('text', 'phone1', get_string('phone'), 'maxlength="20" size="20"');
        $mform->setType('phone1', PARAM_TEXT);

        // City.
        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="30"');
        $mform->setType('city', PARAM_TEXT);

        // Country.
        $choices = get_string_manager()->get_list_of_countries();
        $choices = ['' => get_string('selectacountry') . '...'] + $choices;
        $mform->addElement('select', 'country', get_string('country'), $choices);
        if (!empty($CFG->country)) {
            $mform->setDefault('country', $CFG->country);
        }

        // Identification number.
        $mform->addElement('text', 'idnumber', get_string('signup_idnumber', 'local_jobboard'), 'maxlength="50" size="25"');
        $mform->setType('idnumber', PARAM_TEXT);
        $mform->addHelpButton('idnumber', 'signup_idnumber', 'local_jobboard');

        // Company selection (IOMAD only).
        if ($isiomad && !empty($companies)) {
            $mform->addElement('header', 'companyheader', get_string('signup_companyinfo', 'local_jobboard'));
            $mform->setExpanded('companyheader', true);

            $mform->addElement('html', '<div class="alert alert-info">' .
                get_string('signup_company_help', 'local_jobboard') . '</div>');

            // Company selector.
            $companyoptions = [0 => get_string('selectcompany', 'local_jobboard')] + $companies;
            $mform->addElement('select', 'companyid', get_string('company', 'local_jobboard'), $companyoptions, [
                'id' => 'id_companyid_signup',
            ]);
            $mform->addRule('companyid', get_string('required'), 'required', null, 'client');

            // Department selector (will be populated via AJAX).
            $mform->addElement('select', 'departmentid', get_string('department', 'local_jobboard'),
                [0 => get_string('selectdepartment', 'local_jobboard')], [
                'id' => 'id_departmentid_signup',
            ]);
        }

        // Terms and privacy header.
        $mform->addElement('header', 'termsheader', get_string('signup_termsheader', 'local_jobboard'));
        $mform->setExpanded('termsheader', true);

        // Privacy policy.
        $privacyurl = get_config('local_jobboard', 'privacy_policy_url');
        if (empty($privacyurl)) {
            $privacyurl = new \moodle_url('/admin/tool/policy/viewall.php');
        }

        $privacytext = get_string('signup_privacy_text', 'local_jobboard', $privacyurl);
        $mform->addElement('html', '<div class="privacy-notice mb-3">' . $privacytext . '</div>');

        // Terms acceptance.
        $mform->addElement('advcheckbox', 'policyagreed', '',
            get_string('signup_terms_accept', 'local_jobboard'), ['group' => 1], [0, 1]);
        $mform->addRule('policyagreed', get_string('signup_terms_required', 'local_jobboard'),
            'required', null, 'client');
        $mform->addRule('policyagreed', get_string('signup_terms_required', 'local_jobboard'),
            'nonzero', null, 'client');

        // Data treatment consent.
        $datatreatmenttext = get_config('local_jobboard', 'datatreatmentpolicy');
        if (!empty($datatreatmenttext)) {
            $mform->addElement('html', '<div class="data-treatment-policy my-3"><strong>' .
                get_string('datatreatmentpolicytitle', 'local_jobboard') . '</strong><br>' .
                '<small>' . shorten_text(strip_tags($datatreatmenttext), 200) . '</small></div>');
        }

        $mform->addElement('advcheckbox', 'datatreatmentagreed', '',
            get_string('signup_datatreatment_accept', 'local_jobboard'), ['group' => 1], [0, 1]);
        $mform->addRule('datatreatmentagreed', get_string('signup_datatreatment_required', 'local_jobboard'),
            'required', null, 'client');
        $mform->addRule('datatreatmentagreed', get_string('signup_datatreatment_required', 'local_jobboard'),
            'nonzero', null, 'client');

        // reCAPTCHA if enabled.
        if (!empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey)) {
            $mform->addElement('recaptcha', 'recaptcha_element', get_string('security_question', 'auth'));
            $mform->addHelpButton('recaptcha_element', 'recaptcha', 'auth');
            $mform->closeHeaderBefore('recaptcha_element');
        }

        // Submit button.
        $this->add_action_buttons(true, get_string('signup_createaccount', 'local_jobboard'));
    }

    /**
     * Validate the form data.
     *
     * @param array $data Form data.
     * @param array $files Uploaded files.
     * @return array Validation errors.
     */
    public function validation($data, $files) {
        global $DB, $CFG;

        $errors = parent::validation($data, $files);

        // Username validation.
        $username = trim(strtolower($data['username']));
        if (empty($username)) {
            $errors['username'] = get_string('required');
        } else {
            // Check username format.
            if ($username !== clean_param($username, PARAM_USERNAME)) {
                $errors['username'] = get_string('invalidusername');
            }
            // Check if username exists.
            if ($DB->record_exists('user', ['username' => $username, 'mnethostid' => $CFG->mnet_localhost_id])) {
                $errors['username'] = get_string('usernameexists');
            }
        }

        // Password validation.
        $password = $data['password'] ?? '';
        if (empty($password)) {
            $errors['password'] = get_string('required');
        } else {
            $errmsg = '';
            if (!check_password_policy($password, $errmsg)) {
                $errors['password'] = $errmsg;
            }
        }

        // Email validation.
        $email = trim($data['email']);
        if (empty($email)) {
            $errors['email'] = get_string('required');
        } else if (!validate_email($email)) {
            $errors['email'] = get_string('invalidemail');
        } else if ($DB->record_exists('user', ['email' => $email])) {
            $errors['email'] = get_string('emailexists');
        }

        // Email confirmation.
        if ($email !== trim($data['email2'])) {
            $errors['email2'] = get_string('emailnotmatch', 'local_jobboard');
        }

        // First name validation.
        if (empty(trim($data['firstname']))) {
            $errors['firstname'] = get_string('required');
        }

        // Last name validation.
        if (empty(trim($data['lastname']))) {
            $errors['lastname'] = get_string('required');
        }

        // Company validation (IOMAD).
        $isiomad = $this->_customdata['isiomad'] ?? false;
        if ($isiomad && !empty($this->_customdata['companies'])) {
            if (empty($data['companyid'])) {
                $errors['companyid'] = get_string('required');
            }
        }

        // Terms validation.
        if (empty($data['policyagreed'])) {
            $errors['policyagreed'] = get_string('signup_terms_required', 'local_jobboard');
        }

        // Data treatment validation.
        if (empty($data['datatreatmentagreed'])) {
            $errors['datatreatmentagreed'] = get_string('signup_datatreatment_required', 'local_jobboard');
        }

        // reCAPTCHA validation.
        if (!empty($CFG->recaptchapublickey) && !empty($CFG->recaptchaprivatekey)) {
            $recaptchaelement = $this->_form->getElement('recaptcha_element');
            if (!empty($recaptchaelement)) {
                if (!$recaptchaelement->verify($data['g-recaptcha-response'] ?? '')) {
                    $errors['recaptcha_element'] = get_string('incorrectpleasetryagain', 'auth');
                }
            }
        }

        return $errors;
    }
}
