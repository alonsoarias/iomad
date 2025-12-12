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
 * Public pages renderer trait for Job Board plugin.
 *
 * Contains all public-facing page rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait for public pages rendering functionality.
 */
trait public_renderer {

    /**
     * Render public vacancies page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_public_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/public', $data);
    }

    /**
     * Render public vacancy detail page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_public_detail(array $data): string {
        return $this->render_from_template('local_jobboard/pages/public_detail', $data);
    }

    /**
     * Render public convocatoria page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_public_convocatoria_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/public_convocatoria', $data);
    }

    /**
     * Render public vacancy page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_public_vacancy_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/public_vacancy', $data);
    }

    /**
     * Render browse convocatorias page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_browse_convocatorias_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/browse_convocatorias', $data);
    }

    /**
     * Render update profile page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_updateprofile_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/updateprofile', $data);
    }

    /**
     * Render signup success page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_signup_success_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/signup_success', $data);
    }
}
