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
 * Vacancy form for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for creating and editing vacancies.
 */
class vacancy_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        global $CFG;

        $mform = $this->_form;
        $vacancy = $this->_customdata['vacancy'] ?? null;
        $isedit = !empty($vacancy) && $vacancy->id > 0;

        // Header: Basic Information.
        $mform->addElement('header', 'basicinfo', get_string('vacancytitle', 'local_jobboard'));

        // Vacancy code.
        $mform->addElement('text', 'code', get_string('vacancycode', 'local_jobboard'), ['size' => 20, 'maxlength' => 50]);
        $mform->setType('code', PARAM_ALPHANUMEXT);
        $mform->addRule('code', get_string('error:requiredfield', 'local_jobboard'), 'required', null, 'client');
        $mform->addHelpButton('code', 'vacancycode', 'local_jobboard');

        // Title.
        $mform->addElement('text', 'title', get_string('vacancytitle', 'local_jobboard'), ['size' => 60, 'maxlength' => 255]);
        $mform->setType('title', PARAM_TEXT);
        $mform->addRule('title', get_string('error:requiredfield', 'local_jobboard'), 'required', null, 'client');
        $mform->addHelpButton('title', 'vacancytitle', 'local_jobboard');

        // Description.
        $mform->addElement('editor', 'description', get_string('vacancydescription', 'local_jobboard'), null, [
            'maxfiles' => 0,
            'noclean' => false,
        ]);
        $mform->setType('description', PARAM_RAW);
        $mform->addHelpButton('description', 'vacancydescription', 'local_jobboard');

        // Header: Contract Details.
        $mform->addElement('header', 'contractdetails', get_string('contracttype', 'local_jobboard'));

        // Contract type.
        $contracttypes = ['' => get_string('selectcontracttype', 'local_jobboard')] + \local_jobboard_get_contract_types();
        $mform->addElement('select', 'contracttype', get_string('contracttype', 'local_jobboard'), $contracttypes);
        $mform->setType('contracttype', PARAM_ALPHA);
        $mform->addHelpButton('contracttype', 'contracttype', 'local_jobboard');

        // Duration.
        $mform->addElement('text', 'duration', get_string('duration', 'local_jobboard'), ['size' => 40, 'maxlength' => 100]);
        $mform->setType('duration', PARAM_TEXT);
        $mform->addHelpButton('duration', 'duration', 'local_jobboard');

        // Salary.
        $mform->addElement('text', 'salary', get_string('salary', 'local_jobboard'), ['size' => 40, 'maxlength' => 100]);
        $mform->setType('salary', PARAM_TEXT);
        $mform->addHelpButton('salary', 'salary', 'local_jobboard');

        // Location.
        $mform->addElement('text', 'location', get_string('location', 'local_jobboard'), ['size' => 60, 'maxlength' => 255]);
        $mform->setType('location', PARAM_TEXT);
        $mform->addHelpButton('location', 'location', 'local_jobboard');

        // Department.
        $mform->addElement('text', 'department', get_string('department', 'local_jobboard'), ['size' => 60, 'maxlength' => 255]);
        $mform->setType('department', PARAM_TEXT);
        $mform->addHelpButton('department', 'department', 'local_jobboard');

        // Header: Dates and Positions.
        $mform->addElement('header', 'datepositions', get_string('opendate', 'local_jobboard'));

        // Opening date.
        $mform->addElement('date_time_selector', 'opendate', get_string('opendate', 'local_jobboard'));
        $mform->addRule('opendate', get_string('error:requiredfield', 'local_jobboard'), 'required', null, 'client');
        $mform->addHelpButton('opendate', 'opendate', 'local_jobboard');

        // Closing date.
        $mform->addElement('date_time_selector', 'closedate', get_string('closedate', 'local_jobboard'));
        $mform->addRule('closedate', get_string('error:requiredfield', 'local_jobboard'), 'required', null, 'client');
        $mform->addHelpButton('closedate', 'closedate', 'local_jobboard');

        // Number of positions.
        $mform->addElement('text', 'positions', get_string('positions', 'local_jobboard'), ['size' => 5]);
        $mform->setType('positions', PARAM_INT);
        $mform->setDefault('positions', 1);
        $mform->addHelpButton('positions', 'positions', 'local_jobboard');

        // Header: Requirements.
        $mform->addElement('header', 'requirementsheader', get_string('requirements', 'local_jobboard'));

        // Minimum requirements.
        $mform->addElement('editor', 'requirements', get_string('requirements', 'local_jobboard'), null, [
            'maxfiles' => 0,
            'noclean' => false,
        ]);
        $mform->setType('requirements', PARAM_RAW);
        $mform->addHelpButton('requirements', 'requirements', 'local_jobboard');

        // Desirable requirements.
        $mform->addElement('editor', 'desirable', get_string('desirable', 'local_jobboard'), null, [
            'maxfiles' => 0,
            'noclean' => false,
        ]);
        $mform->setType('desirable', PARAM_RAW);
        $mform->addHelpButton('desirable', 'desirable', 'local_jobboard');

        // Header: Associations.
        $mform->addElement('header', 'associations', get_string('course', 'local_jobboard'));

        // Associated course (optional).
        $courses = $this->get_courses_options();
        $mform->addElement('autocomplete', 'courseid', get_string('course', 'local_jobboard'), $courses, [
            'noselectionstring' => get_string('selectcourse', 'local_jobboard'),
        ]);
        $mform->setType('courseid', PARAM_INT);
        $mform->addHelpButton('courseid', 'course', 'local_jobboard');

        // Associated category (optional).
        $categories = $this->get_categories_options();
        $mform->addElement('autocomplete', 'categoryid', get_string('category', 'local_jobboard'), $categories, [
            'noselectionstring' => get_string('selectcategory', 'local_jobboard'),
        ]);
        $mform->setType('categoryid', PARAM_INT);
        $mform->addHelpButton('categoryid', 'category', 'local_jobboard');

        // Company and Department (Iomad multi-tenant).
        if (\local_jobboard_is_iomad_installed()) {
            // Header for IOMAD section.
            $mform->addElement('header', 'iomadsection', get_string('iomadsettings', 'local_jobboard'));

            // Company selector.
            $companies = [0 => get_string('selectcompany', 'local_jobboard')] + \local_jobboard_get_companies();
            $mform->addElement('select', 'companyid', get_string('company', 'local_jobboard'), $companies, [
                'id' => 'id_companyid',
            ]);
            $mform->setType('companyid', PARAM_INT);
            $mform->addHelpButton('companyid', 'company', 'local_jobboard');

            // Pre-select current user's company.
            $defaultcompanyid = 0;
            if (!$isedit) {
                $usercompanyid = \local_jobboard_get_user_companyid();
                if ($usercompanyid) {
                    $mform->setDefault('companyid', $usercompanyid);
                    $defaultcompanyid = $usercompanyid;
                }
            } else if ($vacancy && $vacancy->companyid) {
                $defaultcompanyid = (int) $vacancy->companyid;
            }

            // Department selector (IOMAD).
            $iomadinfo = \local_jobboard_get_iomad_info();
            if ($iomadinfo['has_departments']) {
                // Get departments for the selected/default company.
                $departments = [0 => get_string('selectdepartment', 'local_jobboard')];
                if ($defaultcompanyid > 0) {
                    $departments += \local_jobboard_get_departments($defaultcompanyid);
                }

                $mform->addElement('select', 'departmentid', get_string('iomad_department', 'local_jobboard'), $departments, [
                    'id' => 'id_departmentid',
                ]);
                $mform->setType('departmentid', PARAM_INT);
                $mform->addHelpButton('departmentid', 'iomad_department', 'local_jobboard');

                // Add JavaScript to update departments when company changes.
                global $PAGE;
                $PAGE->requires->js_call_amd('local_jobboard/vacancy_form', 'init', []);
            }

            // Publication type for IOMAD (public or internal).
            $pubtypes = [
                'public' => get_string('publicationtype:public', 'local_jobboard'),
                'internal' => get_string('publicationtype:internal', 'local_jobboard'),
            ];
            $mform->addElement('select', 'publicationtype', get_string('publicationtype', 'local_jobboard'), $pubtypes);
            $mform->setType('publicationtype', PARAM_ALPHA);
            $mform->setDefault('publicationtype', 'public');
            $mform->addHelpButton('publicationtype', 'publicationtype', 'local_jobboard');
        } else {
            // For non-IOMAD installations, default to public.
            $mform->addElement('hidden', 'publicationtype', 'public');
            $mform->setType('publicationtype', PARAM_ALPHA);
        }

        // Hidden vacancy ID for editing.
        $mform->addElement('hidden', 'id');
        $mform->setType('id', PARAM_INT);

        // Action buttons.
        $this->add_action_buttons(true, $isedit ? get_string('save', 'local_jobboard') : get_string('create', 'local_jobboard'));
    }

    /**
     * Validation.
     *
     * @param array $data The submitted data.
     * @param array $files The submitted files.
     * @return array Array of errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate vacancy code uniqueness.
        $excludeid = !empty($data['id']) ? $data['id'] : 0;
        if (!empty($data['code']) && \local_jobboard\vacancy::code_exists($data['code'], $excludeid)) {
            $errors['code'] = get_string('error:codeexists', 'local_jobboard');
        }

        // Validate dates.
        if (!empty($data['opendate']) && !empty($data['closedate'])) {
            if ($data['closedate'] <= $data['opendate']) {
                $errors['closedate'] = get_string('error:invaliddates', 'local_jobboard');
            }
        }

        // Validate positions.
        if (isset($data['positions']) && $data['positions'] < 1) {
            $errors['positions'] = get_string('error:requiredfield', 'local_jobboard');
        }

        return $errors;
    }

    /**
     * Get courses for dropdown.
     *
     * @return array Course ID => name.
     */
    protected function get_courses_options(): array {
        global $DB;

        $courses = $DB->get_records('course', ['visible' => 1], 'fullname ASC', 'id, fullname, shortname');

        $options = [];
        foreach ($courses as $course) {
            if ($course->id == SITEID) {
                continue;
            }
            $options[$course->id] = $course->fullname . ' (' . $course->shortname . ')';
        }

        return $options;
    }

    /**
     * Get course categories for dropdown.
     *
     * @return array Category ID => name.
     */
    protected function get_categories_options(): array {
        $categories = \core_course_category::make_categories_list();
        return $categories;
    }

    /**
     * Set data from vacancy object.
     *
     * @param \local_jobboard\vacancy $vacancy The vacancy.
     */
    public function set_data_from_vacancy(\local_jobboard\vacancy $vacancy): void {
        $data = new \stdClass();
        $data->id = $vacancy->id;
        $data->code = $vacancy->code;
        $data->title = $vacancy->title;
        $data->description = ['text' => $vacancy->description, 'format' => FORMAT_HTML];
        $data->contracttype = $vacancy->contracttype;
        $data->duration = $vacancy->duration;
        $data->salary = $vacancy->salary;
        $data->location = $vacancy->location;
        $data->department = $vacancy->department;
        $data->courseid = $vacancy->courseid;
        $data->categoryid = $vacancy->categoryid;
        $data->companyid = $vacancy->companyid;
        $data->departmentid = $vacancy->departmentid ?? 0;
        $data->publicationtype = $vacancy->publicationtype ?? 'public';
        $data->opendate = $vacancy->opendate;
        $data->closedate = $vacancy->closedate;
        $data->positions = $vacancy->positions;
        $data->requirements = ['text' => $vacancy->requirements, 'format' => FORMAT_HTML];
        $data->desirable = ['text' => $vacancy->desirable, 'format' => FORMAT_HTML];

        $this->set_data($data);
    }
}
