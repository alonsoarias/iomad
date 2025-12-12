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
 * Dashboard renderer for Job Board plugin.
 *
 * Handles rendering of dashboard pages, statistics, and overview widgets.
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
 * Dashboard renderer class.
 *
 * Responsible for rendering dashboard-related UI components including
 * statistics cards, activity feeds, and overview widgets.
 */
class dashboard_renderer extends renderer_base {

    /**
     * Render main dashboard page.
     *
     * @param array $stats Dashboard statistics.
     * @param array $recentactivity Recent activity items.
     * @param array $pendingactions Pending actions for user.
     * @param bool $canmanage Whether user can manage.
     * @return string HTML output.
     */
    public function render_dashboard(
        array $stats,
        array $recentactivity = [],
        array $pendingactions = [],
        bool $canmanage = false
    ): string {
        $data = [
            'stats' => $this->prepare_dashboard_stats($stats),
            'recentactivity' => $this->prepare_activity_items($recentactivity),
            'hasactivity' => !empty($recentactivity),
            'pendingactions' => $this->prepare_pending_actions($pendingactions),
            'haspending' => !empty($pendingactions),
            'canmanage' => $canmanage,
            'quicklinks' => $this->get_quick_links($canmanage),
        ];

        return $this->render_from_template('local_jobboard/dashboard', $data);
    }

    /**
     * Prepare dashboard statistics data.
     *
     * @param array $stats Raw statistics.
     * @return array Formatted statistics for template.
     */
    protected function prepare_dashboard_stats(array $stats): array {
        return [
            'totalvacancies' => $stats['totalvacancies'] ?? 0,
            'openvacancies' => $stats['openvacancies'] ?? 0,
            'totalapplications' => $stats['totalapplications'] ?? 0,
            'pendingapplications' => $stats['pendingapplications'] ?? 0,
            'approvedapplications' => $stats['approvedapplications'] ?? 0,
            'rejectedapplications' => $stats['rejectedapplications'] ?? 0,
            'totalconvocatorias' => $stats['totalconvocatorias'] ?? 0,
            'openconvocatorias' => $stats['openconvocatorias'] ?? 0,
            'pendingdocuments' => $stats['pendingdocuments'] ?? 0,
            'pendinginterviews' => $stats['pendinginterviews'] ?? 0,
        ];
    }

    /**
     * Prepare activity items for display.
     *
     * @param array $activities Raw activity items.
     * @return array Formatted activity items.
     */
    protected function prepare_activity_items(array $activities): array {
        $items = [];
        foreach ($activities as $activity) {
            $items[] = [
                'id' => $activity->id ?? 0,
                'type' => $activity->type ?? 'general',
                'typeclass' => $this->get_activity_type_class($activity->type ?? 'general'),
                'icon' => $this->get_activity_icon($activity->type ?? 'general'),
                'title' => $activity->title ?? '',
                'description' => $activity->description ?? '',
                'username' => isset($activity->userid) ? $this->get_user_fullname($activity->userid) : '',
                'timecreated' => isset($activity->timecreated) ? $this->format_datetime($activity->timecreated) : '',
                'timeago' => isset($activity->timecreated) ? $this->format_time_ago($activity->timecreated) : '',
                'url' => $activity->url ?? '',
                'hasurl' => !empty($activity->url),
            ];
        }
        return $items;
    }

    /**
     * Prepare pending actions for display.
     *
     * @param array $actions Raw pending actions.
     * @return array Formatted pending actions.
     */
    protected function prepare_pending_actions(array $actions): array {
        $items = [];
        foreach ($actions as $action) {
            $items[] = [
                'type' => $action->type ?? 'task',
                'priority' => $action->priority ?? 'normal',
                'priorityclass' => $this->get_priority_class($action->priority ?? 'normal'),
                'title' => $action->title ?? '',
                'description' => $action->description ?? '',
                'duedate' => isset($action->duedate) ? $this->format_date($action->duedate) : '',
                'isoverdue' => isset($action->duedate) && $action->duedate < time(),
                'actionurl' => $action->url ?? '',
                'actionlabel' => $action->actionlabel ?? get_string('view', 'local_jobboard'),
            ];
        }
        return $items;
    }

