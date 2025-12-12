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
 * Public pages renderer trait for Job Board plugin.
 *
 * Contains all public-facing page rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

use moodle_url;

/**
 * Trait for public pages rendering functionality.
 */
trait public_renderer {

    /**
     * Render public vacancies page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_public_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/public/index', $data);
    }

    /**
     * Render public vacancy detail page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_public_detail(array $data): string {
        return $this->render_from_template('local_jobboard/pages/public/detail', $data);
    }

    /**
     * Render public convocatoria page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_public_convocatoria_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/public/convocatoria', $data);
    }

    /**
     * Render public vacancy page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_public_vacancy_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/public/vacancy', $data);
    }

    /**
     * Render browse convocatorias page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_browse_convocatorias_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/convocatorias/browse', $data);
    }

    /**
     * Render update profile page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_updateprofile_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/user/profile', $data);
    }

    /**
     * Render signup success page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_signup_success_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/user/signup_success', $data);
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
     * Prepare data for signup success page.
     *
     * @param string $useremail User email address.
     * @return array Template data.
     */
    public function prepare_signup_success_data(string $useremail): array {
        return [
            'useremail' => $useremail,
            'backurl' => (new \moodle_url('/local/jobboard/public.php'))->out(false),
            'loginurl' => (new \moodle_url('/login/index.php'))->out(false),
            'str' => [
                'title' => get_string('signup_success_title', 'local_jobboard'),
                'message' => get_string('signup_success_message', 'local_jobboard', $useremail),
                'instructions_title' => get_string('signup_email_instructions_title', 'local_jobboard'),
                'instruction_1' => get_string('signup_email_instruction_1', 'local_jobboard'),
                'instruction_2' => get_string('signup_email_instruction_2', 'local_jobboard'),
                'instruction_3' => get_string('signup_email_instruction_3', 'local_jobboard'),
                'check_spam' => get_string('signup_check_spam', 'local_jobboard'),
                'back_to_vacancies' => get_string('backtovacancies', 'local_jobboard'),
                'login' => get_string('login'),
            ],
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
     * Prepare update profile page data.
     *
     * @param object|null $vacancy Vacancy record or null.
     * @param string $formhtml Rendered form HTML.
     * @return array Template data.
     */
    public function prepare_updateprofile_data(?object $vacancy, string $formhtml): array {
        $strdata = [
            'signup_applying_for' => get_string('signup_applying_for', 'local_jobboard'),
            'code' => get_string('code', 'local_jobboard'),
        ];

        $vacancydata = null;
        if ($vacancy) {
            $vacancydata = [
                'title' => format_string($vacancy->title),
                'code' => format_string($vacancy->code),
            ];
        }

        return [
            'pagetitle' => get_string('updateprofile_title', 'local_jobboard'),
            'intro' => get_string('updateprofile_intro', 'local_jobboard'),
            'hasvacancy' => ($vacancy !== null),
            'vacancy' => $vacancydata,
            'formhtml' => $formhtml,
            'str' => $strdata,
        ];
    }
}
