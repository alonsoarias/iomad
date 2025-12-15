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
 * Export form for local_jobboard migration.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\forms;

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/formslib.php');

/**
 * Export form class for migration.
 */
class migrate_export_form extends \moodleform {

    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'exporthdr', get_string('fullexport', 'local_jobboard'));

        // Show what will be exported (informational only).
        $counts = $this->_customdata['counts'] ?? [];

        $infohtml = $this->build_export_info_html($counts);
        $mform->addElement('static', 'exportinfo', '', $infohtml);

        // Hidden fields to always export everything.
        $mform->addElement('hidden', 'export_all', 1);
        $mform->setType('export_all', PARAM_INT);

        $this->add_action_buttons(true, get_string('exportdownload', 'local_jobboard'));
    }

    /**
     * Build HTML for export information display.
     *
     * @param array $counts Export counts.
     * @return string HTML string.
     */
    protected function build_export_info_html(array $counts): string {
        $infohtml = '<div class="alert alert-info mb-3">' .
            '<i class="fa fa-info-circle me-2"></i>' .
            '<strong>' . get_string('fullexport_info', 'local_jobboard') . '</strong>' .
            '</div>';

        $infohtml .= '<div class="card mb-3"><div class="card-body py-2">' .
            '<h6 class="card-title mb-2"><i class="fa fa-database me-2"></i>' .
            get_string('datatorexport', 'local_jobboard') . '</h6>' .
            '<div class="row small">';

        $infohtml .= '<div class="col-md-6">' .
            '<ul class="list-unstyled mb-0">' .
            '<li><i class="fa fa-check text-success me-1"></i>' .
            get_string('doctypes', 'local_jobboard') . ': <strong>' . ($counts['doctypes'] ?? 0) . '</strong></li>' .
            '<li><i class="fa fa-check text-success me-1"></i>' .
            get_string('emailtemplates', 'local_jobboard') . ': <strong>' . ($counts['email_templates'] ?? 0) . '</strong></li>' .
            '<li><i class="fa fa-check text-success me-1"></i>' .
            get_string('convocatorias', 'local_jobboard') . ': <strong>' . ($counts['convocatorias'] ?? 0) . '</strong></li>' .
            '<li><i class="fa fa-check text-success me-1"></i>' .
            get_string('vacancies', 'local_jobboard') . ': <strong>' . ($counts['vacancies'] ?? 0) . '</strong></li>' .
            '</ul></div>';

        $infohtml .= '<div class="col-md-6">' .
            '<ul class="list-unstyled mb-0">' .
            '<li><i class="fa fa-check text-success me-1"></i>' .
            get_string('applications', 'local_jobboard') . ': <strong>' . ($counts['applications'] ?? 0) . '</strong></li>' .
            '<li><i class="fa fa-check text-success me-1"></i>' .
            get_string('documents', 'local_jobboard') . ': <strong>' . ($counts['documents'] ?? 0) . '</strong></li>' .
            '<li><i class="fa fa-check text-success me-1"></i>' .
            get_string('exemptions', 'local_jobboard') . ': <strong>' . ($counts['exemptions'] ?? 0) . '</strong></li>' .
            '<li><i class="fa fa-check text-success me-1"></i>' .
            get_string('files', 'local_jobboard') . ': <strong>' . ($counts['files'] ?? 0) . '</strong></li>' .
            '</ul></div>';

        $infohtml .= '</div></div></div>';

        // Warning about file size.
        if (($counts['files'] ?? 0) > 0) {
            $infohtml .= '<div class="alert alert-warning">' .
                '<i class="fa fa-triangle-exclamation me-2"></i>' .
                get_string('exportwarning_files', 'local_jobboard') .
                '</div>';
        }

        return $infohtml;
    }
}
