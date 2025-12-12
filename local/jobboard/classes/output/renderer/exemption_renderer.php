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
 * Exemption renderer trait for Job Board plugin.
 *
 * Contains all exemption-related rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait for exemption rendering functionality.
 */
trait exemption_renderer {

    /**
     * Render manage exemptions page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_manage_exemptions_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/manage_exemptions', $data);
    }

    /**
     * Render exemption form page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_exemption_form_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/exemption_form', $data);
    }

    /**
     * Render exemption revoke page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_exemption_revoke_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/exemption_revoke', $data);
    }

    /**
     * Render exemption view page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_exemption_view_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/exemption_view', $data);
    }
}
