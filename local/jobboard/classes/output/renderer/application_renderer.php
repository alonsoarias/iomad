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
 * Application renderer trait for Job Board plugin.
 *
 * Contains all application-related rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait for application rendering functionality.
 */
trait application_renderer {

    /**
     * Render applications list page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_applications_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/applications', $data);
    }

    /**
     * Render application form page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_apply_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/apply', $data);
    }

    /**
     * Render application detail page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_application_detail_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/application_detail', $data);
    }
}
