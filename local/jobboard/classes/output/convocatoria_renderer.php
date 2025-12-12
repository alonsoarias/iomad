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
 * Convocatoria renderer for Job Board plugin.
 *
 * Handles rendering of convocatoria cards, lists, and management pages.
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
 * Convocatoria renderer class.
 *
 * Responsible for rendering convocatoria-related UI components including
 * cards, lists, detail views, and management interfaces.
 */
class convocatoria_renderer extends renderer_base {

    /**
     * Render convocatoria card.
     *
     * @param object $convocatoria The convocatoria object.
     * @param bool $canmanage Whether user can manage.
     * @return string HTML output.
     */
    public function render_convocatoria_card($convocatoria, bool $canmanage = false): string {
        $data = $this->prepare_convocatoria_card_data($convocatoria, $canmanage);
        return $this->render_from_template('local_jobboard/convocatoria_card', $data);
    }

    /**
     * Prepare convocatoria card template data.
     *
     * @param object $convocatoria The convocatoria object.
     * @param bool $canmanage Whether user can manage.
     * @return array Template data.
     */
    public function prepare_convocatoria_card_data($convocatoria, bool $canmanage): array {
        global $DB;

        $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoria->id]);
        $applicationcount = $DB->count_records_sql(
            "SELECT COUNT(a.id)
             FROM {local_jobboard_application} a
             JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
             WHERE v.convocatoriaid = ?",
            [$convocatoria->id]
        );
        $daysremaining = $this->calculate_days_remaining($convocatoria->enddate);

        return [
            'id' => $convocatoria->id,
            'code' => $convocatoria->code,
            'name' => $convocatoria->name,
            'description' => $this->shorten_text($convocatoria->description ?? '', 200),
            'startdate' => $this->format_date($convocatoria->startdate),
            'enddate' => $this->format_date($convocatoria->enddate),
            'daysremaining' => $daysremaining,
            'urgent' => $this->is_date_urgent($convocatoria->enddate),
            'isopen' => $convocatoria->status === 'open' && $convocatoria->enddate > time(),
            'status' => $convocatoria->status,
            'statusclass' => $this->get_convocatoria_status_class($convocatoria->status),
            'statuslabel' => get_string('convocatoriastatus:' . $convocatoria->status, 'local_jobboard'),
            'vacancycount' => $vacancycount,
            'applicationcount' => $applicationcount,
            'viewurl' => $this->get_url('convocatoria', ['id' => $convocatoria->id]),
            'canmanage' => $canmanage,
            'editurl' => $this->get_url('convocatorias', ['action' => 'edit', 'id' => $convocatoria->id]),
            'vacanciesurl' => $this->get_url('vacancies', ['convocatoriaid' => $convocatoria->id]),
        ];
    }

    /**
     * Render convocatoria list.
     *
     * @param array $convocatorias Array of convocatoria objects.
     * @param bool $cancreate Whether user can create convocatorias.
     * @param string $filterform Filter form HTML.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_convocatoria_list(
        array $convocatorias,
        bool $cancreate = false,
        string $filterform = '',
        string $pagination = ''
    ): string {
        $cards = [];
        foreach ($convocatorias as $convocatoria) {
            $cards[] = $this->prepare_convocatoria_card_data($convocatoria, $cancreate);
        }

        $data = [
            'hasconvocatorias' => !empty($cards),
            'convocatorias' => $cards,
            'count' => count($cards),
            'cancreate' => $cancreate,
            'createurl' => $this->get_url('convocatorias', ['action' => 'create']),
            'filterform' => $filterform,
            'pagination' => $pagination,
        ];

        return $this->render_from_template('local_jobboard/convocatoria_list', $data);
    }

    /**
     * Render convocatoria detail page.
     *
     * @param object $convocatoria The convocatoria object.
     * @param array $vacancies Vacancies in this convocatoria.
     * @param bool $canmanage Whether user can manage.
     * @return string HTML output.
     */
    public function render_convocatoria_detail(
        $convocatoria,
        array $vacancies = [],
        bool $canmanage = false
    ): string {
        global $DB;

        $data = $this->prepare_convocatoria_card_data($convocatoria, $canmanage);

        // Full description.
        $data['fulldescription'] = format_text($convocatoria->description ?? '', FORMAT_HTML);

        // Statistics.
        $data['stats'] = $this->prepare_convocatoria_stats($convocatoria);

        // Vacancies.
        $vacancyrenderer = new vacancy_renderer($this->page, $this->target);
        $data['vacancies'] = array_map(function($v) use ($vacancyrenderer) {
            return $vacancyrenderer->prepare_vacancy_card_data($v, true);
        }, $vacancies);
        $data['hasvacancies'] = !empty($vacancies);

        // Management actions.
        if ($canmanage) {
            $data['openurl'] = $this->get_url('convocatorias', ['action' => 'open', 'id' => $convocatoria->id]);
            $data['closeurl'] = $this->get_url('convocatorias', ['action' => 'close', 'id' => $convocatoria->id]);
            $data['archiveurl'] = $this->get_url('convocatorias', ['action' => 'archive', 'id' => $convocatoria->id]);
            $data['deleteurl'] = $this->get_url('convocatorias', ['action' => 'delete', 'id' => $convocatoria->id]);
            $data['createvacancyurl'] = $this->get_url('manage', ['action' => 'create', 'convocatoriaid' => $convocatoria->id]);
            $data['canopen'] = $convocatoria->status === 'draft';
            $data['canclose'] = $convocatoria->status === 'open';
            $data['canarchive'] = $convocatoria->status === 'closed';
            $data['candelete'] = $data['vacancycount'] === 0;
        }

        return $this->render_from_template('local_jobboard/convocatoria_detail', $data);
    }

    /**
     * Prepare convocatoria statistics.
     *
     * @param object $convocatoria The convocatoria object.
     * @return array Statistics data.
     */
    protected function prepare_convocatoria_stats($convocatoria): array {
        global $DB;

        $vacancies = $DB->get_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoria->id]);
        $vacancyids = array_keys($vacancies);

        if (empty($vacancyids)) {
            return [
                'totalvacancies' => 0,
                'publishedvacancies' => 0,
                'totalapplications' => 0,
                'pendingapplications' => 0,
                'totalpositions' => 0,
            ];
        }

        list($insql, $params) = $DB->get_in_or_equal($vacancyids);

        $totalapplications = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} WHERE vacancyid $insql",
            $params
        );

        $pendingapplications = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} WHERE vacancyid $insql AND status IN ('submitted', 'reviewing')",
            $params
        );

        $totalpositions = array_sum(array_column($vacancies, 'positions'));
        $publishedcount = count(array_filter($vacancies, fn($v) => $v->status === 'published'));

        return [
            'totalvacancies' => count($vacancies),
            'publishedvacancies' => $publishedcount,
            'totalapplications' => $totalapplications,
            'pendingapplications' => $pendingapplications,
            'totalpositions' => $totalpositions,
        ];
    }

    /**
     * Render convocatoria management table.
     *
     * @param array $convocatorias Array of convocatoria objects.
     * @param string $filterform Filter form HTML.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_convocatoria_management_table(
        array $convocatorias,
        string $filterform = '',
        string $pagination = ''
    ): string {
        $rows = [];
        foreach ($convocatorias as $convocatoria) {
            $rows[] = $this->prepare_convocatoria_table_row($convocatoria);
        }

        $data = [
            'hasconvocatorias' => !empty($rows),
            'convocatorias' => $rows,
            'count' => count($rows),
            'filterform' => $filterform,
            'pagination' => $pagination,
            'createurl' => $this->get_url('convocatorias', ['action' => 'create']),
        ];

        return $this->render_from_template('local_jobboard/convocatoria_management_table', $data);
    }

    /**
     * Prepare convocatoria table row data.
     *
     * @param object $convocatoria The convocatoria object.
     * @return array Row data.
     */
    protected function prepare_convocatoria_table_row($convocatoria): array {
        $data = $this->prepare_convocatoria_card_data($convocatoria, true);
        $data['stats'] = $this->prepare_convocatoria_stats($convocatoria);
        return $data;
    }

    /**
     * Render convocatoria form page.
     *
     * @param \moodleform $form The convocatoria form.
     * @param bool $isedit Whether editing existing convocatoria.
     * @param object|null $convocatoria Existing convocatoria if editing.
     * @return string HTML output.
     */
    public function render_convocatoria_form($form, bool $isedit = false, $convocatoria = null): string {
        $data = [
            'formhtml' => $form->render(),
            'isedit' => $isedit,
            'title' => $isedit ?
                get_string('editconvocatoria', 'local_jobboard') :
                get_string('createconvocatoria', 'local_jobboard'),
            'backurl' => $this->get_url('convocatorias'),
        ];

        if ($isedit && $convocatoria) {
            $data['convocatoriacode'] = $convocatoria->code;
            $data['convocatorianame'] = $convocatoria->name;
        }

        return $this->render_from_template('local_jobboard/convocatoria_form', $data);
    }

    /**
     * Render convocatoria selector dropdown.
     *
     * @param array $convocatorias Array of convocatoria objects.
     * @param int|null $selected Currently selected convocatoria ID.
     * @param string $name Form field name.
     * @param bool $required Whether field is required.
     * @return string HTML output.
     */
    public function render_convocatoria_selector(
        array $convocatorias,
        ?int $selected = null,
        string $name = 'convocatoriaid',
        bool $required = false
    ): string {
        $options = [];
        foreach ($convocatorias as $convocatoria) {
            $options[] = [
                'value' => $convocatoria->id,
                'label' => $convocatoria->code . ' - ' . $convocatoria->name,
                'selected' => $selected === (int) $convocatoria->id,
            ];
        }

        return $this->render_from_template('local_jobboard/convocatoria_selector', [
            'name' => $name,
            'options' => $options,
            'hasoptions' => !empty($options),
            'required' => $required,
            'placeholder' => get_string('selectconvocatoria', 'local_jobboard'),
        ]);
    }
}
