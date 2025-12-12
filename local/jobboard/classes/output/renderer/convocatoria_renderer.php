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
 * Convocatoria renderer trait for Job Board plugin.
 *
 * Contains all convocatoria-related rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

use moodle_url;

/**
 * Trait for convocatoria rendering functionality.
 */
trait convocatoria_renderer {

    /**
     * Render convocatoria edit page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_convocatoria_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/convocatoria', $data);
    }

    /**
     * Render convocatorias list page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_convocatorias_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/convocatorias', $data);
    }

    /**
     * Render view convocatoria page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_view_convocatoria_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/view_convocatoria', $data);
    }

    /**
     * Prepare convocatorias page data for template.
     *
     * @param array $convocatorias Array of convocatoria records.
     * @param int $total Total count.
     * @param array $statscounts Status counts array.
     * @param string $status Current status filter.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @param string $sesskey Session key.
     * @return array Complete template data.
     */
    public function prepare_convocatorias_page_data(
        array $convocatorias,
        int $total,
        array $statscounts,
        string $status,
        int $page,
        int $perpage,
        string $sesskey
    ): array {
        global $DB, $OUTPUT;

        $baseurl = new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']);

        // Prepare convocatoria data.
        $convocatoriadata = [];
        foreach ($convocatorias as $c) {
            // Get counts.
            $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $c->id]);
            $applicationcount = $DB->get_field_sql(
                "SELECT COUNT(a.id)
                   FROM {local_jobboard_application} a
                   JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                  WHERE v.convocatoriaid = :convid",
                ['convid' => $c->id]
            ) ?: 0;
            $selectedcount = $DB->get_field_sql(
                "SELECT COUNT(a.id)
                   FROM {local_jobboard_application} a
                   JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                  WHERE v.convocatoriaid = :convid AND a.status = 'selected'",
                ['convid' => $c->id]
            ) ?: 0;

            // Status actions based on current status.
            $statusactions = [];
            if ($c->status === 'draft') {
                $statusactions[] = [
                    'url' => (new moodle_url($baseurl, ['action' => 'open', 'id' => $c->id, 'sesskey' => $sesskey]))->out(false),
                    'icon' => 'play',
                    'label' => get_string('openconvocatoria', 'local_jobboard'),
                    'confirm' => get_string('confirmopenconvocatoria', 'local_jobboard'),
                ];
                $statusactions[] = [
                    'url' => (new moodle_url($baseurl, ['action' => 'delete', 'id' => $c->id, 'sesskey' => $sesskey]))->out(false),
                    'icon' => 'trash',
                    'label' => get_string('delete'),
                    'confirm' => get_string('confirmdeletevconvocatoria', 'local_jobboard'),
                ];
            } elseif ($c->status === 'open') {
                $statusactions[] = [
                    'url' => (new moodle_url($baseurl, ['action' => 'close', 'id' => $c->id, 'sesskey' => $sesskey]))->out(false),
                    'icon' => 'lock',
                    'label' => get_string('closeconvocatoria', 'local_jobboard'),
                    'confirm' => get_string('confirmcloseconvocatoria', 'local_jobboard'),
                ];
            } elseif ($c->status === 'closed') {
                $statusactions[] = [
                    'url' => (new moodle_url($baseurl, ['action' => 'reopen', 'id' => $c->id, 'sesskey' => $sesskey]))->out(false),
                    'icon' => 'redo',
                    'label' => get_string('reopenconvocatoria', 'local_jobboard'),
                    'confirm' => get_string('confirmreopenconvocatoria', 'local_jobboard'),
                ];
                $statusactions[] = [
                    'url' => (new moodle_url($baseurl, ['action' => 'archive', 'id' => $c->id, 'sesskey' => $sesskey]))->out(false),
                    'icon' => 'archive',
                    'label' => get_string('archiveconvocatoria', 'local_jobboard'),
                    'confirm' => get_string('confirmarchiveconvocatoria', 'local_jobboard'),
                ];
            } elseif ($c->status === 'archived') {
                $statusactions[] = [
                    'url' => (new moodle_url($baseurl, ['action' => 'delete', 'id' => $c->id, 'sesskey' => $sesskey]))->out(false),
                    'icon' => 'trash',
                    'label' => get_string('delete'),
                    'confirm' => get_string('confirmdeletevconvocatoria', 'local_jobboard'),
                ];
            }

