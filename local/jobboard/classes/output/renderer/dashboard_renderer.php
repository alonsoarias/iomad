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
 * Dashboard renderer trait for Job Board plugin.
 *
 * Contains all dashboard-related rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

use moodle_url;

/**
 * Trait for dashboard rendering functionality.
 */
trait dashboard_renderer {

    /**
     * Render dashboard page using new template.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_dashboard_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin/dashboard', $data);
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

        // AGENTS.md Section 22.2: Add dashboard consolidated features.

        // 1. Quick access to next convocatoria closing (for all users).
        $data['nextconvocatoria'] = $this->prepare_next_convocatoria_data();
        $data['hasnextconvocatoria'] = !empty($data['nextconvocatoria']);

        // 2. Pending notifications (for current user).
        $data['pendingnotifications'] = $this->prepare_pending_notifications($userid);
        $data['haspendingnotifications'] = !empty($data['pendingnotifications']);
        $data['pendingnotificationcount'] = count($data['pendingnotifications']);

        // 3. Recent activity summary.
        $data['recentactivity'] = $this->prepare_recent_activity($userid, $caps);
        $data['hasrecentactivity'] = !empty($data['recentactivity']);

        // 4. Quick actions based on role (already partially implemented in sections).

        return $data;
    }

    /**
     * Get the next convocatoria closing soon.
     *
     * AGENTS.md Section 22.2: Quick access to last/next convocatoria.
     *
     * @return array|null Convocatoria data or null if none.
     */
    protected function prepare_next_convocatoria_data(): ?array {
        global $DB;

        // Get convocatoria open and closing soonest.
        $now = time();
        $sql = "SELECT c.id, c.code, c.name, c.enddate, c.status,
                       COUNT(DISTINCT v.id) as vacancycount,
                       COUNT(DISTINCT a.id) as applicationcount
                  FROM {local_jobboard_convocatoria} c
                  LEFT JOIN {local_jobboard_vacancy} v ON v.convocatoriaid = c.id
                  LEFT JOIN {local_jobboard_application} a ON a.vacancyid = v.id
                 WHERE c.status = 'open'
                   AND c.enddate > :now
                 GROUP BY c.id, c.code, c.name, c.enddate, c.status
                 ORDER BY c.enddate ASC
                 LIMIT 1";

        $conv = $DB->get_record_sql($sql, ['now' => $now]);

        if (!$conv) {
            return null;
        }

        $daysremaining = ceil(($conv->enddate - $now) / 86400);
        $isurgent = $daysremaining <= 7;
        $iscritical = $daysremaining <= 3;

        return [
            'id' => $conv->id,
            'code' => $conv->code,
            'name' => $conv->name,
            'enddate' => userdate($conv->enddate, '%d %b %Y'),
            'daysremaining' => $daysremaining,
            'vacancycount' => (int)$conv->vacancycount,
            'applicationcount' => (int)$conv->applicationcount,
            'isurgent' => $isurgent,
            'iscritical' => $iscritical,
            'alertclass' => $iscritical ? 'danger' : ($isurgent ? 'warning' : 'info'),
            'url' => (new moodle_url('/local/jobboard/index.php', [
                'view' => 'view_convocatoria',
                'id' => $conv->id,
            ]))->out(false),
            'browseurl' => (new moodle_url('/local/jobboard/index.php', [
                'view' => 'browse_convocatorias',
            ]))->out(false),
        ];
    }

    /**
     * Get pending notifications for user.
     *
     * AGENTS.md Section 22.2: Pending notifications.
     *
     * @param int $userid User ID.
     * @return array Notification items.
     */
    protected function prepare_pending_notifications(int $userid): array {
        global $DB;

        $notifications = $DB->get_records_select(
            'local_jobboard_notification',
            'userid = :userid AND status = :status',
            ['userid' => $userid, 'status' => 'pending'],
            'timecreated DESC',
            '*',
            0,
            5
        );

        $items = [];
        foreach ($notifications as $notif) {
            $items[] = [
                'id' => $notif->id,
                'template' => $notif->templatecode,
                'label' => get_string('notification_' . $notif->templatecode, 'local_jobboard'),
                'timecreated' => $this->format_time_ago((int)$notif->timecreated),
                'icon' => $this->get_notification_icon($notif->templatecode),
            ];
        }

        return $items;
    }

    /**
     * Get notification icon based on template code.
     *
     * @param string $templatecode Template code.
     * @return string Icon name.
     */
    protected function get_notification_icon(string $templatecode): string {
        $icons = [
            'application_received' => 'inbox',
            'docs_validated' => 'check-circle',
            'docs_rejected' => 'x-circle',
            'interview_scheduled' => 'calendar',
            'status_changed' => 'bell',
            'application_selected' => 'award',
            'application_rejected' => 'alert-circle',
        ];
        return $icons[$templatecode] ?? 'bell';
    }

