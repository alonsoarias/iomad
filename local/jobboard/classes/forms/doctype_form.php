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
 * Form for creating/editing document types.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Document type form.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class doctype_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;
        $doctype = $this->_customdata['doctype'] ?? null;
        $isedit = !empty($doctype);

        // Hidden ID for editing.
        $mform->addElement('hidden', 'id', 0);
        $mform->setType('id', PARAM_INT);

        // Basic information header.
        $mform->addElement('header', 'basicheader', get_string('basicinfo', 'local_jobboard'));

        // Code.
        $mform->addElement('text', 'code', get_string('code', 'local_jobboard'), ['size' => 30, 'maxlength' => 50]);
        $mform->setType('code', PARAM_ALPHANUMEXT);
        $mform->addRule('code', get_string('required'), 'required', null, 'client');
        $mform->addRule('code', get_string('maximumchars', '', 50), 'maxlength', 50, 'client');
        if ($isedit) {
            $mform->freeze('code');
        }

        // Name.
        $mform->addElement('text', 'name', get_string('name'), ['size' => 60, 'maxlength' => 255]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        // Description.
        $mform->addElement('editor', 'description_editor', get_string('description'), null, [
            'maxfiles' => 0,
            'noclean' => false,
            'context' => \context_system::instance(),
        ]);

        // Category.
        $categories = [
            '' => get_string('select'),
            'identity' => get_string('doccategory_identity', 'local_jobboard'),
            'academic' => get_string('doccategory_academic', 'local_jobboard'),
            'professional' => get_string('doccategory_professional', 'local_jobboard'),
            'background' => get_string('doccategory_background', 'local_jobboard'),
            'financial' => get_string('doccategory_financial', 'local_jobboard'),
            'health' => get_string('doccategory_health', 'local_jobboard'),
            'other' => get_string('other'),
        ];
        $mform->addElement('select', 'category', get_string('category', 'local_jobboard'), $categories);

        // Input type (how applicants provide this document).
        $inputtypes = [
            'file' => get_string('inputtype_file', 'local_jobboard'),
            'text' => get_string('inputtype_text', 'local_jobboard'),
            'url' => get_string('inputtype_url', 'local_jobboard'),
            'number' => get_string('inputtype_number', 'local_jobboard'),
        ];
        $mform->addElement('select', 'input_type', get_string('inputtype', 'local_jobboard'), $inputtypes);
        $mform->addHelpButton('input_type', 'inputtype', 'local_jobboard');
        $mform->setDefault('input_type', 'file');

        // Requirements header.
        $mform->addElement('header', 'requirementsheader', get_string('requirements', 'local_jobboard'));

        // Is required.
        $mform->addElement('advcheckbox', 'isrequired', get_string('required', 'local_jobboard'),
            get_string('doctype_isrequired_help', 'local_jobboard'));
        $mform->setDefault('isrequired', 1);

        // External URL.
        $mform->addElement('text', 'externalurl', get_string('externalurl', 'local_jobboard'),
            ['size' => 60, 'maxlength' => 512]);
        $mform->setType('externalurl', PARAM_URL);
        $mform->addHelpButton('externalurl', 'externalurl', 'local_jobboard');

        // Requirements text.
        $mform->addElement('editor', 'requirements_editor', get_string('validationrequirements', 'local_jobboard'), null, [
            'maxfiles' => 0,
            'noclean' => false,
            'context' => \context_system::instance(),
        ]);

        // Default max age days.
        $mform->addElement('text', 'defaultmaxagedays', get_string('defaultmaxagedays', 'local_jobboard'), ['size' => 5]);
        $mform->setType('defaultmaxagedays', PARAM_INT);
        $mform->addHelpButton('defaultmaxagedays', 'defaultmaxagedays', 'local_jobboard');

        // Exemptions header.
        $mform->addElement('header', 'exemptionsheader', get_string('exemptions', 'local_jobboard'));

        // ISER exempted.
        $mform->addElement('advcheckbox', 'iserexempted', get_string('iserexempted', 'local_jobboard'),
            get_string('iserexempted_help', 'local_jobboard'));

        // Gender condition.
        $genders = [
            '' => get_string('allapplicants', 'local_jobboard'),
            'M' => get_string('menonly', 'local_jobboard'),
            'F' => get_string('womenonly', 'local_jobboard'),
        ];
        $mform->addElement('select', 'gender_condition', get_string('gendercondition', 'local_jobboard'), $genders);
        $mform->addHelpButton('gender_condition', 'gendercondition', 'local_jobboard');

        // Age exemption threshold.
        $mform->addElement('text', 'age_exemption_threshold', get_string('ageexemptionthreshold', 'local_jobboard'),
            ['size' => 5]);
        $mform->setType('age_exemption_threshold', PARAM_INT);
        $mform->addHelpButton('age_exemption_threshold', 'ageexemptionthreshold', 'local_jobboard');

        // Profession exemption (JSON array of education types).
        $educationtypes = [
            'tecnico' => get_string('signup_edu_tecnico', 'local_jobboard'),
            'tecnologo' => get_string('signup_edu_tecnologo', 'local_jobboard'),
            'profesional' => get_string('signup_edu_profesional', 'local_jobboard'),
            'especialista' => get_string('signup_edu_especialista', 'local_jobboard'),
            'magister' => get_string('signup_edu_magister', 'local_jobboard'),
            'doctor' => get_string('signup_edu_doctor', 'local_jobboard'),
        ];
        $select = $mform->addElement('select', 'profession_exempt_arr',
            get_string('professionexempt', 'local_jobboard'), $educationtypes);
        $select->setMultiple(true);
        $mform->addHelpButton('profession_exempt_arr', 'professionexempt', 'local_jobboard');

        // Conditional note.
        $mform->addElement('editor', 'conditional_note_editor', get_string('conditionalnote', 'local_jobboard'), null, [
            'maxfiles' => 0,
            'noclean' => false,
            'context' => \context_system::instance(),
        ]);
        $mform->addHelpButton('conditional_note_editor', 'conditionalnote', 'local_jobboard');

        // Configuration header.
        $mform->addElement('header', 'configheader', get_string('configuration', 'local_jobboard'));

        // Checklist items (JSON array).
        $mform->addElement('textarea', 'checklistitems', get_string('checklistitems', 'local_jobboard'),
            ['rows' => 4, 'cols' => 60]);
        $mform->setType('checklistitems', PARAM_TEXT);
        $mform->addHelpButton('checklistitems', 'checklistitems', 'local_jobboard');

        // Sort order.
        $mform->addElement('text', 'sortorder', get_string('sortorder', 'local_jobboard'), ['size' => 5]);
        $mform->setType('sortorder', PARAM_INT);
        $mform->setDefault('sortorder', 0);

        // Enabled.
        $mform->addElement('advcheckbox', 'enabled', get_string('enabled', 'local_jobboard'));
        $mform->setDefault('enabled', 1);

        // Action buttons.
        $this->add_action_buttons(true, $isedit ? get_string('savechanges') : get_string('create'));

        // Set data for editing.
        if ($doctype) {
            // Convert profession_exempt JSON to array.
            if (!empty($doctype->profession_exempt)) {
                $doctype->profession_exempt_arr = json_decode($doctype->profession_exempt, true) ?? [];
            }

            // Prepare editor fields.
            $doctype->description_editor = [
                'text' => $doctype->description ?? '',
                'format' => FORMAT_HTML,
            ];
            $doctype->requirements_editor = [
                'text' => $doctype->requirements ?? '',
                'format' => FORMAT_HTML,
            ];
            $doctype->conditional_note_editor = [
                'text' => $doctype->conditional_note ?? '',
                'format' => FORMAT_HTML,
            ];

            $this->set_data($doctype);
        }
    }

    /**
     * Form validation.
     *
     * @param array $data Submitted data.
     * @param array $files Uploaded files.
     * @return array Validation errors.
     */
    public function validation($data, $files) {
        global $DB;

        $errors = parent::validation($data, $files);

        // Validate code uniqueness.
        if (!empty($data['code'])) {
            $conditions = ['code' => $data['code']];
            if (!empty($data['id'])) {
                $existing = $DB->get_record('local_jobboard_doctype', $conditions);
                if ($existing && $existing->id != $data['id']) {
                    $errors['code'] = get_string('error:codealreadyexists', 'local_jobboard');
                }
            } else {
                if ($DB->record_exists('local_jobboard_doctype', $conditions)) {
                    $errors['code'] = get_string('error:codealreadyexists', 'local_jobboard');
                }
            }
        }

        // Validate code format.
        if (!empty($data['code']) && !preg_match('/^[a-zA-Z][a-zA-Z0-9_]*$/', $data['code'])) {
            $errors['code'] = get_string('error:invalidcode', 'local_jobboard');
        }

        // Validate age exemption threshold.
        if (!empty($data['age_exemption_threshold'])) {
            $age = (int) $data['age_exemption_threshold'];
            if ($age < 18 || $age > 100) {
                $errors['age_exemption_threshold'] = get_string('error:invalidage', 'local_jobboard');
            }
        }

        // Validate URL format.
        if (!empty($data['externalurl']) && !filter_var($data['externalurl'], FILTER_VALIDATE_URL)) {
            $errors['externalurl'] = get_string('error:invalidurl', 'local_jobboard');
        }

        return $errors;
    }

    /**
     * Get processed data from form.
     *
     * @return object|null Form data or null if not submitted/cancelled.
     */
    public function get_data() {
        $data = parent::get_data();

        if ($data) {
            // Convert profession_exempt array to JSON.
            if (!empty($data->profession_exempt_arr) && is_array($data->profession_exempt_arr)) {
                $data->profession_exempt = json_encode(array_values($data->profession_exempt_arr));
            } else {
                $data->profession_exempt = null;
            }
            unset($data->profession_exempt_arr);

            // Extract text from editor fields.
            if (isset($data->description_editor) && is_array($data->description_editor)) {
                $data->description = $data->description_editor['text'] ?? '';
            }
            unset($data->description_editor);

            if (isset($data->requirements_editor) && is_array($data->requirements_editor)) {
                $data->requirements = $data->requirements_editor['text'] ?? '';
            }
            unset($data->requirements_editor);

            if (isset($data->conditional_note_editor) && is_array($data->conditional_note_editor)) {
                $data->conditional_note = $data->conditional_note_editor['text'] ?? '';
            }
            unset($data->conditional_note_editor);
        }

        return $data;
    }
}