    /**
     * Get quick links for dashboard.
     *
     * @param bool $canmanage Whether user can manage.
     * @return array Quick link items.
     */
    protected function get_quick_links(bool $canmanage): array {
        $links = [
            [
                'label' => get_string('browsevacancies', 'local_jobboard'),
                'url' => $this->get_url('vacancies'),
                'icon' => 'briefcase',
            ],
            [
                'label' => get_string('myapplications', 'local_jobboard'),
                'url' => $this->get_url('myapplications'),
                'icon' => 'file-text',
            ],
        ];

        if ($canmanage) {
            $links[] = [
                'label' => get_string('managevacancies', 'local_jobboard'),
                'url' => $this->get_url('manage'),
                'icon' => 'settings',
            ];
            $links[] = [
                'label' => get_string('reviewdashboard', 'local_jobboard'),
                'url' => $this->get_url('review'),
                'icon' => 'check-circle',
            ];
            $links[] = [
                'label' => get_string('reports', 'local_jobboard'),
                'url' => $this->get_url('reports'),
                'icon' => 'bar-chart-2',
            ];
        }

        return $links;
    }

    /**
     * Get CSS class for activity type.
     *
     * @param string $type Activity type.
     * @return string CSS class.
     */
    protected function get_activity_type_class(string $type): string {
        $classes = [
            'application' => 'info',
            'document' => 'primary',
            'vacancy' => 'success',
            'convocatoria' => 'warning',
            'review' => 'secondary',
            'interview' => 'danger',
            'general' => 'dark',
        ];
        return $classes[$type] ?? 'secondary';
    }

    /**
     * Get icon for activity type.
     *
     * @param string $type Activity type.
     * @return string Icon name.
     */
    protected function get_activity_icon(string $type): string {
        $icons = [
            'application' => 'file-text',
            'document' => 'file',
            'vacancy' => 'briefcase',
            'convocatoria' => 'calendar',
            'review' => 'check-circle',
            'interview' => 'users',
            'general' => 'activity',
        ];
        return $icons[$type] ?? 'activity';
    }

    /**
     * Format time as "time ago" string.
     *
     * @param int $timestamp Timestamp to format.
     * @return string Time ago string.
     */
    protected function format_time_ago(int $timestamp): string {
        $diff = time() - $timestamp;

        if ($diff < 60) {
            return get_string('timeago:justnow', 'local_jobboard');
        } elseif ($diff < 3600) {
            $minutes = floor($diff / 60);
            return get_string('timeago:minutes', 'local_jobboard', $minutes);
        } elseif ($diff < 86400) {
            $hours = floor($diff / 3600);
            return get_string('timeago:hours', 'local_jobboard', $hours);
        } elseif ($diff < 604800) {
            $days = floor($diff / 86400);
            return get_string('timeago:days', 'local_jobboard', $days);
        } else {
            return $this->format_date($timestamp);
        }
    }

    /**
     * Render statistics card.
     *
     * @param string $title Card title.
     * @param int|string $value Main value.
     * @param string $icon Icon name.
     * @param string $type Card type (primary, success, warning, danger, info).
     * @param string $url Optional link URL.
     * @param string $subtitle Optional subtitle.
     * @return string HTML output.
     */
    public function render_stat_card(
        string $title,
        $value,
        string $icon = 'activity',
        string $type = 'primary',
        string $url = '',
        string $subtitle = ''
    ): string {
        return $this->render_from_template('local_jobboard/components/stat_card', [
            'title' => $title,
            'value' => $value,
            'icon' => $icon,
            'type' => $type,
            'url' => $url,
            'hasurl' => !empty($url),
            'subtitle' => $subtitle,
            'hassubtitle' => !empty($subtitle),
        ]);
    }

