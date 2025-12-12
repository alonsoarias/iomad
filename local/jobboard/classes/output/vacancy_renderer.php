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
 * Vacancy renderer for Job Board plugin.
 *
 * Handles rendering of vacancy cards, lists, details, and management pages.
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
 * Vacancy renderer class.
 *
 * Responsible for rendering vacancy-related UI components including
 * cards, lists, detail views, and management interfaces.
 */
class vacancy_renderer extends renderer_base {

    /**
     * Render a vacancy card.
     *
     * @param \local_jobboard\vacancy $vacancy The vacancy object.
     * @param bool $canapply Whether user can apply.
     * @return string HTML output.
     */
    public function render_vacancy_card($vacancy, bool $canapply = true): string {
        $data = $this->prepare_vacancy_card_data($vacancy, $canapply);
        return $this->render_from_template('local_jobboard/vacancy_card', $data);
    }

    /**
     * Prepare vacancy card template data.
     *
     * @param object $vacancy The vacancy object.
     * @param bool $canapply Whether user can apply.
     * @return array Template data.
     */
    public function prepare_vacancy_card_data($vacancy, bool $canapply): array {
        $daysremaining = $this->calculate_days_remaining($vacancy->closedate);

        return [
            'id' => $vacancy->id,
            'code' => $vacancy->code,
            'title' => $vacancy->title,
            'description' => $this->shorten_text($vacancy->description ?? '', 150),
            'contracttype' => get_string('contract:' . $vacancy->contracttype, 'local_jobboard'),
            'location' => $vacancy->location ?? '',
            'department' => $vacancy->department ?? '',
            'positions' => $vacancy->positions,
            'closedate' => $this->format_date($vacancy->closedate),
            'daysremaining' => $daysremaining,
            'urgent' => $this->is_date_urgent($vacancy->closedate),
            'status' => $vacancy->status,
            'statusclass' => $this->get_status_class($vacancy->status),
            'statuslabel' => get_string('vacancystatus:' . $vacancy->status, 'local_jobboard'),
            'viewurl' => $this->get_url('vacancy', ['id' => $vacancy->id]),
            'applyurl' => $this->get_url('apply', ['id' => $vacancy->id]),
            'canapply' => $canapply && $vacancy->status === 'published' && $vacancy->closedate > time(),
        ];
    }

    /**
     * Render vacancy list.
     *
     * @param array $vacancies Array of vacancy objects.
     * @param bool $cancreatevacancy Whether user can create vacancies.
     * @param string $filterform Filter form HTML.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_vacancy_list(
        array $vacancies,
        bool $cancreatevacancy = false,
        string $filterform = '',
        string $pagination = ''
    ): string {
        $vacancydata = [];
        foreach ($vacancies as $vacancy) {
            $vacancydata[] = $this->prepare_vacancy_card_data($vacancy, true);
        }

        $data = [
            'hasvacancies' => !empty($vacancydata),
            'vacancies' => $vacancydata,
            'count' => count($vacancydata),
            'cancreatevacancy' => $cancreatevacancy,
            'createurl' => $this->get_url('manage', ['action' => 'create']),
            'filterform' => $filterform,
            'pagination' => $pagination,
        ];

        return $this->render_from_template('local_jobboard/vacancy_list', $data);
    }

    /**
     * Render vacancy detail page.
     *
     * @param object $vacancy The vacancy object.
     * @param bool $canapply Whether user can apply.
     * @param bool $canmanage Whether user can manage vacancy.
     * @param array $relatedvacancies Related vacancies to show.
     * @return string HTML output.
     */
    public function render_vacancy_detail(
        $vacancy,
        bool $canapply = true,
        bool $canmanage = false,
        array $relatedvacancies = []
    ): string {
        $data = $this->prepare_vacancy_detail_data($vacancy, $canapply, $canmanage);
        $data['relatedvacancies'] = array_map(function($v) {
            return $this->prepare_vacancy_card_data($v, true);
        }, $relatedvacancies);
        $data['hasrelatedvacancies'] = !empty($relatedvacancies);

        return $this->render_from_template('local_jobboard/vacancy_detail', $data);
    }

