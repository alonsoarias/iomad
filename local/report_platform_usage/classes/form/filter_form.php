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
 * Report filter form.
 *
 * @package   local_report_platform_usage
 * @copyright 2024 IOMAD
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_report_platform_usage\form;

defined('MOODLE_INTERNAL') || die();

require_once($GLOBALS['CFG']->libdir . '/formslib.php');

/**
 * Filter form for the platform usage report.
 */
class filter_form extends \moodleform {

    /**
     * Form definition.
     */
    public function definition() {
        $mform = $this->_form;

        // Company selector.
        $companies = \local_report_platform_usage\report::get_companies();
        $companyoptions = [0 => get_string('allcompanies', 'local_report_platform_usage')];
        foreach ($companies as $id => $name) {
            $companyoptions[$id] = format_string($name);
        }

        $mform->addElement('select', 'companyid', get_string('company', 'local_report_platform_usage'), $companyoptions);
        $mform->setType('companyid', PARAM_INT);
        $mform->setDefault('companyid', 0);

        // Date range.
        $mform->addElement('date_selector', 'datefrom', get_string('datefrom', 'local_report_platform_usage'));
        $mform->setDefault('datefrom', strtotime('-30 days midnight'));

        $mform->addElement('date_selector', 'dateto', get_string('dateto', 'local_report_platform_usage'));
        $mform->setDefault('dateto', time());

        // Buttons.
        $this->add_action_buttons(false, get_string('filter', 'local_report_platform_usage'));
    }
}
