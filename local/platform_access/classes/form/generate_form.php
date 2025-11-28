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

        // Header.
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

        // Date range.
        $mform->addElement('date_selector', 'datefrom', get_string('datefrom', 'local_platform_access'));
        $mform->setDefault('datefrom', strtotime('-30 days'));

        $mform->addElement('date_selector', 'dateto', get_string('dateto', 'local_platform_access'));
        $mform->setDefault('dateto', time());

        // Logins per user.
        $mform->addElement('text', 'loginsperuser', get_string('loginsperuser', 'local_platform_access'));
        $mform->setType('loginsperuser', PARAM_INT);
        $mform->setDefault('loginsperuser', 1);
        $mform->addRule('loginsperuser', null, 'numeric', null, 'client');

        // Course access per user.
        $mform->addElement('text', 'courseaccessperuser', get_string('courseaccessperuser', 'local_platform_access'));
        $mform->setType('courseaccessperuser', PARAM_INT);
        $mform->setDefault('courseaccessperuser', 1);
        $mform->addRule('courseaccessperuser', null, 'numeric', null, 'client');

        // Activity access per course.
        $mform->addElement('text', 'activityaccesspercourse', get_string('activityaccesspercourse', 'local_platform_access'));
        $mform->setType('activityaccesspercourse', PARAM_INT);
        $mform->setDefault('activityaccesspercourse', 1);
        $mform->addRule('activityaccesspercourse', null, 'numeric', null, 'client');

        // Randomize timestamps.
        $mform->addElement('advcheckbox', 'randomize', get_string('randomize', 'local_platform_access'));
        $mform->setDefault('randomize', 1);
        $mform->addHelpButton('randomize', 'randomize', 'local_platform_access');

        // Include admin users.
        $mform->addElement('advcheckbox', 'includeadmins', get_string('includeadmins', 'local_platform_access'));
        $mform->setDefault('includeadmins', 0);

        // Only active users.
        $mform->addElement('advcheckbox', 'onlyactive', get_string('onlyactiveusers', 'local_platform_access'));
        $mform->setDefault('onlyactive', 1);

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

        if ($data['loginsperuser'] < 0) {
            $errors['loginsperuser'] = get_string('error');
        }

        if ($data['courseaccessperuser'] < 0) {
            $errors['courseaccessperuser'] = get_string('error');
        }

        if ($data['activityaccesspercourse'] < 0) {
            $errors['activityaccesspercourse'] = get_string('error');
        }

        return $errors;
    }
}
