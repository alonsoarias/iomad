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

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Support form for reporting technical issues with the Job Board plugin.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class support_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        global $USER, $CFG;

        $mform = $this->_form;

        // Editor options for Moodle text editor with image support.
        $context = \context_system::instance();
        $editoroptions = [
            'maxfiles' => EDITOR_UNLIMITED_FILES,
            'maxbytes' => $CFG->maxbytes,
            'trusttext' => false,
            'noclean' => false,
            'context' => $context,
            'subdirs' => false,
        ];

        // Instructions section.
        $mform->addElement('header', 'instructionsheader', get_string('support_instructions_title', 'local_jobboard'));
        $mform->setExpanded('instructionsheader', true);

        // Important instructions message.
        $instructionshtml = '<div class="alert alert-info">';
        $instructionshtml .= '<h6 class="alert-heading"><i class="fa fa-info-circle me-2"></i>' . get_string('support_instructions_title', 'local_jobboard') . '</h6>';
        $instructionshtml .= '<p class="mb-2">' . get_string('support_instructions_detail', 'local_jobboard') . '</p>';
        $instructionshtml .= '<ul class="mb-0">';
        $instructionshtml .= '<li>' . get_string('support_instructions_tip1', 'local_jobboard') . '</li>';
        $instructionshtml .= '<li>' . get_string('support_instructions_tip2', 'local_jobboard') . '</li>';
        $instructionshtml .= '<li>' . get_string('support_instructions_tip3', 'local_jobboard') . '</li>';
        $instructionshtml .= '</ul>';
        $instructionshtml .= '</div>';
        $mform->addElement('html', $instructionshtml);

        // Error type section.
        $mform->addElement('header', 'errorheader', get_string('support_error_details', 'local_jobboard'));
        $mform->setExpanded('errorheader', true);

        // Error type selection.
        $errortypes = [
            '' => get_string('support_select_error_type', 'local_jobboard'),
            'document_upload' => get_string('support_type_document_upload', 'local_jobboard'),
            'form_submission' => get_string('support_type_form_submission', 'local_jobboard'),
            'page_error' => get_string('support_type_page_error', 'local_jobboard'),
            'login_issue' => get_string('support_type_login_issue', 'local_jobboard'),
            'display_issue' => get_string('support_type_display_issue', 'local_jobboard'),
            'notification_issue' => get_string('support_type_notification_issue', 'local_jobboard'),
            'other' => get_string('support_type_other', 'local_jobboard'),
        ];
        $mform->addElement('select', 'error_type', get_string('support_error_type', 'local_jobboard'), $errortypes);
        $mform->addRule('error_type', get_string('required'), 'required', null, 'client');
        $mform->setType('error_type', PARAM_TEXT);

        // Error description using Moodle editor with image support.
        $mform->addElement('editor', 'description_editor', get_string('support_error_description', 'local_jobboard'), null, $editoroptions);
        $mform->addRule('description_editor', get_string('required'), 'required', null, 'client');
        $mform->setType('description_editor', PARAM_RAW);
        $mform->addHelpButton('description_editor', 'support_error_description', 'local_jobboard');

        // Steps to reproduce (REQUIRED) using Moodle editor with image support.
        $mform->addElement('editor', 'steps_to_reproduce_editor', get_string('support_steps_to_reproduce', 'local_jobboard'), null, $editoroptions);
        $mform->addRule('steps_to_reproduce_editor', get_string('required'), 'required', null, 'client');
        $mform->setType('steps_to_reproduce_editor', PARAM_RAW);
        $mform->addHelpButton('steps_to_reproduce_editor', 'support_steps_to_reproduce', 'local_jobboard');

        // Expected behavior (optional) using Moodle editor with image support.
        $mform->addElement('editor', 'expected_behavior_editor', get_string('support_expected_behavior', 'local_jobboard'), null, $editoroptions);
        $mform->setType('expected_behavior_editor', PARAM_RAW);

        // Page URL where the error occurred (optional).
        $mform->addElement('text', 'page_url', get_string('support_page_url', 'local_jobboard'), ['size' => 60]);
        $mform->setType('page_url', PARAM_URL);

        // Contact information section.
        $mform->addElement('header', 'contactheader', get_string('support_contact_info', 'local_jobboard'));
        $mform->setExpanded('contactheader', true);

        // Reporter name.
        $defaultname = '';
        if (isloggedin() && !isguestuser()) {
            $defaultname = fullname($USER);
        }
        $mform->addElement('text', 'reporter_name', get_string('support_reporter_name', 'local_jobboard'), ['size' => 40]);
        $mform->addRule('reporter_name', get_string('required'), 'required', null, 'client');
        $mform->setType('reporter_name', PARAM_TEXT);
        $mform->setDefault('reporter_name', $defaultname);

        // Reporter email.
        $defaultemail = '';
        if (isloggedin() && !isguestuser()) {
            $defaultemail = $USER->email;
        }
        $mform->addElement('text', 'reporter_email', get_string('support_reporter_email', 'local_jobboard'), ['size' => 40]);
        $mform->addRule('reporter_email', get_string('required'), 'required', null, 'client');
        $mform->addRule('reporter_email', get_string('invalidemail'), 'email', null, 'client');
        $mform->setType('reporter_email', PARAM_EMAIL);
        $mform->setDefault('reporter_email', $defaultemail);

        // Submit buttons.
        $this->add_action_buttons(true, get_string('support_submit', 'local_jobboard'));
    }

    /**
     * Validation.
     *
     * @param array $data Form data.
     * @param array $files Files.
     * @return array Errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate error type is selected.
        if (empty($data['error_type'])) {
            $errors['error_type'] = get_string('required');
        }

        // Validate description has minimum length (editor returns array with 'text' key).
        $descriptiontext = '';
        if (!empty($data['description_editor']['text'])) {
            $descriptiontext = strip_tags($data['description_editor']['text']);
        }
        if (strlen(trim($descriptiontext)) < 20) {
            $errors['description_editor'] = get_string('support_description_too_short', 'local_jobboard');
        }

        // Validate steps to reproduce has minimum length (now required).
        $stepstext = '';
        if (!empty($data['steps_to_reproduce_editor']['text'])) {
            $stepstext = strip_tags($data['steps_to_reproduce_editor']['text']);
        }
        if (strlen(trim($stepstext)) < 20) {
            $errors['steps_to_reproduce_editor'] = get_string('support_steps_too_short', 'local_jobboard');
        }

        return $errors;
    }
}
