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
 * when applying for vacancies in IOMAD environments. It captures complete
 * profile information for job applicants.
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

        // ==========================================
        // SECTION 1: Account Credentials
        // ==========================================
        $mform->addElement('header', 'accountheader', get_string('signup_account_header', 'local_jobboard'));
        $mform->setExpanded('accountheader', true);

        // Notice about username being the ID number.
        $mform->addElement('html', '<div class="alert alert-info mb-3">' .
            '<i class="fa fa-info-circle me-2"></i>' .
            get_string('signup_username_is_idnumber', 'local_jobboard') . '</div>');

        // Password with strength requirements.
        $mform->addElement('passwordunmask', 'password', get_string('password'), 'maxlength="32" size="25"');
        $mform->setType('password', PARAM_RAW);
        $mform->addRule('password', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('password', 'signup_password', 'local_jobboard');

        // Email.
        $mform->addElement('text', 'email', get_string('email'), 'maxlength="100" size="40"');
        $mform->setType('email', PARAM_RAW_TRIMMED);
        $mform->addRule('email', get_string('required'), 'required', null, 'client');
        $mform->addRule('email', get_string('invalidemail'), 'email', null, 'client');
        $mform->addHelpButton('email', 'signup_email', 'local_jobboard');

        // Email confirmation.
        $mform->addElement('text', 'email2', get_string('emailagain'), 'maxlength="100" size="40"');
        $mform->setType('email2', PARAM_RAW_TRIMMED);
        $mform->addRule('email2', get_string('required'), 'required', null, 'client');

        // ==========================================
        // SECTION 2: Personal Information
        // ==========================================
        $mform->addElement('header', 'personalinfo', get_string('signup_personalinfo', 'local_jobboard'));
        $mform->setExpanded('personalinfo', true);

        // First name.
        $mform->addElement('text', 'firstname', get_string('firstname'), 'maxlength="100" size="30"');
        $mform->setType('firstname', PARAM_TEXT);
        $mform->addRule('firstname', get_string('required'), 'required', null, 'client');

        // Last name.
        $mform->addElement('text', 'lastname', get_string('lastname'), 'maxlength="100" size="30"');
        $mform->setType('lastname', PARAM_TEXT);
        $mform->addRule('lastname', get_string('required'), 'required', null, 'client');

        // Document type.
        $doctypes = [
            '' => get_string('select') . '...',
            'cc' => get_string('signup_doctype_cc', 'local_jobboard'),
            'ce' => get_string('signup_doctype_ce', 'local_jobboard'),
            'passport' => get_string('signup_doctype_passport', 'local_jobboard'),
            'ti' => get_string('signup_doctype_ti', 'local_jobboard'),
            'pep' => get_string('signup_doctype_pep', 'local_jobboard'),
            'ppt' => get_string('signup_doctype_ppt', 'local_jobboard'),
            'other' => get_string('other'),
        ];
        $mform->addElement('select', 'doctype', get_string('signup_doctype', 'local_jobboard'), $doctypes);
        $mform->addRule('doctype', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('doctype', 'signup_doctype', 'local_jobboard');

        // Identification number (this will be the username).
        $mform->addElement('text', 'idnumber', get_string('signup_idnumber', 'local_jobboard'),
            'maxlength="50" size="25" id="id_idnumber"');
        $mform->setType('idnumber', PARAM_TEXT);
        $mform->addRule('idnumber', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('idnumber', 'signup_idnumber_username', 'local_jobboard');

        // Date of birth.
        $mform->addElement('date_selector', 'birthdate', get_string('signup_birthdate', 'local_jobboard'), [
            'startyear' => 1940,
            'stopyear' => date('Y') - 18,
            'optional' => false,
        ]);
        $mform->addRule('birthdate', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('birthdate', 'signup_birthdate', 'local_jobboard');

        // Gender.
        $genders = [
            '' => get_string('select') . '...',
            'M' => get_string('signup_gender_male', 'local_jobboard'),
            'F' => get_string('signup_gender_female', 'local_jobboard'),
            'O' => get_string('signup_gender_other', 'local_jobboard'),
            'N' => get_string('signup_gender_prefer_not', 'local_jobboard'),
        ];
        $mform->addElement('select', 'gender', get_string('signup_gender', 'local_jobboard'), $genders);

        // ==========================================
        // SECTION 3: Contact Information
        // ==========================================
        $mform->addElement('header', 'contactinfo', get_string('signup_contactinfo', 'local_jobboard'));
        $mform->setExpanded('contactinfo', true);

        // Phone (primary/mobile).
        $mform->addElement('text', 'phone1', get_string('signup_phone_mobile', 'local_jobboard'), 'maxlength="20" size="20"');
        $mform->setType('phone1', PARAM_TEXT);
        $mform->addRule('phone1', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('phone1', 'signup_phone', 'local_jobboard');

        // Phone (secondary/home).
        $mform->addElement('text', 'phone2', get_string('signup_phone_home', 'local_jobboard'), 'maxlength="20" size="20"');
        $mform->setType('phone2', PARAM_TEXT);

        // Address.
        $mform->addElement('text', 'address', get_string('address'), 'maxlength="255" size="50"');
        $mform->setType('address', PARAM_TEXT);

        // City.
        $mform->addElement('text', 'city', get_string('city'), 'maxlength="120" size="30"');
        $mform->setType('city', PARAM_TEXT);
        $mform->addRule('city', get_string('required'), 'required', null, 'client');

        // Department/State/Province.
        $mform->addElement('text', 'department_region', get_string('signup_department_region', 'local_jobboard'), 'maxlength="100" size="30"');
        $mform->setType('department_region', PARAM_TEXT);

        // Country.
        $choices = get_string_manager()->get_list_of_countries();
        $choices = ['' => get_string('selectacountry') . '...'] + $choices;
        $mform->addElement('select', 'country', get_string('country'), $choices);
        $mform->addRule('country', get_string('required'), 'required', null, 'client');
        if (!empty($CFG->country)) {
            $mform->setDefault('country', $CFG->country);
        }

        // ==========================================
        // SECTION 4: Academic and Professional Profile
        // ==========================================
        $mform->addElement('header', 'academicheader', get_string('signup_academic_header', 'local_jobboard'));
        $mform->setExpanded('academicheader', true);

        // Highest education level.
        $educationlevels = [
            '' => get_string('select') . '...',
            'highschool' => get_string('signup_edu_highschool', 'local_jobboard'),
            'technical' => get_string('signup_edu_technical', 'local_jobboard'),
            'technological' => get_string('signup_edu_technological', 'local_jobboard'),
            'undergraduate' => get_string('signup_edu_undergraduate', 'local_jobboard'),
            'specialization' => get_string('signup_edu_specialization', 'local_jobboard'),
            'masters' => get_string('signup_edu_masters', 'local_jobboard'),
            'doctorate' => get_string('signup_edu_doctorate', 'local_jobboard'),
            'postdoctorate' => get_string('signup_edu_postdoctorate', 'local_jobboard'),
        ];
        $mform->addElement('select', 'education_level', get_string('signup_education_level', 'local_jobboard'), $educationlevels);
        $mform->addRule('education_level', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('education_level', 'signup_education_level', 'local_jobboard');

        // Degree/Title obtained.
        $mform->addElement('text', 'degree_title', get_string('signup_degree_title', 'local_jobboard'), 'maxlength="255" size="50"');
        $mform->setType('degree_title', PARAM_TEXT);
        $mform->addHelpButton('degree_title', 'signup_degree_title', 'local_jobboard');

        // Institution where degree was obtained.
        $mform->addElement('text', 'institution', get_string('institution'), 'maxlength="255" size="50"');
        $mform->setType('institution', PARAM_TEXT);
        $mform->addHelpButton('institution', 'signup_institution', 'local_jobboard');

        // Area of expertise/specialization.
        $mform->addElement('text', 'expertise_area', get_string('signup_expertise_area', 'local_jobboard'), 'maxlength="255" size="50"');
        $mform->setType('expertise_area', PARAM_TEXT);
        $mform->addHelpButton('expertise_area', 'signup_expertise_area', 'local_jobboard');

        // Years of professional experience.
        $experienceoptions = [
            '' => get_string('select') . '...',
            '0' => get_string('signup_exp_none', 'local_jobboard'),
            '1' => get_string('signup_exp_less_1', 'local_jobboard'),
            '2' => get_string('signup_exp_1_3', 'local_jobboard'),
            '3' => get_string('signup_exp_3_5', 'local_jobboard'),
            '4' => get_string('signup_exp_5_10', 'local_jobboard'),
            '5' => get_string('signup_exp_more_10', 'local_jobboard'),
        ];
        $mform->addElement('select', 'experience_years', get_string('signup_experience_years', 'local_jobboard'), $experienceoptions);

        // Professional profile / brief description.
        $mform->addElement('textarea', 'description', get_string('signup_professional_profile', 'local_jobboard'),
            ['rows' => 4, 'cols' => 60, 'maxlength' => 1000]);
        $mform->setType('description', PARAM_TEXT);
        $mform->addHelpButton('description', 'signup_professional_profile', 'local_jobboard');

        // ==========================================
        // SECTION 5: Company Selection (IOMAD only)
        // ==========================================
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
            $mform->addHelpButton('companyid', 'companyid', 'local_jobboard');

            // Department selector (will be populated via AJAX).
            $mform->addElement('select', 'departmentid', get_string('department', 'local_jobboard'),
                [0 => get_string('selectdepartment', 'local_jobboard')], [
                'id' => 'id_departmentid_signup',
            ]);
            $mform->addHelpButton('departmentid', 'departmentid', 'local_jobboard');
        }

        // ==========================================
        // SECTION 6: Terms and Privacy
        // ==========================================
        $mform->addElement('header', 'termsheader', get_string('signup_termsheader', 'local_jobboard'));
        $mform->setExpanded('termsheader', true);

        // Privacy policy notice.
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
            $mform->addElement('html', '<div class="data-treatment-policy my-3 p-3 border rounded"><strong>' .
                get_string('datatreatmentpolicytitle', 'local_jobboard') . '</strong><br>' .
                '<div class="small mt-2" style="max-height: 150px; overflow-y: auto;">' .
                format_text($datatreatmenttext, FORMAT_HTML) . '</div></div>');
        }

        $mform->addElement('advcheckbox', 'datatreatmentagreed', '',
            get_string('signup_datatreatment_accept', 'local_jobboard'), ['group' => 1], [0, 1]);
        $mform->addRule('datatreatmentagreed', get_string('signup_datatreatment_required', 'local_jobboard'),
            'required', null, 'client');
        $mform->addRule('datatreatmentagreed', get_string('signup_datatreatment_required', 'local_jobboard'),
            'nonzero', null, 'client');

        // Data accuracy declaration.
        $mform->addElement('advcheckbox', 'dataaccuracy', '',
            get_string('signup_dataaccuracy_accept', 'local_jobboard'), ['group' => 1], [0, 1]);
        $mform->addRule('dataaccuracy', get_string('signup_dataaccuracy_required', 'local_jobboard'),
            'required', null, 'client');
        $mform->addRule('dataaccuracy', get_string('signup_dataaccuracy_required', 'local_jobboard'),
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

        // ID Number validation (this will be the username).
        $idnumber = trim($data['idnumber'] ?? '');
        if (empty($idnumber)) {
            $errors['idnumber'] = get_string('required');
        } else {
            // Clean the ID number for use as username.
            $username = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($idnumber));

            // Check minimum length.
            if (strlen($username) < 4) {
                $errors['idnumber'] = get_string('signup_idnumber_tooshort', 'local_jobboard');
            }

            // Check if username (idnumber) already exists.
            if ($DB->record_exists('user', ['username' => $username, 'mnethostid' => $CFG->mnet_localhost_id])) {
                $errors['idnumber'] = get_string('signup_idnumber_exists_as_user', 'local_jobboard');
            }

            // Check if idnumber already exists.
            if ($DB->record_exists('user', ['idnumber' => $idnumber])) {
                $errors['idnumber'] = get_string('signup_idnumber_exists', 'local_jobboard');
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

        // Document type validation.
        if (empty($data['doctype'])) {
            $errors['doctype'] = get_string('required');
        }

        // Birthdate validation (must be at least 18 years old).
        if (!empty($data['birthdate'])) {
            $birthdate = $data['birthdate'];
            $minage = 18 * 365 * 24 * 60 * 60; // 18 years in seconds.
            if ((time() - $birthdate) < $minage) {
                $errors['birthdate'] = get_string('signup_birthdate_minage', 'local_jobboard');
            }
        }

        // Phone validation.
        if (empty(trim($data['phone1']))) {
            $errors['phone1'] = get_string('required');
        }

        // City validation.
        if (empty(trim($data['city']))) {
            $errors['city'] = get_string('required');
        }

        // Country validation.
        if (empty($data['country'])) {
            $errors['country'] = get_string('required');
        }

        // Education level validation.
        if (empty($data['education_level'])) {
            $errors['education_level'] = get_string('required');
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

        // Data accuracy validation.
        if (empty($data['dataaccuracy'])) {
            $errors['dataaccuracy'] = get_string('signup_dataaccuracy_required', 'local_jobboard');
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
