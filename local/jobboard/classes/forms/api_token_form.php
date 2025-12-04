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
 * API token creation form for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

use local_jobboard\api_token;

/**
 * Form for creating API tokens.
 */
class api_token_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        global $USER;

        $mform = $this->_form;

        // Token description.
        $mform->addElement('text', 'description', get_string('description'), ['size' => 50]);
        $mform->setType('description', PARAM_TEXT);
        $mform->addRule('description', get_string('required'), 'required', null, 'client');
        $mform->addHelpButton('description', 'api:token:description', 'local_jobboard');

        // User selector.
        $mform->addElement('autocomplete', 'userid', get_string('user'), [], [
            'ajax' => 'core_user/form_user_selector',
            'multiple' => false,
            'valuehtmlcallback' => function($value) {
                global $DB;
                $user = $DB->get_record('user', ['id' => $value]);
                if ($user) {
                    return fullname($user) . ' (' . $user->email . ')';
                }
                return '';
            }
        ]);
        $mform->addRule('userid', get_string('required'), 'required', null, 'client');
        $mform->setDefault('userid', $USER->id);

        // Permissions section.
        $mform->addElement('header', 'permissionsheader', get_string('permissions', 'local_jobboard'));

        $permnames = api_token::get_permission_names();
        foreach (api_token::PERMISSIONS as $permission) {
            $mform->addElement('checkbox', 'perm_' . $permission, $permnames[$permission] ?? $permission);
            $mform->setDefault('perm_' . $permission, 1);
        }

        // Validity period.
        $mform->addElement('header', 'validityheader', get_string('api:token:validity', 'local_jobboard'));

        $mform->addElement('date_selector', 'validfrom', get_string('validfrom', 'local_jobboard'), ['optional' => true]);
        $mform->addHelpButton('validfrom', 'api:token:validfrom', 'local_jobboard');

        $mform->addElement('date_selector', 'validuntil', get_string('validuntil', 'local_jobboard'), ['optional' => true]);
        $mform->addHelpButton('validuntil', 'api:token:validuntil', 'local_jobboard');

        // IP whitelist.
        $mform->addElement('header', 'securityheader', get_string('security', 'local_jobboard'));

        $mform->addElement('textarea', 'ipwhitelist', get_string('api:token:ipwhitelist', 'local_jobboard'),
            ['rows' => 4, 'cols' => 40]);
        $mform->setType('ipwhitelist', PARAM_TEXT);
        $mform->addHelpButton('ipwhitelist', 'api:token:ipwhitelist', 'local_jobboard');

        // Submit buttons.
        $this->add_action_buttons(true, get_string('api:token:create', 'local_jobboard'));
    }

    /**
     * Validate form data.
     *
     * @param array $data Form data.
     * @param array $files Files.
     * @return array Validation errors.
     */
    public function validation($data, $files) {
        $errors = parent::validation($data, $files);

        // Check user exists.
        global $DB;
        if (!$DB->record_exists('user', ['id' => $data['userid'], 'deleted' => 0])) {
            $errors['userid'] = get_string('error:usernotfound', 'local_jobboard');
        }

        // Check at least one permission is selected.
        $haspermission = false;
        foreach (api_token::PERMISSIONS as $permission) {
            if (!empty($data['perm_' . $permission])) {
                $haspermission = true;
                break;
            }
        }
        if (!$haspermission) {
            $errors['perm_view_vacancies'] = get_string('error:nopermission', 'local_jobboard');
        }

        // Validate IP whitelist entries.
        if (!empty($data['ipwhitelist'])) {
            $ips = array_filter(array_map('trim', explode("\n", $data['ipwhitelist'])));
            foreach ($ips as $ip) {
                // Check if it's a valid IP or CIDR.
                if (!filter_var($ip, FILTER_VALIDATE_IP) && !$this->is_valid_cidr($ip)) {
                    $errors['ipwhitelist'] = get_string('error:invalidip', 'local_jobboard', $ip);
                    break;
                }
            }
        }

        // Validate validity period.
        if (!empty($data['validfrom']) && !empty($data['validuntil'])) {
            if ($data['validfrom'] >= $data['validuntil']) {
                $errors['validuntil'] = get_string('error:invaliddates', 'local_jobboard');
            }
        }

        return $errors;
    }

    /**
     * Check if a string is a valid CIDR notation.
     *
     * @param string $cidr The CIDR string.
     * @return bool True if valid.
     */
    private function is_valid_cidr(string $cidr): bool {
        if (strpos($cidr, '/') === false) {
            return false;
        }

        list($ip, $bits) = explode('/', $cidr);

        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return false;
        }

        $bits = (int) $bits;
        return $bits >= 0 && $bits <= 32;
    }
}
