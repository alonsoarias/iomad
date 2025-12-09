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
 * Email template edit form.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for editing email templates with rich text editor.
 */
class email_template_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;
        $code = $this->_customdata['code'] ?? '';
        $template = $this->_customdata['template'] ?? null;
        $placeholders = $this->_customdata['placeholders'] ?? [];

        // Hidden fields.
        $mform->addElement('hidden', 'action', 'save');
        $mform->setType('action', PARAM_ALPHA);

        $mform->addElement('hidden', 'code', $code);
        $mform->setType('code', PARAM_ALPHANUMEXT);

        $mform->addElement('hidden', 'sesskey', sesskey());
        $mform->setType('sesskey', PARAM_RAW);

        // Template info header.
        $mform->addElement('header', 'templateinfo', get_string('emailtemplate', 'local_jobboard'));

        // Subject field.
        $mform->addElement('text', 'subject', get_string('templatesubject', 'local_jobboard'), ['size' => 80]);
        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('subject', 'templatesubject', 'local_jobboard');

        if ($template) {
            $mform->setDefault('subject', $template->subject);
        }

        // Body field with HTML editor.
        $editoroptions = [
            'maxfiles' => 0,
            'noclean' => false,
            'context' => \context_system::instance(),
            'subdirs' => false,
            'maxbytes' => 0,
            'trusttext' => false,
        ];

        $mform->addElement('editor', 'body_editor', get_string('templatebody', 'local_jobboard'),
            ['rows' => 20, 'cols' => 80], $editoroptions);
        $mform->setType('body_editor', PARAM_RAW);
        $mform->addRule('body_editor', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('body_editor', 'templatebody', 'local_jobboard');

        if ($template) {
            $mform->setDefault('body_editor', [
                'text' => $template->body,
                'format' => FORMAT_HTML,
            ]);
        }

        // Placeholders info.
        if (!empty($placeholders)) {
            $mform->addElement('header', 'placeholdersheader', get_string('availableplaceholders', 'local_jobboard'));
            $mform->setExpanded('placeholdersheader', true);

            $placeholderhtml = '<div class="alert alert-info">';
            $placeholderhtml .= '<p class="mb-2">' . get_string('placeholders_help', 'local_jobboard') . '</p>';
            $placeholderhtml .= '<div class="row">';

            $count = 0;
            $cols = ceil(count($placeholders) / 2);
            foreach ($placeholders as $placeholder => $desc) {
                if ($count % $cols === 0) {
                    $placeholderhtml .= '<div class="col-md-6"><ul class="list-unstyled">';
                }

                $placeholderhtml .= '<li class="mb-2">';
                $placeholderhtml .= '<code class="bg-white px-2 py-1 border rounded">' . s($placeholder) . '</code>';
                $placeholderhtml .= '<br><small class="text-muted">' . s($desc) . '</small>';
                $placeholderhtml .= '</li>';

                $count++;
                if ($count % $cols === 0 || $count === count($placeholders)) {
                    $placeholderhtml .= '</ul></div>';
                }
            }

            $placeholderhtml .= '</div></div>';

            $mform->addElement('html', $placeholderhtml);
        }

        // Preview section with live updates.
        $mform->addElement('header', 'previewheader', get_string('livepreview', 'local_jobboard'));
        $mform->setExpanded('previewheader', true);

        $previewhtml = '<div id="template-preview" class="border rounded p-3 bg-light">';
        $previewhtml .= '<div class="text-center text-muted py-4">';
        $previewhtml .= '<i class="fa fa-spinner fa-spin fa-2x mb-2"></i>';
        $previewhtml .= '<p>' . get_string('preview_loading', 'local_jobboard') . '</p>';
        $previewhtml .= '</div>';
        $previewhtml .= '</div>';
        $previewhtml .= '<div class="alert alert-info mt-2 small">';
        $previewhtml .= '<i class="fa fa-info-circle mr-1"></i>';
        $previewhtml .= get_string('preview_hint', 'local_jobboard');
        $previewhtml .= '</div>';
        $mform->addElement('html', $previewhtml);

        // Action buttons.
        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * Form validation.
     *
     * @param array $data Form data.
     * @param array $files Uploaded files.
     * @return array Validation errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Subject must not be empty.
        if (empty(trim($data['subject']))) {
            $errors['subject'] = get_string('required');
        }

        // Body must have content.
        $bodytext = $data['body_editor']['text'] ?? '';
        if (empty(trim(strip_tags($bodytext)))) {
            $errors['body_editor'] = get_string('required');
        }

        return $errors;
    }

    /**
     * Get the body text from the editor.
     *
     * @return string The body text.
     */
    public function get_body_text(): string {
        $data = $this->get_data();
        if (!$data) {
            return '';
        }

        return $data->body_editor['text'] ?? '';
    }
}
