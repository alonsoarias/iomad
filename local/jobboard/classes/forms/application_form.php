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
 * Application form - Rebuilt for reliability and admin customization.
 *
 * Document types are configurable from admin at:
 * Site administration > Plugins > Local plugins > Job Board > Manage document types
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Application submission form.
 *
 * Reads document types from database (local_jobboard_doctype table).
 * Documents can be managed at Site admin > Plugins > Job Board > Manage document types.
 */
class application_form extends \moodleform {

    /** @var array Filtered document types for this user */
    protected $filtereddocs = [];

    /**
     * Form definition.
     */
    protected function definition() {
        global $DB, $USER, $CFG;

        $mform = $this->_form;

        // Get custom data.
        $vacancy = $this->_customdata['vacancy'] ?? null;
        $isexemption = $this->_customdata['isexemption'] ?? false;
        $convocatoriaid = $this->_customdata['convocatoriaid'] ?? 0;

        if (!$vacancy) {
            throw new \moodle_exception('missingvacancy', 'local_jobboard');
        }

        // Get user profile for filtering.
        $userprofile = $DB->get_record('local_jobboard_applicant_profile', ['userid' => $USER->id]);
        $usergender = $userprofile->gender ?? '';
        $birthdate = (int) ($userprofile->birthdate ?? 0);
        $userage = $this->calculate_user_age($birthdate);

        // Check if user has active ISER exemption.
        $hasiserexemption = \local_jobboard\exemption::user_has_active_exemption($USER->id);

        // Load enabled document types.
        $doctypes = $this->load_document_types($convocatoriaid, $isexemption);

        // Filter based on user profile and exemptions.
        $this->filtereddocs = $this->filter_documents_for_user($doctypes, $usergender, $userage, $hasiserexemption);

        // Add form CSS class for styling.
        $mform->_attributes['class'] = 'jb-application-form';

        // ============================================================
        // HIDDEN FIELDS
        // ============================================================
        $mform->addElement('hidden', 'vacancyid', $vacancy->id);
        $mform->setType('vacancyid', PARAM_INT);

        // ============================================================
        // SECTION 1: CONSENT AND SIGNATURE
        // ============================================================
        $mform->addElement('header', 'consentheader', get_string('consentheader', 'local_jobboard'));
        $mform->setExpanded('consentheader', true);

        // Data treatment policy.
        $this->add_policy_section($mform);

        // Consent checkbox.
        $mform->addElement(
            'advcheckbox',
            'consentaccepted',
            get_string('consent', 'local_jobboard'),
            get_string('consentaccepttext', 'local_jobboard'),
            ['class' => 'jb-consent-checkbox'],
            [0, 1]
        );
        $mform->addRule('consentaccepted', get_string('consentrequired', 'local_jobboard'), 'nonzero', null, 'server');

        // Digital signature.
        $mform->addElement(
            'text',
            'digitalsignature',
            get_string('digitalsignature', 'local_jobboard'),
            ['size' => 50, 'maxlength' => 200, 'placeholder' => fullname($USER)]
        );
        $mform->setType('digitalsignature', PARAM_TEXT);
        $mform->addRule('digitalsignature', get_string('required'), 'required', null, 'server');
        $mform->setDefault('digitalsignature', fullname($USER));
        $mform->addHelpButton('digitalsignature', 'digitalsignature', 'local_jobboard');

        // ============================================================
        // SECTION 2: DOCUMENT UPLOADS
        // ============================================================
        if (!empty($this->filtereddocs)) {
            $mform->addElement('header', 'documentsheader', get_string('documents', 'local_jobboard'));
            $mform->setExpanded('documentsheader', true);

            // Instructions alert.
            $instructions = '<div class="alert alert-info">';
            $instructions .= '<i class="fa fa-info-circle me-2"></i>';
            $instructions .= get_string('documentshelp', 'local_jobboard');
            $instructions .= '</div>';
            $mform->addElement('html', $instructions);

            // Add document fields grouped by category.
            $this->add_document_fields($mform);
        }

        // ============================================================
        // SECTION 3: COVER LETTER (Optional)
        // ============================================================
        $mform->addElement('header', 'additionalheader', get_string('additionalinfo', 'local_jobboard'));

        $mform->addElement(
            'textarea',
            'coverletter',
            get_string('coverletter', 'local_jobboard'),
            ['rows' => 6, 'cols' => 60, 'class' => 'form-control']
        );
        $mform->setType('coverletter', PARAM_TEXT);
        $mform->addHelpButton('coverletter', 'coverletter', 'local_jobboard');

        // ============================================================
        // SECTION 4: DECLARATION
        // ============================================================
        $mform->addElement('header', 'declarationheader', get_string('declaration', 'local_jobboard'));
        $mform->setExpanded('declarationheader', true);

        // Declaration text.
        $declhtml = '<div class="alert alert-warning">';
        $declhtml .= '<i class="fa fa-exclamation-triangle me-2"></i>';
        $declhtml .= '<strong>' . get_string('declarationtitle', 'local_jobboard') . '</strong><br>';
        $declhtml .= get_string('declarationtext', 'local_jobboard');
        $declhtml .= '</div>';
        $mform->addElement('html', $declhtml);

        // Declaration checkbox.
        $mform->addElement(
            'advcheckbox',
            'declarationaccepted',
            get_string('declaration', 'local_jobboard'),
            get_string('declarationaccept', 'local_jobboard'),
            ['class' => 'jb-declaration-checkbox'],
            [0, 1]
        );
        $mform->addRule('declarationaccepted', get_string('declarationrequired', 'local_jobboard'), 'nonzero', null, 'server');

        // ============================================================
        // SUBMIT BUTTONS
        // ============================================================
        $this->add_action_buttons(true, get_string('submitapplication', 'local_jobboard'));
    }

