<?php
// This file is part of Moodle
declare(strict_types=1);

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
 * Application form with consent and document uploads.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use moodleform;
use local_jobboard\exemption;

/**
 * Form for submitting a job application.
 */
class application_form extends moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        global $DB, $USER;

        $mform = $this->_form;
        $vacancy = $this->_customdata['vacancy'];
        $requireddocs = $this->_customdata['requireddocs'] ?? [];
        $isexemption = $this->_customdata['isexemption'] ?? false;
        $exemptioninfo = $this->_customdata['exemptioninfo'] ?? null;
        $usergender = $this->_customdata['usergender'] ?? '';

        // Filter documents based on user's gender.
        // gender_condition: 'M' = men only, 'F' = women only, null = all.
        $requireddocs = array_filter($requireddocs, function($doctype) use ($usergender) {
            if (empty($doctype->gender_condition)) {
                return true; // No gender restriction.
            }
            // If user's gender matches the condition, show the document.
            return $usergender === $doctype->gender_condition;
        });

        // Document codes that accept multiple certificates in a single file.
        $multipledocs = [
            'titulo_academico',
            'formacion_complementaria',
            'certificacion_laboral',
        ];

        // Hidden fields.
        $mform->addElement('hidden', 'vacancyid', $vacancy->id);
        $mform->setType('vacancyid', PARAM_INT);

        // Vacancy information header.
        $mform->addElement('header', 'vacancyheader', get_string('vacancyinfo', 'local_jobboard'));

        $vacancyhtml = '<div class="vacancy-summary">';
        $vacancyhtml .= '<p><strong>' . get_string('code', 'local_jobboard') . ':</strong> ' .
            format_string($vacancy->code) . '</p>';
        $vacancyhtml .= '<p><strong>' . get_string('title', 'local_jobboard') . ':</strong> ' .
            format_string($vacancy->title) . '</p>';
        if (!empty($vacancy->location)) {
            $vacancyhtml .= '<p><strong>' . get_string('location', 'local_jobboard') . ':</strong> ' .
                format_string($vacancy->location) . '</p>';
        }
        $vacancyhtml .= '<p><strong>' . get_string('closedate', 'local_jobboard') . ':</strong> ' .
            userdate($vacancy->closedate, get_string('strftimedatetime', 'langconfig')) . '</p>';
        $vacancyhtml .= '</div>';
        $mform->addElement('html', $vacancyhtml);

        // ISER Exemption notice if applicable.
        if ($isexemption && $exemptioninfo) {
            $mform->addElement('header', 'exemptionheader', get_string('exemptionnotice', 'local_jobboard'));
            $mform->setExpanded('exemptionheader', true);

            $exemptionhtml = '<div class="alert alert-info">';
            $exemptionhtml .= '<p><strong>' . get_string('exemptionapplied', 'local_jobboard') . '</strong></p>';
            $exemptionhtml .= '<p>' . get_string('exemptiontype_' . $exemptioninfo->exemptiontype, 'local_jobboard') . '</p>';
            if (!empty($exemptioninfo->documentref)) {
                $exemptionhtml .= '<p>' . get_string('documentref', 'local_jobboard') . ': ' .
                    format_string($exemptioninfo->documentref) . '</p>';
            }
            $exemptionhtml .= '<p><em>' . get_string('exemptionreduceddocs', 'local_jobboard') . '</em></p>';
            $exemptionhtml .= '</div>';
            $mform->addElement('html', $exemptionhtml);

            $mform->addElement('hidden', 'isexemption', 1);
            $mform->setType('isexemption', PARAM_INT);
        }

        // Consent section - MANDATORY.
        $mform->addElement('header', 'consentheader', get_string('consentheader', 'local_jobboard'));
        $mform->setExpanded('consentheader', true);

        // Data treatment policy text.
        $policytext = get_config('local_jobboard', 'datatreatmentpolicy');
        if (empty($policytext)) {
            $policytext = get_string('defaultdatatreatmentpolicy', 'local_jobboard');
        }

        $policyhtml = '<div class="data-treatment-policy">';
        $policyhtml .= '<h5>' . get_string('datatreatmentpolicytitle', 'local_jobboard') . '</h5>';
        $policyhtml .= '<div class="policy-text">' . format_text($policytext, FORMAT_HTML) . '</div>';
        $policyhtml .= '</div>';
        $mform->addElement('html', $policyhtml);

        // Consent checkbox.
        $mform->addElement('advcheckbox', 'consentaccepted', '',
            get_string('consentaccepttext', 'local_jobboard'), ['group' => 1], [0, 1]);
        $mform->addRule('consentaccepted', get_string('consentrequired', 'local_jobboard'), 'required', null, 'client');
        $mform->addRule('consentaccepted', get_string('consentrequired', 'local_jobboard'), 'nonzero', null, 'client');
        $mform->addHelpButton('consentaccepted', 'consentaccepted', 'local_jobboard');

        // Digital signature - Full name.
        $mform->addElement('text', 'digitalsignature', get_string('digitalsignature', 'local_jobboard'),
            ['size' => 50, 'maxlength' => 200]);
        $mform->setType('digitalsignature', PARAM_TEXT);
        $mform->addRule('digitalsignature', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('digitalsignature', 'digitalsignature', 'local_jobboard');

        // Pre-fill with user's full name as suggestion.
        $fullname = fullname($USER);
        $mform->setDefault('digitalsignature', $fullname);

        // Documents section with tabbed interface by category.
        if (!empty($requireddocs)) {
            $mform->addElement('header', 'documentsheader', get_string('requireddocuments', 'local_jobboard'));
            $mform->setExpanded('documentsheader', true);

            $mform->addElement('html', '<div class="alert alert-warning">' .
                get_string('documentshelp', 'local_jobboard') . '</div>');

            // Get accepted file types from settings.
            $acceptedtypes = get_config('local_jobboard', 'acceptedfiletypes');
            if (empty($acceptedtypes)) {
                $acceptedtypes = '.pdf,.jpg,.jpeg,.png';
            }
            $maxsize = get_config('local_jobboard', 'maxfilesize');
            if (empty($maxsize)) {
                $maxsize = 10 * 1024 * 1024; // 10MB default.
            }

            $fileoptions = [
                'subdirs' => 0,
                'maxbytes' => $maxsize,
                'maxfiles' => 1,
                'accepted_types' => explode(',', $acceptedtypes),
            ];

            // Group documents by category.
            $categories = [
                'employment' => ['icon' => 'fa-briefcase', 'docs' => []],
                'identification' => ['icon' => 'fa-id-card', 'docs' => []],
                'academic' => ['icon' => 'fa-graduation-cap', 'docs' => []],
                'financial' => ['icon' => 'fa-university', 'docs' => []],
                'health' => ['icon' => 'fa-heartbeat', 'docs' => []],
                'legal' => ['icon' => 'fa-gavel', 'docs' => []],
            ];

            foreach ($requireddocs as $doctype) {
                $cat = $doctype->category ?? 'employment';
                if (!isset($categories[$cat])) {
                    $cat = 'employment';
                }
                $categories[$cat]['docs'][] = $doctype;
            }

            // Remove empty categories.
            $categories = array_filter($categories, function($cat) {
                return !empty($cat['docs']);
            });

            // Build tabbed interface.
            $tabshtml = '<div class="jb-document-tabs mb-4">';
            $tabshtml .= '<ul class="nav nav-pills nav-fill mb-3" id="docCategoryTabs" role="tablist">';

            $first = true;
            foreach ($categories as $catkey => $catdata) {
                $active = $first ? 'active' : '';
                $selected = $first ? 'true' : 'false';
                $doccount = count($catdata['docs']);
                $requiredcount = count(array_filter($catdata['docs'], function($d) {
                    return !empty($d->isrequired);
                }));
                $badge = $requiredcount > 0 ? '<span class="badge badge-danger ml-1">' . $requiredcount . '</span>' : '';

                $tabshtml .= '<li class="nav-item" role="presentation">';
                $tabshtml .= '<a class="nav-link ' . $active . '" id="tab-' . $catkey . '" ';
                $tabshtml .= 'data-toggle="pill" href="#panel-' . $catkey . '" role="tab" ';
                $tabshtml .= 'aria-controls="panel-' . $catkey . '" aria-selected="' . $selected . '">';
                $tabshtml .= '<i class="fa ' . $catdata['icon'] . ' mr-1"></i>';
                $tabshtml .= '<span class="d-none d-md-inline">' . get_string('doccat_' . $catkey, 'local_jobboard') . '</span>';
                $tabshtml .= $badge;
                $tabshtml .= '</a></li>';
                $first = false;
            }

            $tabshtml .= '</ul>';
            $tabshtml .= '<div class="tab-content" id="docCategoryTabsContent">';

            $mform->addElement('html', $tabshtml);

            // Render each category panel with its documents.
            $first = true;
            foreach ($categories as $catkey => $catdata) {
                $activeclass = $first ? 'show active' : '';

                $panelhtml = '<div class="tab-pane fade ' . $activeclass . '" id="panel-' . $catkey . '" ';
                $panelhtml .= 'role="tabpanel" aria-labelledby="tab-' . $catkey . '">';
                $panelhtml .= '<div class="card border-0 shadow-sm">';
                $panelhtml .= '<div class="card-header bg-light">';
                $panelhtml .= '<h5 class="mb-0"><i class="fa ' . $catdata['icon'] . ' mr-2 text-primary"></i>';
                $panelhtml .= get_string('doccat_' . $catkey, 'local_jobboard');
                $panelhtml .= '</h5>';
                $panelhtml .= '<small class="text-muted">' . get_string('doccat_' . $catkey . '_desc', 'local_jobboard') . '</small>';
                $panelhtml .= '</div>';
                $panelhtml .= '<div class="card-body">';
                $mform->addElement('html', $panelhtml);

                // Add document fields for this category.
                foreach ($catdata['docs'] as $doctype) {
                    $fieldname = 'doc_' . $doctype->code;
                    $required = !empty($doctype->isrequired);
                    $ismultiple = in_array($doctype->code, $multipledocs);

                    // Document card wrapper.
                    $dochtml = '<div class="jb-document-field mb-4 p-3 border rounded bg-white">';
                    $dochtml .= '<div class="d-flex justify-content-between align-items-start mb-2">';
                    $dochtml .= '<h6 class="mb-0">';
                    $dochtml .= format_string($doctype->name);
                    if ($required) {
                        $dochtml .= ' <span class="badge badge-danger">' . get_string('required') . '</span>';
                    } else {
                        $dochtml .= ' <span class="badge badge-secondary">' . get_string('optional', 'local_jobboard') . '</span>';
                    }
                    $dochtml .= '</h6>';
                    $dochtml .= '</div>';

                    // Description in a prominent box.
                    if (!empty($doctype->description)) {
                        $dochtml .= '<div class="alert alert-info py-2 px-3 mb-2">';
                        $dochtml .= '<i class="fa fa-info-circle mr-2"></i>';
                        $dochtml .= '<span>' . format_string($doctype->description) . '</span>';
                        $dochtml .= '</div>';
                    }

                    // Multiple documents notice - prominent warning for certificates that may have multiple files.
                    if ($ismultiple) {
                        $dochtml .= '<div class="alert alert-warning py-2 px-3 mb-2">';
                        $dochtml .= '<i class="fa fa-file-pdf mr-2"></i>';
                        $dochtml .= '<strong>' . get_string('multipledocs_notice', 'local_jobboard') . '</strong><br>';
                        $dochtml .= '<small>' . get_string('multipledocs_' . $doctype->code, 'local_jobboard') . '</small>';
                        $dochtml .= '</div>';
                    }

                    // Requirements in collapsible section.
                    if (!empty($doctype->requirements)) {
                        $dochtml .= '<details class="mb-2">';
                        $dochtml .= '<summary class="text-primary" style="cursor:pointer;">';
                        $dochtml .= '<i class="fa fa-list-ul mr-1"></i>' . get_string('docrequirements', 'local_jobboard');
                        $dochtml .= '</summary>';
                        $dochtml .= '<div class="small text-muted mt-1 pl-3">' . format_string($doctype->requirements) . '</div>';
                        $dochtml .= '</details>';
                    }

                    $dochtml .= '</div>';
                    $mform->addElement('html', $dochtml);

                    // File manager for document upload (label hidden, shown in card above).
                    $mform->addElement('filemanager', $fieldname, '', null, $fileoptions);

                    if ($required) {
                        $mform->addRule($fieldname, get_string('documentrequired', 'local_jobboard', $doctype->name),
                            'required', null, 'client');
                    }

                    // Issue date for certain document types.
                    if (in_array($doctype->code, ['antecedentes_disciplinarios', 'antecedentes_fiscales',
                        'antecedentes_judiciales', 'medidas_correctivas', 'inhabilidades', 'redam',
                        'libreta_militar', 'certificado_medico', 'eps', 'pension'])) {
                        $mform->addElement('date_selector', $fieldname . '_issuedate',
                            get_string('documentissuedate', 'local_jobboard'));
                        $mform->hideIf($fieldname . '_issuedate', $fieldname, 'eq', 0);
                    }
                }

                // Close card-body and card.
                $mform->addElement('html', '</div></div></div>');
                $first = false;
            }

            // Close tab-content and tabs container.
            $mform->addElement('html', '</div></div>');
        }

        // Additional information section.
        $mform->addElement('header', 'additionalheader', get_string('additionalinfo', 'local_jobboard'));

        // Cover letter / motivation (rich text editor).
        $mform->addElement('editor', 'coverletter', get_string('coverletter', 'local_jobboard'), null, [
            'maxfiles' => 0,
            'noclean' => false,
            'maxbytes' => 0,
            'rows' => 8,
        ]);
        $mform->setType('coverletter', PARAM_RAW);
        $mform->addHelpButton('coverletter', 'coverletter', 'local_jobboard');

        // Declaration.
        $mform->addElement('header', 'declarationheader', get_string('declaration', 'local_jobboard'));
        $mform->setExpanded('declarationheader', true);

        $declarationtext = get_string('declarationtext', 'local_jobboard');
        $mform->addElement('html', '<div class="declaration-text">' . $declarationtext . '</div>');

        $mform->addElement('advcheckbox', 'declarationaccepted', '',
            get_string('declarationaccept', 'local_jobboard'), ['group' => 1], [0, 1]);
        $mform->addRule('declarationaccepted', get_string('declarationrequired', 'local_jobboard'),
            'required', null, 'client');
        $mform->addRule('declarationaccepted', get_string('declarationrequired', 'local_jobboard'),
            'nonzero', null, 'client');
        $mform->addHelpButton('declarationaccepted', 'declarationaccepted', 'local_jobboard');

        // Submit buttons.
        $this->add_action_buttons(true, get_string('submitapplication', 'local_jobboard'));
    }

    /**
     * Form validation.
     *
     * @param array $data Form data.
     * @param array $files Uploaded files.
     * @return array Validation errors.
     */
    public function validation($data, $files) {
        global $USER;

        $errors = parent::validation($data, $files);

        // Validate consent acceptance.
        if (empty($data['consentaccepted'])) {
            $errors['consentaccepted'] = get_string('consentrequired', 'local_jobboard');
        }

        // Validate declaration acceptance.
        if (empty($data['declarationaccepted'])) {
            $errors['declarationaccepted'] = get_string('declarationrequired', 'local_jobboard');
        }

        // Validate digital signature matches user's name.
        $fullname = fullname($USER);
        $signature = trim($data['digitalsignature'] ?? '');
        if (empty($signature)) {
            $errors['digitalsignature'] = get_string('required');
        } else if (strlen($signature) < 5) {
            $errors['digitalsignature'] = get_string('signaturetoooshort', 'local_jobboard');
        }

        // Filter documents by gender (same logic as definition).
        $requireddocs = $this->_customdata['requireddocs'] ?? [];
        $usergender = $this->_customdata['usergender'] ?? '';
        $requireddocs = array_filter($requireddocs, function($doctype) use ($usergender) {
            if (empty($doctype->gender_condition)) {
                return true;
            }
            return $usergender === $doctype->gender_condition;
        });

        // Validate required documents.
        foreach ($requireddocs as $doctype) {
            if (!empty($doctype->isrequired)) {
                $fieldname = 'doc_' . $doctype->code;
                $draftitemid = $data[$fieldname] ?? 0;
                if (empty($draftitemid) || !$this->has_files_in_draft($draftitemid)) {
                    $errors[$fieldname] = get_string('documentrequired', 'local_jobboard', $doctype->name);
                }
            }
        }

        // Validate document issue dates where applicable.
        foreach ($requireddocs as $doctype) {
            $fieldname = 'doc_' . $doctype->code;
            $issuedatefield = $fieldname . '_issuedate';

            if (isset($data[$issuedatefield]) && !empty($data[$fieldname])) {
                $issuedate = $data[$issuedatefield];

                // Check document is not expired based on validity rules.
                if (in_array($doctype->code, ['antecedentes_procuraduria', 'antecedentes_contraloria',
                    'antecedentes_policia', 'rnmc', 'sijin'])) {
                    // Background checks typically valid for 3 months.
                    $maxage = 90 * 24 * 60 * 60;
                    if ($issuedate < (time() - $maxage)) {
                        $errors[$issuedatefield] = get_string('documentexpired', 'local_jobboard', '90 ' .
                            get_string('days', 'local_jobboard'));
                    }
                } else if ($doctype->code === 'certificado_medico') {
                    // Medical certificate valid for 6 months.
                    $maxage = 180 * 24 * 60 * 60;
                    if ($issuedate < (time() - $maxage)) {
                        $errors[$issuedatefield] = get_string('documentexpired', 'local_jobboard', '180 ' .
                            get_string('days', 'local_jobboard'));
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
    protected function has_files_in_draft(int $draftitemid): bool {
        global $USER;

        $fs = get_file_storage();
        $context = \context_user::instance($USER->id);
        $files = $fs->get_area_files($context->id, 'user', 'draft', $draftitemid, 'id', false);

        return !empty($files);
    }

    /**
     * Get submitted document data.
     *
     * @return array Array of document data keyed by document type code.
     */
    public function get_document_data(): array {
        $data = $this->get_data();
        if (!$data) {
            return [];
        }

        $documents = [];
        $requireddocs = $this->_customdata['requireddocs'] ?? [];
        $usergender = $this->_customdata['usergender'] ?? '';

        // Filter by gender.
        $requireddocs = array_filter($requireddocs, function($doctype) use ($usergender) {
            if (empty($doctype->gender_condition)) {
                return true;
            }
            return $usergender === $doctype->gender_condition;
        });

        foreach ($requireddocs as $doctype) {
            $fieldname = 'doc_' . $doctype->code;
            $issuedatefield = $fieldname . '_issuedate';

            if (!empty($data->$fieldname)) {
                $documents[$doctype->code] = [
                    'draftitemid' => $data->$fieldname,
                    'issuedate' => $data->$issuedatefield ?? null,
                    'doctypeid' => $doctype->id,
                ];
            }
        }

        return $documents;
    }
}
