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
