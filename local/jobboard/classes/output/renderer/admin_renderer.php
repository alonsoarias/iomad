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
 * Admin renderer trait for Job Board plugin.
 *
 * Contains all admin-related rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait for admin rendering functionality.
 */
trait admin_renderer {

    /**
     * Render admin roles page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_admin_roles_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin_roles', $data);
    }

    /**
     * Render migrate page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_migrate_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/migrate', $data);
    }

    /**
     * Render admin doctypes page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_admin_doctypes_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin_doctypes', $data);
    }

    /**
     * Render admin templates page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_admin_templates_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin_templates', $data);
    }

    /**
     * Render import vacancies page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_import_vacancies_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/import_vacancies', $data);
    }

    /**
     * Render import vacancies results page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_import_vacancies_results_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/import_vacancies_results', $data);
    }

    /**
     * Render reports page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_reports_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/reports', $data);
    }
}
