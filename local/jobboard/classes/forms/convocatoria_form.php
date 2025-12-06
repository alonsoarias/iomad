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

        // Description.
        $mform->addElement('editor', 'description', get_string('convocatoriadescription', 'local_jobboard'), null, [
            'maxfiles' => 0,
            'noclean' => false,
        ]);
        $mform->setType('description', PARAM_RAW);
        $mform->addHelpButton('description', 'convocatoriadescription', 'local_jobboard');

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
        if ($isiomad && !empty($companies)) {
            $mform->addElement('header', 'companyheader', get_string('company', 'local_jobboard'));

            $companyoptions = [0 => get_string('allcompanies', 'local_jobboard')] + $companies;
            $mform->addElement('select', 'companyid', get_string('company', 'local_jobboard'), $companyoptions);
            $mform->setType('companyid', PARAM_INT);
            $mform->addHelpButton('companyid', 'convocatoria_companyid', 'local_jobboard');

            // Department selector (populated via AJAX or simple select).
            $mform->addElement('select', 'departmentid', get_string('department', 'local_jobboard'),
                [0 => get_string('selectdepartment', 'local_jobboard')]);
            $mform->setType('departmentid', PARAM_INT);
            $mform->addHelpButton('departmentid', 'convocatoria_departmentid', 'local_jobboard');
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
        $data = new \stdClass();
        $data->id = $convocatoria->id;
        $data->code = $convocatoria->code;
        $data->name = $convocatoria->name;
        $data->description = ['text' => $convocatoria->description ?? '', 'format' => FORMAT_HTML];
        $data->startdate = $convocatoria->startdate;
        $data->enddate = $convocatoria->enddate;
        $data->publicationtype = $convocatoria->publicationtype ?? 'internal';
        $data->companyid = $convocatoria->companyid ?? 0;
        $data->departmentid = $convocatoria->departmentid ?? 0;
        $data->terms = ['text' => $convocatoria->terms ?? '', 'format' => FORMAT_HTML];

        $this->set_data($data);
    }
}