    /**
     * Format timestamp as "time ago" string.
     *
     * @param int $timestamp Timestamp.
     * @return string Formatted time ago.
     */
    protected function format_time_ago(int $timestamp): string {
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return get_string('timeago_justnow', 'local_jobboard');
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return get_string('timeago_minutes', 'local_jobboard', $minutes);
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return get_string('timeago_hours', 'local_jobboard', $hours);
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return get_string('timeago_days', 'local_jobboard', $days);
        } else {
            return userdate($timestamp, '%d %b %Y');
        }
    }

    /**
     * Get recent activity for the dashboard.
     *
     * AGENTS.md Section 22.2: Recent activity summary.
     *
     * @param int $userid User ID.
     * @param array $caps User capabilities.
     * @return array Activity items.
     */
    protected function prepare_recent_activity(int $userid, array $caps): array {
        global $DB;

        $isadmin = $caps['configure'] ?? false;
        $ismanager = ($caps['createvacancy'] ?? false) || ($caps['manageconvocatorias'] ?? false);

        $activities = [];
        $limit = 5;

        if ($isadmin || $ismanager) {
            // Admin/Manager: see recent system activity.
            $sql = "SELECT id, action, entitytype, entityid, timecreated
                      FROM {local_jobboard_audit}
                     WHERE action IN ('application_submitted', 'document_uploaded', 'vacancy_published', 'convocatoria_opened')
                     ORDER BY timecreated DESC";
            $records = $DB->get_records_sql($sql, [], 0, $limit);

            foreach ($records as $rec) {
                $activities[] = [
                    'type' => $rec->entitytype,
                    'action' => $rec->action,
                    'label' => get_string('activity_' . $rec->action, 'local_jobboard'),
                    'icon' => $this->get_activity_icon($rec->action),
                    'color' => $this->get_activity_color($rec->action),
                    'timecreated' => $this->format_time_ago((int)$rec->timecreated),
                    'url' => $this->get_activity_url($rec->entitytype, $rec->entityid),
                ];
            }
        } else {
            // Regular user: see their own recent activity.
            $sql = "SELECT id, action, entitytype, entityid, timecreated
                      FROM {local_jobboard_audit}
                     WHERE userid = :userid
                       AND action IN ('application_submitted', 'document_uploaded', 'status_viewed')
                     ORDER BY timecreated DESC";
            $records = $DB->get_records_sql($sql, ['userid' => $userid], 0, $limit);

            foreach ($records as $rec) {
                $activities[] = [
                    'type' => $rec->entitytype,
                    'action' => $rec->action,
                    'label' => get_string('activity_' . $rec->action, 'local_jobboard'),
                    'icon' => $this->get_activity_icon($rec->action),
                    'color' => $this->get_activity_color($rec->action),
                    'timecreated' => $this->format_time_ago((int)$rec->timecreated),
                    'url' => $this->get_activity_url($rec->entitytype, $rec->entityid),
                ];
            }
        }

        return $activities;
    }

    /**
     * Get icon for activity action.
     *
     * @param string $action Activity action.
     * @return string Icon name.
     */
    protected function get_activity_icon(string $action): string {
        $icons = [
            'application_submitted' => 'send',
            'document_uploaded' => 'upload',
            'vacancy_published' => 'briefcase',
            'convocatoria_opened' => 'calendar',
            'status_viewed' => 'eye',
        ];
        return $icons[$action] ?? 'activity';
    }

    /**
     * Get color for activity action.
     *
     * @param string $action Activity action.
     * @return string Color name.
     */
    protected function get_activity_color(string $action): string {
        $colors = [
            'application_submitted' => 'success',
            'document_uploaded' => 'info',
            'vacancy_published' => 'primary',
            'convocatoria_opened' => 'warning',
            'status_viewed' => 'secondary',
        ];
        return $colors[$action] ?? 'secondary';
    }

    /**
     * Get URL for activity entity.
     *
     * @param string $entitytype Entity type.
     * @param int $entityid Entity ID.
     * @return string URL.
     */
    protected function get_activity_url(string $entitytype, int $entityid): string {
        $views = [
            'application' => 'application',
            'vacancy' => 'vacancy',
            'convocatoria' => 'view_convocatoria',
            'document' => 'application',
        ];

        $view = $views[$entitytype] ?? 'dashboard';
        return (new moodle_url('/local/jobboard/index.php', [
            'view' => $view,
            'id' => $entityid,
        ]))->out(false);
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
                'url' => (new moodle_url('/local/jobboard/admin/assign_reviewer.php'))->out(false),
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
                'url' => (new moodle_url('/local/jobboard/admin/bulk_validate.php'))->out(false),
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
                'url' => (new moodle_url('/local/jobboard/admin/manage_committee.php'))->out(false),
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
                'url' => (new moodle_url('/local/jobboard/admin/manage_program_reviewers.php'))->out(false),
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
                'url' => (new moodle_url('/local/jobboard/admin/import_vacancies.php'))->out(false),
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
                'url' => (new moodle_url('/local/jobboard/admin/manage_exemptions.php'))->out(false),
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

        // Migration Tool.
        if ($caps['configure'] ?? false) {
            $sections[] = [
                'id' => 'migrate',
                'title' => get_string('migrateplugin', 'local_jobboard'),
                'description' => get_string('migrateplugin_desc', 'local_jobboard'),
                'icon' => 'exchange-alt',
                'color' => 'dark',
                'url' => (new moodle_url('/local/jobboard/admin/migrate.php'))->out(false),
                'buttonlabel' => get_string('access', 'local_jobboard'),
                'buttonicon' => 'exchange-alt',
            ];
        }

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
}
