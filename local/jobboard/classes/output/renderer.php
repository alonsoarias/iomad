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
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output;

defined('MOODLE_INTERNAL') || die();

use plugin_renderer_base;
use moodle_url;

/**
 * Renderer class for the Job Board plugin.
 */
class renderer extends plugin_renderer_base {

    /**
     * Render a vacancy card.
     *
     * @param \local_jobboard\vacancy $vacancy The vacancy object.
     * @param bool $canapply Whether user can apply.
     * @return string HTML output.
     */
    public function render_vacancy_card(\local_jobboard\vacancy $vacancy, bool $canapply = true): string {
        $data = $this->prepare_vacancy_card_data($vacancy, $canapply);
        return $this->render_from_template('local_jobboard/vacancy_card', $data);
    }

    /**
     * Prepare vacancy card template data.
     *
     * @param \local_jobboard\vacancy $vacancy The vacancy object.
     * @param bool $canapply Whether user can apply.
     * @return array Template data.
     */
    protected function prepare_vacancy_card_data(\local_jobboard\vacancy $vacancy, bool $canapply): array {
        $closedate = $vacancy->closedate;
        $daysremaining = max(0, floor(($closedate - time()) / 86400));

        return [
            'id' => $vacancy->id,
            'code' => $vacancy->code,
            'title' => $vacancy->title,
            'description' => shorten_text(strip_tags($vacancy->description), 150),
            'contracttype' => get_string('contract:' . $vacancy->contracttype, 'local_jobboard'),
            'location' => $vacancy->location ?? '',
            'department' => $vacancy->department ?? '',
            'positions' => $vacancy->positions,
            'closedate' => userdate($closedate, get_string('strftimedate')),
            'daysremaining' => $daysremaining,
            'urgent' => $daysremaining <= 7,
            'status' => $vacancy->status,
            'statusclass' => $this->get_status_class($vacancy->status),
            'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancy->id]))->out(false),
            'applyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'id' => $vacancy->id]))->out(false),
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
            'cancreatevacancy' => $cancreatevacancy,
            'createurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'action' => 'create']))->out(false),
            'filterform' => $filterform,
            'pagination' => $pagination,
        ];

        return $this->render_from_template('local_jobboard/vacancy_list', $data);
    }

    /**
     * Render dashboard.
     *
     * @param array $widgets Dashboard widgets data.
     * @param array $recentactivity Recent activity items.
     * @param array $alerts Alert items.
     * @param bool $canmanage Whether user can manage.
     * @return string HTML output.
     */
    public function render_dashboard(
        array $widgets,
        array $recentactivity = [],
        array $alerts = [],
        bool $canmanage = false
    ): string {
        $data = [
            'widgets' => $widgets,
            'recentactivity' => $recentactivity,
            'hasrecentactivity' => !empty($recentactivity),
            'alerts' => $alerts,
            'hasalerts' => !empty($alerts),
            'canmanage' => $canmanage,
            'createvacancyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'action' => 'create']))->out(false),
            'manageapplicationsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'applications']))->out(false),
            'reportsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'reports']))->out(false),
        ];

        return $this->render_from_template('local_jobboard/dashboard', $data);
    }

