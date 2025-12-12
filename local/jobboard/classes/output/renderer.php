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
 * Renderer for Job Board plugin.
 *
 * This is the main renderer class that uses traits for organization.
 * Renderer traits are located in classes/output/renderer/ directory.
 *
 * Structure:
 * - dashboard_renderer: Dashboard page and widgets
 * - public_renderer: Public-facing pages (browse, vacancy, convocatoria)
 * - vacancy_renderer: Vacancy management pages
 * - convocatoria_renderer: Convocatoria management pages
 * - application_renderer: Application pages
 * - review_renderer: Review and validation pages
 * - admin_renderer: Admin settings and tools pages
 * - exemption_renderer: Exemption management pages
 * - committee_renderer: Committee and reviewer assignment pages
 * - reports_renderer: Reports and analytics pages
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output;

defined('MOODLE_INTERNAL') || die();

// Load renderer traits.
require_once(__DIR__ . '/renderer/dashboard_renderer.php');
require_once(__DIR__ . '/renderer/public_renderer.php');
require_once(__DIR__ . '/renderer/vacancy_renderer.php');
require_once(__DIR__ . '/renderer/convocatoria_renderer.php');
require_once(__DIR__ . '/renderer/application_renderer.php');
require_once(__DIR__ . '/renderer/review_renderer.php');
require_once(__DIR__ . '/renderer/admin_renderer.php');
require_once(__DIR__ . '/renderer/exemption_renderer.php');
require_once(__DIR__ . '/renderer/committee_renderer.php');
require_once(__DIR__ . '/renderer/reports_renderer.php');

/**
 * Renderer class for the Job Board plugin.
 *
 * All rendering functionality is organized into traits:
 * - dashboard_renderer: Dashboard and widgets
 * - public_renderer: Public-facing pages
 * - vacancy_renderer: Vacancy management
 * - convocatoria_renderer: Convocatoria management
 * - application_renderer: Application handling
 * - review_renderer: Document review and validation
 * - admin_renderer: Admin settings and tools
 * - exemption_renderer: Exemption management
 * - committee_renderer: Committee and reviewer assignment
 * - reports_renderer: Reports and analytics
 */
class renderer extends renderer_base {

    // Traits define all render_* and prepare_* methods.
    use renderer\dashboard_renderer;
    use renderer\public_renderer;
    use renderer\vacancy_renderer;
    use renderer\convocatoria_renderer;
    use renderer\application_renderer;
    use renderer\review_renderer;
    use renderer\admin_renderer;
    use renderer\exemption_renderer;
    use renderer\committee_renderer;
    use renderer\reports_renderer;
}
