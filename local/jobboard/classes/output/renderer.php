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

use moodle_url;

/**
 * Renderer class for the Job Board plugin.
 */
class renderer extends renderer_base {

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
     * Prepare data for review page template.
     *
     * Handles both list mode (all applications pending review) and single application review mode.
     *
     * @param array $params View parameters including vacancyid, applicationid, page, perpage.
     * @param \context $context Current context.
     * @param int $total Total count (for list mode pagination).
     * @param array $applications Applications data (for list mode).
     * @param array $queuestats Queue statistics (for list mode).
     * @param ?object $selectedapp Selected application object (for single app mode).
     * @param ?object $vacancy Vacancy object (for single app mode).
     * @param ?object $applicant User object (for single app mode).
     * @param array $documents Documents array (for single app mode).
     * @param array $navdata Navigation data: previd, nextid, navposition, navtotal.
     * @return array Template data.
     */
    public function prepare_review_page_data(
        array $params,
        \context $context,
        int $total = 0,
        array $applications = [],
        array $queuestats = [],
        ?object $selectedapp = null,
        ?object $vacancy = null,
        ?object $applicant = null,
        array $documents = [],
        array $navdata = []
    ): array {
        global $OUTPUT;

        $vacancyid = $params['vacancyid'] ?? 0;
        $applicationid = $params['applicationid'] ?? 0;
        $page = $params['page'] ?? 0;
        $perpage = $params['perpage'] ?? 20;

        $dashboardurl = (new moodle_url('/local/jobboard/index.php'))->out(false);
        $myreviewsurl = (new moodle_url('/local/jobboard/index.php', ['view' => 'myreviews']))->out(false);

        // Common breadcrumbs.
        $breadcrumbs = [
            [
                'label' => get_string('dashboard', 'local_jobboard'),
                'url' => $dashboardurl,
            ],
            [
                'label' => get_string('reviewdocuments', 'local_jobboard'),
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review']))->out(false),
            ],
        ];

        // Base data structure.
        $data = [
            'pagetitle' => get_string('reviewdocuments', 'local_jobboard'),
            'breadcrumbs' => $breadcrumbs,
            'dashboardurl' => $dashboardurl,
            'myreviewsurl' => $myreviewsurl,
            'selectedapplication' => null,
            'hasapplications' => false,
            'applications' => [],
            'stats' => [],
            'filterform' => null,
            'pagination' => null,
            'headeractions' => [
                [
                    'url' => $myreviewsurl,
                    'label' => get_string('myreviews', 'local_jobboard'),
                    'icon' => 'clipboard-list',
                    'class' => 'outline-primary',
                ],
            ],
        ];

        // Single application review mode.
        if ($selectedapp && $vacancy && $applicant) {
            return $this->prepare_review_single_application_data(
                $data,
                $params,
                $selectedapp,
                $vacancy,
                $applicant,
                $documents,
                $navdata
            );
        }

        // List mode - applications pending review.
        return $this->prepare_review_list_data(
            $data,
            $params,
            $total,
            $applications,
            $queuestats,
            $context
        );
    }

    /**
     * Prepare review list page data.
     *
     * @param array $data Base template data.
     * @param array $params View parameters.
     * @param int $total Total applications count.
     * @param array $applications Applications array.
     * @param array $queuestats Queue statistics.
     * @param \context $context Current context.
     * @return array Template data.
     */
    protected function prepare_review_list_data(
        array $data,
        array $params,
        int $total,
        array $applications,
        array $queuestats,
        \context $context
    ): array {
        global $DB, $OUTPUT;

        $vacancyid = $params['vacancyid'] ?? 0;
        $page = $params['page'] ?? 0;
        $perpage = $params['perpage'] ?? 20;

        // Stats cards.
        $data['stats'] = [
            [
                'value' => (string) ($queuestats['total'] ?? 0),
                'label' => get_string('pendingreview', 'local_jobboard'),
                'color' => 'primary',
                'icon' => 'file-alt',
            ],
            [
                'value' => (string) ($queuestats['pending'] ?? 0),
                'label' => get_string('pendingdocuments', 'local_jobboard'),
                'color' => 'warning',
                'icon' => 'clock',
            ],
            [
                'value' => (string) ($queuestats['urgent'] ?? 0),
                'label' => get_string('urgent', 'local_jobboard'),
                'color' => 'danger',
                'icon' => 'exclamation-triangle',
            ],
        ];

        // Filter form.
        $vacancies = $DB->get_records('local_jobboard_vacancy', ['status' => 'published'], 'code ASC', 'id, code, title');
        $vacancyoptions = [['value' => 0, 'label' => get_string('allvacancies', 'local_jobboard'), 'selected' => ($vacancyid == 0)]];
        foreach ($vacancies as $v) {
            $vacancyoptions[] = [
                'value' => $v->id,
                'label' => format_string($v->code) . ' - ' . format_string($v->title),
                'selected' => ($vacancyid == $v->id),
            ];
        }

        $data['filterform'] = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => [
                ['name' => 'view', 'value' => 'review'],
            ],
            'fields' => [
                [
                    'name' => 'vacancyid',
                    'options' => $vacancyoptions,
                ],
            ],
        ];

