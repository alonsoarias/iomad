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

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for creating/editing ISER exemptions.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class exemption_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        global $DB;

        $mform = $this->_form;
        $exemption = $this->_customdata['exemption'] ?? null;

        // User selection.
        $mform->addElement('header', 'userheader', get_string('user'));

        if ($exemption) {
            // Show user info for editing.
            $user = $DB->get_record('user', ['id' => $exemption->userid]);
            $mform->addElement('static', 'userinfo', get_string('user'),
                fullname($user) . ' (' . $user->email . ')');
            $mform->addElement('hidden', 'userid', $exemption->userid);
            $mform->setType('userid', PARAM_INT);
        } else {
            // User autocomplete for new exemption.
            $mform->addElement('autocomplete', 'userid', get_string('user'), [],
                [
                    'ajax' => 'core_user/form_user_selector',
                    'valuehtmlcallback' => function($value) {
                        global $DB;
                        $user = $DB->get_record('user', ['id' => $value]);
                        if ($user) {
                            return fullname($user) . ' (' . $user->email . ')';
                        }
                        return '';
                    }
                ]
            );
            $mform->addRule('userid', get_string('required'), 'required', null, 'client');
        }

        // Exemption type.
        $mform->addElement('header', 'exemptionheader', get_string('exemptiondetails', 'local_jobboard'));

        $types = [
            '' => get_string('select'),
            'historico_iser' => get_string('exemptiontype_historico_iser', 'local_jobboard'),
            'documentos_recientes' => get_string('exemptiontype_documentos_recientes', 'local_jobboard'),
            'traslado_interno' => get_string('exemptiontype_traslado_interno', 'local_jobboard'),
            'recontratacion' => get_string('exemptiontype_recontratacion', 'local_jobboard'),
        ];
        $mform->addElement('select', 'exemptiontype', get_string('exemptiontype', 'local_jobboard'), $types);
        $mform->addRule('exemptiontype', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('exemptiontype', 'exemptiontype', 'local_jobboard');

        // Document reference.
        $mform->addElement('text', 'documentref', get_string('documentref', 'local_jobboard'));
        $mform->setType('documentref', PARAM_TEXT);
        $mform->addHelpButton('documentref', 'documentref', 'local_jobboard');

        // Exempted document types.
        $doctypes = [
            'cedula' => get_string('doctype_cedula', 'local_jobboard'),
            'rut' => get_string('doctype_rut', 'local_jobboard'),
            'eps' => get_string('doctype_eps', 'local_jobboard'),
            'pension' => get_string('doctype_pension', 'local_jobboard'),
            'cuenta_bancaria' => get_string('doctype_cuenta_bancaria', 'local_jobboard'),
            'libreta_militar' => get_string('doctype_libreta_militar', 'local_jobboard'),
            'titulo_pregrado' => get_string('doctype_titulo_pregrado', 'local_jobboard'),
            'titulo_postgrado' => get_string('doctype_titulo_postgrado', 'local_jobboard'),
            'tarjeta_profesional' => get_string('doctype_tarjeta_profesional', 'local_jobboard'),
            'sigep' => get_string('doctype_sigep', 'local_jobboard'),
            'antecedentes_procuraduria' => get_string('doctype_antecedentes_procuraduria', 'local_jobboard'),
            'antecedentes_contraloria' => get_string('doctype_antecedentes_contraloria', 'local_jobboard'),
            'antecedentes_policia' => get_string('doctype_antecedentes_policia', 'local_jobboard'),
            'rnmc' => get_string('doctype_rnmc', 'local_jobboard'),
            'certificado_medico' => get_string('doctype_certificado_medico', 'local_jobboard'),
        ];

        $select = $mform->addElement('select', 'exempteddoctypes',
            get_string('exempteddocs', 'local_jobboard'), $doctypes);
        $select->setMultiple(true);
        $mform->addRule('exempteddoctypes', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('exempteddoctypes', 'exempteddocs', 'local_jobboard');

        // Quick select buttons (via AMD module for CSP compliance).
        global $PAGE;
        $PAGE->requires->js_call_amd('local_jobboard/exemption_form', 'init', ['id_exempteddoctypes']);

        $mform->addElement('html', '
            <div class="form-group row mb-3 exemption-quick-select">
                <div class="col-md-9 offset-md-3">
                    <button type="button" class="btn btn-sm btn-outline-secondary mr-2"
                        data-action="selectall">
                        ' . get_string('selectall', 'local_jobboard') . '
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary mr-2"
                        data-action="selectidentity">
                        ' . get_string('selectidentitydocs', 'local_jobboard') . '
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary"
                        data-action="selectbackground">
                        ' . get_string('selectbackgrounddocs', 'local_jobboard') . '
                    </button>
                </div>
            </div>
        ');

        // Validity period.
        $mform->addElement('header', 'validityheader', get_string('validityperiod', 'local_jobboard'));

        $mform->addElement('date_selector', 'validfrom', get_string('validfrom', 'local_jobboard'));
        $mform->addRule('validfrom', get_string('required'), 'required', null, 'client');
        $mform->setDefault('validfrom', time());

        $mform->addElement('date_selector', 'validuntil', get_string('validuntil', 'local_jobboard'),
            ['optional' => true]);
        $mform->addHelpButton('validuntil', 'validuntil', 'local_jobboard');

        // Notes.
        $mform->addElement('header', 'notesheader', get_string('additionalinfo', 'local_jobboard'));

        $mform->addElement('textarea', 'notes', get_string('notes'), ['rows' => 4, 'cols' => 50]);
        $mform->setType('notes', PARAM_TEXT);

        // Buttons.
        $this->add_action_buttons(true, $exemption ? get_string('savechanges') : get_string('create'));

        // Set data for editing.
        if ($exemption) {
            $exemption->exempteddoctypes = explode(',', $exemption->exempteddoctypes);
            $this->set_data($exemption);
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
        $errors = parent::validation($data, $files);

        // Check user exists.
        global $DB;
        if (!empty($data['userid'])) {
            if (!$DB->record_exists('user', ['id' => $data['userid'], 'deleted' => 0])) {
                $errors['userid'] = get_string('usernotfound', 'local_jobboard');
            }
        }

        // Validate dates.
        if (!empty($data['validuntil']) && $data['validuntil'] < $data['validfrom']) {
            $errors['validuntil'] = get_string('error:invaliddates', 'local_jobboard');
        }

        // Check exemption type.
        if (empty($data['exemptiontype'])) {
            $errors['exemptiontype'] = get_string('required');
        }

        // Check at least one document type selected.
        if (empty($data['exempteddoctypes'])) {
            $errors['exempteddoctypes'] = get_string('selectatleastone', 'local_jobboard');
        }

        return $errors;
    }
}