            $isending = ($c->enddate - time()) <= 7 * 86400;

            $convocatoriadata[] = [
                'id' => $c->id,
                'code' => format_string($c->code),
                'name' => format_string($c->name),
                'description' => !empty($c->description) ? shorten_text(strip_tags($c->description), 100) : null,
                'status' => $c->status,
                'statuslabel' => get_string('convocatoria_status_' . $c->status, 'local_jobboard'),
                'statuscolor' => $this->get_convocatoria_status_class($c->status),
                'publicationtypelabel' => !empty($c->publicationtype) ? get_string('publicationtype:' . $c->publicationtype, 'local_jobboard') : null,
                'publicationtypecolor' => $c->publicationtype === 'public' ? 'success' : 'info',
                'publicationtypeicon' => $c->publicationtype === 'public' ? 'globe' : 'building',
                'startdateformatted' => userdate($c->startdate, get_string('strftimedate', 'langconfig')),
                'enddateformatted' => userdate($c->enddate, get_string('strftimedate', 'langconfig')),
                'isending' => $isending,
                'isdraft' => $c->status === 'draft',
                'isopen' => $c->status === 'open',
                'vacancycount' => $vacancycount,
                'applicationcount' => $applicationcount,
                'selectedcount' => $selectedcount,
                'hasvacancies' => $vacancycount > 0,
                'editurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'id' => $c->id]))->out(false),
                'addvacancyurl' => (new moodle_url('/local/jobboard/admin/edit.php', ['convocatoriaid' => $c->id]))->out(false),
                'vacanciesurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $c->id]))->out(false),
                'statusactions' => $statusactions,
                'hasstatusactions' => !empty($statusactions),
            ];
        }

        // Filter form.
        $statusoptions = [
            ['value' => '', 'label' => get_string('allstatuses', 'local_jobboard'), 'selected' => empty($status)],
            ['value' => 'draft', 'label' => get_string('convocatoria_status_draft', 'local_jobboard'), 'selected' => $status === 'draft'],
            ['value' => 'open', 'label' => get_string('convocatoria_status_open', 'local_jobboard'), 'selected' => $status === 'open'],
            ['value' => 'closed', 'label' => get_string('convocatoria_status_closed', 'local_jobboard'), 'selected' => $status === 'closed'],
            ['value' => 'archived', 'label' => get_string('convocatoria_status_archived', 'local_jobboard'), 'selected' => $status === 'archived'],
        ];

        $filterform = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => [
                ['name' => 'view', 'value' => 'convocatorias'],
            ],
            'fields' => [
                [
                    'name' => 'status',
                    'label' => get_string('status', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $statusoptions,
                    'col' => 'jb-col-md-4',
                ],
            ],
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
            $paginationurl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'convocatorias',
                'status' => $status,
                'perpage' => $perpage,
            ]);
            $pagination = $OUTPUT->paging_bar($total, $page, $perpage, $paginationurl);
        }

        // Stats cards.
        $stats = [
            [
                'value' => (string)$total,
                'label' => get_string('totalconvocatorias', 'local_jobboard'),
                'icon' => 'calendar-alt',
                'color' => 'primary',
            ],
            [
                'value' => (string)($statscounts['draft'] ?? 0),
                'label' => get_string('convocatoria_status_draft', 'local_jobboard'),
                'icon' => 'edit',
                'color' => 'secondary',
            ],
            [
                'value' => (string)($statscounts['open'] ?? 0),
                'label' => get_string('convocatoria_status_open', 'local_jobboard'),
                'icon' => 'check-circle',
                'color' => 'success',
            ],
            [
                'value' => (string)($statscounts['closed'] ?? 0),
                'label' => get_string('convocatoria_status_closed', 'local_jobboard'),
                'icon' => 'lock',
                'color' => 'warning',
            ],
        ];

        return [
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'createurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'action' => 'add']))->out(false),
            'importurl' => (new moodle_url('/local/jobboard/admin/import_vacancies.php'))->out(false),
            'cancreate' => true,
            'helptext' => get_string('convocatoriahelp', 'local_jobboard'),
            'stats' => $stats,
            'filterform' => $filterform,
            'showinginfo' => $showinginfo,
            'hasconvocatorias' => !empty($convocatoriadata),
            'convocatorias' => $convocatoriadata,
            'pagination' => $pagination,
        ];
    }

    /**
     * Prepare convocatoria data for templates.
     *
     * @param object $convocatoria Convocatoria record.
     * @return array Template data.
     */
    public function prepare_convocatoria_data(object $convocatoria): array {
        global $DB;

        // Count vacancies.
        $vacancycount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoria->id]);

        // Count applications.
        $applicationcount = $DB->get_field_sql(
            "SELECT COUNT(a.id)
               FROM {local_jobboard_application} a
               JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
              WHERE v.convocatoriaid = :convid",
            ['convid' => $convocatoria->id]
        );

        // Count selected.
        $selectedcount = $DB->get_field_sql(
            "SELECT COUNT(a.id)
               FROM {local_jobboard_application} a
               JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
              WHERE v.convocatoriaid = :convid AND a.status = 'selected'",
            ['convid' => $convocatoria->id]
        );

        $statuscolor = $this->get_convocatoria_status_class($convocatoria->status);
        $isending = ($convocatoria->enddate - time()) <= 7 * 86400;
        $isopen = $convocatoria->status === 'open';

        return [
            'id' => $convocatoria->id,
            'code' => $convocatoria->code,
            'name' => format_string($convocatoria->name),
            'description' => format_text($convocatoria->description ?? '', FORMAT_HTML),
            'status' => $convocatoria->status,
            'statuslabel' => get_string('convocatoria_status_' . $convocatoria->status, 'local_jobboard'),
            'statuscolor' => $statuscolor,
            'publicationtype' => $convocatoria->publicationtype ?? 'internal',
            'publicationtypelabel' => get_string('publicationtype:' . ($convocatoria->publicationtype ?? 'internal'), 'local_jobboard'),
            'publicationtypecolor' => $convocatoria->publicationtype === 'public' ? 'success' : 'info',
            'publicationtypeicon' => $convocatoria->publicationtype === 'public' ? 'globe' : 'building',
            'startdate' => userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig')),
            'enddate' => userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig')),
            'startdateformatted' => userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig')),
            'enddateformatted' => userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig')),
            'isending' => $isending,
            'isopen' => $isopen,
            'vacancycount' => $vacancycount,
            'applicationcount' => $applicationcount,
            'selectedcount' => $selectedcount,
            'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'id' => $convocatoria->id]))->out(false),
            'editurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'id' => $convocatoria->id]))->out(false),
            'addvacancyurl' => (new moodle_url('/local/jobboard/admin/edit.php', ['convocatoriaid' => $convocatoria->id]))->out(false),
        ];
    }

    /**
     * Prepare browse convocatorias page data for template.
     *
     * @param array $convocatorias Array of convocatoria records.
     * @param int $total Total count.
     * @param array $statuscounts Status counts array.
     * @param string $status Current status filter.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @return array Complete template data.
     */
    public function prepare_browse_convocatorias_page_data(
        array $convocatorias,
        int $total,
        array $statuscounts,
        string $status,
        int $page,
        int $perpage
    ): array {
        global $OUTPUT;

        // Prepare convocatoria data.
        $convocatoriadata = [];
        foreach ($convocatorias as $conv) {
            $now = time();
            $daysRemaining = (int)ceil(($conv->enddate - $now) / 86400);
            $isClosingSoon = ($daysRemaining <= 7 && $daysRemaining > 0 && $conv->status === 'open');
            $isClosed = ($conv->status === 'closed' || $conv->enddate < $now);
            $isOpen = ($conv->status === 'open' && !$isClosed);

            $convocatoriadata[] = [
                'id' => $conv->id,
                'code' => format_string($conv->code),
                'name' => format_string($conv->name),
                'description' => !empty($conv->description) ? shorten_text(strip_tags($conv->description), 120) : null,
                'status' => $conv->status,
                'startdateformatted' => userdate($conv->startdate, get_string('strftimedate', 'langconfig')),
                'enddateformatted' => userdate($conv->enddate, get_string('strftimedate', 'langconfig')),
                'daysremaining' => max(0, $daysRemaining),
                'isclosingsoon' => $isClosingSoon,
                'isclosed' => $isClosed,
                'isopen' => $isOpen,
                'vacancycount' => $conv->vacancy_count ?? 0,
                'hasvacancies' => ($conv->vacancy_count ?? 0) > 0,
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'view_convocatoria', 'id' => $conv->id]))->out(false),
            ];
        }

        // Status tabs.
        $tabs = [
            [
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias', 'status' => 'open']))->out(false),
                'label' => get_string('convocatoriaactive', 'local_jobboard'),
                'count' => $statuscounts['open'] ?? 0,
                'color' => 'success',
                'active' => $status === 'open',
            ],
            [
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias', 'status' => 'closed']))->out(false),
                'label' => get_string('convocatoriaclosed', 'local_jobboard'),
                'count' => $statuscounts['closed'] ?? 0,
                'color' => 'secondary',
                'active' => $status === 'closed',
            ],
        ];

        // Stats cards.
        $stats = [
            [
                'value' => (string)($statuscounts['open'] ?? 0),
                'label' => get_string('convocatoriaactive', 'local_jobboard'),
                'icon' => 'calendar-check',
                'color' => 'success',
            ],
            [
                'value' => (string)($statuscounts['closed'] ?? 0),
                'label' => get_string('convocatoriaclosed', 'local_jobboard'),
                'icon' => 'calendar-times',
                'color' => 'secondary',
            ],
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
            $baseurl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'browse_convocatorias',
                'status' => $status,
                'perpage' => $perpage,
            ]);
            $pagination = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
        }

        return [
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'vacanciesurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']))->out(false),
            'stats' => $stats,
            'tabs' => $tabs,
            'showinginfo' => $showinginfo,
            'hasconvocatorias' => !empty($convocatoriadata),
            'convocatorias' => $convocatoriadata,
            'pagination' => $pagination,
        ];
    }

    /**
     * Prepare view convocatoria page data.
     *
     * @param object $convocatoria The convocatoria record.
     * @param array $vacancies Array of vacancy objects.
     * @param int $total Total vacancy count.
     * @param array $stats Statistics array.
     * @param bool $canapply Whether user can apply.
     * @param bool $canmanage Whether user can manage.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @return array Template data.
     */
    public function prepare_view_convocatoria_page_data(
        object $convocatoria,
        array $vacancies,
        int $total,
        array $stats,
        bool $canapply,
        bool $canmanage,
        int $page,
        int $perpage
    ): array {
        global $DB, $USER;

        // Calculate timing.
        $now = time();
        $daysremaining = ceil(($convocatoria->enddate - $now) / 86400);
        $isopen = ($convocatoria->status === 'open' && $convocatoria->enddate >= $now);
        $isclosingsoon = ($daysremaining <= 7 && $daysremaining > 0 && $isopen);

        // Status color.
        $statuscolor = 'secondary';
        if ($isopen) {
            $statuscolor = $isclosingsoon ? 'warning' : 'success';
        }

        // Breadcrumbs.
        $breadcrumbs = [
            ['label' => get_string('dashboard', 'local_jobboard'), 'url' => (new moodle_url('/local/jobboard/index.php'))->out(false)],
            ['label' => get_string('convocatorias', 'local_jobboard'), 'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']))->out(false)],
            ['label' => format_string($convocatoria->name), 'url' => null, 'active' => true],
        ];

        // Prepare stats for template.
        $statsdata = [
            [
                'value' => $stats['total_vacancies'],
                'label' => get_string('vacancies', 'local_jobboard'),
                'color' => 'primary',
                'icon' => 'briefcase',
            ],
            [
                'value' => $stats['positions'],
                'label' => get_string('positions', 'local_jobboard'),
                'color' => 'success',
                'icon' => 'users',
            ],
        ];

        if ($canmanage && isset($stats['applications'])) {
            $statsdata[] = [
                'value' => $stats['applications'],
                'label' => get_string('applications', 'local_jobboard'),
                'color' => 'info',
                'icon' => 'file-alt',
            ];
        }

        // Contract types.
        $contracttypes = \local_jobboard_get_contract_types();

        // Prepare vacancies.
        $vacanciesdata = [];
        foreach ($vacancies as $vacancy) {
            $vacclosedate = !empty($vacancy->closedate) ? $vacancy->closedate : $convocatoria->enddate;
            $vacdaysremaining = \local_jobboard_days_between($now, $vacclosedate);
            $isurgent = ($vacdaysremaining <= 7 && $vacdaysremaining >= 0);
            $isclosed = ($vacclosedate < $now || $vacancy->status === 'closed');
            $vacisopen = $vacancy->is_open();

            // Check if user already applied.
            $hasapplied = $DB->record_exists('local_jobboard_application', [
                'vacancyid' => $vacancy->id,
                'userid' => $USER->id,
            ]);

            $vacanciesdata[] = [
                'id' => $vacancy->id,
                'code' => $vacancy->code,
                'title' => format_string($vacancy->title),
                'location' => $vacancy->location ?? null,
                'contracttype' => $vacancy->contracttype,
                'contracttypelabel' => $contracttypes[$vacancy->contracttype] ?? $vacancy->contracttype,
                'positions' => $vacancy->positions,
                'status' => $vacancy->status,
                'statuslabel' => get_string('status_' . $vacancy->status, 'local_jobboard'),
                'statuscolor' => $this->get_status_class($vacancy->status),
                'closedateformatted' => \local_jobboard_format_date($vacclosedate),
                'daysremaining' => $vacdaysremaining,
                'isurgent' => $isurgent,
                'isclosed' => $isclosed,
                'isopen' => $vacisopen,
                'canapply' => $canapply,
                'hasapplied' => $hasapplied,
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancy->id]))->out(false),
                'applyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]))->out(false),
            ];
        }

        // Pagination.
        $pagination = '';
        if ($total > $perpage) {
            $baseurl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'view_convocatoria',
                'id' => $convocatoria->id,
                'perpage' => $perpage,
            ]);
            $pagination = $this->output->paging_bar($total, $page, $perpage, $baseurl);
        }

        return [
            'breadcrumbs' => $breadcrumbs,
            'convocatoria' => [
                'id' => $convocatoria->id,
                'code' => $convocatoria->code,
                'name' => format_string($convocatoria->name),
                'description' => !empty($convocatoria->description) ? format_text($convocatoria->description, FORMAT_HTML) : null,
                'terms' => !empty($convocatoria->terms) ? format_text($convocatoria->terms, FORMAT_HTML) : null,
                'startdateformatted' => userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig')),
                'enddateformatted' => userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig')),
                'status' => $convocatoria->status,
            ],
            'isopen' => $isopen,
            'isclosingsoon' => $isclosingsoon,
            'daysremaining' => $daysremaining,
            'statuscolor' => $statuscolor,
            'statuslabel' => get_string('convocatoria_status_' . $convocatoria->status, 'local_jobboard'),
            'stats' => $statsdata,
            'vacancies' => $vacanciesdata,
            'hasvacancies' => !empty($vacanciesdata),
            'pagination' => $pagination,
            'canapply' => $canapply,
            'canmanage' => $canmanage,
            'editurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatoria', 'id' => $convocatoria->id]))->out(false),
            'backurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']))->out(false),
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
        ];
    }

    /**
     * Prepare data for convocatoria create/edit page template.
     *
     * @param ?object $convocatoria Convocatoria record (null for new).
     * @param string $formhtml Rendered form HTML.
     * @param int $vacancycount Total vacancy count.
     * @param int $applicationcount Total application count.
     * @param array $vacancies Array of vacancy records (max 5).
     * @return array Template data.
     */
    public function prepare_convocatoria_edit_page_data(
        ?object $convocatoria,
        string $formhtml,
        int $vacancycount = 0,
        int $applicationcount = 0,
        array $vacancies = []
    ): array {
        $isediting = !empty($convocatoria);

        // Page title.
        $pagetitle = $isediting
            ? get_string('editconvocatoria', 'local_jobboard') . ': ' . format_string($convocatoria->name)
            : get_string('addconvocatoria', 'local_jobboard');

        // Breadcrumbs.
        $breadcrumbs = [
            ['label' => get_string('dashboard', 'local_jobboard'), 'url' => (new moodle_url('/local/jobboard/index.php'))->out(false)],
            ['label' => get_string('manageconvocatorias', 'local_jobboard'), 'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']))->out(false)],
            ['label' => $isediting ? format_string($convocatoria->name) : get_string('addconvocatoria', 'local_jobboard'), 'url' => null, 'active' => true],
        ];

        // URLs.
        $backurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']))->out(false);
        $dashboardurl = (new moodle_url('/local/jobboard/index.php'))->out(false);

        // Base data.
        $data = [
            'pagetitle' => $pagetitle,
            'breadcrumbs' => $breadcrumbs,
            'isediting' => $isediting,
            'formhtml' => $formhtml,
            'backurl' => $backurl,
            'dashboardurl' => $dashboardurl,
            'hasstats' => false,
            'stats' => [],
            'convocatoria' => null,
            'hasvacancies' => false,
            'vacancies' => [],
            'vacancycount' => 0,
            'applicationcount' => 0,
            'hasapplications' => false,
            'canaddvacancy' => false,
            'addvacancyurl' => null,
            'viewallurl' => null,
            'applicationsurl' => null,
            'showviewall' => false,
        ];

        if (!$isediting) {
            return $data;
        }

        // Status colors.
        $statusColors = [
            'draft' => 'secondary',
            'open' => 'success',
            'closed' => 'warning',
            'archived' => 'dark',
        ];
        $statusColor = $statusColors[$convocatoria->status] ?? 'secondary';

        // Stats cards.
        $data['hasstats'] = true;
        $data['stats'] = [
            ['value' => (string) $vacancycount, 'label' => get_string('vacancies', 'local_jobboard'), 'color' => 'primary', 'icon' => 'briefcase'],
            ['value' => (string) $applicationcount, 'label' => get_string('applications', 'local_jobboard'), 'color' => 'info', 'icon' => 'file-alt'],
            ['value' => get_string('convocatoria_status_' . $convocatoria->status, 'local_jobboard'), 'label' => get_string('status', 'local_jobboard'), 'color' => $statusColor, 'icon' => 'flag'],
        ];

        // Convocatoria info.
        $data['convocatoria'] = [
            'id' => $convocatoria->id,
            'name' => format_string($convocatoria->name),
            'status' => $convocatoria->status,
            'statuslabel' => get_string('convocatoria_status_' . $convocatoria->status, 'local_jobboard'),
            'statuscolor' => $statusColor,
            'startdateformatted' => userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig')),
            'enddateformatted' => userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig')),
        ];

        // URLs.
        $data['addvacancyurl'] = (new moodle_url('/local/jobboard/admin/edit.php', ['convocatoriaid' => $convocatoria->id]))->out(false);
        $data['viewallurl'] = (new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $convocatoria->id]))->out(false);
        $data['applicationsurl'] = (new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'convocatoriaid' => $convocatoria->id]))->out(false);

        // Can add vacancy?
        $data['canaddvacancy'] = in_array($convocatoria->status, ['draft', 'open']);

        // Vacancies and applications.
        $data['vacancycount'] = $vacancycount;
        $data['applicationcount'] = $applicationcount;
        $data['hasvacancies'] = ($vacancycount > 0);
        $data['hasapplications'] = ($applicationcount > 0);
        $data['showviewall'] = ($vacancycount > 5);

        // Vacancies list (max 5).
        $vacsdata = [];
        foreach ($vacancies as $v) {
            $vStatusColor = 'secondary';
            if ($v->status === 'published') {
                $vStatusColor = 'success';
            } else if ($v->status === 'closed') {
                $vStatusColor = 'warning';
            }

            $vacsdata[] = [
                'id' => $v->id,
                'code' => format_string($v->code),
                'title' => format_string($v->title),
                'status' => $v->status,
                'statuslabel' => get_string('status:' . $v->status, 'local_jobboard'),
                'statuscolor' => $vStatusColor,
                'applicationcount' => (int) ($v->app_count ?? 0),
                'editurl' => (new moodle_url('/local/jobboard/admin/edit.php', ['id' => $v->id]))->out(false),
            ];
        }
        $data['vacancies'] = $vacsdata;

        return $data;
    }
}
