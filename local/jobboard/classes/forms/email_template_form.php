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

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_jobboard\email_template;

/**
 * Email template edit form - Refactored v3.0.
 *
 * Modern form for editing email templates with rich text editor,
 * placeholder suggestions, and live preview support.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class email_template_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        /** @var email_template|null $template */
        $template = $this->_customdata['template'] ?? null;
        $code = $this->_customdata['code'] ?? ($template->code ?? '');
        $companyid = (int) ($this->_customdata['companyid'] ?? 0);
        $placeholders = $this->_customdata['placeholders'] ?? email_template::get_placeholders($code);

        // =========================================================================
        // HIDDEN FIELDS
        // =========================================================================

        $mform->addElement('hidden', 'action', 'save');
        $mform->setType('action', PARAM_ALPHA);

        $mform->addElement('hidden', 'code', $code);
        $mform->setType('code', PARAM_ALPHANUMEXT);

        $mform->addElement('hidden', 'companyid', $companyid);
        $mform->setType('companyid', PARAM_INT);

        $mform->addElement('hidden', 'sesskey', sesskey());
        $mform->setType('sesskey', PARAM_RAW);

        if ($template && $template->id > 0) {
            $mform->addElement('hidden', 'id', $template->id);
            $mform->setType('id', PARAM_INT);
        }

        // =========================================================================
        // TEMPLATE INFORMATION
        // =========================================================================

        $mform->addElement('header', 'templateinfo', get_string('template_info', 'local_jobboard'));
        $mform->setExpanded('templateinfo', true);

        // Template name (read-only for system templates).
        $mform->addElement('text', 'name', get_string('template_name', 'local_jobboard'), ['size' => 60]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('required'), 'required', null, 'client');

        if ($template) {
            $mform->setDefault('name', $template->name);
        }

        // Category display (read-only).
        if ($template) {
            $categoryname = email_template::get_category_name($template->category);
            $mform->addElement('static', 'category_display', get_string('template_category', 'local_jobboard'),
                '<span class="badge badge-info">' . s($categoryname) . '</span>'
            );
        }

        // Description.
        $mform->addElement('textarea', 'description', get_string('template_description', 'local_jobboard'),
            ['rows' => 2, 'cols' => 60]);
        $mform->setType('description', PARAM_TEXT);

        if ($template) {
            $mform->setDefault('description', $template->description);
        }

        // =========================================================================
        // EMAIL CONTENT
        // =========================================================================

        $mform->addElement('header', 'emailcontent', get_string('template_content', 'local_jobboard'));
        $mform->setExpanded('emailcontent', true);

        // Subject field.
        $mform->addElement('text', 'subject', get_string('template_subject', 'local_jobboard'),
            ['size' => 80, 'class' => 'form-control-lg']);
        $mform->setType('subject', PARAM_TEXT);
        $mform->addRule('subject', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('subject', 'template_subject', 'local_jobboard');

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

        $mform->addElement('editor', 'body_editor', get_string('template_body', 'local_jobboard'),
            ['rows' => 20, 'cols' => 80], $editoroptions);
        $mform->setType('body_editor', PARAM_RAW);
        $mform->addRule('body_editor', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('body_editor', 'template_body', 'local_jobboard');

        if ($template) {
            $mform->setDefault('body_editor', [
                'text' => $template->body,
                'format' => $template->bodyformat ?: FORMAT_HTML,
            ]);
        }

        // =========================================================================
        // AVAILABLE PLACEHOLDERS
        // =========================================================================

        if (!empty($placeholders)) {
            $mform->addElement('header', 'placeholdersheader', get_string('available_placeholders', 'local_jobboard'));
            $mform->setExpanded('placeholdersheader', true);

            $placeholderhtml = $this->render_placeholders_panel($placeholders);
            $mform->addElement('html', $placeholderhtml);
        }

        // =========================================================================
        // SETTINGS
        // =========================================================================

        $mform->addElement('header', 'settingsheader', get_string('template_settings', 'local_jobboard'));
        $mform->setExpanded('settingsheader', false);

        // Enabled checkbox.
        $mform->addElement('advcheckbox', 'enabled', get_string('template_enabled', 'local_jobboard'),
            get_string('template_enabled_desc', 'local_jobboard'));
        $mform->setDefault('enabled', $template ? ($template->enabled ? 1 : 0) : 1);

        // Priority.
        $priorityoptions = [];
        for ($i = 0; $i <= 100; $i += 10) {
            $priorityoptions[$i] = $i;
        }
        $mform->addElement('select', 'priority', get_string('template_priority', 'local_jobboard'), $priorityoptions);
        $mform->setDefault('priority', $template ? $template->priority : 0);
        $mform->addHelpButton('priority', 'template_priority', 'local_jobboard');

        // =========================================================================
        // PREVIEW SECTION
        // =========================================================================

        $mform->addElement('header', 'previewheader', get_string('template_preview', 'local_jobboard'));
        $mform->setExpanded('previewheader', false);

        $previewhtml = $this->render_preview_panel();
        $mform->addElement('html', $previewhtml);

        // =========================================================================
        // ACTION BUTTONS
        // =========================================================================

        $this->add_action_buttons(true, get_string('savechanges'));
    }

    /**
     * Render the placeholders panel HTML.
     *
     * @param array $placeholders Placeholder definitions.
     * @return string HTML content.
     */
    protected function render_placeholders_panel(array $placeholders): string {
        $html = '<div class="card border-info mb-3">';
        $html .= '<div class="card-body">';
        $html .= '<p class="card-text text-muted small mb-3">';
        $html .= '<i class="fa fa-info-circle mr-1"></i>';
        $html .= get_string('placeholders_help', 'local_jobboard');
        $html .= '</p>';

        $html .= '<div class="row">';

        $count = 0;
        $perColumn = ceil(count($placeholders) / 2);

        foreach ($placeholders as $placeholder => $description) {
            if ($count % $perColumn === 0) {
                $html .= '<div class="col-md-6">';
            }

            $html .= '<div class="placeholder-item mb-2 p-2 border rounded bg-light">';
            $html .= '<button type="button" class="btn btn-sm btn-outline-primary copy-placeholder mr-2" ';
            $html .= 'data-placeholder="' . s($placeholder) . '" title="' . get_string('copy_placeholder', 'local_jobboard') . '">';
            $html .= '<i class="fa fa-copy"></i>';
            $html .= '</button>';
            $html .= '<code class="text-primary font-weight-bold">' . s($placeholder) . '</code>';
            $html .= '<br><small class="text-muted">' . s($description) . '</small>';
            $html .= '</div>';

            $count++;
            if ($count % $perColumn === 0 || $count === count($placeholders)) {
                $html .= '</div>';
            }
        }

        $html .= '</div>'; // row
        $html .= '</div>'; // card-body
        $html .= '</div>'; // card

        // JavaScript for copy functionality.
        $html .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll(".copy-placeholder").forEach(function(btn) {
                btn.addEventListener("click", function() {
                    var placeholder = this.getAttribute("data-placeholder");
                    navigator.clipboard.writeText(placeholder).then(function() {
                        btn.innerHTML = \'<i class="fa fa-check text-success"></i>\';
                        setTimeout(function() {
                            btn.innerHTML = \'<i class="fa fa-copy"></i>\';
                        }, 1500);
                    });
                });
            });
        });
        </script>';

        return $html;
    }

    /**
     * Render the preview panel HTML.
     *
     * @return string HTML content.
     */
    protected function render_preview_panel(): string {
        $html = '<div id="template-preview-container" class="card">';
        $html .= '<div class="card-header d-flex justify-content-between align-items-center">';
        $html .= '<span><i class="fa fa-eye mr-2"></i>' . get_string('template_preview', 'local_jobboard') . '</span>';
        $html .= '<button type="button" id="refresh-preview" class="btn btn-sm btn-outline-secondary">';
        $html .= '<i class="fa fa-sync-alt mr-1"></i>' . get_string('refresh', 'local_jobboard') . '</button>';
        $html .= '</div>';
        $html .= '<div class="card-body">';

        // Preview subject.
        $html .= '<div class="preview-section mb-3">';
        $html .= '<label class="text-muted small text-uppercase">' . get_string('subject') . '</label>';
        $html .= '<div id="preview-subject" class="border rounded p-2 bg-light">';
        $html .= '<span class="text-muted">' . get_string('template_preview_hint', 'local_jobboard') . '</span>';
        $html .= '</div>';
        $html .= '</div>';

        // Preview body.
        $html .= '<div class="preview-section">';
        $html .= '<label class="text-muted small text-uppercase">' . get_string('template_body', 'local_jobboard') . '</label>';
        $html .= '<div id="preview-body" class="border rounded p-3 bg-white jb-preview-container">';
        $html .= '<span class="text-muted">' . get_string('template_preview_hint', 'local_jobboard') . '</span>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>'; // card-body
        $html .= '</div>'; // card

        // Preview JavaScript.
        $html .= '<script>
        document.addEventListener("DOMContentLoaded", function() {
            var subjectInput = document.getElementById("id_subject");
            var bodyEditor = document.getElementById("id_body_editor_ifr") || document.querySelector("[name=\'body_editor[text]\']");
            var previewSubject = document.getElementById("preview-subject");
            var previewBody = document.getElementById("preview-body");
            var refreshBtn = document.getElementById("refresh-preview");

            // Sample data for preview.
            var sampleData = {
                "{user_fullname}": "Juan Pérez García",
                "{user_firstname}": "Juan",
                "{user_lastname}": "Pérez García",
                "{user_email}": "juan.perez@ejemplo.com",
                "{site_name}": document.title || "Sistema ISER",
                "{site_url}": window.location.origin,
                "{company_name}": "Centro Tutorial Cúcuta",
                "{vacancy_code}": "CONV-2024-001",
                "{vacancy_title}": "Docente de Programación",
                "{application_id}": "12345",
                "{application_url}": window.location.origin + "/local/jobboard/",
                "{submit_date}": new Date().toLocaleDateString(),
                "{current_date}": new Date().toLocaleDateString(),
                "{documents_count}": "5",
                "{rejected_docs}": "- Hoja de vida: Formato incorrecto",
                "{observations}": "Por favor revise los documentos señalados.",
                "{interview_date}": new Date(Date.now() + 3*24*60*60*1000).toLocaleDateString(),
                "{interview_time}": "10:00 AM",
                "{interview_location}": "Sala A, Edificio Principal"
            };

            function replacePlaceholders(text) {
                if (!text) return "";
                for (var key in sampleData) {
                    text = text.split(key).join(sampleData[key]);
                }
                return text;
            }

            function updatePreview() {
                // Get subject.
                var subject = subjectInput ? subjectInput.value : "";
                previewSubject.innerHTML = replacePlaceholders(subject) || "<em class=\"text-muted\">Sin asunto</em>";

                // Get body from editor.
                var body = "";
                if (window.tinymce && tinymce.get("id_body_editoreditable")) {
                    body = tinymce.get("id_body_editoreditable").getContent();
                } else if (bodyEditor && bodyEditor.contentDocument) {
                    body = bodyEditor.contentDocument.body.innerHTML;
                } else {
                    var textarea = document.querySelector("[name=\'body_editor[text]\']");
                    if (textarea) body = textarea.value;
                }

                previewBody.innerHTML = replacePlaceholders(body) || "<em class=\"text-muted\">Sin contenido</em>";
            }

            // Refresh button click.
            if (refreshBtn) {
                refreshBtn.addEventListener("click", updatePreview);
            }

            // Auto-update on subject change.
            if (subjectInput) {
                subjectInput.addEventListener("input", updatePreview);
            }

            // Initial preview.
            setTimeout(updatePreview, 1000);
        });
        </script>';

        return $html;
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
        if (empty(trim($data['subject'] ?? ''))) {
            $errors['subject'] = get_string('required');
        }

        // Body must have content.
        $bodytext = $data['body_editor']['text'] ?? '';
        if (empty(trim(strip_tags($bodytext)))) {
            $errors['body_editor'] = get_string('required');
        }

        // Name must not be empty.
        if (empty(trim($data['name'] ?? ''))) {
            $errors['name'] = get_string('required');
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

    /**
     * Get the body format from the editor.
     *
     * @return int The body format.
     */
    public function get_body_format(): int {
        $data = $this->get_data();
        if (!$data) {
            return FORMAT_HTML;
        }

        return (int) ($data->body_editor['format'] ?? FORMAT_HTML);
    }

    /**
     * Process form data and return a template object.
     *
     * @return email_template|null Template object or null if cancelled/invalid.
     */
    public function get_template(): ?email_template {
        $data = $this->get_data();
        if (!$data) {
            return null;
        }

        $template = new email_template();

        if (!empty($data->id)) {
            $existing = email_template::get_by_id((int) $data->id);
            if ($existing) {
                $template = $existing;
            }
        }

        $template->code = $data->code ?? '';
        $template->companyid = (int) ($data->companyid ?? 0);
        $template->name = $data->name ?? '';
        $template->description = $data->description ?? '';
        $template->subject = $data->subject ?? '';
        $template->body = $data->body_editor['text'] ?? '';
        $template->bodyformat = (int) ($data->body_editor['format'] ?? FORMAT_HTML);
        $template->enabled = !empty($data->enabled);
        $template->priority = (int) ($data->priority ?? 0);

        // Set category from code if not already set.
        if (empty($template->category)) {
            $template->category = email_template::get_category_for_code($template->code);
        }

        return $template;
    }
}