    /**
     * Add the data treatment policy section.
     *
     * @param \MoodleQuickForm $mform Form object.
     */
    protected function add_policy_section(\MoodleQuickForm $mform): void {
        $policytext = get_config('local_jobboard', 'datatreatmentpolicy');
        if (empty($policytext)) {
            $policytext = get_string('defaultdatatreatmentpolicy', 'local_jobboard');
        }

        $html = '<div class="jb-policy-container card mb-3">';
        $html .= '<div class="card-header bg-light">';
        $html .= '<h6 class="mb-0"><i class="fa fa-shield-halved me-2"></i>';
        $html .= get_string('datatreatmentpolicytitle', 'local_jobboard') . '</h6>';
        $html .= '</div>';
        $html .= '<div class="card-body" style="max-height: 200px; overflow-y: auto;">';
        $html .= '<div class="small">' . format_text($policytext, FORMAT_HTML) . '</div>';
        $html .= '</div>';
        $html .= '</div>';

        $mform->addElement('html', $html);
    }

    /**
     * Add document upload fields grouped by category.
     *
     * @param \MoodleQuickForm $mform Form object.
     */
    protected function add_document_fields(\MoodleQuickForm $mform): void {
        // Get file options from settings.
        $maxsize = (int) get_config('local_jobboard', 'maxfilesize');
        $maxsize = $maxsize > 0 ? $maxsize * 1024 * 1024 : 10 * 1024 * 1024;

        $allowedformats = get_config('local_jobboard', 'allowedformats');
        $acceptedtypes = !empty($allowedformats) ? '.' . str_replace(',', ',.', $allowedformats) : '.pdf';

        $fileoptions = [
            'subdirs' => 0,
            'maxbytes' => $maxsize,
            'maxfiles' => 1,
            'accepted_types' => explode(',', $acceptedtypes),
        ];

        // Group documents by category.
        $categories = [];
        foreach ($this->filtereddocs as $doc) {
            $cat = $doc->category ?: 'general';
            if (!isset($categories[$cat])) {
                $categories[$cat] = [];
            }
            $categories[$cat][] = $doc;
        }

        // Category icons.
        $caticons = [
            'identification' => 'fa-id-card',
            'academic' => 'fa-graduation-cap',
            'employment' => 'fa-briefcase',
            'health' => 'fa-heart-pulse',
            'financial' => 'fa-building-columns',
            'legal' => 'fa-scale-balanced',
            'general' => 'fa-folder-open',
        ];

        foreach ($categories as $catkey => $catdocs) {
            // Category header.
            $catname = get_string_manager()->string_exists('doccat_' . $catkey, 'local_jobboard')
                ? get_string('doccat_' . $catkey, 'local_jobboard')
                : ucfirst($catkey);
            $caticon = $caticons[$catkey] ?? 'fa-folder';

            $catheader = '<div class="jb-doc-category-header mt-4 mb-3 pb-2 border-bottom">';
            $catheader .= '<h5 class="mb-0 text-primary">';
            $catheader .= '<i class="fa ' . $caticon . ' me-2"></i>' . $catname;
            $catheader .= '</h5>';
            $catheader .= '</div>';
            $mform->addElement('html', $catheader);

            // Add each document in this category.
            foreach ($catdocs as $doctype) {
                $this->add_single_document_field($mform, $doctype, $fileoptions);
            }
        }
    }

