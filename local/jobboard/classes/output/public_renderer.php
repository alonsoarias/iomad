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
 * Public renderer for Job Board plugin.
 *
 * Handles rendering of public-facing pages like landing page and vacancy browser.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output;

defined('MOODLE_INTERNAL') || die();

use moodle_url;

/**
 * Public renderer class.
 *
 * Responsible for rendering public-facing UI components including
 * landing pages, vacancy browsers, and search results.
 */
class public_renderer extends renderer_base {

    /**
     * Render public landing page.
     *
     * @param array $featurevacancies Featured vacancies.
     * @param array $recentvacancies Recent vacancies.
     * @param array $stats Public statistics.
     * @param array $categories Vacancy categories.
     * @return string HTML output.
     */
    public function render_landing_page(
        array $featurevacancies = [],
        array $recentvacancies = [],
        array $stats = [],
        array $categories = []
    ): string {
        $vacancyrenderer = new vacancy_renderer($this->page, $this->target);

        $featureddata = array_map(function($v) use ($vacancyrenderer) {
            return $vacancyrenderer->prepare_vacancy_card_data($v, true);
        }, $featurevacancies);

        $recentdata = array_map(function($v) use ($vacancyrenderer) {
            return $vacancyrenderer->prepare_vacancy_card_data($v, true);
        }, $recentvacancies);

        $data = [
            'featuredvacancies' => $featureddata,
            'hasfeatured' => !empty($featureddata),
            'recentvacancies' => $recentdata,
            'hasrecent' => !empty($recentdata),
            'stats' => $this->prepare_public_stats($stats),
            'categories' => $this->prepare_categories($categories),
            'hascategories' => !empty($categories),
            'searchurl' => $this->get_url('vacancies'),
            'browsevacanciesurl' => $this->get_url('vacancies'),
            'convocatoriasurl' => $this->get_url('convocatorias'),
        ];

        return $this->render_from_template('local_jobboard/landing_page', $data);
    }

    /**
     * Prepare public statistics for display.
     *
     * @param array $stats Raw statistics.
     * @return array Formatted statistics.
     */
    protected function prepare_public_stats(array $stats): array {
        return [
            'openvacancies' => $stats['openvacancies'] ?? 0,
            'totalpositions' => $stats['totalpositions'] ?? 0,
            'activeconvocatorias' => $stats['activeconvocatorias'] ?? 0,
            'departments' => $stats['departments'] ?? 0,
        ];
    }

    /**
     * Prepare categories for display.
     *
     * @param array $categories Raw categories.
     * @return array Formatted categories.
     */
    protected function prepare_categories(array $categories): array {
        $items = [];
        foreach ($categories as $category) {
            $items[] = [
                'id' => $category->id ?? 0,
                'name' => $category->name ?? '',
                'icon' => $category->icon ?? 'folder',
                'count' => $category->count ?? 0,
                'url' => $this->get_url('vacancies', ['category' => $category->id ?? 0]),
            ];
        }
        return $items;
    }

    /**
     * Render vacancy browser page.
     *
     * @param array $vacancies Vacancies to display.
     * @param array $filters Current filter values.
     * @param string $pagination Pagination HTML.
     * @param int $totalcount Total vacancy count.
     * @return string HTML output.
     */
    public function render_vacancy_browser(
        array $vacancies,
        array $filters = [],
        string $pagination = '',
        int $totalcount = 0
    ): string {
        $vacancyrenderer = new vacancy_renderer($this->page, $this->target);

        $vacancydata = array_map(function($v) use ($vacancyrenderer) {
            return $vacancyrenderer->prepare_vacancy_card_data($v, true);
        }, $vacancies);

        $data = [
            'vacancies' => $vacancydata,
            'hasvacancies' => !empty($vacancydata),
            'count' => count($vacancydata),
            'totalcount' => $totalcount,
            'filters' => $this->prepare_filter_data($filters),
            'hasactivefilters' => $this->has_active_filters($filters),
            'clearfiltersurl' => $this->get_url('vacancies'),
            'pagination' => $pagination,
            'searchformhtml' => $this->render_search_form($filters),
            'filterformhtml' => $this->render_filter_sidebar($filters),
        ];

        return $this->render_from_template('local_jobboard/vacancy_browser', $data);
    }

