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
 * Generate access form.
 *
 * @package   local_platform_access
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_platform_access\form;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Form for generating access records.
 */
class generate_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        // Default date: November 15, 2025.
        $defaultdate = strtotime('2025-11-15');

        // Header: General options.
        $mform->addElement('header', 'generaloptions', get_string('generateaccess', 'local_platform_access'));

        // Description.
        $mform->addElement('static', 'description', '', get_string('generateaccessdesc', 'local_platform_access'));

        // Company selector.
        $companies = \local_platform_access\generator::get_companies();
        $companyoptions = [0 => get_string('all', 'local_platform_access')];
        foreach ($companies as $company) {
            $companyoptions[$company->id] = $company->name . ' (' . $company->shortname . ')';
        }
        $mform->addElement('select', 'companyid', get_string('company', 'local_platform_access'), $companyoptions);
        $mform->setDefault('companyid', 0);
        $mform->addHelpButton('companyid', 'company', 'local_platform_access');

        // Access type.
        $accesstypes = [
            'all' => get_string('all_access', 'local_platform_access'),
            'login' => get_string('loginonly', 'local_platform_access'),
            'course' => get_string('courseonly', 'local_platform_access'),
            'activity' => get_string('activityonly', 'local_platform_access'),
        ];
        $mform->addElement('select', 'accesstype', get_string('accesstype', 'local_platform_access'), $accesstypes);
        $mform->setDefault('accesstype', 'all');

        // Header: User settings.
        $mform->addElement('header', 'usersettings', get_string('usersettings', 'local_platform_access'));

        // Update user creation date.
        $mform->addElement('advcheckbox', 'updateusercreated', get_string('updateusercreated', 'local_platform_access'));
        $mform->setDefault('updateusercreated', 1);
        $mform->addHelpButton('updateusercreated', 'updateusercreated', 'local_platform_access');

        // User creation date.
        $mform->addElement('date_selector', 'usercreateddate', get_string('usercreateddate', 'local_platform_access'));
        $mform->setDefault('usercreateddate', $defaultdate);
        $mform->disabledIf('usercreateddate', 'updateusercreated', 'notchecked');

        // Only active users.
        $mform->addElement('advcheckbox', 'onlyactive', get_string('onlyactiveusers', 'local_platform_access'));
        $mform->setDefault('onlyactive', 1);

        // Include admin users.
        $mform->addElement('advcheckbox', 'includeadmins', get_string('includeadmins', 'local_platform_access'));
        $mform->setDefault('includeadmins', 0);

        // Header: Date range.
        $mform->addElement('header', 'daterange', get_string('daterange', 'local_platform_access'));

        // Date range.
        $mform->addElement('date_selector', 'datefrom', get_string('datefrom', 'local_platform_access'));
        $mform->setDefault('datefrom', $defaultdate);

        $mform->addElement('date_selector', 'dateto', get_string('dateto', 'local_platform_access'));
        $mform->setDefault('dateto', time());

        // Randomize timestamps.
        $mform->addElement('advcheckbox', 'randomize', get_string('randomize', 'local_platform_access'));
        $mform->setDefault('randomize', 1);
        $mform->addHelpButton('randomize', 'randomize', 'local_platform_access');

        // Header: Access counts (random ranges).
        $mform->addElement('header', 'accesscounts', get_string('accesscounts', 'local_platform_access'));

        // Logins per user (min-max).
        $loginsgroup = [];
        $loginsgroup[] = $mform->createElement('text', 'loginsmin', '', ['size' => 5]);
        $loginsgroup[] = $mform->createElement('static', 'loginssep', '', ' - ');
        $loginsgroup[] = $mform->createElement('text', 'loginsmax', '', ['size' => 5]);
        $mform->addGroup($loginsgroup, 'loginsgroup', get_string('loginsperuser', 'local_platform_access'), ' ', false);
        $mform->setType('loginsmin', PARAM_INT);
        $mform->setType('loginsmax', PARAM_INT);
        $mform->setDefault('loginsmin', 1);
        $mform->setDefault('loginsmax', 5);

        // Course access per user (min-max).
        $coursegroup = [];
        $coursegroup[] = $mform->createElement('text', 'courseaccessmin', '', ['size' => 5]);
        $coursegroup[] = $mform->createElement('static', 'courseaccesssep', '', ' - ');
        $coursegroup[] = $mform->createElement('text', 'courseaccessmax', '', ['size' => 5]);
        $mform->addGroup($coursegroup, 'coursegroup', get_string('courseaccessperuser', 'local_platform_access'), ' ', false);
        $mform->setType('courseaccessmin', PARAM_INT);
        $mform->setType('courseaccessmax', PARAM_INT);
        $mform->setDefault('courseaccessmin', 1);
        $mform->setDefault('courseaccessmax', 3);

        // Activity access per course (min-max).
        $activitygroup = [];
        $activitygroup[] = $mform->createElement('text', 'activityaccessmin', '', ['size' => 5]);
        $activitygroup[] = $mform->createElement('static', 'activityaccesssep', '', ' - ');
        $activitygroup[] = $mform->createElement('text', 'activityaccessmax', '', ['size' => 5]);
        $mform->addGroup($activitygroup, 'activitygroup', get_string('activityaccesspercourse', 'local_platform_access'), ' ', false);
        $mform->setType('activityaccessmin', PARAM_INT);
        $mform->setType('activityaccessmax', PARAM_INT);
        $mform->setDefault('activityaccessmin', 1);
        $mform->setDefault('activityaccessmax', 2);

        // Submit button.
        $this->add_action_buttons(true, get_string('generatebutton', 'local_platform_access'));
    }

    /**
     * Validation.
     *
     * @param array $data Form data
     * @param array $files Files
     * @return array Errors
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        if ($data['datefrom'] > $data['dateto']) {
            $errors['datefrom'] = get_string('error');
        }

        if ($data['loginsmin'] < 0 || $data['loginsmax'] < 0) {
            $errors['loginsgroup'] = get_string('error');
        }

        if ($data['loginsmin'] > $data['loginsmax']) {
            $errors['loginsgroup'] = get_string('minmaxerror', 'local_platform_access');
        }

        if ($data['courseaccessmin'] < 0 || $data['courseaccessmax'] < 0) {
            $errors['coursegroup'] = get_string('error');
        }

        if ($data['courseaccessmin'] > $data['courseaccessmax']) {
            $errors['coursegroup'] = get_string('minmaxerror', 'local_platform_access');
        }

        if ($data['activityaccessmin'] < 0 || $data['activityaccessmax'] < 0) {
            $errors['activitygroup'] = get_string('error');
        }

        if ($data['activityaccessmin'] > $data['activityaccessmax']) {
            $errors['activitygroup'] = get_string('minmaxerror', 'local_platform_access');
        }

        return $errors;
    }
}
