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
 * Committee renderer trait for Job Board plugin.
 *
 * Contains all committee and reviewer assignment rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait for committee rendering functionality.
 */
trait committee_renderer {

    /**
     * Render program reviewers page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_program_reviewers_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/program_reviewers', $data);
    }

    /**
     * Render committee page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_committee_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/committee', $data);
    }

    /**
     * Render assign reviewer page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_assign_reviewer_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/assign_reviewer', $data);
    }

    /**
     * Render schedule interview page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_schedule_interview_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/schedule_interview', $data);
    }

    /**
     * Render interview complete form page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_interview_complete_form_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/interview_complete_form', $data);
    }
}
