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
 *
 * Note: As of v2.1.0, vacancies no longer have their own dates.
 * Dates are inherited from the associated convocatoria.
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
        $defaultconvocatoriaid = $this->_customdata['convocatoriaid'] ?? 0;

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

        // Location (Centro Tutorial / Sede).
        $mform->addElement('text', 'location', get_string('location', 'local_jobboard'), ['size' => 60, 'maxlength' => 255]);
        $mform->setType('location', PARAM_TEXT);
        $mform->addHelpButton('location', 'location', 'local_jobboard');

        // Modality (educational modality).
        $modalities = ['' => get_string('selectmodality', 'local_jobboard')] + \local_jobboard_get_modalities();
        $mform->addElement('select', 'modality', get_string('modality', 'local_jobboard'), $modalities);
        $mform->setType('modality', PARAM_ALPHANUMEXT);
        $mform->addHelpButton('modality', 'modality', 'local_jobboard');

        // Department (text field - usually same as modality for ISER).
        $mform->addElement('text', 'department', get_string('department', 'local_jobboard'), ['size' => 60, 'maxlength' => 255]);
        $mform->setType('department', PARAM_TEXT);
        $mform->addHelpButton('department', 'department', 'local_jobboard');

        // Header: Convocatoria and Positions.
        $mform->addElement('header', 'convocatoriaheader', get_string('convocatoria', 'local_jobboard'));

        // Convocatoria selector (REQUIRED - vacancies must belong to a convocatoria).
        $convocatoriaoptions = ['' => get_string('selectconvocatoria', 'local_jobboard')];

        // Get current convocatoria ID for preselection.
        $currentconvocatoriaid = 0;
        if ($isedit && !empty($vacancy->convocatoriaid)) {
            $currentconvocatoriaid = (int) $vacancy->convocatoriaid;
        } elseif ($defaultconvocatoriaid) {
            $currentconvocatoriaid = (int) $defaultconvocatoriaid;
        }

        // Pre-load current convocatoria if editing or has default.
        if ($currentconvocatoriaid > 0) {
            $currentconv = \local_jobboard_get_convocatoria($currentconvocatoriaid);
            if ($currentconv) {
                $convocatoriaoptions[$currentconv->id] = $currentconv->code . ' - ' . $currentconv->name;
            }
        }

        $mform->addElement('select', 'convocatoriaid', get_string('convocatoria', 'local_jobboard'), $convocatoriaoptions, [
            'id' => 'id_convocatoriaid',
        ]);
        $mform->setType('convocatoriaid', PARAM_INT);
        $mform->addRule('convocatoriaid', get_string('error:convocatoriarequired', 'local_jobboard'), 'required', null, 'client');
        $mform->addHelpButton('convocatoriaid', 'convocatoria', 'local_jobboard');
        if ($defaultconvocatoriaid && !$isedit) {
            $mform->setDefault('convocatoriaid', $defaultconvocatoriaid);
        }

        // Show convocatoria dates info (read-only).
        $convocatoriarecord = null;
        if ($currentconvocatoriaid > 0) {
            $convocatoriarecord = \local_jobboard_get_convocatoria($currentconvocatoriaid);
        }

        if ($convocatoriarecord) {
            $convdates = userdate($convocatoriarecord->startdate, '%d/%m/%Y %H:%M') . ' - ' .
                         userdate($convocatoriarecord->enddate, '%d/%m/%Y %H:%M');
            $mform->addElement('static', 'convocatoria_dates_info',
                get_string('convocatoriadates', 'local_jobboard'),
                \html_writer::tag('span', $convdates, ['class' => 'badge badge-info'])
            );
        }

        // Informational note about dates.
        $mform->addElement('static', 'dates_info', '',
            \html_writer::tag('div',
                '<i class="fa fa-info-circle"></i> ' .
                get_string('vacancy_inherits_dates', 'local_jobboard'),
                ['class' => 'alert alert-info']
            )
        );

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

        // Company and Department (Iomad multi-tenant).
        $isiomad = \local_jobboard_is_iomad_installed();
        if ($isiomad) {
            // Header for IOMAD section.
            $mform->addElement('header', 'iomadsection', get_string('iomadsettings', 'local_jobboard'));

            // Company selector - initial options with placeholder, populated via AJAX.
            $companyoptions = [0 => get_string('selectcompany', 'local_jobboard')];

            // Pre-select current user's company.
            $defaultcompanyid = 0;
            if (!$isedit) {
                $usercompanyid = \local_jobboard_get_user_companyid();
                if ($usercompanyid) {
                    $defaultcompanyid = $usercompanyid;
                }
            } else if ($vacancy && $vacancy->companyid) {
                $defaultcompanyid = (int) $vacancy->companyid;
            }

            // Pre-load current company if editing or has default.
            if ($defaultcompanyid > 0) {
                global $DB;
                $currentcompany = $DB->get_record('company', ['id' => $defaultcompanyid]);
                if ($currentcompany) {
                    $companyoptions[$currentcompany->id] = format_string($currentcompany->name);
                }
            }

            $mform->addElement('select', 'companyid', get_string('company', 'local_jobboard'), $companyoptions, [
                'id' => 'id_companyid',
            ]);
            $mform->setType('companyid', PARAM_INT);
            $mform->addHelpButton('companyid', 'company', 'local_jobboard');
            if ($defaultcompanyid) {
                $mform->setDefault('companyid', $defaultcompanyid);
            }

            // Department selector (IOMAD).
            $iomadinfo = \local_jobboard_get_iomad_info();

            // Get current department ID for preselection.
            $currentdeptid = 0;
            if ($isedit && !empty($vacancy->departmentid)) {
                $currentdeptid = (int) $vacancy->departmentid;
            }

            if ($iomadinfo['has_departments']) {
                // Initial options with placeholder - populated via AJAX.
                $departments = [0 => get_string('selectdepartment', 'local_jobboard')];
                if ($defaultcompanyid > 0) {
                    $departments += \local_jobboard_get_departments($defaultcompanyid);
                }

                $mform->addElement('select', 'departmentid', get_string('iomad_department', 'local_jobboard'), $departments, [
                    'id' => 'id_departmentid',
                ]);
                $mform->setType('departmentid', PARAM_INT);
                $mform->addHelpButton('departmentid', 'iomad_department', 'local_jobboard');

                // Set default value when editing.
                if ($currentdeptid > 0) {
                    $mform->setDefault('departmentid', $currentdeptid);
                }
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

        // Add JavaScript for AJAX loading of companies, departments, and convocatorias.
        global $PAGE;
        $jsoptions = [
            'convocatoriaPreselect' => $currentconvocatoriaid,
            'includeAllConvocatorias' => true,
        ];

        if ($isiomad) {
            $jsoptions['companyPreselect'] = $defaultcompanyid ?? 0;
            $jsoptions['departmentPreselect'] = $currentdeptid ?? 0;
        }

        $PAGE->requires->js_call_amd('local_jobboard/vacancy_form', 'init', [$jsoptions]);

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

        // Validate convocatoria is required.
        $convocatoriaid = !empty($data['convocatoriaid']) ? (int)$data['convocatoriaid'] : 0;
        if ($convocatoriaid <= 0) {
            $errors['convocatoriaid'] = get_string('error:convocatoriarequired', 'local_jobboard');
        }

        // Validate positions.
        if (isset($data['positions']) && $data['positions'] < 1) {
            $errors['positions'] = get_string('error:requiredfield', 'local_jobboard');
        }

        return $errors;
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
        $data->location = $vacancy->location;
        $data->modality = $vacancy->modality ?? '';
        $data->department = $vacancy->department;
        $data->companyid = $vacancy->companyid;
        $data->departmentid = $vacancy->departmentid ?? 0;
        $data->convocatoriaid = $vacancy->convocatoriaid ?? 0;
        $data->publicationtype = $vacancy->publicationtype ?? 'public';
        $data->positions = $vacancy->positions;
        $data->requirements = ['text' => $vacancy->requirements, 'format' => FORMAT_HTML];
        $data->desirable = ['text' => $vacancy->desirable, 'format' => FORMAT_HTML];

        $this->set_data($data);
    }
}
