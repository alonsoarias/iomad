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

declare(strict_types=1);

/**
 * Profile update form for Job Board applicants.
 *
 * This form allows existing users to update their profile and select
 * a company/department when applying for vacancies in IOMAD environments.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use moodleform;

/**
 * Profile update form for job applicants.
 */
class updateprofile_form extends moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        global $CFG, $DB;

        $mform = $this->_form;
        $vacancyid = $this->_customdata['vacancyid'] ?? 0;
        $companies = $this->_customdata['companies'] ?? [];
        $isiomad = $this->_customdata['isiomad'] ?? false;
        $userid = $this->_customdata['userid'] ?? 0;
        $usercompanyid = $this->_customdata['usercompanyid'] ?? 0;

        // Get user record for checking existing values.
        $user = $DB->get_record('user', ['id' => $userid]);

        // Hidden field for vacancy redirect.
        $mform->addElement('hidden', 'vacancyid', $vacancyid);
        $mform->setType('vacancyid', PARAM_INT);

        // ==========================================
        // SECTION 1: Account Information (Read-only)
        // ==========================================
        $mform->addElement('header', 'accountheader', get_string('signup_account_header', 'local_jobboard'));
        $mform->setExpanded('accountheader', true);

        // Email (read-only).
        $mform->addElement('static', 'email_display', get_string('email'), $user->email ?? '');
        $mform->addElement('hidden', 'email', $user->email ?? '');
        $mform->setType('email', PARAM_EMAIL);

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

        // Identification number.
        if (!empty($user->idnumber)) {
            // If already set, show as read-only.
            $mform->addElement('static', 'idnumber_display', get_string('signup_idnumber', 'local_jobboard'), $user->idnumber);
            $mform->addElement('hidden', 'idnumber', $user->idnumber);
            $mform->setType('idnumber', PARAM_TEXT);
        } else {
            // Allow editing if not set.
            $mform->addElement('text', 'idnumber', get_string('signup_idnumber', 'local_jobboard'),
                'maxlength="50" size="25"');
            $mform->setType('idnumber', PARAM_TEXT);
            $mform->addRule('idnumber', get_string('required'), 'required', null, 'client');
        }

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

            // Department selector (will be populated via AJAX).
            $departmentoptions = [0 => get_string('selectdepartment', 'local_jobboard')];
            // If user already has a company, load its departments.
            if (!empty($usercompanyid)) {
                $departments = local_jobboard_get_departments($usercompanyid);
                if (!empty($departments)) {
                    $departmentoptions = $departmentoptions + $departments;
                }
            }
            $mform->addElement('select', 'departmentid', get_string('department', 'local_jobboard'),
                $departmentoptions, [
                'id' => 'id_departmentid_signup',
            ]);
        }

        // Submit button.
        $this->add_action_buttons(true, get_string('updateprofile_submit', 'local_jobboard'));
    }

    /**
     * Validate the form data.
     *
     * @param array $data Form data.
     * @param array $files Uploaded files.
     * @return array Validation errors.
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);
        $userid = $this->_customdata['userid'] ?? 0;

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

        // ID number validation (only if editable).
        $user = $DB->get_record('user', ['id' => $userid]);
        if (empty($user->idnumber) && empty(trim($data['idnumber'] ?? ''))) {
            $errors['idnumber'] = get_string('required');
        } else if (empty($user->idnumber) && !empty($data['idnumber'])) {
            // Check if idnumber already exists for another user.
            $existing = $DB->get_record('user', ['idnumber' => trim($data['idnumber'])]);
            if ($existing && $existing->id != $userid) {
                $errors['idnumber'] = get_string('signup_idnumber_exists', 'local_jobboard');
            }
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

        return $errors;
    }
}
