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
 * Vacancy renderer trait for Job Board plugin.
 *
 * Contains all vacancy-related rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait for vacancy rendering functionality.
 */
trait vacancy_renderer {

    /**
     * Render vacancies page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_vacancies_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/vacancies', $data);
    }

    /**
     * Render vacancy management page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_manage_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/manage', $data);
    }

    /**
     * Render vacancy detail page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_vacancy_detail_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/vacancy_detail', $data);
    }

    /**
     * Render manage applications page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_manage_applications_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/manage_applications', $data);
    }

    /**
     * Render edit/select convocatoria page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_edit_select_convocatoria_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/edit_select_convocatoria', $data);
    }

    /**
     * Render edit vacancy form page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_edit_vacancy_form_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/edit_vacancy_form', $data);
    }
}
