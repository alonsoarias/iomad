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

use moodle_url;

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
        return $this->render_from_template('local_jobboard/pages/vacancies/list', $data);
    }

    /**
     * Render vacancy management page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_manage_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/vacancies/manage', $data);
    }

    /**
     * Render vacancy detail page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_vacancy_detail_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/vacancies/detail', $data);
    }

    /**
     * Render manage applications page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_manage_applications_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/applications/manage', $data);
    }

    /**
     * Render edit/select convocatoria page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_edit_select_convocatoria_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/vacancies/edit_select_convocatoria', $data);
    }

    /**
     * Render edit vacancy form page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_edit_vacancy_form_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/vacancies/edit_form', $data);
    }

    /**
     * Prepare vacancies page data for template.
     *
     * @param array $vacancies Array of vacancy objects.
     * @param int $total Total count.
     * @param int $urgentcount Number of urgent vacancies.
     * @param array $filters Current filter values.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @param object|null $convocatoria Convocatoria if filtering by one.
     * @param bool $canapply Whether user can apply.
     * @param bool $canviewall Whether user can view all vacancies.
     * @return array Complete template data.
     */
    public function prepare_vacancies_page_data(
        array $vacancies,
        int $total,
        int $urgentcount,
        array $filters,
        int $page,
        int $perpage,
        ?object $convocatoria,
        bool $canapply,
        bool $canviewall
    ): array {
        global $DB, $USER, $OUTPUT;

        // Contract types for labels.
        $contractTypes = local_jobboard_get_contract_types();

        // Prepare vacancy data.
        $vacancydata = [];
        foreach ($vacancies as $v) {
            $daysRemaining = local_jobboard_days_between(time(), $v->closedate);
            $isUrgent = ($daysRemaining <= 7 && $daysRemaining >= 0);
            $isClosed = ($v->closedate < time() || $v->status === 'closed');

            // Check if user has applied.
            $hasApplied = $DB->record_exists('local_jobboard_application', [
                'vacancyid' => $v->id,
                'userid' => $USER->id,
            ]);

            // Get convocatoria code if exists.
            $convocatoriacode = null;
            if (!empty($v->convocatoriaid)) {
                $convocatoriacode = $DB->get_field('local_jobboard_convocatoria', 'code', ['id' => $v->convocatoriaid]);
            }

            $vacancydata[] = [
                'id' => $v->id,
                'code' => format_string($v->code),
                'title' => format_string($v->title),
                'location' => !empty($v->location) ? format_string($v->location) : null,
                'contracttype' => $v->contracttype ?? null,
                'contracttypelabel' => !empty($v->contracttype) && isset($contractTypes[$v->contracttype])
                    ? $contractTypes[$v->contracttype] : null,
                'positions' => $v->positions,
                'status' => $v->status,
                'statuslabel' => get_string('status:' . $v->status, 'local_jobboard'),
                'statuscolor' => $this->get_vacancy_status_class($v->status),
                'convocatoriacode' => $convocatoriacode,
                'daysremaining' => max(0, $daysRemaining),
                'closedateformatted' => local_jobboard_format_date($v->closedate),
                'urgent' => $isUrgent && !$isClosed,
                'isclosed' => $isClosed,
                'hasapplied' => $hasApplied,
                'canapply' => $canapply && !$isClosed && !$hasApplied,
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $v->id]))->out(false),
                'applyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $v->id]))->out(false),
            ];
        }

        // Prepare filter form fields.
        $filterfields = [];

        // Search field.
        $filterfields[] = [
            'name' => 'search',
            'label' => get_string('search', 'local_jobboard'),
            'istext' => true,
            'placeholder' => get_string('searchvacancies', 'local_jobboard') . '...',
            'value' => $filters['search'] ?? '',
            'col' => 'jb-col-md-4',
        ];

        // Contract type field.
        $contractoptions = [['value' => '', 'label' => get_string('allcontracttypes', 'local_jobboard'), 'selected' => empty($filters['contracttype'])]];
        foreach ($contractTypes as $key => $label) {
            $contractoptions[] = [
                'value' => $key,
                'label' => $label,
                'selected' => ($filters['contracttype'] ?? '') === $key,
            ];
        }
        $filterfields[] = [
            'name' => 'contracttype',
            'label' => get_string('contracttype', 'local_jobboard'),
            'isselect' => true,
            'options' => $contractoptions,
            'col' => 'jb-col-md-3',
        ];

        // Status filter (only for managers).
        if ($canviewall) {
            $vacancyStatuses = local_jobboard_get_vacancy_statuses();
            $statusoptions = [['value' => '', 'label' => get_string('allstatuses', 'local_jobboard'), 'selected' => empty($filters['status'])]];
            foreach ($vacancyStatuses as $key => $label) {
                $statusoptions[] = [
                    'value' => $key,
                    'label' => $label,
                    'selected' => ($filters['status'] ?? '') === $key,
                ];
            }
            $filterfields[] = [
                'name' => 'status',
                'label' => get_string('status', 'local_jobboard'),
                'isselect' => true,
                'options' => $statusoptions,
                'col' => 'jb-col-md-2',
            ];
        }

        // Hidden fields.
        $hiddenfields = [['name' => 'view', 'value' => 'vacancies']];
        if (!empty($filters['convocatoriaid'])) {
            $hiddenfields[] = ['name' => 'convocatoriaid', 'value' => $filters['convocatoriaid']];
        }

        $filterform = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => $hiddenfields,
            'fields' => $filterfields,
        ];

        // Showing info.
        $showinginfo = '';
        if ($total > 0) {
            $from = ($page * $perpage) + 1;
            $to = min(($page + 1) * $perpage, $total);
            $showinginfo = get_string('showingxtoy', 'local_jobboard', (object)['from' => $from, 'to' => $to, 'total' => $total]);
        }

        // Pagination.
        $pagination = '';
        if ($total > $perpage) {
            $paginationParams = [
                'view' => 'vacancies',
                'search' => $filters['search'] ?? '',
                'status' => $filters['status'] ?? '',
                'companyid' => $filters['companyid'] ?? 0,
                'departmentid' => $filters['departmentid'] ?? 0,
                'contracttype' => $filters['contracttype'] ?? '',
                'perpage' => $perpage,
            ];
            if (!empty($filters['convocatoriaid'])) {
                $paginationParams['convocatoriaid'] = $filters['convocatoriaid'];
            }
            $baseurl = new moodle_url('/local/jobboard/index.php', $paginationParams);
            $pagination = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
        }

        // Stats cards.
        $stats = [
            [
                'value' => (string)$total,
                'label' => get_string('availablevacancies', 'local_jobboard'),
                'icon' => 'briefcase',
                'color' => 'success',
            ],
            [
                'value' => (string)$urgentcount,
                'label' => get_string('closingsoon', 'local_jobboard'),
                'icon' => 'clock',
                'color' => 'warning',
            ],
        ];

        // Convocatoria data for breadcrumbs.
        $convocatoriadata = null;
        if ($convocatoria) {
            $convocatoriadata = [
                'id' => $convocatoria->id,
                'name' => format_string($convocatoria->name),
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'view_convocatoria', 'id' => $convocatoria->id]))->out(false),
            ];
        }

        return [
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'browseconvocatoriasurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']))->out(false),
            'convocatoria' => $convocatoriadata,
            'welcometitle' => get_string('explorevacancias', 'local_jobboard'),
            'welcomedesc' => get_string('browse_vacancies_desc', 'local_jobboard'),
            'stats' => $stats,
            'filterform' => $filterform,
            'showinginfo' => $showinginfo,
            'hasvacancies' => !empty($vacancydata),
            'vacancies' => $vacancydata,
            'pagination' => $pagination,
        ];
    }

    /**
     * Prepare vacancy data for templates.
     *
     * @param object $vacancy Vacancy record.
     * @param bool $canapply Whether user can apply.
     * @param bool $canedit Whether user can edit.
     * @return array Template data.
     */
    public function prepare_vacancy_data(object $vacancy, bool $canapply = false, bool $canedit = false): array {
        global $DB;

        $daysremaining = max(0, (int)floor(($vacancy->closedate - time()) / 86400));
        $isurgent = $daysremaining <= 7;

        // Get contract type label.
        $contracttypelabel = '';
        if (!empty($vacancy->contracttype)) {
            $contracttypelabel = get_string('contract:' . $vacancy->contracttype, 'local_jobboard');
        }

        // Get convocatoria info.
        $convocatoriacode = '';
        $convocatorianame = '';
        if (!empty($vacancy->convocatoriaid)) {
            $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $vacancy->convocatoriaid]);
            if ($convocatoria) {
                $convocatoriacode = $convocatoria->code;
                $convocatorianame = $convocatoria->name;
            }
        }

        // Publication type.
        $publicationtypelabel = get_string('publicationtype:' . ($vacancy->publicationtype ?? 'internal'), 'local_jobboard');
        $publicationtypecolor = $vacancy->publicationtype === 'public' ? 'success' : 'info';

        return [
            'id' => $vacancy->id,
            'code' => $vacancy->code,
            'title' => format_string($vacancy->title),
            'description' => format_text($vacancy->description ?? '', FORMAT_HTML),
            'descriptionexcerpt' => shorten_text(strip_tags($vacancy->description ?? ''), 100),
            'requirements' => format_text($vacancy->requirements ?? '', FORMAT_HTML),
            'desirable' => format_text($vacancy->desirable ?? '', FORMAT_HTML),
            'location' => $vacancy->location ?? '',
            'department' => $vacancy->department ?? '',
            'contracttype' => $vacancy->contracttype ?? '',
            'contracttypelabel' => $contracttypelabel,
            'positions' => $vacancy->positions ?? 1,
            'duration' => $vacancy->duration ?? '',
            'status' => $vacancy->status,
            'statuslabel' => get_string('status:' . $vacancy->status, 'local_jobboard'),
            'statuscolor' => $this->get_status_class($vacancy->status),
            'statusicon' => $this->get_status_icon($vacancy->status),
            'publicationtype' => $vacancy->publicationtype ?? 'internal',
            'publicationtypelabel' => $publicationtypelabel,
            'publicationtypecolor' => $publicationtypecolor,
            'opendateformatted' => userdate($vacancy->opendate, get_string('strftimedate', 'langconfig')),
            'closedateformatted' => userdate($vacancy->closedate, get_string('strftimedate', 'langconfig')),
            'daysremaining' => $daysremaining,
            'isurgent' => $isurgent,
            'isclosing' => $isurgent,
            'convocatoriaid' => $vacancy->convocatoriaid ?? 0,
            'convocatoriacode' => $convocatoriacode,
            'convocatorianame' => $convocatorianame,
            'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancy->id]))->out(false),
            'applyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]))->out(false),
            'editurl' => (new moodle_url('/local/jobboard/admin/edit.php', ['id' => $vacancy->id]))->out(false),
            'canapply' => $canapply,
            'canedit' => $canedit,
            'showstatus' => true,
            'showactions' => true,
        ];
    }

    /**
     * Prepare manage vacancies page data for template.
     *
     * @param array $vacancies Array of vacancy objects.
     * @param int $total Total count.
     * @param array $statscounts Status counts.
     * @param array $filtervalues Current filter values.
     * @param object|null $convocatoriainfo Convocatoria if filtering by one.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @param string $sesskey Session key.
     * @param array $caps User capabilities.
     * @return array Complete template data.
     */
    public function prepare_manage_page_data(
        array $vacancies,
        int $total,
        array $statscounts,
        array $filtervalues,
        ?object $convocatoriainfo,
        int $page,
        int $perpage,
        string $sesskey,
        array $caps
    ): array {
        global $OUTPUT;

        $baseurl = new moodle_url('/local/jobboard/index.php', ['view' => 'manage']);

        // Prepare vacancy data.
        $vacancydata = [];
        foreach ($vacancies as $v) {
            $opendate = $v->opendate ?? ($v->convocatoria_startdate ?? null);
            $closedate = $v->closedate ?? ($v->convocatoria_enddate ?? null);
            $isClosing = $closedate && (($closedate - time()) <= 7 * 86400) && $closedate > time();

            // Build actions.
            $actions = [];

            // Edit.
            if (($caps['editvacancy'] ?? false) && ($v->status === 'draft' || $v->status === 'published')) {
                $actions[] = [
                    'url' => (new moodle_url('/local/jobboard/admin/edit.php', ['id' => $v->id]))->out(false),
                    'icon' => 't/edit',
                    'title' => get_string('edit', 'local_jobboard'),
                    'class' => 'jb-btn-outline-primary',
                    'isconfirm' => false,
                ];
            }

            // Publish.
            if (($caps['publishvacancy'] ?? false) && $v->status === 'draft') {
                $actions[] = [
                    'url' => (new moodle_url($baseurl, ['action' => 'publish', 'id' => $v->id, 'sesskey' => $sesskey]))->out(false),
                    'icon' => 't/show',
                    'title' => get_string('publish', 'local_jobboard'),
                    'class' => 'jb-btn-outline-success',
                    'confirm' => get_string('confirmpublish', 'local_jobboard'),
                    'isconfirm' => true,
                ];
            }

            // Unpublish.
            if (($caps['editvacancy'] ?? false) && $v->status === 'published') {
                $actions[] = [
                    'url' => (new moodle_url($baseurl, ['action' => 'unpublish', 'id' => $v->id, 'sesskey' => $sesskey]))->out(false),
                    'icon' => 't/hide',
                    'title' => get_string('unpublish', 'local_jobboard'),
                    'class' => 'jb-btn-outline-secondary',
                    'confirm' => get_string('confirmunpublish', 'local_jobboard'),
                    'isconfirm' => true,
                ];
            }

            // Close.
            if (($caps['editvacancy'] ?? false) && $v->status === 'published') {
                $actions[] = [
                    'url' => (new moodle_url($baseurl, ['action' => 'close', 'id' => $v->id, 'sesskey' => $sesskey]))->out(false),
                    'icon' => 't/block',
                    'title' => get_string('close', 'local_jobboard'),
                    'class' => 'jb-btn-outline-warning',
                    'confirm' => get_string('confirmclose', 'local_jobboard'),
                    'isconfirm' => true,
                ];
            }

            // Reopen.
            if (($caps['publishvacancy'] ?? false) && $v->status === 'closed') {
                $actions[] = [
                    'url' => (new moodle_url($baseurl, ['action' => 'reopen', 'id' => $v->id, 'sesskey' => $sesskey]))->out(false),
                    'icon' => 't/restore',
                    'title' => get_string('reopen', 'local_jobboard'),
                    'class' => 'jb-btn-outline-success',
                    'confirm' => get_string('confirmreopen', 'local_jobboard'),
                    'isconfirm' => true,
                ];
            }

            // View applications.
            if ($caps['viewallapplications'] ?? false) {
                $actions[] = [
                    'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $v->id]))->out(false),
                    'icon' => 'i/users',
                    'title' => get_string('reviewapplications', 'local_jobboard'),
                    'class' => 'jb-btn-outline-info',
                    'isconfirm' => false,
                ];
            }

            // Delete.
            if (($caps['deletevacancy'] ?? false) && ($v->status === 'draft' || $v->application_count == 0)) {
                $actions[] = [
                    'url' => (new moodle_url($baseurl, ['action' => 'delete', 'id' => $v->id, 'sesskey' => $sesskey]))->out(false),
                    'icon' => 't/delete',
                    'title' => get_string('delete', 'local_jobboard'),
                    'class' => 'jb-btn-outline-danger',
                    'confirm' => get_string('confirmdeletevacancy', 'local_jobboard'),
                    'isconfirm' => true,
                ];
            }

            $vacancydata[] = [
                'id' => $v->id,
                'code' => format_string($v->code),
                'title' => format_string($v->title),
                'companyname' => !empty($v->companyname) ? format_string($v->companyname) : null,
                'status' => $v->status,
                'statuslabel' => get_string('status:' . $v->status, 'local_jobboard'),
                'statuscolor' => $this->get_status_class($v->status),
                'statusicon' => $this->get_status_icon($v->status),
                'opendateformatted' => $opendate ? local_jobboard_format_date($opendate) : '-',
                'closedateformatted' => $closedate ? local_jobboard_format_date($closedate) : '-',
                'isclosing' => $isClosing,
                'applicationcount' => $v->application_count ?? 0,
                'hasapplications' => ($v->application_count ?? 0) > 0,
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $v->id]))->out(false),
                'actions' => $actions,
            ];
        }

        // Stats cards.
        $stats = [
            [
                'value' => (string)$total,
                'label' => get_string('totalvacancies', 'local_jobboard'),
                'icon' => 'briefcase',
                'color' => 'primary',
            ],
            [
                'value' => (string)($statscounts['draft'] ?? 0),
                'label' => get_string('status:draft', 'local_jobboard'),
                'icon' => 'edit',
                'color' => 'secondary',
            ],
            [
                'value' => (string)($statscounts['published'] ?? 0),
                'label' => get_string('status:published', 'local_jobboard'),
                'icon' => 'check-circle',
                'color' => 'success',
            ],
            [
                'value' => (string)($statscounts['closed'] ?? 0),
                'label' => get_string('status:closed', 'local_jobboard'),
                'icon' => 'lock',
                'color' => 'warning',
            ],
        ];

        // Filter form.
        $statusOptions = [['value' => '', 'label' => get_string('allstatuses', 'local_jobboard'), 'selected' => empty($filtervalues['status'])]];
        $vacancyStatuses = local_jobboard_get_vacancy_statuses();
        foreach ($vacancyStatuses as $key => $label) {
            $statusOptions[] = [
                'value' => $key,
                'label' => $label,
                'selected' => ($filtervalues['status'] ?? '') === $key,
            ];
        }

        $filterform = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => [['name' => 'view', 'value' => 'manage']],
            'fields' => [
                [
                    'name' => 'search',
                    'label' => get_string('search', 'local_jobboard'),
                    'istext' => true,
                    'placeholder' => get_string('search', 'local_jobboard') . '...',
                    'value' => $filtervalues['search'] ?? '',
                    'col' => 'jb-col-md-4',
                ],
                [
                    'name' => 'status',
                    'label' => get_string('status', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $statusOptions,
                    'col' => 'jb-col-md-3',
                ],
            ],
        ];

        // Add convocatoriaid to hidden fields if filtering.
        if (!empty($filtervalues['convocatoriaid'])) {
            $filterform['hiddenfields'][] = ['name' => 'convocatoriaid', 'value' => $filtervalues['convocatoriaid']];
        }

        // Convocatoria info.
        $convinfo = null;
        if ($convocatoriainfo) {
            $convinfo = [
                'id' => $convocatoriainfo->id,
                'name' => format_string($convocatoriainfo->name),
                'startdate' => userdate($convocatoriainfo->startdate, get_string('strftimedate', 'langconfig')),
                'enddate' => userdate($convocatoriainfo->enddate, get_string('strftimedate', 'langconfig')),
                'status' => $convocatoriainfo->status,
                'statuslabel' => get_string('convocatoria_status_' . $convocatoriainfo->status, 'local_jobboard'),
                'statuscolor' => $this->get_convocatoria_status_class($convocatoriainfo->status),
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'id' => $convocatoriainfo->id]))->out(false),
            ];
        }

        // Showing info.
        $showinginfo = '';
        if ($total > 0) {
            $from = ($page * $perpage) + 1;
            $to = min(($page + 1) * $perpage, $total);
            $showinginfo = get_string('showingxtoy', 'local_jobboard', (object)['from' => $from, 'to' => $to, 'total' => $total]);
        }

        // Pagination.
        $pagination = '';
        if ($total > $perpage) {
            $paginationParams = [
                'view' => 'manage',
                'search' => $filtervalues['search'] ?? '',
                'status' => $filtervalues['status'] ?? '',
                'companyid' => $filtervalues['companyid'] ?? 0,
                'departmentid' => $filtervalues['departmentid'] ?? 0,
            ];
            if (!empty($filtervalues['convocatoriaid'])) {
                $paginationParams['convocatoriaid'] = $filtervalues['convocatoriaid'];
            }
            $paginationurl = new moodle_url('/local/jobboard/index.php', $paginationParams);
            $pagination = $OUTPUT->paging_bar($total, $page, $perpage, $paginationurl);
        }

        // New vacancy URL.
        $newvacancyurl = new moodle_url('/local/jobboard/admin/edit.php');
        if (!empty($filtervalues['convocatoriaid'])) {
            $newvacancyurl->param('convocatoriaid', $filtervalues['convocatoriaid']);
        }

        return [
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'convocatoriasurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']))->out(false),
            'newvacancyurl' => $newvacancyurl->out(false),
            'convocatoriainfo' => $convinfo,
            'stats' => $stats,
            'filterform' => $filterform,
            'showinginfo' => $showinginfo,
            'hasvacancies' => !empty($vacancydata),
            'vacancies' => $vacancydata,
            'pagination' => $pagination,
        ];
    }

    /**
     * Prepare vacancy detail page data.
     *
     * @param \local_jobboard\vacancy $vacancy The vacancy object.
     * @param object|null $convocatoria Optional convocatoria record.
     * @param bool $canapply Whether user can apply.
     * @param bool $hasapplied Whether user has already applied.
     * @param bool $canedit Whether user can edit the vacancy.
     * @param bool $canmanage Whether user can manage vacancies.
     * @param array $applicationstats Application stats for managers (optional).
     * @return array Template data.
     */
    public function prepare_vacancy_detail_page_data(
        \local_jobboard\vacancy $vacancy,
        ?object $convocatoria,
        bool $canapply,
        bool $hasapplied,
        bool $canedit,
        bool $canmanage,
        array $applicationstats = []
    ): array {
        global $CFG, $USER;

        // Calculate dates and progress.
        $daysremaining = \local_jobboard_days_between(time(), $vacancy->closedate);
        $isurgent = ($daysremaining <= 7 && $daysremaining >= 0);
        $isclosed = ($vacancy->closedate < time() || $vacancy->status === 'closed');

        // Progress calculation.
        $totaldays = \local_jobboard_days_between($vacancy->opendate, $vacancy->closedate);
        $elapseddays = \local_jobboard_days_between($vacancy->opendate, time());
        $progresspercent = $totaldays > 0 ? min(100, round(($elapseddays / $totaldays) * 100)) : 100;
        $progresscolor = 'success';
        if ($progresspercent > 80) {
            $progresscolor = 'danger';
        } elseif ($progresspercent > 50) {
            $progresscolor = 'warning';
        }

        // Status info for banner.
        $statuscolor = 'info';
        $statusicon = 'info-circle';
        $statusmessage = get_string('vacancyopen', 'local_jobboard');

        if ($hasapplied) {
            $statuscolor = 'info';
            $statusicon = 'check-circle';
            $statusmessage = get_string('error:alreadyapplied', 'local_jobboard');
        } elseif ($isclosed) {
            $statuscolor = 'secondary';
            $statusicon = 'lock';
            $statusmessage = get_string('error:vacancyclosed', 'local_jobboard');
        } elseif ($isurgent) {
            $statuscolor = 'warning';
            $statusicon = 'clock';
            $statusmessage = get_string('closingsoondays', 'local_jobboard', $daysremaining);
        } else {
            $statuscolor = 'success';
            $statusicon = 'door-open';
        }

        // Breadcrumbs.
        $breadcrumbs = [
            ['label' => get_string('dashboard', 'local_jobboard'), 'url' => (new moodle_url('/local/jobboard/index.php'))->out(false)],
        ];
        if ($convocatoria) {
            $breadcrumbs[] = [
                'label' => get_string('convocatorias', 'local_jobboard'),
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']))->out(false),
            ];
            $breadcrumbs[] = [
                'label' => format_string($convocatoria->name),
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'view_convocatoria', 'id' => $convocatoria->id]))->out(false),
            ];
        } else {
            $breadcrumbs[] = [
                'label' => get_string('vacancies', 'local_jobboard'),
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']))->out(false),
            ];
        }
        $breadcrumbs[] = ['label' => format_string($vacancy->title), 'url' => null, 'active' => true];

        // Contract types.
        $contracttypes = \local_jobboard_get_contract_types();
        $contracttypelabel = $contracttypes[$vacancy->contracttype] ?? $vacancy->contracttype;

        // Company name for IOMAD.
        $companyname = '';
        if ($vacancy->companyid) {
            $companyname = $vacancy->get_company_name();
        }

        // Prepare convocatoria data.
        $convdata = null;
        if ($convocatoria) {
            $convdata = [
                'id' => $convocatoria->id,
                'name' => format_string($convocatoria->name),
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'view_convocatoria', 'id' => $convocatoria->id]))->out(false),
            ];
        }

        // Back navigation.
        $backurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']))->out(false);
        $backlabel = get_string('backtovacancies', 'local_jobboard');
        if ($convocatoria) {
            $backurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'view_convocatoria', 'id' => $convocatoria->id]))->out(false);
            $backlabel = get_string('backtoconvocatoria', 'local_jobboard');
        }

        // Created/Modified by info for managers.
        $createdbyname = '';
        $modifiedbyname = '';
        $timecreatedformatted = '';
        if ($canmanage) {
            $createdbyname = fullname(\core_user::get_user($vacancy->createdby));
            $timecreatedformatted = \local_jobboard_format_datetime($vacancy->timecreated);
            if ($vacancy->modifiedby) {
                $modifiedbyname = fullname(\core_user::get_user($vacancy->modifiedby));
            }
        }

        return [
            'breadcrumbs' => $breadcrumbs,
            'vacancy' => [
                'id' => $vacancy->id,
                'code' => $vacancy->code,
                'title' => format_string($vacancy->title),
                'description' => format_text($vacancy->description ?? '', FORMAT_HTML),
                'requirements' => format_text($vacancy->requirements ?? '', FORMAT_HTML),
                'desirable' => format_text($vacancy->desirable ?? '', FORMAT_HTML),
                'location' => $vacancy->location ?? '',
                'department' => $vacancy->department ?? '',
                'companyname' => $companyname,
                'duration' => $vacancy->duration ?? '',
                'contracttype' => $vacancy->contracttype,
                'contracttypelabel' => $contracttypelabel,
                'positions' => $vacancy->positions,
                'status' => $vacancy->status,
                'statuslabel' => get_string('status_' . $vacancy->status, 'local_jobboard'),
                'statuscolor' => $this->get_status_class($vacancy->status),
                'opendateformatted' => \local_jobboard_format_date($vacancy->opendate),
                'closedateformatted' => \local_jobboard_format_date($vacancy->closedate),
            ],
            'convocatoria' => $convdata,
            'statuscolor' => $statuscolor,
            'statusicon' => $statusicon,
            'statusmessage' => $statusmessage,
            'canapply' => $canapply && !$isclosed,
            'hasapplied' => $hasapplied,
            'canedit' => $canedit,
            'canmanage' => $canmanage,
            'isurgent' => $isurgent,
            'isclosed' => $isclosed,
            'daysremaining' => $daysremaining,
            'progresspercent' => $progresspercent,
            'progresscolor' => $progresscolor,
            'isloggedin' => isloggedin() && !isguestuser(),
            'applicationstats' => !empty($applicationstats) ? $applicationstats : null,
            'applyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]))->out(false),
            'editurl' => (new moodle_url('/local/jobboard/admin/edit.php', ['id' => $vacancy->id]))->out(false),
            'myapplicationsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'applications']))->out(false),
            'loginurl' => (new moodle_url('/login/index.php'))->out(false),
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'backurl' => $backurl,
            'backlabel' => $backlabel,
            'createdbyname' => $createdbyname,
            'modifiedbyname' => $modifiedbyname,
            'timecreatedformatted' => $timecreatedformatted,
        ];
    }

    /**
     * Prepare manage applications page data for template.
     *
     * @param object $vacancy Vacancy object.
     * @param array $applications List of applications.
     * @param array $stats Status statistics.
     * @param int $total Total count for pagination.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @param string $status Current status filter.
     * @param string $search Current search term.
     * @param string $sort Current sort column.
     * @param string $order Current sort order.
     * @param \moodle_url $baseurl Base page URL.
     * @return array Template data.
     */
    public function prepare_manage_applications_page_data(
        object $vacancy,
        array $applications,
        array $stats,
        int $total,
        int $page,
        int $perpage,
        string $status,
        string $search,
        string $sort,
        string $order,
        \moodle_url $baseurl
    ): array {
        global $OUTPUT;

        // Status card colors.
        $statuscolors = [
            'submitted' => 'info',
            'under_review' => 'primary',
            'docs_validated' => 'success',
            'docs_rejected' => 'danger',
            'interview' => 'warning',
            'selected' => 'success',
            'rejected' => 'danger',
            'withdrawn' => 'secondary',
        ];

        // Prepare stats cards.
        $statscards = [];
        foreach ($statuscolors as $s => $color) {
            $statscards[] = [
                'status' => $s,
                'color' => $color,
                'count' => $stats[$s] ?? 0,
                'label' => get_string('status_' . $s, 'local_jobboard'),
            ];
        }

        // Prepare statuses for filter dropdown.
        $statusoptions = [];
        foreach (\local_jobboard\application::STATUSES as $s) {
            $statusoptions[] = [
                'value' => $s,
                'label' => get_string('status_' . $s, 'local_jobboard'),
                'selected' => ($status === $s),
            ];
        }

        // Prepare table headers.
        $columns = [
            'applicant' => ['label' => get_string('applicant', 'local_jobboard'), 'sortable' => false],
            'timecreated' => ['label' => get_string('dateapplied', 'local_jobboard'), 'sortable' => true],
            'status' => ['label' => get_string('status', 'local_jobboard'), 'sortable' => true],
            'documents' => ['label' => get_string('documents', 'local_jobboard'), 'sortable' => false],
            'actions' => ['label' => get_string('actions'), 'sortable' => false],
        ];

        $tableheaders = [];
        foreach ($columns as $col => $info) {
            $header = ['label' => $info['label'], 'sortable' => $info['sortable']];
            if ($info['sortable']) {
                $neworder = ($sort === $col && $order === 'ASC') ? 'DESC' : 'ASC';
                $sorturl = new \moodle_url('/local/jobboard/admin/manage_applications.php', [
                    'vacancyid' => $vacancy->id,
                    'status' => $status,
                    'search' => $search,
                    'sort' => $col,
                    'order' => $neworder,
                ]);
                $header['sorturl'] = $sorturl->out(false);
                if ($sort === $col) {
                    $header['sorticon'] = ($order === 'ASC') ? ' ▲' : ' ▼';
                }
            }
            $tableheaders[] = $header;
        }

        // Status badge class mapping.
        $statusbadges = [
            'submitted' => 'jb-badge-info',
            'under_review' => 'jb-badge-primary',
            'docs_validated' => 'jb-badge-success',
            'docs_rejected' => 'jb-badge-danger',
            'interview' => 'jb-badge-warning',
            'selected' => 'jb-badge-success',
            'rejected' => 'jb-badge-danger',
            'withdrawn' => 'jb-badge-secondary',
        ];

        // Prepare applications data.
        $appsdata = [];
        foreach ($applications as $app) {
            // Document status HTML.
            $docstatushtml = '';
            $pendingdocs = $app->pending_validations ?? 0;
            $totaldocs = $app->document_count ?? 0;
            if ($totaldocs > 0) {
                $validated = $totaldocs - $pendingdocs;
                $docstatushtml = "{$validated}/{$totaldocs} " . get_string('validated', 'local_jobboard');
                if ($pendingdocs > 0) {
                    $docstatushtml .= '<br><span class="jb-badge jb-badge-warning">' . $pendingdocs . ' ' .
                        get_string('pending', 'local_jobboard') . '</span>';
                }
            } else {
                $docstatushtml = get_string('nodocuments', 'local_jobboard');
            }

            $appsdata[] = [
                'id' => $app->id,
                'fullname' => format_string($app->userfirstname . ' ' . $app->userlastname),
                'email' => format_string($app->useremail),
                'isexemption' => !empty($app->isexemption),
                'dateapplied' => userdate($app->timecreated, get_string('strftimedatetime', 'langconfig')),
                'status' => $app->status,
                'statuslabel' => get_string('status_' . $app->status, 'local_jobboard'),
                'statusbadgeclass' => $statusbadges[$app->status] ?? 'jb-badge-secondary',
                'docstatushtml' => $docstatushtml,
                'viewurl' => (new \moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $app->id]))->out(false),
            ];
        }

        // Generate pagination HTML using Moodle's paging_bar.
        $paginationhtml = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);

        // Build string data.
        $strdata = [
            'manageapplications' => get_string('manageapplications', 'local_jobboard'),
            'backtomanage' => get_string('backtomanage', 'local_jobboard'),
            'search' => get_string('search'),
            'searchapplicant' => get_string('searchapplicant', 'local_jobboard'),
            'allstatuses' => get_string('allstatuses', 'local_jobboard'),
            'filter' => get_string('filter'),
            'reset' => get_string('reset'),
            'noapplicationsfound' => get_string('noapplicationsfound', 'local_jobboard'),
            'exemption' => get_string('exemption', 'local_jobboard'),
            'view' => get_string('view'),
            'export' => get_string('export', 'local_jobboard'),
            'exportcsv' => get_string('exportcsv', 'local_jobboard'),
            'exportexcel' => get_string('exportexcel', 'local_jobboard'),
        ];

        return [
            'vacancytitle' => format_string($vacancy->title),
            'vacancyid' => $vacancy->id,
            'backurl' => (new \moodle_url('/local/jobboard/index.php', ['view' => 'manage']))->out(false),
            'reseturl' => (new \moodle_url('/local/jobboard/admin/manage_applications.php', ['vacancyid' => $vacancy->id]))->out(false),
            'stats' => $statscards,
            'statuses' => $statusoptions,
            'currentstatus' => $status,
            'currentsearch' => s($search),
            'hasapplications' => !empty($applications),
            'applications' => $appsdata,
            'tableheaders' => $tableheaders,
            'paginationhtml' => $paginationhtml,
            'exportcsvurl' => (new \moodle_url('/local/jobboard/export_applications.php', [
                'vacancyid' => $vacancy->id,
                'status' => $status,
                'format' => 'csv',
            ]))->out(false),
            'exportexcelurl' => (new \moodle_url('/local/jobboard/export_applications.php', [
                'vacancyid' => $vacancy->id,
                'status' => $status,
                'format' => 'excel',
            ]))->out(false),
            'str' => $strdata,
        ];
    }

    /**
     * Prepare edit vacancy convocatoria selection page data.
     *
     * @param array $convocatorias Array of convocatoria ID => name pairs.
     * @return array Template data.
     */
    public function prepare_edit_select_convocatoria_data(array $convocatorias): array {
        global $DB;

        // Status colors mapping.
        $statuscolors = [
            'draft' => 'secondary',
            'open' => 'success',
            'closed' => 'warning',
            'archived' => 'dark',
        ];

        // Prepare convocatoria cards data.
        $convdata = [];
        foreach ($convocatorias as $cid => $cname) {
            $conv = $DB->get_record('local_jobboard_convocatoria', ['id' => $cid]);
            if (!$conv) {
                continue;
            }

            $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $cid]);

            $convdata[] = [
                'id' => $cid,
                'name' => format_string($conv->name),
                'code' => format_string($conv->code),
                'status' => $conv->status,
                'statuscolor' => $statuscolors[$conv->status] ?? 'secondary',
                'statuslabel' => get_string('convocatoria_status_' . $conv->status, 'local_jobboard'),
                'daterange' => userdate($conv->startdate, get_string('strftimedate', 'langconfig')) . ' - ' .
                               userdate($conv->enddate, get_string('strftimedate', 'langconfig')),
                'vacancycount' => $vacancycount,
                'selecturl' => (new \moodle_url('/local/jobboard/admin/edit.php', ['convocatoriaid' => $cid]))->out(false),
            ];
        }

        // Build string data.
        $strdata = [
            'selectconvocatoriafirst' => get_string('selectconvocatoriafirst', 'local_jobboard'),
            'createvacancyinconvocatoriadesc' => get_string('createvacancyinconvocatoriadesc', 'local_jobboard'),
            'noconvocatoriasavailable' => get_string('noconvocatoriasavailable', 'local_jobboard'),
            'gotocreateconvocatoria' => get_string('gotocreateconvocatoria', 'local_jobboard'),
            'selectconvocatoria' => get_string('selectconvocatoria', 'local_jobboard'),
            'vacancies' => get_string('vacancies', 'local_jobboard'),
            'addvacancy' => get_string('addvacancy', 'local_jobboard'),
            'or' => get_string('or', 'moodle'),
            'addconvocatoria' => get_string('addconvocatoria', 'local_jobboard'),
        ];

        return [
            'pagetitle' => get_string('newvacancy', 'local_jobboard'),
            'hasconvocatorias' => !empty($convdata),
            'convocatorias' => $convdata,
            'createconvocatoriaurl' => (new \moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'action' => 'add']))->out(false),
            'str' => $strdata,
        ];
    }

    /**
     * Prepare edit vacancy form page data.
     *
     * @param object|null $convocatoriarecord Convocatoria record or null.
     * @param string $formhtml Rendered form HTML.
     * @return array Template data.
     */
    public function prepare_edit_vacancy_form_data(?object $convocatoriarecord, string $formhtml): array {
        // Status colors mapping.
        $statuscolors = [
            'draft' => 'secondary',
            'open' => 'success',
            'closed' => 'warning',
            'archived' => 'dark',
        ];

        $convdata = null;
        if ($convocatoriarecord) {
            $convdata = [
                'id' => $convocatoriarecord->id,
                'name' => format_string($convocatoriarecord->name),
                'daterange' => userdate($convocatoriarecord->startdate, get_string('strftimedate', 'langconfig')) . ' - ' .
                               userdate($convocatoriarecord->enddate, get_string('strftimedate', 'langconfig')),
                'status' => $convocatoriarecord->status,
                'statuscolor' => $statuscolors[$convocatoriarecord->status] ?? 'secondary',
                'statuslabel' => get_string('convocatoria_status_' . $convocatoriarecord->status, 'local_jobboard'),
            ];
        }

        // Build string data.
        $strdata = [
            'convocatoria' => get_string('convocatoria', 'local_jobboard'),
        ];

        return [
            'hasconvocatoria' => ($convocatoriarecord !== null),
            'convocatoria' => $convdata,
            'formhtml' => $formhtml,
            'str' => $strdata,
        ];
    }
}