        // Applications list.
        $appsdata = [];
        foreach ($applications as $app) {
            $isurgent = !empty($app->closedate) && ($app->closedate - time()) <= 7 * 86400;
            $haspendingdocs = (int) ($app->pendingcount ?? 0) > 0;

            $appsdata[] = [
                'id' => $app->id,
                'applicantname' => fullname($app),
                'email' => $app->email,
                'vacancycode' => format_string($app->vacancy_code ?? ''),
                'vacancytitle' => format_string($app->vacancy_title ?? ''),
                'statuslabel' => get_string('status_' . $app->status, 'local_jobboard'),
                'doccount' => (int) ($app->doccount ?? 0),
                'pendingcount' => (int) ($app->pendingcount ?? 0),
                'datesubmitted' => userdate($app->timecreated, get_string('strftimedate', 'langconfig')),
                'isurgent' => $isurgent,
                'haspendingdocs' => $haspendingdocs,
                'reviewurl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'review',
                    'applicationid' => $app->id,
                ]))->out(false),
                'viewurl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'application',
                    'id' => $app->id,
                ]))->out(false),
            ];
        }

        $data['hasapplications'] = !empty($appsdata);
        $data['applications'] = $appsdata;

        // Pagination.
        if ($total > $perpage) {
            $baseurl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'review',
                'vacancyid' => $vacancyid,
            ]);
            $data['pagination'] = $OUTPUT->paging_bar($total, $page, $perpage, $baseurl);
        }

        return $data;
    }

    /**
     * Prepare single application review data.
     *
     * @param array $data Base template data.
     * @param array $params View parameters.
     * @param object $application Application object.
     * @param object $vacancy Vacancy object.
     * @param object $applicant User object.
     * @param array $documents Documents array.
     * @param array $navdata Navigation data.
     * @return array Template data.
     */
    protected function prepare_review_single_application_data(
        array $data,
        array $params,
        object $application,
        object $vacancy,
        object $applicant,
        array $documents,
        array $navdata
    ): array {
        global $DB;

        $vacancyid = $params['vacancyid'] ?? 0;
        $applicationid = $application->id;

        // Update page title.
        $data['pagetitle'] = get_string('reviewapplication', 'local_jobboard');

        // Update breadcrumbs.
        $data['breadcrumbs'][] = [
            'label' => fullname($applicant),
            'url' => null,
            'active' => true,
        ];

        // Header actions for single view.
        $data['headeractions'] = [
            [
                'url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancyid]))->out(false),
                'label' => get_string('back'),
                'icon' => 'arrow-left',
                'class' => 'outline-secondary',
            ],
        ];

        // Count document statuses.
        $docstats = ['pending' => 0, 'approved' => 0, 'rejected' => 0];
        foreach ($documents as $doc) {
            $status = $doc->status ?? 'pending';
            if (isset($docstats[$status])) {
                $docstats[$status]++;
            }
        }
        $totaldocs = count($documents);

        // Navigation.
        $previd = $navdata['previd'] ?? null;
        $nextid = $navdata['nextid'] ?? null;
        $navposition = $navdata['navposition'] ?? 0;
        $navtotal = $navdata['navtotal'] ?? 0;

        $shownav = $navtotal > 1;
        $prevurl = $previd ? (new moodle_url('/local/jobboard/index.php', [
            'view' => 'review',
            'applicationid' => $previd,
            'vacancyid' => $vacancyid,
        ]))->out(false) : null;

        $nexturl = $nextid ? (new moodle_url('/local/jobboard/index.php', [
            'view' => 'review',
            'applicationid' => $nextid,
            'vacancyid' => $vacancyid,
        ]))->out(false) : null;

        // Prepare documents.
        $docsdata = [];
        foreach ($documents as $doc) {
            $status = $doc->status ?? 'pending';
            $statusconfig = [
                'pending' => ['icon' => 'clock', 'color' => 'warning'],
                'approved' => ['icon' => 'check-circle', 'color' => 'success'],
                'rejected' => ['icon' => 'times-circle', 'color' => 'danger'],
            ];
            $config = $statusconfig[$status] ?? $statusconfig['pending'];

            $downloadurl = null;
            if (method_exists($doc, 'get_download_url')) {
                $downloadurl = $doc->get_download_url();
            }

            $typename = method_exists($doc, 'get_doctype_name') ? $doc->get_doctype_name() : ($doc->typename ?? 'Document');

            $reviewedby = null;
            $reviewedat = null;
            if (!empty($doc->reviewerid) && $status !== 'pending') {
                $reviewer = $DB->get_record('user', ['id' => $doc->reviewerid]);
                if ($reviewer) {
                    $reviewedby = fullname($reviewer);
                    $reviewedat = !empty($doc->reviewedat) ? userdate($doc->reviewedat, get_string('strftimedatetime', 'langconfig')) : '';
                }
            }

            $docsdata[] = [
                'id' => $doc->id,
                'typename' => format_string($typename),
                'filename' => format_string($doc->filename ?? ''),
                'status' => $status,
                'statuslabel' => get_string('docstatus:' . $status, 'local_jobboard'),
                'statusicon' => $config['icon'],
                'statuscolor' => $config['color'],
                'ispending' => ($status === 'pending'),
                'downloadurl' => $downloadurl,
                'validateurl' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'review',
                    'applicationid' => $applicationid,
                    'documentid' => $doc->id,
                    'action' => 'validate',
                    'sesskey' => sesskey(),
                ]))->out(false),
                'rejecturl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
                'rejectreason' => $doc->rejectreason ?? null,
                'reviewedby' => $reviewedby,
                'reviewedat' => $reviewedat,
                'sesskey' => sesskey(),
            ];
        }

        // Application status class.
        $appstatusclass = 'secondary';
        $appstatus = $application->status ?? 'draft';
        if (in_array($appstatus, ['docs_validated', 'selected'])) {
            $appstatusclass = 'success';
        } else if (in_array($appstatus, ['docs_rejected', 'rejected'])) {
            $appstatusclass = 'danger';
        } else if ($appstatus === 'under_review') {
            $appstatusclass = 'warning';
        } else if ($appstatus === 'submitted') {
            $appstatusclass = 'info';
        }

        // Progress percentages.
        $approvedpercent = $totaldocs > 0 ? round(($docstats['approved'] / $totaldocs) * 100) : 0;
        $rejectedpercent = $totaldocs > 0 ? round(($docstats['rejected'] / $totaldocs) * 100) : 0;

        // All reviewed?
        $allreviewed = ($docstats['pending'] === 0 && $totaldocs > 0);
        $haspending = ($docstats['pending'] > 0);

        // Complete button color.
        $completebtncolor = ($docstats['rejected'] > 0) ? 'warning' : 'success';

        // Stats for single application.
        $data['stats'] = [
            [
                'value' => (string) $totaldocs,
                'label' => get_string('documents', 'local_jobboard'),
                'color' => 'primary',
                'icon' => 'file-alt',
            ],
            [
                'value' => (string) $docstats['approved'],
                'label' => get_string('docstatus:approved', 'local_jobboard'),
                'color' => 'success',
                'icon' => 'check-circle',
            ],
            [
                'value' => (string) $docstats['rejected'],
                'label' => get_string('docstatus:rejected', 'local_jobboard'),
                'color' => 'danger',
                'icon' => 'times-circle',
            ],
            [
                'value' => (string) $docstats['pending'],
                'label' => get_string('docstatus:pending', 'local_jobboard'),
                'color' => 'warning',
                'icon' => 'clock',
            ],
        ];

        $data['selectedapplication'] = [
            'id' => $applicationid,
            'applicantname' => fullname($applicant),
            'email' => $applicant->email,
            'dateapplied' => userdate($application->timecreated, get_string('strftimedate', 'langconfig')),
            'vacancycode' => format_string($vacancy->code ?? ''),
            'vacancytitle' => format_string($vacancy->title ?? ''),
            'status' => $appstatus,
            'statuslabel' => get_string('status_' . $appstatus, 'local_jobboard'),
            'statuscolor' => $appstatusclass,
        ];

        $data['hasdocuments'] = !empty($docsdata);
        $data['documents'] = $docsdata;

        $data['shownav'] = $shownav;
        $data['prevurl'] = $prevurl;
        $data['nexturl'] = $nexturl;
        $data['navposition'] = $navposition;
        $data['navtotal'] = $navtotal;

        $data['approvedcount'] = $docstats['approved'];
        $data['rejectedcount'] = $docstats['rejected'];
        $data['pendingcount'] = $docstats['pending'];
        $data['approvedpercent'] = $approvedpercent;
        $data['rejectedpercent'] = $rejectedpercent;

        $data['allreviewed'] = $allreviewed;
        $data['haspending'] = $haspending;
        $data['completebtncolor'] = $completebtncolor;

        $data['canvalidateall'] = ($docstats['pending'] > 0);
        $data['validateallurl'] = (new moodle_url('/local/jobboard/index.php', [
            'view' => 'review',
            'applicationid' => $applicationid,
            'action' => 'validateall',
            'sesskey' => sesskey(),
        ]))->out(false);

        $data['submitreviewurl'] = (new moodle_url('/local/jobboard/index.php'))->out(false);
        $data['sesskey'] = sesskey();

        $data['backurl'] = (new moodle_url('/local/jobboard/index.php', ['view' => 'review', 'vacancyid' => $vacancyid]))->out(false);

        return $data;
    }

    /**
     * Prepare data for apply page template.
     *
     * @param object $vacancy Vacancy object.
     * @param array $requireddocs Required document types array.
     * @param bool $isexemption Whether user has exemption.
     * @param ?object $exemptioninfo Exemption info object if applicable.
     * @param string $formhtml Rendered form HTML.
     * @param int $daysuntilclose Days until vacancy closes.
     * @return array Template data.
     */
    public function prepare_apply_page_data(
        object $vacancy,
        array $requireddocs,
        bool $isexemption,
        ?object $exemptioninfo,
        string $formhtml,
        int $daysuntilclose
    ): array {
        $vacancyid = $vacancy->id;

        // Progress steps.
        $steps = [
            ['icon' => 'fa-user-check', 'label' => get_string('step_profile', 'local_jobboard'), 'target' => 'id_profilereviewheader'],
            ['icon' => 'fa-file-signature', 'label' => get_string('step_consent', 'local_jobboard'), 'target' => 'id_consentheader'],
            ['icon' => 'fa-upload', 'label' => get_string('step_documents', 'local_jobboard'), 'target' => 'id_documentsheader'],
            ['icon' => 'fa-envelope-open-text', 'label' => get_string('step_coverletter', 'local_jobboard'), 'target' => 'id_additionalheader'],
            ['icon' => 'fa-paper-plane', 'label' => get_string('step_submit', 'local_jobboard'), 'target' => 'id_declarationheader'],
        ];

        $stepsdata = [];
        foreach ($steps as $i => $step) {
            $stepsdata[] = [
                'stepnum' => $i + 1,
                'icon' => $step['icon'],
                'label' => $step['label'],
                'target' => $step['target'],
                'isactive' => ($i === 0),
                'islast' => ($i === count($steps) - 1),
            ];
        }

        // Deadline badge color.
        $deadlinebadgecolor = 'success';
        if ($daysuntilclose <= 3) {
            $deadlinebadgecolor = 'danger';
        } else if ($daysuntilclose <= 7) {
            $deadlinebadgecolor = 'warning';
        }

        // Get vacancy close date.
        $closedate = 0;
        if (method_exists($vacancy, 'get_close_date')) {
            $closedate = $vacancy->get_close_date();
        } else if (!empty($vacancy->closedate)) {
            $closedate = $vacancy->closedate;
        }

        // Determine location (from company or direct field).
        $location = null;
        if (method_exists($vacancy, 'get_company_name')) {
            $location = $vacancy->get_company_name();
        }
        if (empty($location) && !empty($vacancy->location)) {
            $location = $vacancy->location;
        }

        // Determine modality (from department or direct field).
        $modality = null;
        if (method_exists($vacancy, 'get_department_name')) {
            $modality = $vacancy->get_department_name();
        }
        if (empty($modality)) {
            $vacancyrecord = method_exists($vacancy, 'get_record') ? $vacancy->get_record() : $vacancy;
            if (!empty($vacancyrecord->modality)) {
                $modalities = \local_jobboard_get_modalities();
                $modality = $modalities[$vacancyrecord->modality] ?? $vacancyrecord->modality;
            }
        }

        // Guidelines.
        $guidelines = [
            get_string('guideline1', 'local_jobboard'),
            get_string('guideline2', 'local_jobboard'),
            get_string('guideline3', 'local_jobboard'),
            get_string('guideline4', 'local_jobboard'),
        ];

        // Quick tips.
        $quicktips = [
            get_string('tip_saveoften', 'local_jobboard'),
            get_string('tip_checkdocs', 'local_jobboard'),
            get_string('tip_deadline', 'local_jobboard'),
        ];

        // Required docs data.
        $docsdata = [];
        foreach ($requireddocs as $doc) {
            $docsdata[] = [
                'name' => format_string($doc->name),
                'isrequired' => !empty($doc->isrequired),
            ];
        }

        // Exemption info.
        $exemptiondata = null;
        if ($isexemption && $exemptioninfo) {
            $exemptiondata = [
                'exemptiontype' => $exemptioninfo->exemptiontype ?? '',
                'documentref' => $exemptioninfo->documentref ?? '',
            ];
        }

        return [
            'vacancy' => [
                'id' => $vacancy->id,
                'code' => format_string($vacancy->code),
                'title' => format_string($vacancy->title),
                'location' => $location,
                'modality' => $modality,
                'closedateformatted' => $closedate ? userdate($closedate, get_string('strftimedatetime', 'langconfig')) : '',
            ],
            'steps' => $stepsdata,
            'daysuntilclose' => $daysuntilclose,
            'showdeadlinewarning' => ($daysuntilclose <= 3 && $daysuntilclose > 0),
            'deadlinewarningtext' => get_string('deadlinewarning', 'local_jobboard', ceil($daysuntilclose)),
            'showdaysremaining' => ($daysuntilclose > 0),
            'daysremainingtext' => get_string('daysremaining', 'local_jobboard', ceil($daysuntilclose)),
            'deadlinebadgecolor' => $deadlinebadgecolor,
            'guidelines' => $guidelines,
            'isexemption' => $isexemption,
            'exemptioninfo' => $exemptiondata,
            'formhtml' => $formhtml,
            'requireddocs' => $docsdata,
            'hasrequireddocs' => !empty($docsdata),
            'quicktips' => $quicktips,
            'viewvacancyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'id' => $vacancyid]))->out(false),
            'backvacancyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancy', 'id' => $vacancyid]))->out(false),
            'browservacanciesurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'vacancies']))->out(false),
        ];
    }

    /**
     * Prepare data for public page template - Convocatorias list mode.
     *
     * @param array $convocatorias Array of convocatoria records with vacancy stats.
     * @param bool $isloggedin Whether user is logged in.
     * @param array $caps User capabilities array.
     * @param string $pagetitle Page title.
     * @param string $pagedescription Page description HTML.
     * @return array Template data.
     */
    public function prepare_public_convocatorias_data(
        array $convocatorias,
        bool $isloggedin,
        array $caps,
        string $pagetitle,
        string $pagedescription
    ): array {
        // Calculate statistics.
        $totalConvocatorias = count($convocatorias);
        $totalVacancies = 0;
        $totalPositions = 0;
        $urgentCount = 0;

        foreach ($convocatorias as $conv) {
            $totalVacancies += (int) ($conv->vacancy_count ?? 0);
            $totalPositions += (int) ($conv->total_positions ?? 0);
            $daysRemaining = max(0, (int) floor(($conv->enddate - time()) / 86400));
            if ($daysRemaining <= 7) {
                $urgentCount++;
            }
        }

        // Stats cards.
        $stats = [
            ['value' => (string) $totalConvocatorias, 'label' => get_string('activeconvocatorias', 'local_jobboard'), 'color' => 'primary', 'icon' => 'folder-open'],
            ['value' => (string) $totalVacancies, 'label' => get_string('openvacancies', 'local_jobboard'), 'color' => 'success', 'icon' => 'briefcase'],
            ['value' => (string) $totalPositions, 'label' => get_string('totalpositions', 'local_jobboard'), 'color' => 'info', 'icon' => 'users'],
            ['value' => (string) $urgentCount, 'label' => get_string('closingsoon', 'local_jobboard'), 'color' => 'warning', 'icon' => 'clock'],
        ];

        // Quick access buttons.
        $quickaccess = [];
        $isManager = !empty($caps['configure']) || !empty($caps['createvacancy']) || !empty($caps['manageconvocatorias']);
        $isReviewer = !empty($caps['reviewdocuments']);

        if ($isManager) {
            $quickaccess[] = ['url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'dashboard']))->out(false), 'icon' => 'tachometer-alt', 'label' => get_string('dashboard', 'local_jobboard')];
        }
        if ($isReviewer) {
            $quickaccess[] = ['url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'myreviews']))->out(false), 'icon' => 'clipboard-check', 'label' => get_string('myreviews', 'local_jobboard')];
        }
        if (!empty($caps['apply'])) {
            $quickaccess[] = ['url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'applications']))->out(false), 'icon' => 'folder-open', 'label' => get_string('myapplications', 'local_jobboard')];
        }
        if (!empty($caps['viewreports'])) {
            $quickaccess[] = ['url' => (new moodle_url('/local/jobboard/index.php', ['view' => 'reports']))->out(false), 'icon' => 'chart-bar', 'label' => get_string('reports', 'local_jobboard')];
        }

        // Convocatorias cards data.
        $convsdata = [];
        foreach ($convocatorias as $conv) {
            $daysRemaining = max(0, (int) floor(($conv->enddate - time()) / 86400));
            $isUrgent = $daysRemaining <= 7;

            $excerpt = '';
            if (!empty($conv->description)) {
                $excerpt = strip_tags($conv->description);
                if (strlen($excerpt) > 120) {
                    $excerpt = substr($excerpt, 0, 120) . '...';
                }
            }

            $convsdata[] = [
                'id' => $conv->id,
                'code' => format_string($conv->code),
                'name' => format_string($conv->name),
                'descriptionexcerpt' => $excerpt,
                'vacancycount' => (int) ($conv->vacancy_count ?? 0),
                'totalpositions' => (int) ($conv->total_positions ?? 0),
                'startdateformatted' => userdate($conv->startdate, get_string('strftimedate', 'langconfig')),
                'enddateformatted' => userdate($conv->enddate, get_string('strftimedate', 'langconfig')),
                'isurgent' => $isUrgent,
                'urgenttext' => $isUrgent ? get_string('closesindays', 'local_jobboard', $daysRemaining) : '',
                'detailsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $conv->id]))->out(false),
                'vacanciesurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $conv->id]))->out(false),
            ];
        }

        // Share links.
        $shareUrl = (new moodle_url('/local/jobboard/index.php', ['view' => 'public']))->out(false);
        $shareTitle = rawurlencode($pagetitle);
        $shareText = rawurlencode($pagetitle . ' - ' . get_string('openvacancies', 'local_jobboard'));

        $sharelinks = [
            'facebookurl' => 'https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode($shareUrl),
            'twitterurl' => 'https://twitter.com/intent/tweet?text=' . $shareText . '&url=' . rawurlencode($shareUrl),
            'linkedinurl' => 'https://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode($shareUrl) . '&title=' . $shareTitle,
            'whatsappurl' => 'https://wa.me/?text=' . $shareText . '%20' . rawurlencode($shareUrl),
        ];

        return [
            'showconvocatorias' => true,
            'showvacancies' => false,
            'pagetitle' => $pagetitle,
            'pagedescription' => format_text($pagedescription, FORMAT_HTML),
            'isloggedin' => $isloggedin,
            'hasquickaccess' => !empty($quickaccess),
            'quickaccess' => $quickaccess,
            'hasstats' => !empty($stats),
            'stats' => $stats,
            'hasconvocatorias' => !empty($convsdata),
            'convocatorias' => $convsdata,
            'sharelinks' => $sharelinks,
            'loginurl' => (new moodle_url('/login/index.php'))->out(false),
            'signupurl' => (new moodle_url('/local/jobboard/signup.php'))->out(false),
        ];
    }

    /**
     * Prepare data for public page template - Vacancies mode (for specific convocatoria).
     *
     * @param object $convocatoria Convocatoria record.
     * @param array $vacancies Array of vacancy records.
     * @param int $totalVacancies Total vacancy count (for pagination).
     * @param array $allVacanciesForStats All vacancies for stats calculation.
     * @param array $filters Current filter values: contracttype, location, search.
     * @param array $filterOptions Available filter options.
     * @param bool $isloggedin Whether user is logged in.
     * @param bool $canapply Whether user can apply.
     * @param array $contracttypes Contract types lookup.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @return array Template data.
     */
    public function prepare_public_vacancies_data(
        object $convocatoria,
        array $vacancies,
        int $totalVacancies,
        array $allVacanciesForStats,
        array $filters,
        array $filterOptions,
        bool $isloggedin,
        bool $canapply,
        array $contracttypes,
        int $page,
        int $perpage
    ): array {
        global $USER, $DB, $OUTPUT;

        $convocatoriaid = $convocatoria->id;

        // Calculate convocatoria stats.
        $daysRemaining = max(0, (int) floor(($convocatoria->enddate - time()) / 86400));
        $isUrgent = $daysRemaining <= 7;
        $totalPositions = 0;
        foreach ($allVacanciesForStats as $v) {
            $totalPositions += (int) $v->positions;
        }

        // Convocatoria info.
        $convdata = [
            'id' => $convocatoria->id,
            'code' => format_string($convocatoria->code),
            'name' => format_string($convocatoria->name),
            'vacancycount' => count($allVacanciesForStats),
            'totalpositions' => $totalPositions,
            'startdateformatted' => userdate($convocatoria->startdate, get_string('strftimedate', 'langconfig')),
            'enddateformatted' => userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig')),
            'isurgent' => $isUrgent,
            'urgenttext' => $isUrgent ? get_string('closesindays', 'local_jobboard', $daysRemaining) : '',
            'detailsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public_convocatoria', 'id' => $convocatoria->id]))->out(false),
        ];

        // Filter form.
        $filtercontract = $filters['contracttype'] ?? '';
        $filterlocation = $filters['location'] ?? '';
        $filtersearch = $filters['search'] ?? '';
        $hasfilters = !empty($filtercontract) || !empty($filterlocation) || !empty($filtersearch);

        $contractoptions = [];
        foreach ($filterOptions['contracttypes'] ?? [] as $key => $label) {
            $contractoptions[] = ['value' => $key, 'label' => $label, 'selected' => ($filtercontract === $key)];
        }

        $locationoptions = [];
        foreach ($filterOptions['locations'] ?? [] as $loc) {
            $locationoptions[] = ['value' => $loc, 'label' => $loc, 'selected' => ($filterlocation === $loc)];
        }

        $filterform = [
            'action' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'hiddenfields' => [
                ['name' => 'view', 'value' => 'public'],
                ['name' => 'convocatoriaid', 'value' => $convocatoriaid],
            ],
            'searchvalue' => $filtersearch,
            'showcontractfilter' => !empty($contractoptions),
            'contractoptions' => $contractoptions,
            'showlocationfilter' => !empty($locationoptions),
            'locationoptions' => $locationoptions,
            'hasfilters' => $hasfilters,
            'clearfiltersurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoriaid]))->out(false),
        ];

        // Vacancies data.
        $vacsdata = [];
        foreach ($vacancies as $vacancy) {
            // Check if user has applied.
            $hasApplied = false;
            if ($isloggedin) {
                $hasApplied = $DB->record_exists('local_jobboard_application', [
                    'vacancyid' => $vacancy->id,
                    'userid' => $USER->id,
                ]);
            }

            // Location.
            $location = '';
            if (!empty($vacancy->companyid)) {
                $location = \local_jobboard_get_company_name($vacancy->companyid);
            }
            if (empty($location) && !empty($vacancy->location)) {
                $location = $vacancy->location;
            }

            // Publication type.
            $publicationtypecolor = $vacancy->publicationtype === 'public' ? 'success' : 'secondary';
            $publicationtypelabel = $vacancy->publicationtype === 'public'
                ? get_string('public', 'local_jobboard')
                : get_string('internal', 'local_jobboard');

            $vacsdata[] = [
                'id' => $vacancy->id,
                'code' => format_string($vacancy->code),
                'title' => format_string($vacancy->title),
                'location' => $location,
                'contracttypelabel' => $contracttypes[$vacancy->contracttype] ?? '',
                'positions' => (int) $vacancy->positions,
                'publicationtypecolor' => $publicationtypecolor,
                'publicationtypelabel' => $publicationtypelabel,
                'closedateformatted' => userdate($convocatoria->enddate, get_string('strftimedate', 'langconfig')),
                'isurgent' => $isUrgent,
                'urgenttext' => $isUrgent ? get_string('closesindays', 'local_jobboard', $daysRemaining) : '',
                'viewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public_vacancy', 'id' => $vacancy->id]))->out(false),
                'applyurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]))->out(false),
                'loginapplyurl' => (new moodle_url('/login/index.php', [
                    'wantsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'apply', 'vacancyid' => $vacancy->id]))->out(false),
                ]))->out(false),
                'hasapplied' => $hasApplied,
                'canapply' => $canapply && !$hasApplied,
                'isloggedin' => $isloggedin,
            ];
        }

        // Pagination.
        $pagination = null;
        if ($totalVacancies > $perpage) {
            $baseurl = new moodle_url('/local/jobboard/index.php', [
                'view' => 'public',
                'convocatoriaid' => $convocatoriaid,
                'perpage' => $perpage,
                'contracttype' => $filtercontract,
                'location' => $filterlocation,
                'search' => $filtersearch,
            ]);
            $pagination = $OUTPUT->paging_bar($totalVacancies, $page, $perpage, $baseurl);
        }

        return [
            'showconvocatorias' => false,
            'showvacancies' => true,
            'backtoconvocatoriasurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public']))->out(false),
            'convocatoria' => $convdata,
            'filterform' => $filterform,
            'hasfilters' => $hasfilters,
            'clearfiltersurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'public', 'convocatoriaid' => $convocatoriaid]))->out(false),
            'resultstext' => get_string('vacanciesfound', 'local_jobboard', $totalVacancies),
            'hasvacancies' => !empty($vacsdata),
            'vacancies' => $vacsdata,
            'pagination' => $pagination,
            'isloggedin' => $isloggedin,
            'canapply' => $canapply,
            'loginurl' => (new moodle_url('/login/index.php'))->out(false),
            'signupurl' => (new moodle_url('/local/jobboard/signup.php'))->out(false),
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
        $data['addvacancyurl'] = (new moodle_url('/local/jobboard/edit.php', ['convocatoriaid' => $convocatoria->id]))->out(false);
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
                'editurl' => (new moodle_url('/local/jobboard/edit.php', ['id' => $v->id]))->out(false),
            ];
        }
        $data['vacancies'] = $vacsdata;

        return $data;
    }

    /**
     * Prepare reports page data for template.
     *
     * @param string $reporttype Current report type.
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @param array $vacancies Array of vacancy options for filter.
     * @param \context $context Current context.
     * @return array Template data.
     */
    public function prepare_reports_page_data(
        string $reporttype,
        int $vacancyid,
        int $datefrom,
        int $dateto,
        array $vacancies,
        \context $context
    ): array {
        global $DB;

        // Base URLs.
        $baseurl = new moodle_url('/local/jobboard/index.php', [
            'view' => 'reports',
            'report' => $reporttype,
            'vacancyid' => $vacancyid,
            'datefrom' => $datefrom,
            'dateto' => $dateto,
        ]);

        // Report type tabs.
        $reporttypes = [
            'overview' => ['label' => get_string('reportoverview', 'local_jobboard'), 'icon' => 'chart-pie'],
            'applications' => ['label' => get_string('reportapplications', 'local_jobboard'), 'icon' => 'file-alt'],
            'documents' => ['label' => get_string('reportdocuments', 'local_jobboard'), 'icon' => 'folder-open'],
            'reviewers' => ['label' => get_string('reportreviewers', 'local_jobboard'), 'icon' => 'user-check'],
            'timeline' => ['label' => get_string('reporttimeline', 'local_jobboard'), 'icon' => 'calendar-alt'],
        ];

        $tabs = [];
        foreach ($reporttypes as $type => $info) {
            $tabs[] = [
                'type' => $type,
                'label' => $info['label'],
                'icon' => $info['icon'],
                'url' => (new moodle_url('/local/jobboard/index.php', [
                    'view' => 'reports',
                    'report' => $type,
                    'vacancyid' => $vacancyid,
                    'datefrom' => $datefrom,
                    'dateto' => $dateto,
                ]))->out(false),
                'isactive' => ($reporttype === $type),
            ];
        }

        // Export links.
        $exportlinks = [
            ['url' => (new moodle_url($baseurl, ['format' => 'csv']))->out(false), 'label' => 'CSV', 'icon' => 'file-csv', 'color' => 'secondary'],
            ['url' => (new moodle_url($baseurl, ['format' => 'excel']))->out(false), 'label' => 'Excel', 'icon' => 'file-excel', 'color' => 'success'],
            ['url' => (new moodle_url($baseurl, ['format' => 'pdf']))->out(false), 'label' => 'PDF', 'icon' => 'file-pdf', 'color' => 'danger'],
        ];

        // Vacancy filter options.
        $vacancyoptions = [];
        foreach ($vacancies as $v) {
            $vacancyoptions[] = [
                'id' => $v->id,
                'label' => format_string($v->code . ' - ' . $v->title),
                'selected' => ($v->id == $vacancyid),
            ];
        }

        // Base data.
        $data = [
            'pagetitle' => get_string('reports', 'local_jobboard'),
            'reporttypes' => $tabs,
            'currentreport' => $reporttype,
            'filteraction' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'vacancies' => $vacancyoptions,
            'datefrom' => date('Y-m-d', $datefrom),
            'dateto' => date('Y-m-d', $dateto),
            'exportlinks' => $exportlinks,
            'hasdata' => false,
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'manageurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'manage']))->out(false),
            'bulkvalidateurl' => (new moodle_url('/local/jobboard/bulk_validate.php'))->out(false),
            'caps' => [
                'viewallapplications' => has_capability('local/jobboard:viewallapplications', $context),
                'reviewdocuments' => has_capability('local/jobboard:reviewdocuments', $context),
            ],
            // Report type flags.
            'isoverview' => false,
            'isapplications' => false,
            'isdocuments' => false,
            'isreviewers' => false,
            'istimeline' => false,
            // Report data.
            'overview' => [],
            'applications' => [],
            'documents' => [],
            'reviewers' => [],
            'timeline' => [],
        ];

        // Prepare specific report data.
        switch ($reporttype) {
            case 'overview':
                $data['isoverview'] = true;
                $data['overview'] = $this->prepare_overview_report_data($vacancyid, $datefrom, $dateto);
                $data['hasdata'] = true;
                break;

            case 'applications':
                $data['isapplications'] = true;
                $data['applications'] = $this->prepare_applications_report_data($vacancyid, $datefrom, $dateto);
                $data['hasdata'] = !empty($data['applications']['rows']);
                break;

            case 'documents':
                $data['isdocuments'] = true;
                $data['documents'] = $this->prepare_documents_report_data($vacancyid, $datefrom, $dateto);
                $data['hasdata'] = true;
                break;

            case 'reviewers':
                $data['isreviewers'] = true;
                $data['reviewers'] = $this->prepare_reviewers_report_data($vacancyid, $datefrom, $dateto);
                $data['hasdata'] = true;
                break;

            case 'timeline':
                $data['istimeline'] = true;
                $data['timeline'] = $this->prepare_timeline_report_data($vacancyid, $datefrom, $dateto);
                $data['hasdata'] = !empty($data['timeline']['rows']);
                break;
        }

        return $data;
    }

    /**
     * Prepare overview report data.
     *
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @return array Report data.
     */
    protected function prepare_overview_report_data(int $vacancyid, int $datefrom, int $dateto): array {
        global $DB;

        $params = ['from' => $datefrom, 'to' => $dateto];
        $vacancywhere = '';
        if ($vacancyid) {
            $vacancywhere = ' AND a.vacancyid = :vacancyid';
            $params['vacancyid'] = $vacancyid;
        }

        // Summary stats.
        $totalapps = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} a
              WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}",
            $params
        );

        $selectedapps = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} a
              WHERE a.timecreated BETWEEN :from AND :to AND a.status = 'selected' {$vacancywhere}",
            $params
        );

        $rejectedapps = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} a
              WHERE a.timecreated BETWEEN :from AND :to AND a.status = 'rejected' {$vacancywhere}",
            $params
        );

        $pendingapps = $DB->count_records_sql(
            "SELECT COUNT(*) FROM {local_jobboard_application} a
              WHERE a.timecreated BETWEEN :from AND :to AND a.status IN ('submitted', 'under_review') {$vacancywhere}",
            $params
        );

        $selectionrate = $totalapps > 0 ? round(($selectedapps / $totalapps) * 100, 1) : 0;

        // Stats cards.
        $stats = [
            ['value' => (string) $totalapps, 'label' => get_string('totalapplications', 'local_jobboard'), 'color' => 'primary', 'icon' => 'file-alt'],
            ['value' => (string) $selectedapps, 'label' => get_string('selected', 'local_jobboard'), 'color' => 'success', 'icon' => 'trophy'],
            ['value' => (string) $rejectedapps, 'label' => get_string('rejected', 'local_jobboard'), 'color' => 'danger', 'icon' => 'times-circle'],
            ['value' => $selectionrate . '%', 'label' => get_string('selectionrate', 'local_jobboard'), 'color' => 'info', 'icon' => 'chart-line'],
        ];

        // Applications by status.
        $statusdata = $DB->get_records_sql(
            "SELECT a.status, COUNT(*) as count
               FROM {local_jobboard_application} a
              WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}
              GROUP BY a.status",
            $params
        );

        $statusrows = [];
        foreach ($statusdata as $row) {
            $pct = $totalapps > 0 ? round(($row->count / $totalapps) * 100) : 0;
            $statusrows[] = [
                'status' => $row->status,
                'statuslabel' => get_string('status_' . $row->status, 'local_jobboard'),
                'count' => (int) $row->count,
                'percentage' => $pct,
                'color' => $this->get_application_status_color($row->status),
            ];
        }

        return [
            'stats' => $stats,
            'statusdata' => $statusrows,
            'hasstatusdata' => !empty($statusrows),
        ];
    }

    /**
     * Get color class for application status.
     *
     * @param string $status Application status.
     * @return string Color class.
     */
    protected function get_application_status_color(string $status): string {
        $colors = [
            'submitted' => 'info',
            'under_review' => 'warning',
            'docs_validated' => 'success',
            'docs_rejected' => 'danger',
            'interview' => 'purple',
            'selected' => 'success',
            'rejected' => 'secondary',
            'withdrawn' => 'dark',
        ];
        return $colors[$status] ?? 'secondary';
    }

    /**
     * Prepare applications report data.
     *
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @return array Report data.
     */
    protected function prepare_applications_report_data(int $vacancyid, int $datefrom, int $dateto): array {
        global $DB;

        $params = ['from' => $datefrom, 'to' => $dateto];
        $vacancywhere = '';
        if ($vacancyid) {
            $vacancywhere = ' AND v.id = :vacancyid';
            $params['vacancyid'] = $vacancyid;
        }

        // Applications by vacancy.
        $byvacancy = $DB->get_records_sql(
            "SELECT v.id, v.code, v.title, COUNT(a.id) as total,
                    SUM(CASE WHEN a.status = 'selected' THEN 1 ELSE 0 END) as selected,
                    SUM(CASE WHEN a.status = 'rejected' THEN 1 ELSE 0 END) as rejected
               FROM {local_jobboard_vacancy} v
               LEFT JOIN {local_jobboard_application} a ON a.vacancyid = v.id
                    AND a.timecreated BETWEEN :from AND :to
              WHERE 1=1 {$vacancywhere}
              GROUP BY v.id, v.code, v.title
              ORDER BY total DESC",
            $params
        );

        $rows = [];
        foreach ($byvacancy as $row) {
            $pending = (int) $row->total - (int) $row->selected - (int) $row->rejected;
            $rows[] = [
                'id' => $row->id,
                'code' => format_string($row->code),
                'title' => format_string($row->title),
                'total' => (int) $row->total,
                'selected' => (int) $row->selected,
                'rejected' => (int) $row->rejected,
                'pending' => max(0, $pending),
            ];
        }

        return [
            'rows' => $rows,
            'hasdata' => !empty($rows),
        ];
    }

    /**
     * Prepare documents report data.
     *
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @return array Report data.
     */
    protected function prepare_documents_report_data(int $vacancyid, int $datefrom, int $dateto): array {
        // Get stats from bulk_validator class.
        $docstats = \local_jobboard\bulk_validator::get_validation_stats($vacancyid ?: null, $datefrom);
        $rejectionreasons = \local_jobboard\bulk_validator::get_rejection_reasons_stats($vacancyid ?: null, $datefrom);

        // Stats cards.
        $stats = [
            ['value' => (string) ($docstats['total'] ?? 0), 'label' => get_string('totaldocuments', 'local_jobboard'), 'color' => 'primary', 'icon' => 'folder'],
            [
                'value' => ($docstats['validated'] ?? 0) . ' (' . ($docstats['validation_rate'] ?? 0) . '%)',
                'label' => get_string('validated', 'local_jobboard'),
                'color' => 'success',
                'icon' => 'check-circle',
            ],
            [
                'value' => ($docstats['rejected'] ?? 0) . ' (' . ($docstats['rejection_rate'] ?? 0) . '%)',
                'label' => get_string('rejected', 'local_jobboard'),
                'color' => 'danger',
                'icon' => 'times-circle',
            ],
            [
                'value' => ($docstats['avg_validation_time_hours'] ?? 0) . 'h',
                'label' => get_string('avgvalidationtime', 'local_jobboard'),
                'color' => 'info',
                'icon' => 'clock',
            ],
        ];

        // Rejection reasons.
        $rejectionrows = [];
        foreach ($rejectionreasons as $reason) {
            $reasontext = get_string('rejectreason_' . $reason->rejectreason, 'local_jobboard');
            $rejectionrows[] = [
                'reason' => $reasontext,
                'count' => (int) $reason->count,
            ];
        }

        // Pending by type.
        $pendingbytype = [];
        foreach (($docstats['by_type'] ?? []) as $row) {
            if (isset($row->pending) && $row->pending > 0) {
                $typename = get_string('doctype_' . $row->documenttype, 'local_jobboard');
                $pendingbytype[] = [
                    'typename' => $typename,
                    'pending' => (int) $row->pending,
                ];
            }
        }

        // By document type table.
        $bytype = [];
        foreach (($docstats['by_type'] ?? []) as $row) {
            $typename = get_string('doctype_' . $row->documenttype, 'local_jobboard');
            $bytype[] = [
                'typename' => $typename,
                'total' => (int) ($row->total ?? 0),
                'validated' => (int) ($row->validated ?? 0),
                'rejected' => (int) ($row->rejected ?? 0),
                'pending' => (int) ($row->pending ?? 0),
            ];
        }

        return [
            'stats' => $stats,
            'rejectionreasons' => $rejectionrows,
            'hasrejections' => !empty($rejectionrows),
            'pendingbytype' => $pendingbytype,
            'haspending' => !empty($pendingbytype),
            'bytype' => $bytype,
            'hasbytypedata' => !empty($bytype),
        ];
    }

    /**
     * Prepare reviewers report data.
     *
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @return array Report data.
     */
    protected function prepare_reviewers_report_data(int $vacancyid, int $datefrom, int $dateto): array {
        $reviewers = \local_jobboard\reviewer::get_all_with_workload();

        $rows = [];
        foreach ($reviewers as $rev) {
            $workloadcolor = 'success';
            if ($rev->workload > 15) {
                $workloadcolor = 'danger';
            } else if ($rev->workload > 10) {
                $workloadcolor = 'warning';
            }

            $rows[] = [
                'id' => $rev->id,
                'fullname' => fullname($rev),
                'workload' => (int) $rev->workload,
                'workloadcolor' => $workloadcolor,
                'reviewed' => (int) ($rev->stats['reviewed'] ?? 0),
                'validated' => (int) ($rev->stats['validated'] ?? 0),
                'rejected' => (int) ($rev->stats['rejected'] ?? 0),
                'avgtime' => (int) ($rev->stats['avg_review_time'] ?? 0),
            ];
        }

        return [
            'rows' => $rows,
            'hasdata' => !empty($rows),
        ];
    }

    /**
     * Prepare timeline report data.
     *
     * @param int $vacancyid Vacancy filter ID.
     * @param int $datefrom Start date timestamp.
     * @param int $dateto End date timestamp.
     * @return array Report data.
     */
    protected function prepare_timeline_report_data(int $vacancyid, int $datefrom, int $dateto): array {
        global $DB;

        $params = ['from' => $datefrom, 'to' => $dateto];
        $vacancywhere = '';
        if ($vacancyid) {
            $vacancywhere = ' AND a.vacancyid = :vacancyid';
            $params['vacancyid'] = $vacancyid;
        }

        // Daily application counts.
        $sql = "SELECT DATE(FROM_UNIXTIME(a.timecreated)) as day, COUNT(*) as count
                  FROM {local_jobboard_application} a
                 WHERE a.timecreated BETWEEN :from AND :to {$vacancywhere}
                 GROUP BY DATE(FROM_UNIXTIME(a.timecreated))
                 ORDER BY day ASC";

        $daily = $DB->get_records_sql($sql, $params);

        // Calculate max for visual bar.
        $maxcount = 0;
        foreach ($daily as $row) {
            if ($row->count > $maxcount) {
                $maxcount = (int) $row->count;
            }
        }

        $rows = [];
        foreach ($daily as $row) {
            $pct = $maxcount > 0 ? round(($row->count / $maxcount) * 100) : 0;
            $rows[] = [
                'day' => $row->day,
                'count' => (int) $row->count,
                'percentage' => $pct,
            ];
        }

        return [
            'rows' => $rows,
            'hasdata' => !empty($rows),
        ];
    }

    /**
     * Render the admin roles page.
     *
     * @param array $data Template data.
     * @return string Rendered HTML.
     */
    public function render_admin_roles_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin_roles', $data);
    }

    /**
     * Prepare admin roles page data for template.
     *
     * @param string|null $selectedroleshortname Currently selected role shortname.
     * @param \context $context Current context.
     * @return array Template data.
     */
    public function prepare_admin_roles_page_data(?string $selectedroleshortname, \context $context): array {
        global $DB;

        // Define the plugin roles.
        $pluginroles = [
            'jobboard_reviewer' => [
                'name' => get_string('role_reviewer', 'local_jobboard'),
                'description' => get_string('role_reviewer_desc', 'local_jobboard'),
                'icon' => 'clipboard-check',
                'color' => 'warning',
                'capspreview' => [
                    get_string('cap_review', 'local_jobboard'),
                    get_string('cap_validate', 'local_jobboard'),
                    get_string('cap_download', 'local_jobboard'),
                ],
            ],
            'jobboard_coordinator' => [
                'name' => get_string('role_coordinator', 'local_jobboard'),
                'description' => get_string('role_coordinator_desc', 'local_jobboard'),
                'icon' => 'user-tie',
                'color' => 'primary',
                'capspreview' => [
                    get_string('cap_manage', 'local_jobboard'),
                    get_string('cap_createvacancy', 'local_jobboard'),
                    get_string('cap_assignreviewers', 'local_jobboard'),
                    get_string('cap_viewreports', 'local_jobboard'),
                ],
            ],
            'jobboard_committee' => [
                'name' => get_string('role_committee', 'local_jobboard'),
                'description' => get_string('role_committee_desc', 'local_jobboard'),
                'icon' => 'users',
                'color' => 'success',
                'capspreview' => [
                    get_string('cap_evaluate', 'local_jobboard'),
                    get_string('cap_viewevaluations', 'local_jobboard'),
                    get_string('cap_download', 'local_jobboard'),
                ],
            ],
        ];

        // Get role statistics.
        $totalusers = 0;
        $rolesdata = [];

        foreach ($pluginroles as $shortname => $roledef) {
            $role = $DB->get_record('role', ['shortname' => $shortname]);
            $usercount = 0;

            if ($role) {
                $usercount = $DB->count_records('role_assignments', [
                    'roleid' => $role->id,
                    'contextid' => $context->id,
                ]);
            }

            $totalusers += $usercount;

            $rolesdata[] = [
                'shortname' => $shortname,
                'name' => $roledef['name'],
                'description' => $roledef['description'],
                'icon' => $roledef['icon'],
                'color' => $roledef['color'],
                'usercount' => $usercount,
                'roleexists' => !empty($role),
                'roleid' => $role->id ?? 0,
                'capspreview' => $roledef['capspreview'],
                'hascaps' => !empty($roledef['capspreview']),
                'manageurl' => (new moodle_url('/local/jobboard/admin/roles.php', ['role' => $shortname]))->out(false),
            ];
        }

        // Base data.
        $data = [
            'pagetitle' => get_string('manageroles', 'local_jobboard'),
            'totalusers' => $totalusers,
            'pluginroles' => $rolesdata,
            'selectedrole' => null,
            'backurl' => (new moodle_url('/local/jobboard/admin/roles.php'))->out(false),
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'committeeurl' => (new moodle_url('/local/jobboard/manage_committee.php'))->out(false),
            'settingsurl' => (new moodle_url('/admin/settings.php', ['section' => 'local_jobboard']))->out(false),
            'assignformurl' => (new moodle_url('/local/jobboard/admin/roles.php'))->out(false),
            'sesskey' => sesskey(),
            'hasassignedusers' => false,
            'assignedusers' => [],
            'assignedusercount' => 0,
            'availableusers' => [],
        ];

        // If a role is selected, get user details.
        if ($selectedroleshortname && isset($pluginroles[$selectedroleshortname])) {
            $roledef = $pluginroles[$selectedroleshortname];
            $role = $DB->get_record('role', ['shortname' => $selectedroleshortname]);

            if ($role) {
                // Find role in rolesdata.
                $selectedroledata = null;
                foreach ($rolesdata as $r) {
                    if ($r['shortname'] === $selectedroleshortname) {
                        $selectedroledata = $r;
                        break;
                    }
                }

                $data['selectedrole'] = $selectedroledata;

                // Get assigned users.
                $sql = "SELECT u.id, u.firstname, u.lastname, u.email, ra.timemodified as assigneddate
                          FROM {role_assignments} ra
                          JOIN {user} u ON u.id = ra.userid
                         WHERE ra.roleid = :roleid AND ra.contextid = :contextid
                         ORDER BY u.lastname, u.firstname";
                $assignedusers = $DB->get_records_sql($sql, [
                    'roleid' => $role->id,
                    'contextid' => $context->id,
                ]);

                $assigneduserids = array_keys($assignedusers);
                $assignedusersdata = [];

                foreach ($assignedusers as $user) {
                    $assignedusersdata[] = [
                        'id' => $user->id,
                        'fullname' => fullname($user),
                        'email' => $user->email,
                        'assigneddate' => userdate($user->assigneddate, get_string('strftimedateshort')),
                        'unassignurl' => (new moodle_url('/local/jobboard/admin/roles.php', [
                            'action' => 'unassign',
                            'role' => $selectedroleshortname,
                            'userid' => $user->id,
                            'sesskey' => sesskey(),
                        ]))->out(false),
                    ];
                }

                $data['assignedusers'] = $assignedusersdata;
                $data['hasassignedusers'] = !empty($assignedusersdata);
                $data['assignedusercount'] = count($assignedusersdata);

                // Get available users (not already assigned).
                if (empty($assigneduserids)) {
                    $assigneduserids = [0];
                }

                $sql = "SELECT u.id, u.firstname, u.lastname, u.email
                          FROM {user} u
                         WHERE u.deleted = 0
                           AND u.suspended = 0
                           AND u.id NOT IN (" . implode(',', $assigneduserids) . ")
                           AND u.id > 1
                         ORDER BY u.lastname, u.firstname
                         LIMIT 200";
                $availableusers = $DB->get_records_sql($sql);

                $availableusersdata = [];
                foreach ($availableusers as $user) {
                    $availableusersdata[] = [
                        'id' => $user->id,
                        'fullname' => fullname($user),
                        'email' => $user->email,
                    ];
                }

                $data['availableusers'] = $availableusersdata;
            }
        }

        return $data;
    }

    /**
     * Render the program reviewers page.
     *
     * @param array $data Template data.
     * @return string Rendered HTML.
     */
    public function render_program_reviewers_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/program_reviewers', $data);
    }

    /**
     * Prepare program reviewers page data for template.
     *
     * @param int $categoryid Category/program ID (0 for list view).
     * @return array Template data.
     */
    public function prepare_program_reviewers_page_data(int $categoryid): array {
        global $DB;

        $pageurl = new moodle_url('/local/jobboard/manage_program_reviewers.php');

        // Base data.
        $data = [
            'pagetitle' => get_string('programreviewers', 'local_jobboard'),
            'islistview' => ($categoryid == 0),
            'isprogramview' => ($categoryid > 0),
            'backurl' => '',
            'backicon' => '',
            'backlabel' => '',
            'sesskey' => sesskey(),
            'addformurl' => $pageurl->out(false),
        ];

        if ($categoryid == 0) {
            // List view.
            $data['backurl'] = (new moodle_url('/local/jobboard/index.php'))->out(false);
            $data['backicon'] = 'tachometer-alt';
            $data['backlabel'] = get_string('dashboard', 'local_jobboard');

            // Get statistics.
            $stats = \local_jobboard\program_reviewer::get_statistics();
            $data['stats'] = [
                'totalreviewers' => $stats['users_as_reviewers'] ?? 0,
                'active' => $stats['active'] ?? 0,
                'leadreviewers' => $stats['lead_reviewers'] ?? 0,
                'programswithreviewers' => $stats['programs_with_reviewers'] ?? 0,
            ];

            // Programs with reviewers.
            $programsWithReviewers = \local_jobboard\program_reviewer::get_programs_with_reviewers();
            $programsdata = [];
            foreach ($programsWithReviewers as $program) {
                $programsdata[] = [
                    'id' => $program->id,
                    'name' => format_string($program->name),
                    'reviewercount' => (int) $program->reviewer_count,
                    'leadcount' => (int) $program->lead_count,
                    'manageurl' => (new moodle_url($pageurl, ['categoryid' => $program->id]))->out(false),
                ];
            }
            $data['programswithreviewers'] = $programsdata;
            $data['hasprogramswithreviewers'] = !empty($programsdata);

            // All categories.
            $allcategories = $DB->get_records('course_categories', [], 'sortorder', 'id, name, parent, depth');
            $categoriesdata = [];

            // Create lookup for programs with reviewers.
            $reviewerLookup = [];
            foreach ($programsWithReviewers as $p) {
                $reviewerLookup[$p->id] = $p->reviewer_count;
            }

            foreach ($allcategories as $cat) {
                $indent = str_repeat('&nbsp;&nbsp;&nbsp;', $cat->depth);
                $icon = $cat->depth == 1 ? 'building' : 'graduation-cap';
                $hasReviewers = isset($reviewerLookup[$cat->id]);

                $categoriesdata[] = [
                    'id' => $cat->id,
                    'name' => format_string($cat->name),
                    'indent' => $indent,
                    'icon' => $icon,
                    'hasreviewers' => $hasReviewers,
                    'reviewercount' => $hasReviewers ? $reviewerLookup[$cat->id] : 0,
                    'selecturl' => (new moodle_url($pageurl, ['categoryid' => $cat->id]))->out(false),
                ];
            }
            $data['allcategories'] = $categoriesdata;
            $data['hasallcategories'] = !empty($categoriesdata);

        } else {
            // Single program view.
            $category = $DB->get_record('course_categories', ['id' => $categoryid], '*', MUST_EXIST);

            $data['pagetitle'] = get_string('programreviewers', 'local_jobboard') . ': ' . format_string($category->name);
            $data['backurl'] = $pageurl->out(false);
            $data['backicon'] = 'arrow-left';
            $data['backlabel'] = get_string('backtolist', 'local_jobboard');
            $data['program'] = [
                'id' => $category->id,
                'name' => format_string($category->name),
            ];

            // Get reviewers for this program.
            $reviewers = \local_jobboard\program_reviewer::get_for_program($categoryid, false);
            $reviewersdata = [];
            $assignedids = [];

            foreach ($reviewers as $reviewer) {
                $assignedids[] = $reviewer->userid;
                $isactive = ($reviewer->status === \local_jobboard\program_reviewer::STATUS_ACTIVE);
                $islead = ($reviewer->role === \local_jobboard\program_reviewer::ROLE_LEAD);
                $newrole = $islead ? \local_jobboard\program_reviewer::ROLE_REVIEWER : \local_jobboard\program_reviewer::ROLE_LEAD;

                $reviewersdata[] = [
                    'id' => $reviewer->id,
                    'userid' => $reviewer->userid,
                    'fullname' => fullname($reviewer),
                    'email' => $reviewer->email,
                    'isactive' => $isactive,
                    'isinactive' => !$isactive,
                    'islead' => $islead,
                    'changeroleurl' => (new moodle_url($pageurl, [
                        'action' => 'changerole',
                        'categoryid' => $categoryid,
                        'userid' => $reviewer->userid,
                        'newrole' => $newrole,
                        'sesskey' => sesskey(),
                    ]))->out(false),
                    'togglestatusurl' => (new moodle_url($pageurl, [
                        'action' => 'togglestatus',
                        'categoryid' => $categoryid,
                        'userid' => $reviewer->userid,
                        'sesskey' => sesskey(),
                    ]))->out(false),
                    'togglestatusicon' => $isactive ? 'fa-toggle-on jb-text-success' : 'fa-toggle-off',
                    'togglestatustitle' => $isactive
                        ? get_string('deactivate', 'local_jobboard')
                        : get_string('activate', 'local_jobboard'),
                    'removeurl' => (new moodle_url($pageurl, [
                        'action' => 'remove',
                        'categoryid' => $categoryid,
                        'userid' => $reviewer->userid,
                        'sesskey' => sesskey(),
                    ]))->out(false),
                ];
            }
            $data['reviewers'] = $reviewersdata;
            $data['hasreviewers'] = !empty($reviewersdata);

            // Get available users.
            $sql = "SELECT DISTINCT u.id, u.firstname, u.lastname, u.email
                      FROM {user} u
                      JOIN {role_assignments} ra ON ra.userid = u.id
                      JOIN {role} r ON r.id = ra.roleid
                     WHERE u.deleted = 0 AND u.suspended = 0
                       AND (r.shortname = 'jobboard_reviewer' OR r.shortname = 'manager' OR r.shortname = 'admin')
                  ORDER BY u.lastname, u.firstname";
            $potentialusers = $DB->get_records_sql($sql);

            $availableusers = [];
            foreach ($potentialusers as $user) {
                if (!in_array($user->id, $assignedids)) {
                    $availableusers[] = [
                        'id' => $user->id,
                        'fullname' => fullname($user),
                        'email' => $user->email,
                    ];
                }
            }
            $data['availableusers'] = $availableusers;
            $data['hasavailableusers'] = !empty($availableusers);
        }

        return $data;
    }

    /**
     * Render the committee management page.
     *
     * @param array $data Template data.
     * @return string Rendered HTML.
     */
    public function render_committee_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/committee', $data);
    }

    /**
     * Prepare committee page data for template.
     *
     * @param int $companyid Company/faculty ID (0 for list view).
     * @param int $vacancyid Legacy vacancy ID.
     * @param string $usersearch User search string.
     * @return array Template data.
     */
    public function prepare_committee_page_data(int $companyid, int $vacancyid, string $usersearch = ''): array {
        global $DB;

        $pageurl = new moodle_url('/local/jobboard/manage_committee.php');

        // Role definitions.
        $memberroles = [
            \local_jobboard\committee::ROLE_CHAIR => [
                'code' => \local_jobboard\committee::ROLE_CHAIR,
                'name' => get_string('role_chair', 'local_jobboard'),
                'icon' => 'user-tie',
                'color' => 'danger',
            ],
            \local_jobboard\committee::ROLE_SECRETARY => [
                'code' => \local_jobboard\committee::ROLE_SECRETARY,
                'name' => get_string('role_secretary', 'local_jobboard'),
                'icon' => 'user-edit',
                'color' => 'primary',
            ],
            \local_jobboard\committee::ROLE_EVALUATOR => [
                'code' => \local_jobboard\committee::ROLE_EVALUATOR,
                'name' => get_string('role_evaluator', 'local_jobboard'),
                'icon' => 'user-check',
                'color' => 'success',
            ],
            \local_jobboard\committee::ROLE_OBSERVER => [
                'code' => \local_jobboard\committee::ROLE_OBSERVER,
                'name' => get_string('role_observer', 'local_jobboard'),
                'icon' => 'user-clock',
                'color' => 'secondary',
            ],
        ];

        // Statistics.
        $stats = [
            'totalcommittees' => $DB->count_records('local_jobboard_committee'),
            'activecommittees' => $DB->count_records('local_jobboard_committee', ['status' => 'active']),
            'totalmembers' => $DB->count_records('local_jobboard_committee_member'),
        ];

        // Get companies with vacancies or committees.
        $companies = $DB->get_records_sql("
            SELECT DISTINCT c.id, c.name, c.shortname
              FROM {company} c
             WHERE c.id IN (
                 SELECT DISTINCT companyid FROM {local_jobboard_vacancy} WHERE companyid IS NOT NULL
                 UNION
                 SELECT DISTINCT companyid FROM {local_jobboard_committee} WHERE companyid IS NOT NULL
             )
             ORDER BY c.name
        ");

        $companiesdata = [];
        foreach ($companies as $c) {
            $companiesdata[] = [
                'id' => $c->id,
                'name' => format_string($c->name),
                'selected' => ($c->id == $companyid),
            ];
        }

        // Base data.
        $data = [
            'pagetitle' => get_string('committees', 'local_jobboard'),
            'islistview' => ($companyid == 0 && $vacancyid == 0),
            'iscompanyview' => ($companyid > 0),
            'isvacancyview' => ($vacancyid > 0 && $companyid == 0),
            'stats' => $stats,
            'companies' => $companiesdata,
            'selectedcompanyid' => $companyid,
            'filterformurl' => $pageurl->out(false),
            'backurl' => $pageurl->out(false),
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'rolesurl' => (new moodle_url('/local/jobboard/admin/roles.php'))->out(false),
            'reviewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review']))->out(false),
            'sesskey' => sesskey(),
            'usersearch' => $usersearch,
            'memberroles' => array_values($memberroles),
        ];

        if ($companyid == 0 && $vacancyid == 0) {
            // List view - all committees.
            $data = array_merge($data, $this->prepare_committee_list_data($pageurl, $companies));
        } else if ($companyid > 0) {
            // Company view.
            $data = array_merge($data, $this->prepare_committee_company_data(
                $companyid, $pageurl, $memberroles, $usersearch
            ));
        } else if ($vacancyid > 0) {
            // Legacy vacancy view.
            $data = array_merge($data, $this->prepare_committee_vacancy_data($vacancyid, $pageurl));
        }

        return $data;
    }

    /**
     * Prepare committee list view data.
     *
     * @param moodle_url $pageurl Base page URL.
     * @param array $companies Available companies.
     * @return array Template data.
     */
    protected function prepare_committee_list_data(moodle_url $pageurl, array $companies): array {
        global $DB;

        // Get all committees.
        $sql = "SELECT c.*, comp.name as company_name, comp.shortname as company_shortname,
                       v.code as vacancy_code, v.title as vacancy_title,
                       (SELECT COUNT(*) FROM {local_jobboard_committee_member} WHERE committeeid = c.id) as membercount
                  FROM {local_jobboard_committee} c
             LEFT JOIN {company} comp ON comp.id = c.companyid
             LEFT JOIN {local_jobboard_vacancy} v ON v.id = c.vacancyid
                 ORDER BY comp.name, c.timecreated DESC";
        $committees = $DB->get_records_sql($sql);

        $committeesdata = [];
        foreach ($committees as $comm) {
            if (!empty($comm->companyid)) {
                $linkparams = ['companyid' => $comm->companyid];
                $entityname = format_string($comm->company_name ?: $comm->company_shortname);
            } else {
                $linkparams = ['vacancyid' => $comm->vacancyid];
                $entityname = format_string($comm->vacancy_code . ' - ' . $comm->vacancy_title);
            }

            $committeesdata[] = [
                'id' => $comm->id,
                'name' => format_string($comm->name),
                'entityname' => $entityname,
                'membercount' => (int) $comm->membercount,
                'status' => $comm->status,
                'isactive' => ($comm->status === 'active'),
                'manageurl' => (new moodle_url($pageurl, $linkparams))->out(false),
            ];
        }

        // Companies without committees.
        $companieswithout = [];
        foreach ($companies as $c) {
            if (!$DB->record_exists('local_jobboard_committee', ['companyid' => $c->id])) {
                $companieswithout[] = [
                    'id' => $c->id,
                    'name' => format_string($c->name),
                    'createurl' => (new moodle_url($pageurl, ['companyid' => $c->id]))->out(false),
                ];
            }
        }

        return [
            'committees' => $committeesdata,
            'hascommittees' => !empty($committeesdata),
            'companieswithoutcommittee' => $companieswithout,
            'hascompanieswithoutcommittee' => !empty($companieswithout),
        ];
    }

    /**
     * Prepare committee company view data.
     *
     * @param int $companyid Company ID.
     * @param moodle_url $pageurl Base page URL.
     * @param array $memberroles Role definitions.
     * @param string $usersearch User search string.
     * @return array Template data.
     */
    protected function prepare_committee_company_data(
        int $companyid,
        moodle_url $pageurl,
        array $memberroles,
        string $usersearch
    ): array {
        global $DB;

        $company = $DB->get_record('company', ['id' => $companyid], '*', MUST_EXIST);
        $vacancycount = $DB->count_records('local_jobboard_vacancy', ['companyid' => $companyid]);
        $existingcommittee = \local_jobboard\committee::get_for_company($companyid);

        $data = [
            'company' => [
                'id' => $company->id,
                'name' => format_string($company->name),
                'vacancycount' => $vacancycount,
            ],
            'hasexistingcommittee' => !empty($existingcommittee),
            'createformurl' => $pageurl->out(false),
            'addmemberformurl' => $pageurl->out(false),
            'defaultcommitteename' => get_string('facultycommitteedefaultname', 'local_jobboard', format_string($company->name)),
        ];

        // Get existing member IDs.
        $existingmemberids = [0];
        if ($existingcommittee && !empty($existingcommittee->members)) {
            $existingmemberids = array_column((array) $existingcommittee->members, 'userid');
            if (empty($existingmemberids)) {
                $existingmemberids = [0];
            }
        }

        // Get available users.
        $searchsql = '';
        $searchparams = [];
        if (!empty($usersearch)) {
            $searchsql = " AND (u.firstname LIKE :search1 OR u.lastname LIKE :search2
                           OR u.email LIKE :search3 OR u.username LIKE :search4)";
            $searchparams = [
                'search1' => '%' . $usersearch . '%',
                'search2' => '%' . $usersearch . '%',
                'search3' => '%' . $usersearch . '%',
                'search4' => '%' . $usersearch . '%',
            ];
        }

        $sql = "SELECT u.id, u.firstname, u.lastname, u.email, u.username
                  FROM {user} u
                 WHERE u.deleted = 0
                   AND u.suspended = 0
                   AND u.id NOT IN (" . implode(',', $existingmemberids) . ")
                   AND u.id > 1
                   $searchsql
                 ORDER BY u.lastname, u.firstname
                 LIMIT 200";
        $availableusers = $DB->get_records_sql($sql, $searchparams);

        $usersdata = [];
        foreach ($availableusers as $user) {
            $usersdata[] = [
                'id' => $user->id,
                'fullname' => fullname($user),
                'username' => $user->username,
                'email' => $user->email,
            ];
        }
        $data['availableusers'] = $usersdata;
        $data['hasavailableusers'] = !empty($usersdata);

        if ($existingcommittee) {
            // Prepare committee data.
            $membersdata = [];
            foreach ($existingcommittee->members as $member) {
                $roledef = $memberroles[$member->role] ?? $memberroles[\local_jobboard\committee::ROLE_EVALUATOR];

                // Available roles for change (exclude current).
                $availableroles = [];
                foreach ($memberroles as $rolecode => $roleinfo) {
                    if ($rolecode !== $member->role) {
                        $availableroles[] = [
                            'code' => $rolecode,
                            'name' => $roleinfo['name'],
                            'changeurl' => (new moodle_url($pageurl, [
                                'action' => 'changerole',
                                'id' => $existingcommittee->id,
                                'userid' => $member->userid,
                                'newrole' => $rolecode,
                                'sesskey' => sesskey(),
                            ]))->out(false),
                        ];
                    }
                }

                $membersdata[] = [
                    'userid' => $member->userid,
                    'fullname' => fullname($member),
                    'username' => $member->username,
                    'email' => $member->email,
                    'role' => $member->role,
                    'rolename' => $roledef['name'],
                    'roleicon' => $roledef['icon'],
                    'rolecolor' => $roledef['color'],
                    'availableroles' => $availableroles,
                    'removeurl' => (new moodle_url($pageurl, [
                        'action' => 'removemember',
                        'id' => $existingcommittee->id,
                        'userid' => $member->userid,
                        'sesskey' => sesskey(),
                    ]))->out(false),
                ];
            }

            $data['existingcommittee'] = [
                'id' => $existingcommittee->id,
                'name' => format_string($existingcommittee->name),
                'membercount' => count($existingcommittee->members),
                'members' => $membersdata,
                'hasmembers' => !empty($membersdata),
            ];

            // Faculty vacancies.
            $facultyvacancies = $DB->get_records('local_jobboard_vacancy',
                ['companyid' => $companyid, 'status' => 'published'],
                'code ASC', 'id, code, title, status');

            $vacanciesdata = [];
            foreach ($facultyvacancies as $v) {
                $vacanciesdata[] = [
                    'id' => $v->id,
                    'code' => $v->code,
                    'title' => format_string($v->title),
                    'status' => $v->status,
                    'statuslabel' => get_string('status:' . $v->status, 'local_jobboard'),
                    'statuscolor' => $this->get_status_class($v->status),
                ];
            }
            $data['facultyvacancies'] = $vacanciesdata;
            $data['hasfacultyvacancies'] = !empty($vacanciesdata);
        }

        return $data;
    }

    /**
     * Prepare committee vacancy view data (legacy).
     *
     * @param int $vacancyid Vacancy ID.
     * @param moodle_url $pageurl Base page URL.
     * @return array Template data.
     */
    protected function prepare_committee_vacancy_data(int $vacancyid, moodle_url $pageurl): array {
        global $DB;

        $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $vacancyid], '*', MUST_EXIST);
        $existingcommittee = \local_jobboard\committee::get_for_vacancy($vacancyid);

        $data = [
            'vacancy' => [
                'id' => $vacancy->id,
                'code' => $vacancy->code,
                'title' => format_string($vacancy->title),
                'status' => $vacancy->status,
                'statuslabel' => get_string('status:' . $vacancy->status, 'local_jobboard'),
                'statuscolor' => $this->get_status_class($vacancy->status),
                'companyid' => $vacancy->companyid,
            ],
            'hasexistingcommittee' => !empty($existingcommittee),
        ];

        if ($existingcommittee) {
            $data['existingcommittee'] = [
                'id' => $existingcommittee->id,
                'name' => format_string($existingcommittee->name),
                'membercount' => count($existingcommittee->members),
            ];
        }

        return $data;
    }

    /**
     * Render the bulk validation page.
     *
     * @param array $data Template data.
     * @return string Rendered HTML.
     */
    public function render_bulk_validate_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/bulk_validate', $data);
    }

    /**
     * Prepare bulk validation page data for template.
     *
     * @param int $vacancyid Vacancy filter ID (0 for all).
     * @param string $documenttype Document type filter.
     * @param \context $context Page context for capability checks.
     * @return array Template data.
     */
    public function prepare_bulk_validate_page_data(int $vacancyid, string $documenttype, \context $context): array {
        global $DB;

        $pageurl = new moodle_url('/local/jobboard/bulk_validate.php');

        // Get vacancy list for filter.
        $vacancies = $DB->get_records_select('local_jobboard_vacancy',
            "status IN ('published', 'closed')", null, 'code ASC', 'id, code, title');

        $vacanciesdata = [];
        foreach ($vacancies as $v) {
            $vacanciesdata[] = [
                'id' => $v->id,
                'label' => format_string($v->code . ' - ' . $v->title),
                'selected' => ($v->id == $vacancyid),
            ];
        }

        // Get pending documents by type.
        $pendingbytype = \local_jobboard\bulk_validator::get_pending_by_type($vacancyid ?: null);

        // Calculate stats.
        $totalPending = 0;
        $typeCount = count($pendingbytype);
        foreach ($pendingbytype as $dt) {
            $totalPending += $dt->count;
        }

        // Prepare pending by type data.
        $pendingtypedata = [];
        $documenttypesdata = [];
        foreach ($pendingbytype as $dt) {
            $typename = get_string('doctype_' . $dt->documenttype, 'local_jobboard');
            $isSelected = ($documenttype === $dt->documenttype);

            $pendingtypedata[] = [
                'documenttype' => $dt->documenttype,
                'typename' => $typename,
                'count' => (int) $dt->count,
                'isselected' => $isSelected,
                'selecturl' => (new moodle_url($pageurl, [
                    'vacancyid' => $vacancyid,
                    'documenttype' => $dt->documenttype,
                ]))->out(false),
            ];

            $documenttypesdata[] = [
                'code' => $dt->documenttype,
                'label' => $typename . ' (' . $dt->count . ')',
                'selected' => $isSelected,
            ];
        }

        // Rejection reasons.
        $reasons = ['illegible', 'expired', 'incomplete', 'wrongtype', 'mismatch'];
        $rejectreasons = [];
        foreach ($reasons as $reason) {
            $rejectreasons[] = [
                'code' => $reason,
                'label' => get_string('rejectreason_' . $reason, 'local_jobboard'),
            ];
        }

        // Base data.
        $data = [
            'pagetitle' => get_string('bulkvalidation', 'local_jobboard'),
            'stats' => [
                'totalpending' => $totalPending,
                'typecount' => $typeCount,
            ],
            'vacancies' => $vacanciesdata,
            'selectedvacancyid' => $vacancyid,
            'documenttypes' => $documenttypesdata,
            'selecteddocumenttype' => $documenttype,
            'pendingbytype' => $pendingtypedata,
            'haspendingbytype' => !empty($pendingtypedata),
            'rejectreasons' => $rejectreasons,
            'filterformurl' => $pageurl->out(false),
            'actionformurl' => $pageurl->out(false),
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'reviewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review']))->out(false),
            'assignreviewerurl' => (new moodle_url('/local/jobboard/assign_reviewer.php'))->out(false),
            'reportsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'reports']))->out(false),
            'canviewreports' => has_capability('local/jobboard:viewreports', $context),
            'sesskey' => sesskey(),
            'hastypeselected' => !empty($documenttype),
        ];

        // If document type selected, get the documents.
        if (!empty($documenttype)) {
            $documents = \local_jobboard\bulk_validator::get_pending_documents_by_type($documenttype, $vacancyid ?: null);
            $typename = get_string('doctype_' . $documenttype, 'local_jobboard');

            $documentsdata = [];
            foreach ($documents as $doc) {
                $docobj = \local_jobboard\document::get($doc->id);
                $downloadurl = $docobj ? $docobj->get_download_url() : null;

                $documentsdata[] = [
                    'id' => $doc->id,
                    'applicantname' => format_string($doc->firstname . ' ' . $doc->lastname),
                    'applicantemail' => $doc->email,
                    'vacancycode' => format_string($doc->vacancy_code),
                    'filename' => format_string($doc->filename),
                    'uploadeddate' => userdate($doc->timecreated, '%Y-%m-%d %H:%M'),
                    'downloadurl' => $downloadurl ? $downloadurl->out(false) : null,
                    'validateurl' => (new moodle_url('/local/jobboard/validate_document.php', ['id' => $doc->id]))->out(false),
                ];
            }

            $data['typename'] = $typename;
            $data['documents'] = $documentsdata;
            $data['hasdocuments'] = !empty($documentsdata);
            $data['documentcount'] = count($documentsdata);
        }

        return $data;
    }

    /**
     * Render the document validation page.
     *
     * @param array $data Template data.
     * @return string Rendered HTML.
     */
    public function render_validate_document_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/validate_document', $data);
    }

    /**
     * Prepare document validation page data for template.
     *
     * @param \local_jobboard\document $document The document to validate.
     * @param \local_jobboard\application $application The related application.
     * @return array Template data.
     */
    public function prepare_validate_document_page_data(
        \local_jobboard\document $document,
        \local_jobboard\application $application
    ): array {
        global $DB;

        // Get applicant user.
        $applicant = $DB->get_record('user', ['id' => $application->userid]);

        // Get document type name.
        $typename = get_string('doctype_' . $document->documenttype, 'local_jobboard');

        // Prepare document data.
        $documentdata = [
            'id' => $document->id,
            'type' => $document->documenttype,
            'typename' => $typename,
            'filename' => format_string($document->filename),
            'uploadeddate' => userdate($document->timecreated, get_string('strftimedatetime', 'langconfig')),
            'issuedate' => !empty($document->issuedate) ?
                userdate($document->issuedate, get_string('strftimedate', 'langconfig')) : null,
        ];

        // Get preview info.
        $downloadurl = $document->get_download_url();
        $previewinfo = \local_jobboard\document_services::get_preview_info($document);

        $previewurl = $previewinfo['url'] ?: ($downloadurl ? $downloadurl->out(false) : null);
        $previewmime = $previewinfo['mimetype'];

        // Determine preview capabilities.
        $canpreview = ($previewinfo['status'] === 'ready' && $previewinfo['url']);
        $directpreview = in_array($document->mimetype, \local_jobboard\document_services::DIRECT_PREVIEW_MIMETYPES);
        $showpreview = $canpreview || $directpreview;

        $ispdf = ($previewmime === 'application/pdf' && $previewinfo['url']);
        $isimage = (strpos($document->mimetype, 'image/') === 0);

        // Get status color.
        $statuscolor = 'secondary';
        if ($previewinfo['status'] === 'ready') {
            $statuscolor = 'success';
        } else if ($previewinfo['status'] === 'converting') {
            $statuscolor = 'info';
        } else if ($previewinfo['status'] === 'failed') {
            $statuscolor = 'danger';
        }

        $previewdata = [
            'downloadurl' => $downloadurl ? $downloadurl->out(false) : null,
            'previewurl' => $previewurl,
            'mimetype' => $previewmime,
            'canpreview' => $showpreview,
            'isconverting' => ($previewinfo['status'] === 'converting'),
            'isfailed' => ($previewinfo['can_convert'] && $previewinfo['status'] === 'failed'),
            'ispdf' => $ispdf,
            'isimage' => $isimage,
            'showstatus' => $previewinfo['can_convert'],
            'statuscolor' => $statuscolor,
            'statusmessage' => \local_jobboard\document_services::get_status_message($previewinfo['status']),
        ];

        // Prepare applicant data.
        $applicantdata = [
            'fullname' => fullname($applicant),
            'email' => $applicant->email,
            'idnumber' => !empty($applicant->idnumber) ? format_string($applicant->idnumber) : null,
        ];

        // Get validation checklist.
        $checklistdata = [];
        $checklistitems = $this->get_validation_checklist($document->documenttype);
        $idx = 0;
        foreach ($checklistitems as $item) {
            $checklistdata[] = [
                'id' => $idx++,
                'text' => $item,
            ];
        }

        // Rejection reasons.
        $reasons = [
            'illegible' => get_string('rejectreason_illegible', 'local_jobboard'),
            'expired' => get_string('rejectreason_expired', 'local_jobboard'),
            'incomplete' => get_string('rejectreason_incomplete', 'local_jobboard'),
            'wrongtype' => get_string('rejectreason_wrongtype', 'local_jobboard'),
            'mismatch' => get_string('rejectreason_mismatch', 'local_jobboard'),
            'other' => get_string('other'),
        ];
        $rejectreasons = [];
        foreach ($reasons as $code => $label) {
            $rejectreasons[] = [
                'code' => $code,
                'label' => $label,
            ];
        }

        // Build URLs.
        $baseurl = new moodle_url('/local/jobboard/validate_document.php', ['id' => $document->id]);
        $backurl = new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id]);

        return [
            'pagetitle' => get_string('validatedocument', 'local_jobboard'),
            'document' => $documentdata,
            'applicant' => $applicantdata,
            'application' => ['id' => $application->id],
            'preview' => $previewdata,
            'checklist' => $checklistdata,
            'haschecklist' => !empty($checklistdata),
            'rejectreasons' => $rejectreasons,
            'approveformurl' => (new moodle_url($baseurl, ['action' => 'validate']))->out(false),
            'rejectformurl' => (new moodle_url($baseurl, ['action' => 'reject']))->out(false),
            'backurl' => $backurl->out(false),
            'sesskey' => sesskey(),
        ];
    }

    /**
     * Get validation checklist items for a document type.
     *
     * @param string $doctype Document type code.
     * @return array Checklist items.
     */
    protected function get_validation_checklist(string $doctype): array {
        $common = [
            get_string('checklist_legible', 'local_jobboard'),
            get_string('checklist_complete', 'local_jobboard'),
            get_string('checklist_namematch', 'local_jobboard'),
        ];

        $specific = [];
        switch ($doctype) {
            case 'cedula':
                $specific = [
                    get_string('checklist_cedula_number', 'local_jobboard'),
                    get_string('checklist_cedula_photo', 'local_jobboard'),
                ];
                break;
            case 'antecedentes_procuraduria':
            case 'antecedentes_contraloria':
            case 'antecedentes_policia':
            case 'rnmc':
            case 'sijin':
                $specific = [
                    get_string('checklist_background_date', 'local_jobboard'),
                    get_string('checklist_background_status', 'local_jobboard'),
                ];
                break;
            case 'titulo_pregrado':
            case 'titulo_postgrado':
            case 'titulo_especializacion':
            case 'titulo_maestria':
            case 'titulo_doctorado':
                $specific = [
                    get_string('checklist_title_institution', 'local_jobboard'),
                    get_string('checklist_title_date', 'local_jobboard'),
                    get_string('checklist_title_program', 'local_jobboard'),
                ];
                break;
            case 'acta_grado':
                $specific = [
                    get_string('checklist_acta_number', 'local_jobboard'),
                    get_string('checklist_acta_date', 'local_jobboard'),
                ];
                break;
            case 'tarjeta_profesional':
                $specific = [
                    get_string('checklist_tarjeta_number', 'local_jobboard'),
                    get_string('checklist_tarjeta_profession', 'local_jobboard'),
                ];
                break;
            case 'rut':
                $specific = [
                    get_string('checklist_rut_nit', 'local_jobboard'),
                    get_string('checklist_rut_updated', 'local_jobboard'),
                ];
                break;
            case 'eps':
                $specific = [
                    get_string('checklist_eps_active', 'local_jobboard'),
                    get_string('checklist_eps_entity', 'local_jobboard'),
                ];
                break;
            case 'pension':
                $specific = [
                    get_string('checklist_pension_fund', 'local_jobboard'),
                    get_string('checklist_pension_active', 'local_jobboard'),
                ];
                break;
            case 'certificado_medico':
                $specific = [
                    get_string('checklist_medical_date', 'local_jobboard'),
                    get_string('checklist_medical_aptitude', 'local_jobboard'),
                ];
                break;
            case 'libreta_militar':
                $specific = [
                    get_string('checklist_military_class', 'local_jobboard'),
                    get_string('checklist_military_number', 'local_jobboard'),
                ];
                break;
        }

        return array_merge($common, $specific);
    }

    /**
     * Render the assign reviewer page.
     *
     * @param array $data Template data.
     * @return string Rendered HTML.
     */
    public function render_assign_reviewer_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/assign_reviewer', $data);
    }

    /**
     * Prepare assign reviewer page data for template.
     *
     * @param int $vacancyid Vacancy filter ID (0 for all).
     * @return array Template data.
     */
    public function prepare_assign_reviewer_page_data(int $vacancyid): array {
        global $DB;

        $pageurl = new moodle_url('/local/jobboard/assign_reviewer.php');

        // Get vacancy list for filter.
        $vacancies = $DB->get_records_select('local_jobboard_vacancy',
            "status IN ('published', 'closed')", null, 'code ASC', 'id, code, title');

        $vacanciesdata = [];
        foreach ($vacancies as $v) {
            $vacanciesdata[] = [
                'id' => $v->id,
                'label' => format_string($v->code . ' - ' . $v->title),
                'selected' => ($v->id == $vacancyid),
            ];
        }

        // Get reviewers with workload.
        $reviewers = \local_jobboard\reviewer::get_all_with_workload();

        // Get unassigned applications.
        $filters = ['reviewerid_null' => true];
        if ($vacancyid) {
            $filters['vacancyid'] = $vacancyid;
        }
        $unassignedresult = \local_jobboard\application::get_list($filters, 'timecreated', 'ASC', 0, 100);
        $unassigned = $unassignedresult['applications'];

        // Calculate stats.
        $totalUnassigned = count($unassigned);
        $totalReviewers = count($reviewers);
        $totalAssigned = 0;
        foreach ($reviewers as $rev) {
            $totalAssigned += $rev->workload;
        }
        $avgWorkload = $totalReviewers > 0 ? round($totalAssigned / $totalReviewers, 1) : 0;

        // Prepare reviewers data.
        $reviewersdata = [];
        foreach ($reviewers as $rev) {
            $workloadcolor = 'success';
            if ($rev->workload > 15) {
                $workloadcolor = 'danger';
            } else if ($rev->workload > 10) {
                $workloadcolor = 'warning';
            }

            $workloadindicator = '';
            if ($rev->workload > 15) {
                $workloadindicator = ' [HIGH]';
            } else if ($rev->workload > 10) {
                $workloadindicator = ' [MEDIUM]';
            }

            $reviewersdata[] = [
                'id' => $rev->id,
                'fullname' => fullname($rev),
                'workload' => (int) $rev->workload,
                'workloadcolor' => $workloadcolor,
                'workloadindicator' => $workloadindicator,
                'hasstats' => isset($rev->stats),
                'stats' => isset($rev->stats) ? [
                    'reviewed' => $rev->stats['reviewed'] ?? 0,
                    'avgtime' => $rev->stats['avg_review_time'] ?? 0,
                ] : null,
            ];
        }

        // Prepare unassigned applications data.
        $unassigneddata = [];
        foreach ($unassigned as $app) {
            $statuscolor = 'secondary';
            if ($app->status === 'submitted') {
                $statuscolor = 'info';
            } else if ($app->status === 'under_review') {
                $statuscolor = 'warning';
            }

            $unassigneddata[] = [
                'id' => $app->id,
                'applicantname' => format_string($app->userfirstname . ' ' . $app->userlastname),
                'vacancycode' => format_string($app->vacancy_code ?? ''),
                'status' => $app->status,
                'statuscolor' => $statuscolor,
                'statustext' => get_string('status_' . $app->status, 'local_jobboard'),
                'dateapplied' => userdate($app->timecreated, '%Y-%m-%d %H:%M'),
            ];
        }

        return [
            'pagetitle' => get_string('assignreviewer', 'local_jobboard'),
            'stats' => [
                'unassigned' => $totalUnassigned,
                'reviewers' => $totalReviewers,
                'assigned' => $totalAssigned,
                'avgworkload' => $avgWorkload,
            ],
            'vacancies' => $vacanciesdata,
            'selectedvacancyid' => $vacancyid,
            'reviewers' => $reviewersdata,
            'hasreviewers' => !empty($reviewersdata),
            'unassigned' => $unassigneddata,
            'hasunassigned' => !empty($unassigneddata),
            'filterformurl' => $pageurl->out(false),
            'actionformurl' => $pageurl->out(false),
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'reviewurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'review']))->out(false),
            'bulkvalidateurl' => (new moodle_url('/local/jobboard/bulk_validate.php'))->out(false),
            'sesskey' => sesskey(),
        ];
    }

    /**
     * Render the schedule interview page.
     *
     * @param array $data Template data.
     * @return string Rendered HTML.
     */
    public function render_schedule_interview_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/schedule_interview', $data);
    }

    /**
     * Prepare schedule interview page data for template.
     *
     * @param int $applicationid Application ID.
     * @param object $applicant Applicant user object.
     * @param object $vacancy Vacancy record.
     * @param object $application Application record.
     * @param string $formhtml Rendered form HTML.
     * @return array Template data.
     */
    public function prepare_schedule_interview_page_data(
        int $applicationid,
        object $applicant,
        object $vacancy,
        object $application,
        string $formhtml
    ): array {
        // Get existing interviews.
        $interviews = \local_jobboard\interview::get_for_application($applicationid);

        $interviewsdata = [];
        foreach ($interviews as $int) {
            // Status color.
            $statuscolor = 'secondary';
            if ($int->status === 'confirmed') {
                $statuscolor = 'success';
            } else if ($int->status === 'completed') {
                $statuscolor = 'info';
            } else if (in_array($int->status, ['cancelled', 'noshow'])) {
                $statuscolor = 'danger';
            } else if ($int->status === 'rescheduled') {
                $statuscolor = 'warning';
            }

            // Result.
            $hasresult = ($int->status === 'completed' && !empty($int->recommendation));
            $resultcolor = 'secondary';
            $resulttext = '';
            if ($hasresult) {
                $resultcolor = $int->recommendation === 'hire' ? 'success' :
                    ($int->recommendation === 'reject' ? 'danger' : 'warning');
                $resulttext = get_string('recommend_' . $int->recommendation, 'local_jobboard');
            }

            // Can act (complete, noshow, cancel).
            $canact = in_array($int->status, ['scheduled', 'confirmed']);

            $baseurl = new moodle_url('/local/jobboard/schedule_interview.php', ['application' => $applicationid]);

            $interviewsdata[] = [
                'id' => $int->id,
                'datetime' => userdate($int->scheduledtime, get_string('strftimedatetime', 'langconfig')),
                'duration' => $int->duration,
                'typename' => get_string('interviewtype_' . $int->interviewtype, 'local_jobboard'),
                'location' => format_string($int->location),
                'status' => $int->status,
                'statuscolor' => $statuscolor,
                'statustext' => get_string('interviewstatus_' . $int->status, 'local_jobboard'),
                'hasresult' => $hasresult,
                'resultcolor' => $resultcolor,
                'resulttext' => $resulttext,
                'rating' => $int->rating ?? 0,
                'canact' => $canact,
                'completeurl' => $canact ? (new moodle_url($baseurl, ['id' => $int->id, 'action' => 'complete']))->out(false) : null,
                'noshowurl' => $canact ? (new moodle_url($baseurl, ['id' => $int->id, 'action' => 'noshow', 'sesskey' => sesskey()]))->out(false) : null,
                'cancelurl' => $canact ? (new moodle_url($baseurl, ['id' => $int->id, 'action' => 'cancel', 'sesskey' => sesskey()]))->out(false) : null,
            ];
        }

        return [
            'pagetitle' => get_string('scheduleinterview', 'local_jobboard'),
            'applicant' => [
                'fullname' => fullname($applicant),
                'email' => $applicant->email,
            ],
            'vacancy' => [
                'code' => format_string($vacancy->code),
                'title' => format_string($vacancy->title),
            ],
            'application' => [
                'id' => $application->id,
                'status' => $application->status,
                'statustext' => get_string('status_' . $application->status, 'local_jobboard'),
            ],
            'interviews' => $interviewsdata,
            'hasinterviews' => !empty($interviewsdata),
            'formhtml' => $formhtml,
            'backurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $applicationid]))->out(false),
        ];
    }

    /**
     * Render the manage exemptions page.
     *
     * @param array $data Template data.
     * @return string Rendered HTML.
     */
    public function render_manage_exemptions_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/manage_exemptions', $data);
    }

    /**
     * Prepare manage exemptions page data for template.
     *
     * @param string $search Search query.
     * @param string $type Exemption type filter.
     * @param string $status Status filter.
     * @param int $page Current page.
     * @param int $perpage Items per page.
     * @param \context $context Page context.
     * @return array Template data.
     */
    public function prepare_manage_exemptions_page_data(
        string $search,
        string $type,
        string $status,
        int $page,
        int $perpage,
        \context $context
    ): array {
        global $DB, $OUTPUT;

        $pageurl = new moodle_url('/local/jobboard/manage_exemptions.php');
        $now = time();

        // Statistics.
        $activecnt = $DB->count_records_sql("
            SELECT COUNT(*) FROM {local_jobboard_exemption}
             WHERE timerevoked IS NULL
               AND validfrom <= :now1
               AND (validuntil IS NULL OR validuntil > :now2)
        ", ['now1' => $now, 'now2' => $now]);

        $expiredcnt = $DB->count_records_sql("
            SELECT COUNT(*) FROM {local_jobboard_exemption}
             WHERE timerevoked IS NULL
               AND validuntil IS NOT NULL AND validuntil < :now
        ", ['now' => $now]);

        $revokedcnt = $DB->count_records_select('local_jobboard_exemption', 'timerevoked IS NOT NULL');
        $totalcnt = $activecnt + $expiredcnt + $revokedcnt;

        // Type options.
        $typesoptions = [['value' => '', 'label' => get_string('all', 'local_jobboard'), 'selected' => empty($type)]];
        $types = ['historico_iser', 'documentos_recientes', 'traslado_interno', 'recontratacion'];
        foreach ($types as $t) {
            $typesoptions[] = [
                'value' => $t,
                'label' => get_string('exemptiontype_' . $t, 'local_jobboard'),
                'selected' => ($type === $t),
            ];
        }

        // Status options.
        $statusoptions = [
            ['value' => '', 'label' => get_string('all', 'local_jobboard'), 'selected' => empty($status)],
            ['value' => 'active', 'label' => get_string('active', 'local_jobboard'), 'selected' => ($status === 'active')],
            ['value' => 'expired', 'label' => get_string('expired', 'local_jobboard'), 'selected' => ($status === 'expired')],
            ['value' => 'revoked', 'label' => get_string('revoked', 'local_jobboard'), 'selected' => ($status === 'revoked')],
        ];

        // Build query.
        $params = [];
        $whereclauses = ['1=1'];

        if ($search) {
            $whereclauses[] = $DB->sql_like("CONCAT(u.firstname, ' ', u.lastname)", ':search', false);
            $params['search'] = '%' . $DB->sql_like_escape($search) . '%';
        }

        if ($type) {
            $whereclauses[] = 'e.exemptiontype = :type';
            $params['type'] = $type;
        }

        if ($status === 'active') {
            $whereclauses[] = 'e.timerevoked IS NULL';
            $whereclauses[] = '(e.validuntil IS NULL OR e.validuntil > :now1)';
            $whereclauses[] = 'e.validfrom <= :now2';
            $params['now1'] = $now;
            $params['now2'] = $now;
        } else if ($status === 'expired') {
            $whereclauses[] = 'e.timerevoked IS NULL';
            $whereclauses[] = 'e.validuntil IS NOT NULL AND e.validuntil < :now';
            $params['now'] = $now;
        } else if ($status === 'revoked') {
            $whereclauses[] = 'e.timerevoked IS NOT NULL';
        }

        $whereclause = implode(' AND ', $whereclauses);

        // Count total.
        $countsql = "SELECT COUNT(*)
                       FROM {local_jobboard_exemption} e
                       JOIN {user} u ON u.id = e.userid
                      WHERE $whereclause";
        $totalcount = $DB->count_records_sql($countsql, $params);

        // Get exemptions.
        $sql = "SELECT e.*, u.firstname, u.lastname, u.email
                  FROM {local_jobboard_exemption} e
                  JOIN {user} u ON u.id = e.userid
                 WHERE $whereclause
                 ORDER BY e.timecreated DESC";
        $exemptions = $DB->get_records_sql($sql, $params, $page * $perpage, $perpage);

        // Prepare exemptions data.
        $exemptionsdata = [];
        foreach ($exemptions as $ex) {
            $isvalid = !$ex->timerevoked &&
                $ex->validfrom <= $now &&
                (!$ex->validuntil || $ex->validuntil > $now);

            $statuscolor = 'warning';
            $statustext = get_string('expired', 'local_jobboard');
            if ($ex->timerevoked) {
                $statuscolor = 'danger';
                $statustext = get_string('revoked', 'local_jobboard');
            } else if ($isvalid) {
                $statuscolor = 'success';
                $statustext = get_string('active', 'local_jobboard');
            }

            $doctypes = explode(',', $ex->exempteddoctypes);

            $exemptionsdata[] = [
                'id' => $ex->id,
                'fullname' => format_string($ex->firstname . ' ' . $ex->lastname),
                'email' => $ex->email,
                'typename' => get_string('exemptiontype_' . $ex->exemptiontype, 'local_jobboard'),
                'doctypescount' => count($doctypes),
                'validfrom' => userdate($ex->validfrom, '%Y-%m-%d'),
                'validuntil' => $ex->validuntil ? userdate($ex->validuntil, '%Y-%m-%d') : null,
                'statuscolor' => $statuscolor,
                'statustext' => $statustext,
                'isactive' => $isvalid,
                'viewurl' => (new moodle_url($pageurl, ['action' => 'view', 'id' => $ex->id]))->out(false),
                'editurl' => (new moodle_url($pageurl, ['action' => 'edit', 'id' => $ex->id]))->out(false),
                'revokeurl' => (new moodle_url($pageurl, ['action' => 'revoke', 'id' => $ex->id]))->out(false),
            ];
        }

        // Pagination.
        $pagination = '';
        if ($totalcount > $perpage) {
            $baseurl = new moodle_url($pageurl, ['search' => $search, 'type' => $type, 'status' => $status]);
            $pagination = $OUTPUT->paging_bar($totalcount, $page, $perpage, $baseurl);
        }

        return [
            'pagetitle' => get_string('manageexemptions', 'local_jobboard'),
            'stats' => [
                'total' => $totalcnt,
                'active' => $activecnt,
                'expired' => $expiredcnt,
                'revoked' => $revokedcnt,
            ],
            'filters' => [
                'search' => $search,
                'type' => $type,
                'status' => $status,
            ],
            'typesoptions' => $typesoptions,
            'statusoptions' => $statusoptions,
            'exemptions' => $exemptionsdata,
            'hasexemptions' => !empty($exemptionsdata),
            'totalcount' => $totalcount,
            'pagination' => $pagination,
            'filterformurl' => $pageurl->out(false),
            'addurl' => (new moodle_url($pageurl, ['action' => 'add']))->out(false),
            'importurl' => (new moodle_url('/local/jobboard/import_exemptions.php'))->out(false),
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'doctypesurl' => (new moodle_url('/local/jobboard/admin/doctypes.php'))->out(false),
            'reportsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'reports']))->out(false),
            'canviewreports' => has_capability('local/jobboard:viewreports', $context),
        ];
    }
}
