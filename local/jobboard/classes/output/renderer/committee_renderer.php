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
 * Committee renderer trait for Job Board plugin.
 *
 * Contains all committee and reviewer assignment rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait for committee rendering functionality.
 */
trait committee_renderer {

    /**
     * Render program reviewers page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_program_reviewers_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/program_reviewers', $data);
    }

    /**
     * Render committee page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_committee_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/committee', $data);
    }

    /**
     * Render assign reviewer page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_assign_reviewer_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/assign_reviewer', $data);
    }

    /**
     * Render schedule interview page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_schedule_interview_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/schedule_interview', $data);
    }

    /**
     * Render interview complete form page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_interview_complete_form_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/interview_complete_form', $data);
    }

    /**
     * Prepare program reviewers page data for template.
     *
     * @param int $categoryid Category/program ID (0 for list view).
     * @return array Template data.
     */
    public function prepare_program_reviewers_page_data(int $categoryid): array {
        global $DB;

        $pageurl = new moodle_url('/local/jobboard/admin/manage_program_reviewers.php');

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
                $indent = str_repeat('&nbsp;&nbsp;&nbsp;', (int)$cat->depth);
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
     * Prepare committee page data for template.
     *
     * @param int $companyid Company/faculty ID (0 for list view).
     * @param int $vacancyid Legacy vacancy ID.
     * @param string $usersearch User search string.
     * @return array Template data.
     */
    public function prepare_committee_page_data(int $companyid, int $vacancyid, string $usersearch = ''): array {
        global $DB;

        $pageurl = new moodle_url('/local/jobboard/admin/manage_committee.php');

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
     * Prepare interview completion form page data.
     *
     * @param object $applicant Applicant user object.
     * @param object $vacancy Vacancy record.
     * @param object $interviewdetails Interview details.
     * @param string $formhtml Rendered form HTML.
     * @return array Template data.
     */
    public function prepare_interview_complete_form_data(
        object $applicant,
        object $vacancy,
        object $interviewdetails,
        string $formhtml
    ): array {
        return [
            'pagetitle' => get_string('completeinterview', 'local_jobboard'),
            'applicantname' => fullname($applicant),
            'vacancytitle' => format_string($vacancy->title),
            'interviewdate' => userdate($interviewdetails->scheduledtime, get_string('strftimedatetime', 'langconfig')),
            'formhtml' => $formhtml,
            'str' => [
                'applicant' => get_string('applicant', 'local_jobboard'),
                'vacancy' => get_string('vacancy', 'local_jobboard'),
                'interviewdate' => get_string('interviewdate', 'local_jobboard'),
            ],
        ];
    }
}
