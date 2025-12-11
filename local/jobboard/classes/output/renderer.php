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
     * Prepare complete dashboard data for template.
     *
     * @param int $userid Current user ID.
     * @param array $caps User capabilities array.
     * @param array $stats Dashboard statistics.
     * @return array Complete template data.
     */
    public function prepare_dashboard_data(int $userid, array $caps, array $stats): array {
        // Determine user roles.
        $isadmin = $caps['configure'] ?? false;
        $ismanager = ($caps['createvacancy'] ?? false) || ($caps['manageconvocatorias'] ?? false);
        $isreviewer = ($caps['reviewdocuments'] ?? false) || ($caps['validatedocuments'] ?? false);
        $isapplicant = $caps['apply'] ?? false;
        $canmanagecontent = $isadmin || $ismanager;

        // Determine role label and welcome message.
        $rolelabel = '';
        $welcomemsg = '';
        if ($isadmin) {
            $rolelabel = get_string('role_administrator', 'local_jobboard');
            $welcomemsg = get_string('dashboard_admin_welcome', 'local_jobboard');
        } else if ($ismanager) {
            $rolelabel = get_string('role_manager', 'local_jobboard');
            $welcomemsg = get_string('dashboard_manager_welcome', 'local_jobboard');
        } else if ($isreviewer) {
            $rolelabel = get_string('role_reviewer', 'local_jobboard');
            $welcomemsg = get_string('dashboard_reviewer_welcome', 'local_jobboard');
        } else if ($isapplicant) {
            $rolelabel = get_string('role_applicant', 'local_jobboard');
            $welcomemsg = get_string('dashboard_applicant_welcome', 'local_jobboard');
        }

        // Check if public page is enabled.
        $enablepublic = get_config('local_jobboard', 'enable_public_page');

        $data = [
            'isadmin' => $canmanagecontent,
            'isreviewer' => $isreviewer && !$canmanagecontent,
            'isapplicant' => $isapplicant && !$canmanagecontent && !$isreviewer,
            'isvieweronly' => !$canmanagecontent && !$isreviewer && !$isapplicant && ($caps['view'] ?? false),
            'welcome' => [
                'rolelabel' => $rolelabel,
                'message' => $welcomemsg,
            ],
            'showpubliclink' => !empty($enablepublic),
            'publicurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public']))->out(false),
            'applicationsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'applications']))->out(false),
            'stats' => [],
            'adminsections' => [],
            'workflowsections' => [],
            'reportsections' => [],
            'configsections' => [],
            'reviewersection' => null,
            'applicantstats' => [],
            'applicantsections' => [],
            'alerts' => [],
        ];

        // Admin/Manager statistics.
        if ($canmanagecontent) {
            $data['stats'] = [
                [
                    'value' => (string)($stats['active_convocatorias'] ?? 0),
                    'label' => get_string('activeconvocatorias', 'local_jobboard'),
                    'icon' => 'calendar-alt',
                    'color' => 'primary',
                    'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']))->out(false),
                ],
                [
                    'value' => (string)($stats['published_vacancies'] ?? 0),
                    'label' => get_string('publishedvacancies', 'local_jobboard'),
                    'icon' => 'briefcase',
                    'color' => 'success',
                    'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'manage', 'status' => 'published']))->out(false),
                ],
                [
                    'value' => (string)($stats['total_applications'] ?? 0),
                    'label' => get_string('totalapplications', 'local_jobboard'),
                    'icon' => 'users',
                    'color' => 'info',
                    'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review']))->out(false),
                ],
                [
                    'value' => (string)($stats['pending_reviews'] ?? 0),
                    'label' => get_string('pendingreviews', 'local_jobboard'),
                    'icon' => 'clock',
                    'color' => 'warning',
                    'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'status' => 'submitted']))->out(false),
                ],
            ];

            // Admin sections.
            $data['adminsections'] = $this->prepare_admin_sections($caps, $stats);

            // Workflow sections.
            $data['workflowsections'] = $this->prepare_workflow_sections($caps);

            // Reports sections.
            $data['reportsections'] = $this->prepare_report_sections($caps);

            // Config sections (admin only).
            if ($isadmin) {
                $data['configsections'] = $this->prepare_config_sections($caps);
            }
        }

        // Reviewer section.
        if ($isreviewer) {
            $data['reviewersection'] = [
                'pendingcount' => $stats['my_pending_reviews'] ?? 0,
                'completedcount' => $stats['my_completed_reviews'] ?? 0,
                'haspendingreview' => ($stats['my_pending_reviews'] ?? 0) > 0,
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'myreviews']))->out(false),
            ];
        }

        // Applicant statistics and sections.
        if ($isapplicant && !$canmanagecontent && !$isreviewer) {
            $data['applicantstats'] = [
                [
                    'value' => (string)($stats['active_convocatorias'] ?? 0),
                    'label' => get_string('activeconvocatorias', 'local_jobboard'),
                    'icon' => 'calendar-alt',
                    'color' => 'primary',
                    'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']))->out(false),
                ],
                [
                    'value' => (string)($stats['my_applications'] ?? 0),
                    'label' => get_string('myapplicationcount', 'local_jobboard'),
                    'icon' => 'folder-open',
                    'color' => 'info',
                    'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'applications']))->out(false),
                ],
                [
                    'value' => (string)($stats['available_vacancies'] ?? 0),
                    'label' => get_string('availablevacancies', 'local_jobboard'),
                    'icon' => 'briefcase',
                    'color' => 'success',
                    'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']))->out(false),
                ],
                [
                    'value' => (string)($stats['pending_docs'] ?? 0),
                    'label' => get_string('pendingdocs', 'local_jobboard'),
                    'icon' => 'file-upload',
                    'color' => 'warning',
                ],
            ];

            $data['applicantsections'] = $this->prepare_applicant_sections($stats);
        }

        return $data;
    }

    /**
     * Prepare workflow sections for dashboard.
     *
     * @param array $caps User capabilities.
     * @return array Workflow sections data.
     */
    protected function prepare_workflow_sections(array $caps): array {
        $sections = [];

        // Assign Reviewers.
        if ($caps['manageworkflow'] ?? $caps['assignreviewers'] ?? false) {
            $sections[] = [
                'id' => 'assignreviewers',
                'title' => get_string('assignreviewers', 'local_jobboard'),
                'description' => get_string('assignreviewers_desc', 'local_jobboard'),
                'icon' => 'user-plus',
                'color' => 'primary',
                'url' => (new moodle_url('/local/jobboard/assign_reviewer.php'))->out(false),
                'buttonlabel' => get_string('assignreviewers', 'local_jobboard'),
                'buttonicon' => 'users-cog',
            ];
        }

        // Bulk Validation.
        if ($caps['reviewdocuments'] ?? $caps['validatedocuments'] ?? false) {
            $sections[] = [
                'id' => 'bulkvalidation',
                'title' => get_string('bulkvalidation', 'local_jobboard'),
                'description' => get_string('bulkvalidation_desc', 'local_jobboard'),
                'icon' => 'tasks',
                'color' => 'success',
                'url' => (new moodle_url('/local/jobboard/bulk_validate.php'))->out(false),
                'buttonlabel' => get_string('bulkvalidation', 'local_jobboard'),
                'buttonicon' => 'check-double',
            ];
        }

        // Selection Committees.
        if ($caps['manageworkflow'] ?? false) {
            $sections[] = [
                'id' => 'committees',
                'title' => get_string('committees', 'local_jobboard'),
                'description' => get_string('committees_desc', 'local_jobboard'),
                'icon' => 'users',
                'color' => 'info',
                'url' => (new moodle_url('/local/jobboard/manage_committee.php'))->out(false),
                'buttonlabel' => get_string('managecommittees', 'local_jobboard'),
                'buttonicon' => 'users-cog',
            ];

            // Program Reviewers.
            $sections[] = [
                'id' => 'programreviewers',
                'title' => get_string('program_reviewers', 'local_jobboard'),
                'description' => get_string('program_reviewers_desc', 'local_jobboard'),
                'icon' => 'user-check',
                'color' => 'success',
                'url' => (new moodle_url('/local/jobboard/manage_program_reviewers.php'))->out(false),
                'buttonlabel' => get_string('program_reviewers', 'local_jobboard'),
                'buttonicon' => 'user-check',
            ];
        }

        return $sections;
    }

    /**
     * Prepare report sections for dashboard.
     *
     * @param array $caps User capabilities.
     * @return array Report sections data.
     */
    protected function prepare_report_sections(array $caps): array {
        $sections = [];

        // Reports.
        if ($caps['viewreports'] ?? false) {
            $sections[] = [
                'id' => 'reports',
                'title' => get_string('reports', 'local_jobboard'),
                'description' => get_string('reports_desc', 'local_jobboard'),
                'icon' => 'chart-line',
                'color' => 'info',
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'reports']))->out(false),
                'buttonlabel' => get_string('viewreports', 'local_jobboard'),
                'buttonicon' => 'chart-bar',
            ];
        }

        // Import Vacancies.
        if ($caps['createvacancy'] ?? false) {
            $sections[] = [
                'id' => 'import',
                'title' => get_string('importvacancies', 'local_jobboard'),
                'description' => get_string('importvacancies_desc', 'local_jobboard'),
                'icon' => 'file-import',
                'color' => 'primary',
                'url' => (new moodle_url('/local/jobboard/import_vacancies.php'))->out(false),
                'buttonlabel' => get_string('import', 'local_jobboard'),
                'buttonicon' => 'upload',
            ];
        }

        // Export Data.
        if ($caps['exportdata'] ?? false) {
            $sections[] = [
                'id' => 'export',
                'title' => get_string('exportdata', 'local_jobboard'),
                'description' => get_string('exportdata_desc', 'local_jobboard'),
                'icon' => 'file-export',
                'color' => 'success',
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']))->out(false),
                'buttonlabel' => get_string('export', 'local_jobboard'),
                'buttonicon' => 'download',
            ];
        }

        return $sections;
    }

    /**
     * Prepare configuration sections for dashboard (admin only).
     *
     * @param array $caps User capabilities.
     * @return array Config sections data.
     */
    protected function prepare_config_sections(array $caps): array {
        $sections = [];

        // Plugin Settings.
        $sections[] = [
            'id' => 'settings',
            'title' => get_string('pluginsettings', 'local_jobboard'),
            'description' => get_string('pluginsettings_desc', 'local_jobboard'),
            'icon' => 'sliders-h',
            'color' => 'secondary',
            'url' => (new moodle_url('/admin/settings.php', ['section' => 'local_jobboard']))->out(false),
            'buttonlabel' => get_string('configure', 'local_jobboard'),
            'buttonicon' => 'cog',
        ];

        // Document Types.
        if ($caps['managedoctypes'] ?? false) {
            $sections[] = [
                'id' => 'doctypes',
                'title' => get_string('doctypes', 'local_jobboard'),
                'description' => get_string('doctypes_desc', 'local_jobboard'),
                'icon' => 'file-alt',
                'color' => 'primary',
                'url' => (new moodle_url('/local/jobboard/admin/doctypes.php'))->out(false),
                'buttonlabel' => get_string('manage', 'local_jobboard'),
                'buttonicon' => 'list',
            ];
        }

        // Email Templates.
        if ($caps['manageemailtemplates'] ?? false) {
            $sections[] = [
                'id' => 'emailtemplates',
                'title' => get_string('emailtemplates', 'local_jobboard'),
                'description' => get_string('emailtemplates_desc', 'local_jobboard'),
                'icon' => 'envelope',
                'color' => 'info',
                'url' => (new moodle_url('/local/jobboard/admin/templates.php'))->out(false),
                'buttonlabel' => get_string('manage', 'local_jobboard'),
                'buttonicon' => 'edit',
            ];
        }

        // User Exemptions.
        if ($caps['manageexemptions'] ?? false) {
            $sections[] = [
                'id' => 'exemptions',
                'title' => get_string('exemptions', 'local_jobboard'),
                'description' => get_string('manageexemptions_desc', 'local_jobboard'),
                'icon' => 'user-shield',
                'color' => 'warning',
                'url' => (new moodle_url('/local/jobboard/manage_exemptions.php'))->out(false),
                'buttonlabel' => get_string('manage', 'local_jobboard'),
                'buttonicon' => 'list',
            ];
        }

        // Role Management.
        $sections[] = [
            'id' => 'roles',
            'title' => get_string('manageroles', 'local_jobboard'),
            'description' => get_string('manageroles_desc', 'local_jobboard'),
            'icon' => 'user-tag',
            'color' => 'success',
            'url' => (new moodle_url('/local/jobboard/admin/roles.php'))->out(false),
            'buttonlabel' => get_string('manage', 'local_jobboard'),
            'buttonicon' => 'users-cog',
        ];

        return $sections;
    }

    /**
     * Prepare admin sections for dashboard.
     *
     * @param array $caps User capabilities.
     * @param array $stats Statistics.
     * @return array Admin sections data.
     */
    protected function prepare_admin_sections(array $caps, array $stats): array {
        $sections = [];

        // Convocatorias section.
        if ($caps['manageconvocatorias'] ?? $caps['configure'] ?? false) {
            $sections[] = [
                'id' => 'convocatorias',
                'title' => get_string('convocatorias', 'local_jobboard'),
                'description' => get_string('convocatorias_dashboard_desc', 'local_jobboard'),
                'icon' => 'calendar-alt',
                'color' => 'primary',
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']))->out(false),
                'buttonlabel' => get_string('manage', 'local_jobboard'),
                'features' => [
                    get_string('feature_create_convocatorias', 'local_jobboard'),
                    get_string('feature_manage_vacancies', 'local_jobboard'),
                    get_string('feature_track_applications', 'local_jobboard'),
                ],
            ];
        }

        // Vacancies section.
        if ($caps['createvacancy'] ?? false) {
            $sections[] = [
                'id' => 'vacancies',
                'title' => get_string('vacancies', 'local_jobboard'),
                'description' => get_string('vacancies_dashboard_desc', 'local_jobboard'),
                'icon' => 'briefcase',
                'color' => 'success',
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'manage']))->out(false),
                'buttonlabel' => get_string('manage', 'local_jobboard'),
                'features' => [
                    get_string('feature_create_vacancies', 'local_jobboard'),
                    get_string('feature_publish_vacancies', 'local_jobboard'),
                    get_string('feature_import_export', 'local_jobboard'),
                ],
            ];
        }

        // Applications/Review section.
        if (($caps['viewallapplications'] ?? false) || ($caps['reviewdocuments'] ?? false)) {
            $sections[] = [
                'id' => 'applications',
                'title' => get_string('applications', 'local_jobboard'),
                'description' => get_string('review_dashboard_desc', 'local_jobboard'),
                'icon' => 'clipboard-check',
                'color' => 'warning',
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review']))->out(false),
                'buttonlabel' => get_string('reviewall', 'local_jobboard'),
                'features' => [
                    get_string('feature_review_documents', 'local_jobboard'),
                    get_string('feature_validate_applications', 'local_jobboard'),
                    get_string('feature_assign_reviewers', 'local_jobboard'),
                ],
            ];
        }

        return $sections;
    }

    /**
     * Prepare applicant sections for dashboard.
     *
     * @param array $stats Statistics.
     * @return array Applicant sections data.
     */
    protected function prepare_applicant_sections(array $stats): array {
        $sections = [];

        // Browse convocatorias.
        $sections[] = [
            'id' => 'browse',
            'title' => get_string('browseconvocatorias', 'local_jobboard'),
            'description' => get_string('browseconvocatorias_desc', 'local_jobboard'),
            'icon' => 'search',
            'color' => 'primary',
            'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'browse_convocatorias']))->out(false),
            'buttonlabel' => get_string('explore', 'local_jobboard'),
        ];

        // My applications.
        $pendingDocs = $stats['pending_docs'] ?? 0;
        $sections[] = [
            'id' => 'myapplications',
            'title' => get_string('myapplications', 'local_jobboard'),
            'description' => get_string('myapplications_desc', 'local_jobboard'),
            'icon' => 'folder-open',
            'color' => 'info',
            'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'applications']))->out(false),
            'buttonlabel' => get_string('viewmyapplications', 'local_jobboard'),
            'alert' => $pendingDocs > 0 ? get_string('pending_docs_alert', 'local_jobboard', $pendingDocs) : null,
            'alerttype' => 'warning',
        ];

        return $sections;
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
                'addvacancyurl' => (new moodle_url('/local/jobboard/edit.php', ['convocatoriaid' => $c->id]))->out(false),
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
            'importurl' => (new moodle_url('/local/jobboard/import_vacancies.php'))->out(false),
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
     * Prepare applications page data for template.
     *
     * @param int $userid User ID.
     * @param array $applications Array of application records.
     * @param int $total Total number of applications.
     * @param array $stats Application statistics.
     * @param string $status Current status filter.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @param object|null $exemption User exemption if any.
     * @return array Complete template data.
     */
    public function prepare_applications_page_data(
        int $userid,
        array $applications,
        int $total,
        array $stats,
        string $status,
        int $page,
        int $perpage,
        ?object $exemption = null
    ): array {
        global $OUTPUT;

        // Progress steps for applications.
        $progresssteps = ['submitted', 'under_review', 'docs_validated', 'interview', 'selected'];

        // Prepare application data.
        $applicationdata = [];
        foreach ($applications as $app) {
            // Determine current step index.
            $currentindex = array_search($app->status, $progresssteps);
            if ($currentindex === false) {
                $currentindex = -1;
            }

            // Prepare progress steps.
            $steps = [];
            foreach ($progresssteps as $i => $step) {
                $steps[] = [
                    'step' => $step,
                    'completed' => $i < $currentindex,
                    'current' => $i === $currentindex,
                ];
            }

            // Calculate progress percent.
            $progresspercent = $currentindex >= 0 ? (($currentindex + 1) / count($progresssteps)) * 100 : 0;

            // Document counts.
            $doccount = $app->document_count ?? 0;
            $docsapproved = $app->docs_approved ?? 0;
            $docsrejected = $app->docs_rejected ?? 0;
            $docspending = max(0, $doccount - $docsapproved - $docsrejected);

            // Can withdraw?
            $canwithdraw = in_array($app->status, ['submitted', 'under_review']);

            $applicationdata[] = [
                'id' => $app->id,
                'vacancyid' => $app->vacancyid,
                'vacancycode' => $app->vacancy_code ?? '',
                'vacancytitle' => format_string($app->vacancy_title ?? get_string('unknownvacancy', 'local_jobboard')),
                'vacancyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $app->vacancyid]))->out(false),
                'convocatorianame' => !empty($app->convocatoria_name) ? format_string($app->convocatoria_name) : null,
                'status' => $app->status,
                'statuslabel' => get_string('appstatus:' . $app->status, 'local_jobboard'),
                'statuscolor' => $this->get_application_status_class($app->status),
                'dateapplied' => userdate($app->timecreated, get_string('strftimedate', 'langconfig')),
                'progresssteps' => $steps,
                'progresspercent' => round($progresspercent),
                'documentcount' => $doccount,
                'docsapproved' => $docsapproved,
                'docsrejected' => $docsrejected,
                'docspending' => $docspending,
                'haspendingdocs' => $docspending > 0 && in_array($app->status, ['submitted', 'under_review', 'docs_rejected']),
                'statusnotes' => !empty($app->statusnotes) ? format_string($app->statusnotes) : null,
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $app->id]))->out(false),
                'withdrawurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $app->id, 'action' => 'withdraw']))->out(false),
                'canwithdraw' => $canwithdraw,
            ];
        }

        // Prepare filter form.
        $statusoptions = [['value' => '', 'label' => get_string('allstatuses', 'local_jobboard'), 'selected' => empty($status)]];
        $statuses = ['submitted', 'under_review', 'docs_validated', 'docs_rejected', 'interview', 'selected', 'rejected', 'withdrawn'];
        foreach ($statuses as $s) {
            $statusoptions[] = [
                'value' => $s,
                'label' => get_string('appstatus:' . $s, 'local_jobboard'),
                'selected' => $status === $s,
            ];
        }

        $filterform = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => [
                ['name' => 'view', 'value' => 'applications'],
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
            $baseurl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'applications',
                'status' => $status,
            ]);
            $pagination = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
        }

        // Stats cards.
        $statscards = [
            [
                'value' => (string)($stats['total'] ?? 0),
                'label' => get_string('myapplications', 'local_jobboard'),
                'icon' => 'file-alt',
                'color' => 'primary',
            ],
            [
                'value' => (string)(($stats['submitted'] ?? 0) + ($stats['under_review'] ?? 0)),
                'label' => get_string('inprogress', 'local_jobboard'),
                'icon' => 'spinner',
                'color' => 'info',
            ],
            [
                'value' => (string)($stats['docs_validated'] ?? 0),
                'label' => get_string('validationapproved', 'local_jobboard'),
                'icon' => 'check-circle',
                'color' => 'success',
            ],
            [
                'value' => (string)($stats['selected'] ?? 0),
                'label' => get_string('appstatus:selected', 'local_jobboard'),
                'icon' => 'trophy',
                'color' => 'success',
            ],
        ];

        // Exemption data.
        $exemptiondata = null;
        if ($exemption) {
            $exemptiondata = [
                'type' => $exemption->exemptiontype,
                'typeformatted' => get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard'),
                'documentref' => !empty($exemption->documentref) ? format_string($exemption->documentref) : null,
            ];
        }

        return [
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'browseurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']))->out(false),
            'hasexemption' => !empty($exemption),
            'exemption' => $exemptiondata,
            'stats' => $statscards,
            'pendingdocscount' => $stats['pending_docs'] ?? 0,
            'filterform' => $filterform,
            'showinginfo' => $showinginfo,
            'hasapplications' => !empty($applicationdata),
            'applications' => $applicationdata,
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
     * Get vacancy status class.
     *
     * @param string $status Status code.
     * @return string CSS class.
     */
    public function get_vacancy_status_class(string $status): string {
        return $this->get_status_class($status);
    }

    /**
     * Render browse convocatorias page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_browse_convocatorias_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/browse_convocatorias', $data);
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
     * Prepare my reviews page data for template.
     *
     * @param int $userid Current user ID.
     * @param array $applications Array of application records.
     * @param int $total Total count.
     * @param array $stats Reviewer statistics.
     * @param array $filtervalues Current filter values.
     * @param array $vacancies Available vacancies for filter.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @return array Complete template data.
     */
    public function prepare_myreviews_page_data(
        int $userid,
        array $applications,
        int $total,
        array $stats,
        array $filtervalues,
        array $vacancies,
        int $page,
        int $perpage
    ): array {
        global $OUTPUT;

        // Prepare assignments data.
        $assignments = [];
        foreach ($applications as $app) {
            $totaldocs = (int)($app->total_docs ?? 0);
            $validatedocs = (int)($app->validated_docs ?? 0);
            $rejecteddocs = (int)($app->rejected_docs ?? 0);
            $pendingdocs = (int)($app->pending_docs ?? 0);

            $daysUntilClose = (int)ceil(($app->closedate - time()) / 86400);
            $isUrgent = $daysUntilClose <= 3 && $daysUntilClose > 0;
            $isClosed = $daysUntilClose <= 0;

            // Status color.
            $statusColor = 'secondary';
            if (in_array($app->status, ['docs_validated', 'selected'])) {
                $statusColor = 'success';
            } else if (in_array($app->status, ['docs_rejected', 'rejected'])) {
                $statusColor = 'danger';
            } else if ($app->status === 'under_review') {
                $statusColor = 'warning';
            } else if ($app->status === 'submitted') {
                $statusColor = 'info';
            }

            // Calculate percentages.
            $approvedpercent = $totaldocs > 0 ? round(($validatedocs / $totaldocs) * 100) : 0;
            $rejectedpercent = $totaldocs > 0 ? round(($rejecteddocs / $totaldocs) * 100) : 0;

            $assignments[] = [
                'id' => $app->id,
                'applicantname' => format_string($app->firstname . ' ' . $app->lastname),
                'email' => $app->email,
                'vacancycode' => format_string($app->vacancy_code),
                'vacancytitle' => format_string($app->vacancy_title),
                'status' => $app->status,
                'statuslabel' => get_string('status_' . $app->status, 'local_jobboard'),
                'statuscolor' => $statusColor,
                'totaldocs' => $totaldocs,
                'approvedcount' => $validatedocs,
                'rejectedcount' => $rejecteddocs,
                'pendingcount' => $pendingdocs,
                'approvedpercent' => $approvedpercent,
                'rejectedpercent' => $rejectedpercent,
                'closedate' => userdate($app->closedate, get_string('strftimedate', 'langconfig')),
                'daysuntilclose' => max(0, $daysUntilClose),
                'isurgent' => $isUrgent,
                'isclosed' => $isClosed,
                'reviewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'applicationid' => $app->id]))->out(false),
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $app->id]))->out(false),
            ];
        }

        // Stats cards.
        $statscards = [
            [
                'value' => (string)($stats['pending'] ?? 0),
                'label' => get_string('pendingassignments', 'local_jobboard'),
                'icon' => 'tasks',
                'color' => 'primary',
            ],
            [
                'value' => (string)($stats['validated'] ?? 0),
                'label' => get_string('documentsvalidated', 'local_jobboard'),
                'icon' => 'check-circle',
                'color' => 'success',
            ],
            [
                'value' => (string)($stats['rejected'] ?? 0),
                'label' => get_string('documentsrejected', 'local_jobboard'),
                'icon' => 'times-circle',
                'color' => 'danger',
            ],
            [
                'value' => round($stats['avg_time_hours'] ?? 0, 1) . 'h',
                'label' => get_string('avgvalidationtime', 'local_jobboard'),
                'icon' => 'clock',
                'color' => 'info',
            ],
        ];

        // Filter form.
        $statusOptions = [['value' => '', 'label' => get_string('allstatuses', 'local_jobboard'), 'selected' => empty($filtervalues['status'])]];
        foreach (['submitted', 'under_review', 'docs_validated', 'docs_rejected'] as $s) {
            $statusOptions[] = [
                'value' => $s,
                'label' => get_string('status_' . $s, 'local_jobboard'),
                'selected' => ($filtervalues['status'] ?? '') === $s,
            ];
        }

        $vacancyOptions = [['value' => '0', 'label' => get_string('allvacancies', 'local_jobboard'), 'selected' => empty($filtervalues['vacancy'])]];
        foreach ($vacancies as $v) {
            $vacancyOptions[] = [
                'value' => (string)$v->id,
                'label' => format_string($v->code . ' - ' . $v->title),
                'selected' => ($filtervalues['vacancy'] ?? 0) == $v->id,
            ];
        }

        $priorityOptions = [
            ['value' => '', 'label' => get_string('datesubmitted', 'local_jobboard'), 'selected' => empty($filtervalues['priority'])],
            ['value' => 'closing', 'label' => get_string('closingdate', 'local_jobboard'), 'selected' => ($filtervalues['priority'] ?? '') === 'closing'],
            ['value' => 'pending', 'label' => get_string('pendingdocuments', 'local_jobboard'), 'selected' => ($filtervalues['priority'] ?? '') === 'pending'],
        ];

        $filterform = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => [['name' => 'view', 'value' => 'myreviews']],
            'fields' => [
                [
                    'name' => 'status',
                    'label' => get_string('status', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $statusOptions,
                    'col' => 'jb-col-md-3',
                ],
                [
                    'name' => 'vacancy',
                    'label' => get_string('vacancy', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $vacancyOptions,
                    'col' => 'jb-col-md-4',
                ],
                [
                    'name' => 'priority',
                    'label' => get_string('sortby', 'local_jobboard'),
                    'isselect' => true,
                    'options' => $priorityOptions,
                    'col' => 'jb-col-md-3',
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
            $baseurl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'myreviews',
                'status' => $filtervalues['status'] ?? '',
                'vacancy' => $filtervalues['vacancy'] ?? 0,
                'priority' => $filtervalues['priority'] ?? '',
            ]);
            $pagination = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
        }

        return [
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'reviewqueueurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review']))->out(false),
            'stats' => $statscards,
            'filterform' => $filterform,
            'showinginfo' => $showinginfo,
            'hasassignments' => !empty($assignments),
            'assignments' => $assignments,
            'pagination' => $pagination,
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
                    'url' => (new moodle_url('/local/jobboard/edit.php', ['id' => $v->id]))->out(false),
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
        $newvacancyurl = new moodle_url('/local/jobboard/edit.php');
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

    /**
     * Render vacancy detail page.
     *
     * @param array $data Template data prepared by prepare_vacancy_detail_page_data().
     * @return string HTML output.
     */
    public function render_vacancy_detail_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/vacancy_detail', $data);
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
            'editurl' => (new moodle_url('/local/jobboard/edit.php', ['id' => $vacancy->id]))->out(false),
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
     * Render application detail page.
     *
     * @param array $data Template data prepared by prepare_application_detail_page_data().
     * @return string HTML output.
     */
    public function render_application_detail_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/application_detail', $data);
    }

    /**
     * Prepare application detail page data.
     *
     * @param \local_jobboard\application $application The application object.
     * @param \local_jobboard\vacancy $vacancy The vacancy object.
     * @param object $applicant The applicant user record.
     * @param array $documents Array of document objects.
     * @param array $history Status history array.
     * @param bool $isowner Whether current user owns the application.
     * @param bool $canreview Whether current user can review.
     * @param bool $canmanage Whether current user can manage workflow.
     * @param array $workflowactions Available workflow transitions.
     * @param object|null $exemption Active exemption record.
     * @return array Template data.
     */
    public function prepare_application_detail_page_data(
        \local_jobboard\application $application,
        \local_jobboard\vacancy $vacancy,
        object $applicant,
        array $documents,
        array $history,
        bool $isowner,
        bool $canreview,
        bool $canmanage,
        array $workflowactions = [],
        ?object $exemption = null
    ): array {
        // Status styling.
        $statuscolor = 'info';
        $statusicon = 'info-circle';

        switch ($application->status) {
            case 'docs_validated':
            case 'selected':
                $statuscolor = 'success';
                $statusicon = 'check-circle';
                break;
            case 'docs_rejected':
            case 'rejected':
                $statuscolor = 'danger';
                $statusicon = 'times-circle';
                break;
            case 'withdrawn':
                $statuscolor = 'secondary';
                $statusicon = 'ban';
                break;
            case 'interview':
                $statuscolor = 'warning';
                $statusicon = 'calendar-check';
                break;
            case 'under_review':
                $statuscolor = 'warning';
                $statusicon = 'clock';
                break;
            case 'submitted':
                $statuscolor = 'info';
                $statusicon = 'paper-plane';
                break;
        }

        // Breadcrumbs.
        $breadcrumbs = [
            ['label' => get_string('dashboard', 'local_jobboard'), 'url' => (new moodle_url('/local/jobboard/index.php'))->out(false)],
        ];
        if ($isowner) {
            $breadcrumbs[] = [
                'label' => get_string('myapplications', 'local_jobboard'),
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'applications']))->out(false),
            ];
        } else {
            $breadcrumbs[] = [
                'label' => get_string('reviewapplications', 'local_jobboard'),
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $application->vacancyid]))->out(false),
            ];
        }
        $breadcrumbs[] = [
            'label' => get_string('application', 'local_jobboard') . ' #' . $application->id,
            'url' => null,
            'active' => true,
        ];

        // Prepare documents data.
        $documentsdata = [];
        foreach ($documents as $doc) {
            $docstatus = $doc->status ?? 'pending';
            $docstatuscolor = 'warning';
            $docstatusicon = 'clock';
            $filecolor = 'secondary';

            switch ($docstatus) {
                case 'approved':
                    $docstatuscolor = 'success';
                    $docstatusicon = 'check-circle';
                    $filecolor = 'success';
                    break;
                case 'rejected':
                    $docstatuscolor = 'danger';
                    $docstatusicon = 'times-circle';
                    $filecolor = 'danger';
                    break;
            }

            $documentsdata[] = [
                'id' => $doc->id,
                'typename' => $doc->get_doctype_name(),
                'filename' => format_string($doc->filename),
                'status' => $docstatus,
                'statuslabel' => get_string('docstatus:' . $docstatus, 'local_jobboard'),
                'statuscolor' => $docstatuscolor,
                'statusicon' => $docstatusicon,
                'filecolor' => $filecolor,
                'downloadurl' => $doc->get_download_url() ? $doc->get_download_url()->out(false) : null,
                'rejectreason' => $doc->rejectreason ?? null,
                'canreupload' => $isowner && $docstatus === 'rejected' && in_array($application->status, ['docs_rejected', 'submitted']),
                'reuploadurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id, 'reupload' => $doc->id]))->out(false),
            ];
        }

        // Prepare history data.
        $historydata = [];
        foreach ($history as $entry) {
            $hstatuscolor = $this->get_application_status_class($entry->newstatus);
            $changedbyname = '';
            if (!empty($entry->changedby)) {
                $changedbyuser = \core_user::get_user($entry->changedby);
                $changedbyname = $changedbyuser ? fullname($changedbyuser) : '';
            }

            $historydata[] = [
                'status' => $entry->newstatus,
                'statuslabel' => get_string('status_' . $entry->newstatus, 'local_jobboard'),
                'statuscolor' => $hstatuscolor,
                'timeformatted' => userdate($entry->timecreated, get_string('strftimedatetime', 'langconfig')),
                'notes' => $entry->notes ?? null,
                'changedbyname' => $changedbyname,
            ];
        }

        // Prepare workflow actions.
        $workflowdata = [];
        foreach ($workflowactions as $status) {
            $workflowdata[] = [
                'value' => $status,
                'label' => get_string('status_' . $status, 'local_jobboard'),
            ];
        }

        // Can withdraw?
        $canwithdraw = $isowner && in_array($application->status, ['submitted', 'under_review']);

        // Back navigation.
        $backurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'applications']))->out(false);
        $backlabel = get_string('backtoapplications', 'local_jobboard');
        if (!$isowner) {
            $backurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $application->vacancyid]))->out(false);
            $backlabel = get_string('backtoreviewlist', 'local_jobboard');
        }

        // Applicant info (for reviewers).
        $applicantdata = null;
        if ($canreview || $canmanage) {
            $applicantdata = [
                'fullname' => fullname($applicant),
                'email' => $applicant->email,
                'idnumber' => $applicant->idnumber ?? null,
                'phone' => $applicant->phone1 ?? null,
            ];
        }

        // Exemption info.
        $hasexemption = false;
        $exemptiontype = '';
        $exemptionref = '';
        if ($exemption) {
            $hasexemption = true;
            $exemptiontype = get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard');
            $exemptionref = $exemption->documentref ?? '';
        }

        return [
            'breadcrumbs' => $breadcrumbs,
            'application' => [
                'id' => $application->id,
                'status' => $application->status,
                'statuslabel' => get_string('status_' . $application->status, 'local_jobboard'),
                'statuscolor' => $statuscolor,
                'statusicon' => $statusicon,
                'statusnotes' => $application->statusnotes ?? null,
                'dateapplied' => userdate($application->timecreated, get_string('strftimedate', 'langconfig')),
                'digitalsignature' => $application->digitalsignature ?? null,
                'consenttimestamp' => !empty($application->consenttimestamp) ?
                    userdate($application->consenttimestamp, get_string('strftimedate', 'langconfig')) : null,
                'coverletter' => !empty($application->coverletter) ? nl2br(format_string($application->coverletter)) : null,
            ],
            'vacancy' => [
                'id' => $vacancy->id,
                'code' => $vacancy->code,
                'title' => format_string($vacancy->title),
                'location' => $vacancy->location ?? null,
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancy->id]))->out(false),
            ],
            'applicant' => $applicantdata,
            'hasexemption' => $hasexemption,
            'exemptiontype' => $exemptiontype,
            'exemptionref' => $exemptionref,
            'documents' => $documentsdata,
            'hasdocuments' => !empty($documentsdata),
            'documentcount' => count($documentsdata),
            'history' => $historydata,
            'hashistory' => !empty($historydata),
            'isowner' => $isowner,
            'canreview' => $canreview && !$isowner,
            'canmanage' => $canmanage,
            'canwithdraw' => $canwithdraw,
            'workflowactions' => $workflowdata,
            'hasworkflowactions' => !empty($workflowdata),
            'sesskey' => sesskey(),
            'workflowurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'withdrawurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id, 'action' => 'withdraw']))->out(false),
            'reviewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'applicationid' => $application->id]))->out(false),
            'backurl' => $backurl,
            'backlabel' => $backlabel,
        ];
    }

    /**
     * Render view convocatoria page.
     *
     * @param array $data Template data prepared by prepare_view_convocatoria_page_data().
     * @return string HTML output.
     */
    public function render_view_convocatoria_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/view_convocatoria', $data);
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
     * Render public convocatoria page.
     *
     * @param array $data Template data.
     * @return string HTML output.
     */
    public function render_public_convocatoria_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/public_convocatoria', $data);
    }

    /**
     * Prepare public convocatoria page data.
     *
     * @param object $convocatoria The convocatoria record.
     * @param int $totalvacancies Total vacancy count.
     * @param int $totalpositions Total positions count.
     * @param bool $isloggedin Whether user is logged in.
     * @return array Template data.
     */
    public function prepare_public_convocatoria_page_data(
        object $convocatoria,
        int $totalvacancies,
        int $totalpositions,
        bool $isloggedin
    ): array {
        $now = time();
        $daysremaining = max(0, (int) ceil(($convocatoria->enddate - $now) / 86400));
        $isopen = ($convocatoria->status === 'open' && $convocatoria->enddate >= $now);
        $isclosingsoon = ($daysremaining <= 7 && $daysremaining > 0 && $isopen);
        $isclosed = !$isopen;

        // Status styling.
        $statuscolor = 'secondary';
        $statusicon = 'lock';
        $statusmessage = get_string('convocatoriaclosed', 'local_jobboard');

        if (!$isclosed) {
            if ($isclosingsoon) {
                $statuscolor = 'warning';
                $statusicon = 'clock';
                $statusmessage = get_string('closesindays', 'local_jobboard', $daysremaining);
            } else {
                $statuscolor = 'success';
                $statusicon = 'door-open';
                $statusmessage = get_string('convocatoria_status_open', 'local_jobboard');
            }
        }

        // Progress calculation.
        $totaldays = max(1, (int) floor(($convocatoria->enddate - $convocatoria->startdate) / 86400));
        $elapseddays = max(0, (int) floor(($now - $convocatoria->startdate) / 86400));
        $progresspercent = min(100, ($elapseddays / $totaldays) * 100);
        $progresscolor = $progresspercent > 80 ? 'danger' : ($progresspercent > 50 ? 'warning' : 'success');

        // Breadcrumbs.
        $breadcrumbs = [
            ['label' => get_string('publicpagetitle', 'local_jobboard'), 'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public']))->out(false)],
            ['label' => format_string($convocatoria->name), 'url' => null, 'active' => true],
        ];

        // Share links.
        $shareurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $convocatoria->id]))->out(false);
        $sharetitle = rawurlencode($convocatoria->name);
        $sharetext = rawurlencode(get_string('convocatoria', 'local_jobboard') . ': ' . $convocatoria->name);

        $sharelinks = [
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareurl),
            'twitter' => 'https://twitter.com/intent/tweet?text=' . $sharetext . '&url=' . rawurlencode($shareurl),
            'linkedin' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode($shareurl) . '&title=' . $sharetitle,
            'whatsapp' => 'https://wa.me/?text=' . $sharetext . '%20' . rawurlencode($shareurl),
        ];

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
                'statuslabel' => get_string('convocatoria_status_' . $convocatoria->status, 'local_jobboard'),
                'statuscolor' => $this->get_convocatoria_status_class($convocatoria->status),
            ],
            'totalvacancies' => $totalvacancies,
            'totalpositions' => $totalpositions,
            'isopen' => $isopen,
            'isclosed' => $isclosed,
            'isclosingsoon' => $isclosingsoon,
            'daysremaining' => $daysremaining,
            'statuscolor' => $statuscolor,
            'statusicon' => $statusicon,
            'statusmessage' => $statusmessage,
            'progresspercent' => $progresspercent,
            'progresscolor' => $progresscolor,
            'isloggedin' => $isloggedin,
            'sharelinks' => $sharelinks,
            'vacanciesurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoria->id]))->out(false),
            'loginurl' => (new moodle_url('/login/index.php'))->out(false),
            'signupurl' => (new moodle_url('/local/jobboard/signup.php'))->out(false),
            'backurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public']))->out(false),
        ];
    }

    /**
     * Render public vacancy page.
     *
     * @param array $data Template data.
     * @return string HTML output.
     */
    public function render_public_vacancy_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/public_vacancy', $data);
    }

    /**
     * Prepare public vacancy page data.
     *
     * @param object $vacancy The vacancy record.
     * @param object|null $convocatoria The convocatoria record.
     * @param bool $isloggedin Whether user is logged in.
     * @param bool $canapply Whether user can apply.
     * @param bool $hasapplied Whether user has already applied.
     * @param object|null $userapplication User's application if exists.
     * @return array Template data.
     */
    public function prepare_public_vacancy_page_data(
        object $vacancy,
        ?object $convocatoria,
        bool $isloggedin,
        bool $canapply,
        bool $hasapplied,
        ?object $userapplication
    ): array {
        // Calculate timing.
        $closedate = $convocatoria ? $convocatoria->enddate : ($vacancy->closedate ?? time());
        $daysremaining = max(0, (int) floor(($closedate - time()) / 86400));
        $isurgent = $daysremaining <= 7;
        $isclosed = ($closedate < time());

        // Status styling.
        $statuscolor = 'success';
        $statusicon = 'door-open';
        $statusmessage = get_string('vacancyopen', 'local_jobboard');

        if ($hasapplied) {
            $statuscolor = 'info';
            $statusicon = 'check-circle';
            $statusmessage = get_string('alreadyapplied', 'local_jobboard');
        } elseif ($isclosed) {
            $statuscolor = 'secondary';
            $statusicon = 'lock';
            $statusmessage = get_string('vacancyclosed', 'local_jobboard');
        } elseif ($isurgent) {
            $statuscolor = 'warning';
            $statusicon = 'clock';
            $statusmessage = get_string('closesindays', 'local_jobboard', $daysremaining);
        }

        // Progress calculation.
        $progresspercent = 0;
        $progresscolor = 'success';
        if ($convocatoria && !$isclosed) {
            $totaldays = max(1, (int) floor(($convocatoria->enddate - $convocatoria->startdate) / 86400));
            $elapseddays = max(0, (int) floor((time() - $convocatoria->startdate) / 86400));
            $progresspercent = min(100, ($elapseddays / $totaldays) * 100);
            $progresscolor = $progresspercent > 80 ? 'danger' : ($progresspercent > 50 ? 'warning' : 'success');
        }

        // Breadcrumbs.
        $breadcrumbs = [
            ['label' => get_string('publicpagetitle', 'local_jobboard'), 'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public']))->out(false)],
        ];
        if ($convocatoria) {
            $breadcrumbs[] = [
                'label' => format_string($convocatoria->name),
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoria->id]))->out(false),
            ];
        }
        $breadcrumbs[] = ['label' => format_string($vacancy->title), 'url' => null, 'active' => true];

        // Contract types.
        $contracttypes = \local_jobboard_get_contract_types();
        $contracttypelabel = !empty($vacancy->contracttype) ? ($contracttypes[$vacancy->contracttype] ?? $vacancy->contracttype) : null;

        // Company name.
        $companyname = null;
        if (!empty($vacancy->companyid)) {
            $companyname = \local_jobboard_get_company_name($vacancy->companyid);
        }

        // Convocatoria data.
        $convdata = null;
        if ($convocatoria) {
            $convdata = [
                'id' => $convocatoria->id,
                'code' => $convocatoria->code,
                'name' => format_string($convocatoria->name),
                'startdateformatted' => userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig')),
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $convocatoria->id]))->out(false),
            ];
        }

        // Share links.
        $shareurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public_vacancy', 'id' => $vacancy->id]))->out(false);
        $sharetitle = rawurlencode($vacancy->title);
        $sharetext = rawurlencode(get_string('sharethisvacancy', 'local_jobboard') . ': ' . $vacancy->title);

        $sharelinks = [
            'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareurl),
            'twitter' => 'https://twitter.com/intent/tweet?text=' . $sharetext . '&url=' . rawurlencode($shareurl),
            'linkedin' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode($shareurl) . '&title=' . $sharetitle,
            'whatsapp' => 'https://wa.me/?text=' . $sharetext . '%20' . rawurlencode($shareurl),
        ];

        // Back navigation.
        $backurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public']))->out(false);
        $backlabel = get_string('backtoconvocatorias', 'local_jobboard');
        if ($convocatoria) {
            $backurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoria->id]))->out(false);
            $backlabel = get_string('backtoconvocatoria', 'local_jobboard');
        }

        // Login URL with return.
        $wantsurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]))->out(false);
        $loginurl = (new moodle_url('/login/index.php', ['wantsurl' => $wantsurl]))->out(false);

        return [
            'breadcrumbs' => $breadcrumbs,
            'vacancy' => [
                'id' => $vacancy->id,
                'code' => $vacancy->code,
                'title' => format_string($vacancy->title),
                'description' => !empty($vacancy->description) ? format_text($vacancy->description, FORMAT_HTML) : null,
                'requirements' => !empty($vacancy->requirements) ? format_text($vacancy->requirements, FORMAT_HTML) : null,
                'desirable' => !empty($vacancy->desirable) ? format_text($vacancy->desirable, FORMAT_HTML) : null,
                'location' => $vacancy->location ?? null,
                'department' => $vacancy->department ?? null,
                'duration' => $vacancy->duration ?? null,
                'contracttype' => $vacancy->contracttype ?? null,
                'contracttypelabel' => $contracttypelabel,
                'companyname' => $companyname,
                'positions' => $vacancy->positions,
                'status' => $vacancy->status,
                'statuslabel' => get_string('status:' . $vacancy->status, 'local_jobboard'),
                'statuscolor' => $this->get_status_class($vacancy->status),
            ],
            'convocatoria' => $convdata,
            'isloggedin' => $isloggedin,
            'canapply' => $canapply,
            'hasapplied' => $hasapplied,
            'isurgent' => $isurgent,
            'isclosed' => $isclosed,
            'daysremaining' => $daysremaining,
            'closedateformatted' => userdate($closedate, get_string('strftimedate', 'langconfig')),
            'progresspercent' => $progresspercent,
            'progresscolor' => $progresscolor,
            'statuscolor' => $statuscolor,
            'statusicon' => $statusicon,
            'statusmessage' => $statusmessage,
            'sharelinks' => $sharelinks,
            'applyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]))->out(false),
            'loginurl' => $loginurl,
            'signupurl' => (new moodle_url('/local/jobboard/signup.php'))->out(false),
            'viewapplicationurl' => $userapplication ? (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $userapplication->id]))->out(false) : null,
            'myapplicationsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'applications']))->out(false),
            'backurl' => $backurl,
            'backlabel' => $backlabel,
        ];
    }

    /**
     * Get convocatoria status CSS class.
     *
     * @param string $status The convocatoria status.
     * @return string CSS class.
     */
    protected function get_convocatoria_status_class(string $status): string {
        $classes = [
            'draft' => 'secondary',
            'open' => 'success',
            'closed' => 'warning',
            'archived' => 'dark',
        ];
        return $classes[$status] ?? 'secondary';
    }
}