    /**
     * Render application row.
     *
     * @param \local_jobboard\application $application The application object.
     * @param bool $canreview Whether user can review.
     * @param bool $showapplicant Whether to show applicant name.
     * @return string HTML output.
     */
    public function render_application_row(
        \local_jobboard\application $application,
        bool $canreview = false,
        bool $showapplicant = false
    ): string {
        global $DB;

        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $application->vacancyid]);
        $documentcount = $DB->count_records('local_jobboard_document', ['applicationid' => $application->id]);
        $documentsvalidated = $DB->count_records('local_jobboard_document', [
            'applicationid' => $application->id,
            'validationstatus' => 'approved',
        ]);

        $data = [
            'id' => $application->id,
            'vacancycode' => $vacancy ? $vacancy->code : '',
            'vacancytitle' => $vacancy ? $vacancy->title : '',
            'status' => $application->status,
            'statusclass' => $this->get_application_status_class($application->status),
            'statuslabel' => get_string('appstatus:' . $application->status, 'local_jobboard'),
            'timecreated' => userdate($application->timecreated, get_string('strftimedatetime')),
            'documentcount' => $documentcount,
            'documentsvalidated' => $documentsvalidated,
            'documentpercent' => $documentcount > 0 ? round(($documentsvalidated / $documentcount) * 100) : 0,
            'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id]))->out(false),
            'canreview' => $canreview,
            'reviewurl' => (new moodle_url('/local/jobboard/index.php', [
                'view' => 'review', 'applicationid' => $application->id,
            ]))->out(false),
        ];

        if ($showapplicant) {
            $user = \core_user::get_user($application->userid);
            $data['applicantname'] = fullname($user);
        }

        return $this->render_from_template('local_jobboard/application_row', $data);
    }

    /**
     * Render API token row.
     *
     * @param \local_jobboard\api_token $token The token object.
     * @param bool $canrevoke Whether user can revoke.
     * @param bool $candelete Whether user can delete.
     * @return string HTML output.
     */
    public function render_api_token_row(
        \local_jobboard\api_token $token,
        bool $canrevoke = true,
        bool $candelete = true
    ): string {
        $data = [
            'id' => $token->id,
            'description' => $token->description,
            'maskedtoken' => substr($token->token, 0, 8) . '...' . substr($token->token, -8),
            'permissions' => array_map(function($perm) {
                return get_string('api:permission:' . $perm, 'local_jobboard');
            }, $token->permissions),
            'status' => $token->get_status(),
            'statusclass' => $this->get_token_status_class($token->get_status()),
            'statuslabel' => get_string('api:token:status:' . $token->get_status(), 'local_jobboard'),
            'lastused' => $token->lastused ?
                userdate($token->lastused, get_string('strftimedatetime')) :
                get_string('api:token:never', 'local_jobboard'),
            'timecreated' => userdate($token->timecreated, get_string('strftimedatetime')),
            'enabled' => $token->enabled,
            'canrevoke' => $canrevoke,
            'candelete' => $candelete,
        ];

        return $this->render_from_template('local_jobboard/api_token_row', $data);
    }

    /**
     * Get CSS class for vacancy status.
     *
     * @param string $status Status code.
     * @return string CSS class.
     */
    protected function get_status_class(string $status): string {
        $classes = [
            'draft' => 'secondary',
            'published' => 'success',
            'closed' => 'danger',
            'assigned' => 'primary',
        ];
        return $classes[$status] ?? 'secondary';
    }

    /**
     * Get CSS class for application status.
     *
     * @param string $status Status code.
     * @return string CSS class.
     */
    protected function get_application_status_class(string $status): string {
        $classes = [
            'submitted' => 'info',
            'under_review' => 'warning',
            'docs_validated' => 'success',
            'docs_rejected' => 'danger',
            'interview' => 'purple',
            'selected' => 'success',
            'rejected' => 'secondary',
            'withdrawn' => 'secondary',
        ];
        return $classes[$status] ?? 'secondary';
    }

    /**
     * Get CSS class for token status.
     *
     * @param string $status Status code.
     * @return string CSS class.
     */
    protected function get_token_status_class(string $status): string {
        $classes = [
            'active' => 'success',
            'disabled' => 'secondary',
            'expired' => 'danger',
            'not_yet_valid' => 'warning',
        ];
        return $classes[$status] ?? 'secondary';
    }

    /**
     * Get CSS class for convocatoria status.
     *
     * @param string $status Status code.
     * @return string CSS class.
     */
    public function get_convocatoria_status_class(string $status): string {
        $classes = [
            'draft' => 'secondary',
            'open' => 'success',
            'closed' => 'warning',
            'archived' => 'dark',
        ];
        return $classes[$status] ?? 'secondary';
    }

    /**
     * Get status icon for vacancy.
     *
     * @param string $status Status code.
     * @return string Icon name.
     */
    public function get_status_icon(string $status): string {
        $icons = [
            'draft' => 'edit',
            'published' => 'check-circle',
            'closed' => 'lock',
            'assigned' => 'user-check',
        ];
        return $icons[$status] ?? 'circle';
    }

    /**
     * Prepare page header data for template.
     *
     * @param string $title Page title.
     * @param array $breadcrumbs Breadcrumb items [label => url].
     * @param array $actions Action buttons.
     * @return array Template data.
     */
    public function prepare_page_header(string $title, array $breadcrumbs = [], array $actions = []): array {
        $breadcrumbdata = [];
        foreach ($breadcrumbs as $label => $url) {
            $breadcrumbdata[] = [
                'label' => $label,
                'url' => $url ? (string)$url : null,
                'isactive' => $url === null,
            ];
        }

        $actiondata = [];
        foreach ($actions as $action) {
            $actiondata[] = [
                'url' => (string)$action['url'],
                'label' => $action['label'],
                'icon' => $action['icon'] ?? null,
                'class' => $action['class'] ?? 'btn btn-primary',
            ];
        }

        return [
            'title' => $title,
            'breadcrumbs' => $breadcrumbdata,
            'actions' => $actiondata,
            'hasactions' => !empty($actiondata),
        ];
    }

    /**
     * Prepare stat card data for template.
     *
     * @param string $value The value to display.
     * @param string $label The label.
     * @param string $color Bootstrap color.
     * @param string $icon FontAwesome icon.
     * @param array $trend Optional trend data.
     * @return array Template data.
     */
    public function prepare_stat_card(string $value, string $label, string $color = 'primary',
            string $icon = 'chart-bar', array $trend = []): array {
        return [
            'value' => $value,
            'label' => $label,
            'color' => $color,
            'icon' => $icon,
            'hastrend' => !empty($trend),
            'trendvalue' => $trend['value'] ?? null,
            'trendlabel' => $trend['label'] ?? null,
            'trendup' => ($trend['direction'] ?? '') === 'up',
            'trenddown' => ($trend['direction'] ?? '') === 'down',
        ];
    }

    /**
     * Prepare filter form data for template.
     *
     * @param string $action Form action URL.
     * @param array $fields Filter field definitions.
     * @param array $values Current filter values.
     * @param array $hiddenfields Hidden form fields.
     * @return array Template data.
     */
    public function prepare_filter_form(string $action, array $fields, array $values = [],
            array $hiddenfields = []): array {
        $fielddata = [];
        foreach ($fields as $field) {
            $fielditem = [
                'name' => $field['name'],
                'col' => $field['col'] ?? 'col-md-3',
                'istext' => $field['type'] === 'text',
                'isselect' => $field['type'] === 'select',
                'isdate' => $field['type'] === 'date',
            ];

            if ($field['type'] === 'text') {
                $fielditem['placeholder'] = $field['placeholder'] ?? '';
                $fielditem['value'] = $values[$field['name']] ?? '';
            } elseif ($field['type'] === 'select') {
                $options = [];
                foreach ($field['options'] as $optvalue => $optlabel) {
                    $options[] = [
                        'value' => $optvalue,
                        'label' => $optlabel,
                        'selected' => isset($values[$field['name']]) && $values[$field['name']] == $optvalue,
                    ];
                }
                $fielditem['options'] = $options;
            } elseif ($field['type'] === 'date') {
                $fielditem['value'] = $values[$field['name']] ?? '';
            }

            $fielddata[] = $fielditem;
        }

        $hiddendata = [];
        foreach ($hiddenfields as $name => $value) {
            $hiddendata[] = ['name' => $name, 'value' => $value];
        }

        return [
            'action' => $action,
            'fields' => $fielddata,
            'hiddenfields' => $hiddendata,
        ];
    }

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
     * Render vacancy management page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_manage_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/manage', $data);
    }

    /**
     * Render application form page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_apply_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/apply', $data);
    }

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
     * Render vacancies page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_vacancies_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/vacancies', $data);
    }

    /**
     * Render vacancy detail page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_vacancy_detail(array $data): string {
        return $this->render_from_template('local_jobboard/pages/vacancy_detail', $data);
    }

    /**
     * Render applications list page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_applications_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/applications', $data);
    }

    /**
     * Render application detail page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_application_detail(array $data): string {
        return $this->render_from_template('local_jobboard/pages/application_detail', $data);
    }

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
     * Render reports page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_reports_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/reports', $data);
    }

    /**
     * Render dashboard page using new template.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_dashboard_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/dashboard', $data);
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
            'editurl' => (new moodle_url('/local/jobboard/edit.php', ['id' => $vacancy->id]))->out(false),
            'canapply' => $canapply,
            'canedit' => $canedit,
            'showstatus' => true,
            'showactions' => true,
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
            'addvacancyurl' => (new moodle_url('/local/jobboard/edit.php', ['convocatoriaid' => $convocatoria->id]))->out(false),
        ];
    }

    /**
     * Prepare application data for templates.
     *
     * @param object $application Application record.
     * @param object|null $vacancy Optional vacancy record.
     * @param object|null $user Optional user record.
     * @return array Template data.
     */
    public function prepare_application_data(object $application, ?object $vacancy = null, ?object $user = null): array {
        global $DB;

        if (!$vacancy) {
            $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $application->vacancyid]);
        }
        if (!$user) {
            $user = \core_user::get_user($application->userid);
        }

        // Document counts.
        $documentcount = $DB->count_records('local_jobboard_document', ['applicationid' => $application->id]);
        $docsapproved = $DB->count_records('local_jobboard_document', [
            'applicationid' => $application->id,
            'validationstatus' => 'approved',
        ]);
        $docsrejected = $DB->count_records('local_jobboard_document', [
            'applicationid' => $application->id,
            'validationstatus' => 'rejected',
        ]);
        $docspending = $documentcount - $docsapproved - $docsrejected;
        $docsprogress = $documentcount > 0 ? round(($docsapproved / $documentcount) * 100) : 0;

        return [
            'id' => $application->id,
            'userid' => $application->userid,
            'vacancyid' => $application->vacancyid,
            'vacancycode' => $vacancy ? $vacancy->code : '',
            'vacancytitle' => $vacancy ? format_string($vacancy->title) : '',
            'applicantname' => $user ? fullname($user) : '',
            'applicantemail' => $user ? $user->email : '',
            'status' => $application->status,
            'statuslabel' => get_string('appstatus:' . $application->status, 'local_jobboard'),
            'statuscolor' => $this->get_application_status_class($application->status),
            'timecreated' => userdate($application->timecreated, get_string('strftimedatetime', 'langconfig')),
            'timecreatedformatted' => userdate($application->timecreated, get_string('strftimedate', 'langconfig')),
            'documentcount' => $documentcount,
            'docsapproved' => $docsapproved,
            'docsrejected' => $docsrejected,
            'docspending' => $docspending,
            'docsprogress' => $docsprogress,
            'isexemption' => !empty($application->isexemption),
            'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id]))->out(false),
            'reviewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'applicationid' => $application->id]))->out(false),
        ];
    }
}