    /**
     * Add a single document field.
     *
     * @param \MoodleQuickForm $mform Form object.
     * @param object $doctype Document type record.
     * @param array $fileoptions File manager options.
     */
    protected function add_single_document_field(\MoodleQuickForm $mform, object $doctype, array $fileoptions): void {
        $fieldname = 'doc_' . $doctype->code;
        $isrequired = !empty($doctype->isrequired);

        // Document card.
        $cardclass = $isrequired ? 'border-danger' : 'border-secondary';
        $html = '<div class="jb-document-card card mb-3 ' . $cardclass . '">';
        $html .= '<div class="card-body py-3">';

        // Title with badge.
        $html .= '<div class="d-flex justify-content-between align-items-center mb-2">';
        $html .= '<strong>' . format_string($doctype->name) . '</strong>';
        if ($isrequired) {
            $html .= '<span class="badge bg-danger">' . get_string('required') . '</span>';
        } else {
            $html .= '<span class="badge bg-secondary">' . get_string('optional', 'local_jobboard') . '</span>';
        }
        $html .= '</div>';

        // Description.
        if (!empty($doctype->description)) {
            $html .= '<p class="small text-muted mb-2">' . format_string($doctype->description) . '</p>';
        }

        // Requirements in collapsible.
        if (!empty($doctype->requirements)) {
            $html .= '<details class="small mb-2">';
            $html .= '<summary class="text-primary" style="cursor: pointer;">';
            $html .= '<i class="fa fa-list-check me-1"></i>' . get_string('docrequirements', 'local_jobboard');
            $html .= '</summary>';
            $html .= '<div class="mt-1 ps-3 text-muted">' . format_string($doctype->requirements) . '</div>';
            $html .= '</details>';
        }

        // Conditional note.
        if (!empty($doctype->conditional_note)) {
            $html .= '<div class="alert alert-secondary py-1 px-2 small mb-2">';
            $html .= '<i class="fa fa-info-circle me-1"></i>';
            $html .= format_string($doctype->conditional_note);
            $html .= '</div>';
        }

        // External URL.
        if (!empty($doctype->externalurl)) {
            $html .= '<div class="small mb-2">';
            $html .= '<i class="fa fa-external-link me-1"></i>';
            $html .= '<a href="' . s($doctype->externalurl) . '" target="_blank" rel="noopener">';
            $html .= get_string('obtaindocument', 'local_jobboard');
            $html .= '</a>';
            $html .= '</div>';
        }

        $html .= '</div></div>';
        $mform->addElement('html', $html);

        // Determine input type.
        $inputtype = $doctype->input_type ?? 'file';

        switch ($inputtype) {
            case 'text':
                $mform->addElement('text', $fieldname, '', ['size' => 50, 'class' => 'form-control']);
                $mform->setType($fieldname, PARAM_TEXT);
                break;

            case 'textarea':
                $mform->addElement('textarea', $fieldname, '', ['rows' => 4, 'class' => 'form-control']);
                $mform->setType($fieldname, PARAM_TEXT);
                break;

            case 'file':
            default:
                $mform->addElement('filemanager', $fieldname, '', null, $fileoptions);
                break;
        }

        // Add rule for required documents.
        if ($isrequired && $inputtype === 'file') {
            // We validate in the validation() method for files.
        } else if ($isrequired) {
            $mform->addRule($fieldname, get_string('required'), 'required', null, 'server');
        }
    }

    /**
     * Load document types from database.
     *
     * @param int $convocatoriaid Convocatoria ID (reserved for future use).
     * @param bool $isexemption Whether user has exemption.
     * @return array Document types.
     */
    protected function load_document_types(int $convocatoriaid, bool $isexemption): array {
        global $DB;

        // Load enabled document types ordered by sortorder and name.
        $doctypes = $DB->get_records('local_jobboard_doctype', ['enabled' => 1], 'sortorder, name');

        // Apply exemption logic - exempt users don't need ISER-exempted docs.
        if ($isexemption) {
            foreach ($doctypes as $id => $doc) {
                if (!empty($doc->iserexempted)) {
                    $doc->isrequired = 0;
                }
            }
        }

        return $doctypes;
    }

