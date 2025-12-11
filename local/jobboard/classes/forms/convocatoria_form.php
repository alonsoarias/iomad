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
 * Convocatoria form for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for creating and editing convocatorias.
 */
class convocatoria_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        global $CFG;

        $mform = $this->_form;
        $convocatoria = $this->_customdata['convocatoria'] ?? null;
        $isiomad = $this->_customdata['isiomad'] ?? false;
        $companies = $this->_customdata['companies'] ?? [];
        $isedit = !empty($convocatoria) && !empty($convocatoria->id);

        // Hidden field for ID.
        if ($isedit) {
            $mform->addElement('hidden', 'id', $convocatoria->id);
            $mform->setType('id', PARAM_INT);
        }

        // Header: Basic Information.
        $mform->addElement('header', 'basicinfo', get_string('convocatoriadetails', 'local_jobboard'));

        // Code.
        $mform->addElement('text', 'code', get_string('convocatoriacode', 'local_jobboard'), ['size' => 20, 'maxlength' => 50]);
        $mform->setType('code', PARAM_ALPHANUMEXT);
        $mform->addRule('code', get_string('error:requiredfield', 'local_jobboard'), 'required', null, 'client');
        $mform->addHelpButton('code', 'convocatoriacode', 'local_jobboard');

        // Name.
        $mform->addElement('text', 'name', get_string('convocatorianame', 'local_jobboard'), ['size' => 60, 'maxlength' => 255]);
        $mform->setType('name', PARAM_TEXT);
        $mform->addRule('name', get_string('error:requiredfield', 'local_jobboard'), 'required', null, 'client');
        $mform->addHelpButton('name', 'convocatorianame', 'local_jobboard');

        // Brief description (for listings).
        $mform->addElement('textarea', 'brief_description', get_string('briefdescription', 'local_jobboard'), [
            'rows' => 3,
            'cols' => 60,
            'maxlength' => 500,
        ]);
        $mform->setType('brief_description', PARAM_TEXT);
        $mform->addHelpButton('brief_description', 'briefdescription', 'local_jobboard');

        // Full description.
        $mform->addElement('editor', 'description', get_string('convocatoriadescription', 'local_jobboard'), null, [
            'maxfiles' => 0,
            'noclean' => false,
        ]);
        $mform->setType('description', PARAM_RAW);
        $mform->addHelpButton('description', 'convocatoriadescription', 'local_jobboard');

        // PDF document upload.
        $mform->addElement('filepicker', 'convocatoria_pdf', get_string('convocatoriapdf', 'local_jobboard'), null, [
            'accepted_types' => ['.pdf'],
            'maxfiles' => 1,
        ]);
        $mform->addHelpButton('convocatoria_pdf', 'convocatoriapdf', 'local_jobboard');

        // Header: Dates.
        $mform->addElement('header', 'datesheader', get_string('dates', 'local_jobboard'));

        // Start date.
        $mform->addElement('date_selector', 'startdate', get_string('convocatoriastartdate', 'local_jobboard'));
        $mform->addRule('startdate', get_string('error:requiredfield', 'local_jobboard'), 'required', null, 'client');
        $mform->addHelpButton('startdate', 'convocatoriastartdate', 'local_jobboard');

        // End date.
        $mform->addElement('date_selector', 'enddate', get_string('convocatoriaenddate', 'local_jobboard'));
        $mform->addRule('enddate', get_string('error:requiredfield', 'local_jobboard'), 'required', null, 'client');
        $mform->addHelpButton('enddate', 'convocatoriaenddate', 'local_jobboard');

        // Header: Publication.
        $mform->addElement('header', 'publicationheader', get_string('publicationtype', 'local_jobboard'));

        // Publication type.
        $publicationtypes = [
            'internal' => get_string('publicationtype:internal', 'local_jobboard'),
            'public' => get_string('publicationtype:public', 'local_jobboard'),
        ];
        $mform->addElement('select', 'publicationtype', get_string('publicationtype', 'local_jobboard'), $publicationtypes);
        $mform->setDefault('publicationtype', 'internal');
        $mform->addHelpButton('publicationtype', 'publicationtype', 'local_jobboard');

        // Header: Company (IOMAD only).
        if ($isiomad) {
            $mform->addElement('header', 'companyheader', get_string('company', 'local_jobboard'));

            // Company selector - initial options with placeholder, populated via AJAX.
            $companyoptions = [0 => get_string('allcompanies', 'local_jobboard')];

            // Get current company ID for preselection.
            $currentcompanyid = 0;
            if ($isedit && !empty($convocatoria->companyid)) {
                $currentcompanyid = (int) $convocatoria->companyid;
                // Pre-load current company.
                global $DB;
                $currentcompany = $DB->get_record('company', ['id' => $currentcompanyid]);
                if ($currentcompany) {
                    $companyoptions[$currentcompany->id] = format_string($currentcompany->name);
                }
            }

            $mform->addElement('select', 'companyid', get_string('company', 'local_jobboard'), $companyoptions, [
                'id' => 'id_companyid',
            ]);
            $mform->setType('companyid', PARAM_INT);
            $mform->addHelpButton('companyid', 'convocatoria_companyid', 'local_jobboard');

            // Get current department ID for preselection.
            $currentdeptid = 0;
            if ($isedit && !empty($convocatoria->departmentid)) {
                $currentdeptid = (int) $convocatoria->departmentid;
            }

            // Department selector - initial options with placeholder, populated via AJAX.
            $departmentoptions = [0 => get_string('selectdepartment', 'local_jobboard')];
            if ($currentcompanyid > 0) {
                $departmentoptions += \local_jobboard_get_departments($currentcompanyid);
            }

            $mform->addElement('select', 'departmentid', get_string('department', 'local_jobboard'),
                $departmentoptions, [
                'id' => 'id_departmentid',
            ]);
            $mform->setType('departmentid', PARAM_INT);
            $mform->addHelpButton('departmentid', 'convocatoria_departmentid', 'local_jobboard');

            // Add JavaScript for AJAX loading of companies and departments.
            global $PAGE;
            $PAGE->requires->js_call_amd('local_jobboard/convocatoria_form', 'init', [[
                'companyPreselect' => $currentcompanyid,
                'departmentPreselect' => $currentdeptid,
            ]]);
        }

        // Header: Terms and Conditions.
        $mform->addElement('header', 'termsheader', get_string('convocatoriaterms', 'local_jobboard'));

        // Terms.
        $mform->addElement('editor', 'terms', get_string('convocatoriaterms', 'local_jobboard'), null, [
            'maxfiles' => 0,
            'noclean' => false,
        ]);
        $mform->setType('terms', PARAM_RAW);
        $mform->addHelpButton('terms', 'convocatoriaterms', 'local_jobboard');

        // Header: Application Restrictions.
        $mform->addElement('header', 'applicationlimitsheader', get_string('applicationlimits', 'local_jobboard'));

        // Allow multiple applications per convocatoria.
        $mform->addElement('advcheckbox', 'allow_multiple_applications',
            get_string('allowmultipleapplications_convocatoria', 'local_jobboard'),
            get_string('allowmultipleapplications_convocatoria_desc', 'local_jobboard'),
            [], [0, 1]);
        $mform->setDefault('allow_multiple_applications', 0);

        // Maximum applications per user.
        $mform->addElement('text', 'max_applications_per_user',
            get_string('maxapplicationsperuser', 'local_jobboard'),
            ['size' => 5]);
        $mform->setType('max_applications_per_user', PARAM_INT);
        $mform->setDefault('max_applications_per_user', 0);
        $mform->addHelpButton('max_applications_per_user', 'maxapplicationsperuser', 'local_jobboard');

        // Only show max applications when multiple are allowed.
        $mform->hideIf('max_applications_per_user', 'allow_multiple_applications', 'eq', 0);

        // Header: Document Exemptions.
        $mform->addElement('header', 'docexemptionsheader', get_string('convocatoriadocexemptions', 'local_jobboard'));
        $mform->setExpanded('docexemptionsheader', false);

        // Get all enabled document types.
        global $DB;
        $doctypes = $DB->get_records('local_jobboard_doctype', ['enabled' => 1], 'sortorder ASC, name ASC');
        $doctypeoptions = [];
        foreach ($doctypes as $dt) {
            // Use translated name if available.
            $name = get_string_manager()->string_exists('doctype_' . $dt->code, 'local_jobboard')
                ? get_string('doctype_' . $dt->code, 'local_jobboard')
                : $dt->name;

            // Add category if available.
            if (!empty($dt->category)) {
                $categoryName = get_string_manager()->string_exists('doccategory_' . $dt->category, 'local_jobboard')
                    ? get_string('doccategory_' . $dt->category, 'local_jobboard')
                    : ucfirst($dt->category);
                $name = "[{$categoryName}] " . $name;
            }

            $doctypeoptions[$dt->id] = $name;
        }

        // Multi-select for exempted document types.
        $select = $mform->addElement('select', 'exempted_doctypes',
            get_string('exempteddoctypes', 'local_jobboard'), $doctypeoptions);
        $select->setMultiple(true);
        $mform->addHelpButton('exempted_doctypes', 'exempteddoctypes', 'local_jobboard');

        // Exemption reason (default reason for all).
        $mform->addElement('textarea', 'exemption_reason',
            get_string('exemptionreason', 'local_jobboard'),
            ['rows' => 2, 'cols' => 60]);
        $mform->setType('exemption_reason', PARAM_TEXT);
        $mform->addHelpButton('exemption_reason', 'exemptionreason', 'local_jobboard');

        // Submit buttons.
        $this->add_action_buttons(true, $isedit ? get_string('savechanges') : get_string('addconvocatoria', 'local_jobboard'));
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
        $convocatoria = $this->_customdata['convocatoria'] ?? null;

        // Code uniqueness check.
        $code = trim($data['code']);
        if (!empty($code)) {
            $existingcode = $DB->get_record('local_jobboard_convocatoria', ['code' => $code]);
            if ($existingcode && (!$convocatoria || $existingcode->id != $convocatoria->id)) {
                $errors['code'] = get_string('error:convocatoriacodeexists', 'local_jobboard');
            }
        }

        // Date validation.
        if (!empty($data['startdate']) && !empty($data['enddate'])) {
            if ($data['enddate'] <= $data['startdate']) {
                $errors['enddate'] = get_string('error:convocatoriadatesinvalid', 'local_jobboard');
            }
        }

        return $errors;
    }

    /**
     * Set form data from a convocatoria object.
     *
     * @param stdClass $convocatoria The convocatoria object.
     */
    public function set_data_from_convocatoria($convocatoria) {
        global $CFG;

        $data = new \stdClass();
        $data->id = $convocatoria->id;
        $data->code = $convocatoria->code;
        $data->name = $convocatoria->name;
        $data->brief_description = $convocatoria->brief_description ?? '';
        $data->description = ['text' => $convocatoria->description ?? '', 'format' => FORMAT_HTML];
        $data->startdate = $convocatoria->startdate;
        $data->enddate = $convocatoria->enddate;
        $data->publicationtype = $convocatoria->publicationtype ?? 'internal';
        $data->companyid = $convocatoria->companyid ?? 0;
        $data->departmentid = $convocatoria->departmentid ?? 0;
        $data->terms = ['text' => $convocatoria->terms ?? '', 'format' => FORMAT_HTML];
        // Application restriction fields.
        $data->allow_multiple_applications = $convocatoria->allow_multiple_applications ?? 0;
        $data->max_applications_per_user = $convocatoria->max_applications_per_user ?? 0;

        // Load document exemptions for this convocatoria.
        $data->exempted_doctypes = \local_jobboard\convocatoria_exemption::get_exempted_doctype_ids((int) $convocatoria->id);

        // Prepare PDF filepicker draft area if PDF exists.
        $context = \context_system::instance();
        $draftitemid = file_get_submitted_draft_itemid('convocatoria_pdf');
        file_prepare_draft_area(
            $draftitemid,
            $context->id,
            'local_jobboard',
            'convocatoria_pdf',
            $convocatoria->id,
            ['maxfiles' => 1, 'accepted_types' => ['.pdf']]
        );
        $data->convocatoria_pdf = $draftitemid;

        $this->set_data($data);
    }

    /**
     * Get processed data from form.
     *
     * @return object|null Form data or null if not submitted/cancelled.
     */
    public function get_data() {
        $data = parent::get_data();

        if ($data) {
            // Process description editor.
            if (isset($data->description) && is_array($data->description)) {
                $data->description = $data->description['text'] ?? '';
            }

            // Process terms editor.
            if (isset($data->terms) && is_array($data->terms)) {
                $data->terms = $data->terms['text'] ?? '';
            }

            // Ensure exempted_doctypes is an array.
            if (!isset($data->exempted_doctypes) || !is_array($data->exempted_doctypes)) {
                $data->exempted_doctypes = [];
            }
        }

        return $data;
    }
}