    /**
     * Render statistics grid.
     *
     * @param array $cards Array of card configurations.
     * @return string HTML output.
     */
    public function render_stats_grid(array $cards): string {
        return $this->render_from_template('local_jobboard/components/stats_grid', [
            'cards' => $cards,
            'hascards' => !empty($cards),
        ]);
    }

    /**
     * Render applicant dashboard.
     *
     * @param int $userid User ID.
     * @param array $applications User's applications.
     * @param array $suggestedvacancies Suggested vacancies.
     * @return string HTML output.
     */
    public function render_applicant_dashboard(
        int $userid,
        array $applications = [],
        array $suggestedvacancies = []
    ): string {
        $applicationrenderer = new application_renderer($this->page, $this->target);
        $vacancyrenderer = new vacancy_renderer($this->page, $this->target);

        // Prepare application summaries.
        $applicationdata = [];
        $statuscounts = [
            'draft' => 0,
            'submitted' => 0,
            'reviewing' => 0,
            'approved' => 0,
            'rejected' => 0,
        ];

        foreach ($applications as $app) {
            $applicationdata[] = $applicationrenderer->prepare_application_row_data($app, false, false);
            if (isset($statuscounts[$app->status])) {
                $statuscounts[$app->status]++;
            }
        }

        // Prepare suggested vacancies.
        $vacancydata = array_map(function($v) use ($vacancyrenderer) {
            return $vacancyrenderer->prepare_vacancy_card_data($v, true);
        }, $suggestedvacancies);

        $data = [
            'username' => $this->get_user_fullname($userid),
            'applications' => array_slice($applicationdata, 0, 5),
            'hasapplications' => !empty($applicationdata),
            'applicationcount' => count($applications),
            'viewallapplicationsurl' => $this->get_url('myapplications'),
            'suggestedvacancies' => array_slice($vacancydata, 0, 4),
            'hassuggestedvacancies' => !empty($vacancydata),
            'viewallvacanciesurl' => $this->get_url('vacancies'),
            'statuscounts' => $statuscounts,
            'hasdraft' => $statuscounts['draft'] > 0,
            'hassubmitted' => $statuscounts['submitted'] > 0,
            'hasreviewing' => $statuscounts['reviewing'] > 0,
        ];

        return $this->render_from_template('local_jobboard/applicant_dashboard', $data);
    }

    /**
     * Render manager dashboard.
     *
     * @param array $stats Manager statistics.
     * @param array $pendingapplications Applications pending review.
     * @param array $pendingdocuments Documents pending validation.
     * @param array $upcominginterviews Upcoming interviews.
     * @return string HTML output.
     */
    public function render_manager_dashboard(
        array $stats,
        array $pendingapplications = [],
        array $pendingdocuments = [],
        array $upcominginterviews = []
    ): string {
        $applicationrenderer = new application_renderer($this->page, $this->target);
        $reviewrenderer = new review_renderer($this->page, $this->target);

        $applicationdata = array_map(function($app) use ($applicationrenderer) {
            return $applicationrenderer->prepare_application_row_data($app, true, true);
        }, array_slice($pendingapplications, 0, 10));

        $documentdata = array_map(function($doc) use ($reviewrenderer) {
            return $reviewrenderer->prepare_document_row_data($doc, true);
        }, array_slice($pendingdocuments, 0, 10));

        $interviewdata = $this->prepare_interview_items($upcominginterviews);

        $data = [
            'stats' => $this->prepare_dashboard_stats($stats),
            'pendingapplications' => $applicationdata,
            'haspendingapplications' => !empty($applicationdata),
            'pendingapplicationcount' => count($pendingapplications),
            'pendingdocuments' => $documentdata,
            'haspendingdocuments' => !empty($documentdata),
            'pendingdocumentcount' => count($pendingdocuments),
            'upcominginterviews' => $interviewdata,
            'hasupcominginterviews' => !empty($interviewdata),
            'reviewurl' => $this->get_url('review'),
            'bulkvalidationurl' => $this->get_url('bulkvalidation'),
            'interviewsurl' => $this->get_url('interviews'),
        ];

        return $this->render_from_template('local_jobboard/manager_dashboard', $data);
    }