    /**
     * Prepare filter data for display.
     *
     * @param array $filters Current filter values.
     * @return array Filter configuration.
     */
    protected function prepare_filter_data(array $filters): array {
        return [
            'search' => $filters['search'] ?? '',
            'contracttype' => $filters['contracttype'] ?? '',
            'location' => $filters['location'] ?? '',
            'department' => $filters['department'] ?? '',
            'convocatoriaid' => $filters['convocatoriaid'] ?? '',
            'sortby' => $filters['sortby'] ?? 'closedate',
            'sortorder' => $filters['sortorder'] ?? 'asc',
        ];
    }

    /**
     * Check if any filters are active.
     *
     * @param array $filters Current filter values.
     * @return bool True if filters are active.
     */
    protected function has_active_filters(array $filters): bool {
        $filterkeys = ['search', 'contracttype', 'location', 'department', 'convocatoriaid'];
        foreach ($filterkeys as $key) {
            if (!empty($filters[$key])) {
                return true;
            }
        }
        return false;
    }

    /**
     * Render search form.
     *
     * @param array $filters Current filter values.
     * @return string HTML output.
     */
    public function render_search_form(array $filters = []): string {
        $data = [
            'searchvalue' => $filters['search'] ?? '',
            'actionurl' => $this->get_url('vacancies'),
            'placeholder' => get_string('searchplaceholder', 'local_jobboard'),
        ];

        return $this->render_from_template('local_jobboard/search_form', $data);
    }

    /**
     * Render filter sidebar.
     *
     * @param array $filters Current filter values.
     * @return string HTML output.
     */
    public function render_filter_sidebar(array $filters = []): string {
        global $DB;

        // Get available filter options.
        $contracttypes = $this->get_contract_type_options($filters['contracttype'] ?? '');
        $locations = $this->get_location_options($filters['location'] ?? '');
        $departments = $this->get_department_options($filters['department'] ?? '');
        $convocatorias = $this->get_convocatoria_options($filters['convocatoriaid'] ?? '');
        $sortoptions = $this->get_sort_options($filters['sortby'] ?? 'closedate');

        $data = [
            'actionurl' => $this->get_url('vacancies'),
            'contracttypes' => $contracttypes,
            'hascontracttypes' => !empty($contracttypes),
            'locations' => $locations,
            'haslocations' => !empty($locations),
            'departments' => $departments,
            'hasdepartments' => !empty($departments),
            'convocatorias' => $convocatorias,
            'hasconvocatorias' => !empty($convocatorias),
            'sortoptions' => $sortoptions,
            'sortorderasc' => ($filters['sortorder'] ?? 'asc') === 'asc',
        ];

        return $this->render_from_template('local_jobboard/filter_sidebar', $data);
    }

    /**
     * Get contract type filter options.
     *
     * @param string $selected Currently selected value.
     * @return array Options array.
     */
    protected function get_contract_type_options(string $selected = ''): array {
        $types = ['fulltime', 'parttime', 'temporary', 'contract', 'internship'];
        $options = [];

        foreach ($types as $type) {
            $options[] = [
                'value' => $type,
                'label' => get_string('contract:' . $type, 'local_jobboard'),
                'selected' => $selected === $type,
            ];
        }

        return $options;
    }

