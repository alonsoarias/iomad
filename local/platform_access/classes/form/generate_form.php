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

        // Clean existing records before generating.
        $mform->addElement('advcheckbox', 'cleanbeforegenerate', get_string('cleanbeforegenerate', 'local_platform_access'));
        $mform->setDefault('cleanbeforegenerate', 1);
        $mform->addHelpButton('cleanbeforegenerate', 'cleanbeforegenerate', 'local_platform_access');

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
        $mform->addElement('text', 'loginsmin', get_string('loginsperuser', 'local_platform_access') . ' (min)', ['size' => 5]);
        $mform->setType('loginsmin', PARAM_INT);
        $mform->setDefault('loginsmin', 1);

        $mform->addElement('text', 'loginsmax', get_string('loginsperuser', 'local_platform_access') . ' (max)', ['size' => 5]);
        $mform->setType('loginsmax', PARAM_INT);
        $mform->setDefault('loginsmax', 5);

        // Course access per user (min-max).
        $mform->addElement('text', 'courseaccessmin', get_string('courseaccessperuser', 'local_platform_access') . ' (min)', ['size' => 5]);
        $mform->setType('courseaccessmin', PARAM_INT);
        $mform->setDefault('courseaccessmin', 1);

        $mform->addElement('text', 'courseaccessmax', get_string('courseaccessperuser', 'local_platform_access') . ' (max)', ['size' => 5]);
        $mform->setType('courseaccessmax', PARAM_INT);
        $mform->setDefault('courseaccessmax', 3);

        // Activity access per course (min-max).
        $mform->addElement('text', 'activityaccessmin', get_string('activityaccesspercourse', 'local_platform_access') . ' (min)', ['size' => 5]);
        $mform->setType('activityaccessmin', PARAM_INT);
        $mform->setDefault('activityaccessmin', 1);

        $mform->addElement('text', 'activityaccessmax', get_string('activityaccesspercourse', 'local_platform_access') . ' (max)', ['size' => 5]);
        $mform->setType('activityaccessmax', PARAM_INT);
        $mform->setDefault('activityaccessmax', 2);

        // Header: Advanced Events.
        $mform->addElement('header', 'advancedevents', get_string('advancedevents', 'local_platform_access'));

        // Generate dashboard access.
        $mform->addElement('advcheckbox', 'generatedashboard', get_string('generatedashboard', 'local_platform_access'));
        $mform->setDefault('generatedashboard', 1);
        $mform->addHelpButton('generatedashboard', 'generatedashboard', 'local_platform_access');

        // Generate logouts.
        $mform->addElement('advcheckbox', 'generatelogouts', get_string('generatelogouts', 'local_platform_access'));
        $mform->setDefault('generatelogouts', 0);
        $mform->addHelpButton('generatelogouts', 'generatelogouts', 'local_platform_access');

        // Generate course completions.
        $mform->addElement('advcheckbox', 'generatecompletions', get_string('generatecompletions', 'local_platform_access'));
        $mform->setDefault('generatecompletions', 0);
        $mform->addHelpButton('generatecompletions', 'generatecompletions', 'local_platform_access');

        // Completion percentage (min-max).
        $mform->addElement('text', 'completionpercentmin', get_string('completionpercent', 'local_platform_access') . ' (min %)', ['size' => 5]);
        $mform->setType('completionpercentmin', PARAM_INT);
        $mform->setDefault('completionpercentmin', 50);
        $mform->disabledIf('completionpercentmin', 'generatecompletions', 'notchecked');

        $mform->addElement('text', 'completionpercentmax', get_string('completionpercent', 'local_platform_access') . ' (max %)', ['size' => 5]);
        $mform->setType('completionpercentmax', PARAM_INT);
        $mform->setDefault('completionpercentmax', 100);
        $mform->disabledIf('completionpercentmax', 'generatecompletions', 'notchecked');

        // Header: Session Duration Tracking (HIGH PRIORITY).
        $mform->addElement('header', 'sessionduration', get_string('sessionduration', 'local_platform_access'));
        $mform->setExpanded('sessionduration', true);

        // Enable session duration tracking.
        $mform->addElement('advcheckbox', 'calculatesessionduration', get_string('calculatesessionduration', 'local_platform_access'));
        $mform->setDefault('calculatesessionduration', 1);
        $mform->addHelpButton('calculatesessionduration', 'calculatesessionduration', 'local_platform_access');

        // Session duration min (in minutes).
        $mform->addElement('text', 'sessiondurationmin', get_string('sessiondurationminutes', 'local_platform_access') . ' (min)', ['size' => 5]);
        $mform->setType('sessiondurationmin', PARAM_INT);
        $mform->setDefault('sessiondurationmin', 10);
        $mform->disabledIf('sessiondurationmin', 'calculatesessionduration', 'notchecked');

        // Session duration max (in minutes).
        $mform->addElement('text', 'sessiondurationmax', get_string('sessiondurationminutes', 'local_platform_access') . ' (max)', ['size' => 5]);
        $mform->setType('sessiondurationmax', PARAM_INT);
        $mform->setDefault('sessiondurationmax', 120);
        $mform->disabledIf('sessiondurationmax', 'calculatesessionduration', 'notchecked');

        // Header: Security Monitoring (MEDIUM PRIORITY).
        $mform->addElement('header', 'securitymonitoring', get_string('securitymonitoring', 'local_platform_access'));

        // Generate failed login attempts.
        $mform->addElement('advcheckbox', 'generatefailedlogins', get_string('generatefailedlogins', 'local_platform_access'));
        $mform->setDefault('generatefailedlogins', 1);
        $mform->addHelpButton('generatefailedlogins', 'generatefailedlogins', 'local_platform_access');

        // Failed logins per user (min-max).
        $mform->addElement('text', 'failedloginsmin', get_string('failedloginsperuser', 'local_platform_access') . ' (min)', ['size' => 5]);
        $mform->setType('failedloginsmin', PARAM_INT);
        $mform->setDefault('failedloginsmin', 0);
        $mform->disabledIf('failedloginsmin', 'generatefailedlogins', 'notchecked');

        $mform->addElement('text', 'failedloginsmax', get_string('failedloginsperuser', 'local_platform_access') . ' (max)', ['size' => 5]);
        $mform->setType('failedloginsmax', PARAM_INT);
        $mform->setDefault('failedloginsmax', 3);
        $mform->disabledIf('failedloginsmax', 'generatefailedlogins', 'notchecked');

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
            $errors['loginsmin'] = get_string('error');
        }

        if ($data['loginsmin'] > $data['loginsmax']) {
            $errors['loginsmax'] = get_string('minmaxerror', 'local_platform_access');
        }

        if ($data['courseaccessmin'] < 0 || $data['courseaccessmax'] < 0) {
            $errors['courseaccessmin'] = get_string('error');
        }

        if ($data['courseaccessmin'] > $data['courseaccessmax']) {
            $errors['courseaccessmax'] = get_string('minmaxerror', 'local_platform_access');
        }

        if ($data['activityaccessmin'] < 0 || $data['activityaccessmax'] < 0) {
            $errors['activityaccessmin'] = get_string('error');
        }

        if ($data['activityaccessmin'] > $data['activityaccessmax']) {
            $errors['activityaccessmax'] = get_string('minmaxerror', 'local_platform_access');
        }

        // Validate completion percentage range.
        if (!empty($data['generatecompletions'])) {
            if ($data['completionpercentmin'] < 0 || $data['completionpercentmin'] > 100) {
                $errors['completionpercentmin'] = get_string('percentageerror', 'local_platform_access');
            }
            if ($data['completionpercentmax'] < 0 || $data['completionpercentmax'] > 100) {
                $errors['completionpercentmax'] = get_string('percentageerror', 'local_platform_access');
            }
            if ($data['completionpercentmin'] > $data['completionpercentmax']) {
                $errors['completionpercentmax'] = get_string('minmaxerror', 'local_platform_access');
            }
        }

        // Validate session duration range.
        if (!empty($data['calculatesessionduration'])) {
            if ($data['sessiondurationmin'] < 1) {
                $errors['sessiondurationmin'] = get_string('sessiondurationminerror', 'local_platform_access');
            }
            if ($data['sessiondurationmax'] < $data['sessiondurationmin']) {
                $errors['sessiondurationmax'] = get_string('minmaxerror', 'local_platform_access');
            }
            if ($data['sessiondurationmax'] > 480) { // Max 8 hours.
                $errors['sessiondurationmax'] = get_string('sessiondurationmaxerror', 'local_platform_access');
            }
        }

        // Validate failed logins range.
        if (!empty($data['generatefailedlogins'])) {
            if ($data['failedloginsmin'] < 0) {
                $errors['failedloginsmin'] = get_string('error');
            }
            if ($data['failedloginsmax'] < $data['failedloginsmin']) {
                $errors['failedloginsmax'] = get_string('minmaxerror', 'local_platform_access');
            }
        }

        return $errors;
    }
}