    /**
     * Prepare vacancy detail template data.
     *
     * @param object $vacancy The vacancy object.
     * @param bool $canapply Whether user can apply.
     * @param bool $canmanage Whether user can manage vacancy.
     * @return array Template data.
     */
    protected function prepare_vacancy_detail_data($vacancy, bool $canapply, bool $canmanage): array {
        $data = $this->prepare_vacancy_card_data($vacancy, $canapply);

        // Add full description and additional fields.
        $data['fulldescription'] = format_text($vacancy->description ?? '', FORMAT_HTML);
        $data['requirements'] = format_text($vacancy->requirements ?? '', FORMAT_HTML);
        $data['responsibilities'] = format_text($vacancy->responsibilities ?? '', FORMAT_HTML);
        $data['benefits'] = format_text($vacancy->benefits ?? '', FORMAT_HTML);
        $data['salary'] = $vacancy->salary ?? '';
        $data['modality'] = $vacancy->modality ?? '';
        $data['schedule'] = $vacancy->schedule ?? '';
        $data['startdate'] = !empty($vacancy->startdate) ? $this->format_date($vacancy->startdate) : '';
        $data['timecreated'] = $this->format_datetime($vacancy->timecreated);
        $data['timemodified'] = !empty($vacancy->timemodified) ? $this->format_datetime($vacancy->timemodified) : '';

        // Management options.
        $data['canmanage'] = $canmanage;
        if ($canmanage) {
            $data['editurl'] = $this->get_url('manage', ['action' => 'edit', 'id' => $vacancy->id]);
            $data['deleteurl'] = $this->get_url('manage', ['action' => 'delete', 'id' => $vacancy->id]);
            $data['publishurl'] = $this->get_url('manage', ['action' => 'publish', 'id' => $vacancy->id]);
            $data['closeurl'] = $this->get_url('manage', ['action' => 'close', 'id' => $vacancy->id]);
            $data['canpublish'] = $vacancy->status === 'draft';
            $data['canclose'] = $vacancy->status === 'published';
        }

        // Share URLs.
        $shareurl = $this->build_url('vacancy', ['id' => $vacancy->id])->out(true);
        $data['shareurl'] = $shareurl;
        $data['emailshareurl'] = 'mailto:?subject=' . rawurlencode($vacancy->title) . '&body=' . rawurlencode($shareurl);

        return $data;
    }

    /**
     * Render vacancy management table.
     *
     * @param array $vacancies Array of vacancy objects.
     * @param string $filterform Filter form HTML.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_vacancy_management_table(
        array $vacancies,
        string $filterform = '',
        string $pagination = ''
    ): string {
        $rows = [];
        foreach ($vacancies as $vacancy) {
            $rows[] = $this->prepare_vacancy_table_row($vacancy);
        }

        $data = [
            'hasvacancies' => !empty($rows),
            'vacancies' => $rows,
            'count' => count($rows),
            'filterform' => $filterform,
            'pagination' => $pagination,
            'createurl' => $this->get_url('manage', ['action' => 'create']),
        ];

        return $this->render_from_template('local_jobboard/vacancy_management_table', $data);
    }

    /**
     * Prepare vacancy table row data.
     *
     * @param object $vacancy The vacancy object.
     * @return array Row data.
     */
    protected function prepare_vacancy_table_row($vacancy): array {
        global $DB;

        $applicationcount = $DB->count_records('local_jobboard_application', ['vacancyid' => $vacancy->id]);

        return [
            'id' => $vacancy->id,
            'code' => $vacancy->code,
            'title' => $vacancy->title,
            'status' => $vacancy->status,
            'statusclass' => $this->get_status_class($vacancy->status),
            'statuslabel' => get_string('vacancystatus:' . $vacancy->status, 'local_jobboard'),
            'positions' => $vacancy->positions,
            'applicationcount' => $applicationcount,
            'closedate' => $this->format_date($vacancy->closedate),
            'urgent' => $this->is_date_urgent($vacancy->closedate),
            'viewurl' => $this->get_url('vacancy', ['id' => $vacancy->id]),
            'editurl' => $this->get_url('manage', ['action' => 'edit', 'id' => $vacancy->id]),
            'deleteurl' => $this->get_url('manage', ['action' => 'delete', 'id' => $vacancy->id]),
            'applicationsurl' => $this->get_url('applications', ['vacancyid' => $vacancy->id]),
            'candelete' => $applicationcount === 0,
        ];
    }

    /**
     * Render vacancy form page.
     *
     * @param \moodleform $form The vacancy form.
     * @param bool $isedit Whether editing existing vacancy.
     * @param object|null $vacancy Existing vacancy if editing.
     * @return string HTML output.
     */
    public function render_vacancy_form($form, bool $isedit = false, $vacancy = null): string {
        $data = [
            'formhtml' => $form->render(),
            'isedit' => $isedit,
            'title' => $isedit ?
                get_string('editvacancy', 'local_jobboard') :
                get_string('createvacancy', 'local_jobboard'),
            'backurl' => $this->get_url('manage'),
        ];

        if ($isedit && $vacancy) {
            $data['vacancycode'] = $vacancy->code;
            $data['vacancytitle'] = $vacancy->title;
        }

        return $this->render_from_template('local_jobboard/vacancy_form', $data);
    }
}