    /**
     * Get location filter options.
     *
     * @param string $selected Currently selected value.
     * @return array Options array.
     */
    protected function get_location_options(string $selected = ''): array {
        global $DB;

        $locations = $DB->get_records_sql(
            "SELECT DISTINCT location FROM {local_jobboard_vacancy}
             WHERE location IS NOT NULL AND location != '' AND status = 'published'
             ORDER BY location"
        );

        $options = [];
        foreach ($locations as $loc) {
            $options[] = [
                'value' => $loc->location,
                'label' => $loc->location,
                'selected' => $selected === $loc->location,
            ];
        }

        return $options;
    }

    /**
     * Get department filter options.
     *
     * @param string $selected Currently selected value.
     * @return array Options array.
     */
    protected function get_department_options(string $selected = ''): array {
        global $DB;

        $departments = $DB->get_records_sql(
            "SELECT DISTINCT department FROM {local_jobboard_vacancy}
             WHERE department IS NOT NULL AND department != '' AND status = 'published'
             ORDER BY department"
        );

        $options = [];
        foreach ($departments as $dept) {
            $options[] = [
                'value' => $dept->department,
                'label' => $dept->department,
                'selected' => $selected === $dept->department,
            ];
        }

        return $options;
    }

    /**
     * Get convocatoria filter options.
     *
     * @param string $selected Currently selected value.
     * @return array Options array.
     */
    protected function get_convocatoria_options(string $selected = ''): array {
        global $DB;

        $convocatorias = $DB->get_records_select(
            'local_jobboard_convocatoria',
            "status = 'open' AND enddate > ?",
            [time()],
            'name ASC'
        );

        $options = [];
        foreach ($convocatorias as $conv) {
            $options[] = [
                'value' => $conv->id,
                'label' => $conv->code . ' - ' . $conv->name,
                'selected' => $selected == $conv->id,
            ];
        }

        return $options;
    }

    /**
     * Get sort options.
     *
     * @param string $selected Currently selected sort field.
     * @return array Options array.
     */
    protected function get_sort_options(string $selected = 'closedate'): array {
        $sorts = [
            'closedate' => get_string('sortby:closedate', 'local_jobboard'),
            'title' => get_string('sortby:title', 'local_jobboard'),
            'timecreated' => get_string('sortby:newest', 'local_jobboard'),
            'positions' => get_string('sortby:positions', 'local_jobboard'),
        ];

        $options = [];
        foreach ($sorts as $value => $label) {
            $options[] = [
                'value' => $value,
                'label' => $label,
                'selected' => $selected === $value,
            ];
        }

        return $options;
    }

    /**
     * Render search results page.
     *
     * @param string $query Search query.
     * @param array $results Search results.
     * @param string $pagination Pagination HTML.
     * @param int $totalcount Total results count.
     * @return string HTML output.
     */
    public function render_search_results(
        string $query,
        array $results,
        string $pagination = '',
        int $totalcount = 0
    ): string {
        $vacancyrenderer = new vacancy_renderer($this->page, $this->target);

        $resultdata = array_map(function($v) use ($vacancyrenderer) {
            return $vacancyrenderer->prepare_vacancy_card_data($v, true);
        }, $results);

        $data = [
            'query' => s($query),
            'results' => $resultdata,
            'hasresults' => !empty($resultdata),
            'count' => count($resultdata),
            'totalcount' => $totalcount,
            'pagination' => $pagination,
            'searchagainurl' => $this->get_url('vacancies'),
            'noresultsmessage' => get_string('noresultsforsearch', 'local_jobboard', s($query)),
        ];

        return $this->render_from_template('local_jobboard/search_results', $data);
    }

