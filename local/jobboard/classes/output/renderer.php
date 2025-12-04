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
}
