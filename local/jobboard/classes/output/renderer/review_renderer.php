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
 * Review renderer trait for Job Board plugin.
 *
 * Contains all review and validation rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait for review rendering functionality.
 */
trait review_renderer {

    /**
     * Render review page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_review_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/review', $data);
    }

    /**
     * Render my reviews page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_myreviews_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/myreviews', $data);
    }

    /**
     * Render bulk validate page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_bulk_validate_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/bulk_validate', $data);
    }

    /**
     * Render validate document page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_validate_document_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/validate_document', $data);
    }
}