    /**
     * Render public convocatoria list.
     *
     * @param array $convocatorias Active convocatorias.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_public_convocatoria_list(
        array $convocatorias,
        string $pagination = ''
    ): string {
        $convocatoriarenderer = new convocatoria_renderer($this->page, $this->target);

        $convocatoriadata = array_map(function($c) use ($convocatoriarenderer) {
            return $convocatoriarenderer->prepare_convocatoria_card_data($c, false);
        }, $convocatorias);

        $data = [
            'convocatorias' => $convocatoriadata,
            'hasconvocatorias' => !empty($convocatoriadata),
            'count' => count($convocatoriadata),
            'pagination' => $pagination,
        ];

        return $this->render_from_template('local_jobboard/public_convocatoria_list', $data);
    }

    /**
     * Render header search widget.
     *
     * @param string $currentquery Current search query if any.
     * @return string HTML output.
     */
    public function render_header_search(string $currentquery = ''): string {
        return $this->render_from_template('local_jobboard/header_search', [
            'query' => s($currentquery),
            'actionurl' => $this->get_url('vacancies'),
            'placeholder' => get_string('searchjobs', 'local_jobboard'),
        ]);
    }

    /**
     * Render featured vacancies section.
     *
     * @param array $vacancies Featured vacancies.
     * @param string $title Section title.
     * @return string HTML output.
     */
    public function render_featured_vacancies(array $vacancies, string $title = ''): string {
        $vacancyrenderer = new vacancy_renderer($this->page, $this->target);

        $vacancydata = array_map(function($v) use ($vacancyrenderer) {
            $data = $vacancyrenderer->prepare_vacancy_card_data($v, true);
            $data['isfeatured'] = true;
            return $data;
        }, $vacancies);

        return $this->render_from_template('local_jobboard/featured_vacancies', [
            'title' => $title ?: get_string('featuredvacancies', 'local_jobboard'),
            'vacancies' => $vacancydata,
            'hasvacancies' => !empty($vacancydata),
            'viewallurl' => $this->get_url('vacancies'),
        ]);
    }

    /**
     * Render job alert subscription form.
     *
     * @param array $categories Available categories.
     * @param array $locations Available locations.
     * @return string HTML output.
     */
    public function render_job_alert_form(array $categories = [], array $locations = []): string {
        $data = [
            'actionurl' => $this->get_url('jobalerts', ['action' => 'subscribe']),
            'categories' => $this->prepare_categories($categories),
            'hascategories' => !empty($categories),
            'locations' => $this->get_location_options(),
            'haslocations' => !empty($locations),
        ];

        return $this->render_from_template('local_jobboard/job_alert_form', $data);
    }

    /**
     * Render social share buttons.
     *
     * @param object $vacancy Vacancy to share.
     * @return string HTML output.
     */
    public function render_share_buttons($vacancy): string {
        $shareurl = $this->build_url('vacancy', ['id' => $vacancy->id])->out(true);
        $title = rawurlencode($vacancy->title);

        $data = [
            'shareurl' => $shareurl,
            'title' => $vacancy->title,
            'facebookurl' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareurl),
            'twitterurl' => 'https://twitter.com/intent/tweet?url=' . rawurlencode($shareurl) . '&text=' . $title,
            'linkedinurl' => 'https://www.linkedin.com/sharing/share-offsite/?url=' . rawurlencode($shareurl),
            'whatsappurl' => 'https://wa.me/?text=' . $title . '%20' . rawurlencode($shareurl),
            'emailurl' => 'mailto:?subject=' . $title . '&body=' . rawurlencode($shareurl),
        ];

        return $this->render_from_template('local_jobboard/share_buttons', $data);
    }

    /**
     * Render breadcrumb navigation.
     *
     * @param array $items Breadcrumb items.
     * @return string HTML output.
     */
    public function render_breadcrumbs(array $items): string {
        $breadcrumbs = [];
        foreach ($items as $index => $item) {
            $breadcrumbs[] = [
                'label' => $item['label'],
                'url' => $item['url'] ?? '',
                'hasurl' => !empty($item['url']),
                'isactive' => $index === count($items) - 1,
            ];
        }

        return $this->render_from_template('local_jobboard/components/breadcrumbs', [
            'items' => $breadcrumbs,
            'hasitems' => !empty($breadcrumbs),
        ]);
    }
}