    /**
     * Filter documents based on user's gender, age and ISER exemption status.
     *
     * @param array $doctypes Document types.
     * @param string $gender User gender (M/F).
     * @param int|null $age User age in years.
     * @param bool $hasiserexemption Whether user has active ISER exemption.
     * @return array Filtered documents.
     */
    protected function filter_documents_for_user(array $doctypes, string $gender, ?int $age, bool $hasiserexemption = false): array {
        $filtered = [];

        foreach ($doctypes as $doc) {
            // ISER exemption filter.
            // If user has ISER exemption and document is marked as ISER exempted, skip it.
            if ($hasiserexemption && !empty($doc->iserexempted)) {
                continue; // User is exempt from this document as ISER employee.
            }

            // Gender filter.
            // If document has gender condition and user's gender doesn't match, skip it.
            if (!empty($doc->gender_condition)) {
                if ($gender !== $doc->gender_condition) {
                    continue; // Skip - document is for different gender.
                }
            }

            // Age exemption filter.
            // If user's age is >= threshold, they are exempt from this document.
            if ($age !== null && !empty($doc->age_exemption_threshold)) {
                if ($age >= (int) $doc->age_exemption_threshold) {
                    continue; // User is exempt by age.
                }
            }

            $filtered[] = $doc;
        }

        return $filtered;
    }

    /**
     * Calculate user age from birthdate.
     *
     * @param int $birthdate Unix timestamp of birthdate.
     * @return int|null Age in years or null if unknown.
     */
    protected function calculate_user_age(int $birthdate): ?int {
        if ($birthdate <= 0) {
            return null;
        }

        $now = new \DateTime();
        $birth = new \DateTime();
        $birth->setTimestamp($birthdate);

        return $birth->diff($now)->y;
    }

    /**
     * Validate form submission.
     *
     * @param array $data Submitted data.
     * @param array $files Uploaded files.
     * @return array Validation errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Validate consent.
        if (empty($data['consentaccepted'])) {
            $errors['consentaccepted'] = get_string('consentrequired', 'local_jobboard');
        }

        // Validate declaration.
        if (empty($data['declarationaccepted'])) {
            $errors['declarationaccepted'] = get_string('declarationrequired', 'local_jobboard');
        }

        // Validate digital signature.
        $signature = trim($data['digitalsignature'] ?? '');
        if (empty($signature)) {
            $errors['digitalsignature'] = get_string('required');
        } else if (mb_strlen($signature) < 3) {
            $errors['digitalsignature'] = get_string('signaturetoooshort', 'local_jobboard');
        }

        // Validate required documents.
        foreach ($this->filtereddocs as $doctype) {
            if (!empty($doctype->isrequired)) {
                $fieldname = 'doc_' . $doctype->code;
                $inputtype = $doctype->input_type ?? 'file';

                if ($inputtype === 'file') {
                    $draftid = $data[$fieldname] ?? 0;
                    if (empty($draftid) || !$this->draft_has_files((int) $draftid)) {
                        $errors[$fieldname] = get_string('documentrequired', 'local_jobboard', $doctype->name);
                    }
                } else {
                    if (empty(trim($data[$fieldname] ?? ''))) {
                        $errors[$fieldname] = get_string('required');
                    }
                }
            }
        }

        return $errors;
    }

    /**
     * Check if draft area has files.
     *
     * @param int $draftitemid Draft item ID.
     * @return bool True if files exist.
     */
    protected function draft_has_files(int $draftitemid): bool {
        global $USER;

        if ($draftitemid <= 0) {
            return false;
        }

        $fs = get_file_storage();
        $context = \context_user::instance($USER->id);
        $files = $fs->get_area_files($context->id, 'user', 'draft', $draftitemid, 'id', false);

        return !empty($files);
    }

    /**
     * Get the filtered document types.
     *
     * @return array Document types applicable to this user.
     */
    public function get_filtered_documents(): array {
        return $this->filtereddocs;
    }

    /**
     * Get submitted documents data for storage.
     *
     * @return array Document data array.
     */
    public function get_submitted_documents(): array {
        $data = $this->get_data();
        if (!$data) {
            return [];
        }

        $documents = [];

        foreach ($this->filtereddocs as $doctype) {
            $fieldname = 'doc_' . $doctype->code;
            $inputtype = $doctype->input_type ?? 'file';

            if ($inputtype === 'file') {
                $draftid = $data->$fieldname ?? 0;
                if (!empty($draftid) && $this->draft_has_files((int) $draftid)) {
                    $documents[$doctype->code] = [
                        'type' => 'file',
                        'draftitemid' => (int) $draftid,
                        'doctypeid' => $doctype->id,
                        'doctypecode' => $doctype->code,
                    ];
                }
            } else {
                $value = trim($data->$fieldname ?? '');
                if (!empty($value)) {
                    $documents[$doctype->code] = [
                        'type' => $inputtype,
                        'value' => $value,
                        'doctypeid' => $doctype->id,
                        'doctypecode' => $doctype->code,
                    ];
                }
            }
        }

        return $documents;
    }
}