    /**
     * Prepare interview items for display.
     *
     * @param array $interviews Raw interview data.
     * @return array Formatted interview items.
     */
    protected function prepare_interview_items(array $interviews): array {
        $items = [];
        foreach ($interviews as $interview) {
            global $DB;
            $application = $DB->get_record('local_jobboard_application', ['id' => $interview->applicationid]);

            $items[] = [
                'id' => $interview->id,
                'applicationid' => $interview->applicationid,
                'applicantname' => $application ? $this->get_user_fullname($application->userid) : '',
                'scheduleddate' => $this->format_datetime($interview->scheduleddate),
                'location' => $interview->location ?? '',
                'isvirtual' => !empty($interview->meetingurl),
                'meetingurl' => $interview->meetingurl ?? '',
                'status' => $interview->status ?? 'scheduled',
                'statusclass' => $this->get_interview_status_class($interview->status ?? 'scheduled'),
                'viewurl' => $this->get_url('interview', ['id' => $interview->id]),
            ];
        }
        return $items;
    }

    /**
     * Get CSS class for interview status.
     *
     * @param string $status Interview status.
     * @return string CSS class.
     */
    protected function get_interview_status_class(string $status): string {
        $classes = [
            'scheduled' => 'info',
            'confirmed' => 'primary',
            'completed' => 'success',
            'cancelled' => 'danger',
            'rescheduled' => 'warning',
            'noshow' => 'dark',
        ];
        return $classes[$status] ?? 'secondary';
    }

    /**
     * Render activity timeline.
     *
     * @param array $activities Activity items.
     * @param string $title Optional title.
     * @return string HTML output.
     */
    public function render_activity_timeline(array $activities, string $title = ''): string {
        $items = $this->prepare_activity_items($activities);

        return $this->render_from_template('local_jobboard/components/activity_timeline', [
            'title' => $title ?: get_string('recentactivity', 'local_jobboard'),
            'items' => $items,
            'hasitems' => !empty($items),
        ]);
    }

    /**
     * Render company dashboard for IOMAD integration.
     *
     * @param int $companyid Company ID.
     * @param array $stats Company statistics.
     * @param array $vacancies Company vacancies.
     * @param array $applications Company applications.
     * @return string HTML output.
     */
    public function render_company_dashboard(
        int $companyid,
        array $stats,
        array $vacancies = [],
        array $applications = []
    ): string {
        global $DB;

        $company = $DB->get_record('company', ['id' => $companyid]);
        $vacancyrenderer = new vacancy_renderer($this->page, $this->target);
        $applicationrenderer = new application_renderer($this->page, $this->target);

        $vacancydata = array_map(function($v) use ($vacancyrenderer) {
            return $vacancyrenderer->prepare_vacancy_card_data($v, false);
        }, array_slice($vacancies, 0, 6));

        $applicationdata = array_map(function($app) use ($applicationrenderer) {
            return $applicationrenderer->prepare_application_row_data($app, true, true);
        }, array_slice($applications, 0, 10));

        $data = [
            'companyid' => $companyid,
            'companyname' => $company ? $company->name : '',
            'stats' => $this->prepare_dashboard_stats($stats),
            'vacancies' => $vacancydata,
            'hasvacancies' => !empty($vacancydata),
            'applications' => $applicationdata,
            'hasapplications' => !empty($applicationdata),
            'managevacanciesurl' => $this->get_url('manage', ['companyid' => $companyid]),
            'viewapplicationsurl' => $this->get_url('applications', ['companyid' => $companyid]),
            'createvacancyurl' => $this->get_url('manage', ['action' => 'create', 'companyid' => $companyid]),
        ];

        return $this->render_from_template('local_jobboard/company_dashboard', $data);
    }
}
