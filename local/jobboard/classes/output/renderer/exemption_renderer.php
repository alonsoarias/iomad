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
 * Exemption renderer trait for Job Board plugin.
 *
 * Contains all exemption-related rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

/**
 * Trait for exemption rendering functionality.
 */
trait exemption_renderer {

    /**
     * Render manage exemptions page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_manage_exemptions_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/manage_exemptions', $data);
    }

    /**
     * Render exemption form page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_exemption_form_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/exemption_form', $data);
    }

    /**
     * Render exemption revoke page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_exemption_revoke_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/exemption_revoke', $data);
    }

    /**
     * Render exemption view page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_exemption_view_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/exemption_view', $data);
    }

    /**
     * Render import exemptions page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_import_exemptions_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/import_exemptions', $data);
    }

    /**
     * Render import exemptions results page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_import_exemptions_results_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/import_exemptions_results', $data);
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

        $pageurl = new moodle_url('/local/jobboard/admin/manage_exemptions.php');
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
            'importurl' => (new moodle_url('/local/jobboard/admin/import_exemptions.php'))->out(false),
            'dashboardurl' => (new moodle_url('/local/jobboard/index.php'))->out(false),
            'doctypesurl' => (new moodle_url('/local/jobboard/admin/doctypes.php'))->out(false),
            'reportsurl' => (new moodle_url('/local/jobboard/index.php', ['view' => 'reports']))->out(false),
            'canviewreports' => has_capability('local/jobboard:viewreports', $context),
        ];
    }

    /**
     * Prepare import exemptions page data.
     *
     * @param string $formhtml Rendered form HTML.
     * @return array Template data.
     */
    public function prepare_import_exemptions_data(string $formhtml): array {
        $sample = "email,exemptiontype,exempteddocs\n" .
                  "juan.perez@example.com,historico_iser,cedula|rut|eps\n" .
                  "maria.garcia@example.com,historico_iser,\n" .
                  "pedro.lopez@example.com,documentos_recientes,antecedentes_procuraduria|antecedentes_contraloria";

        return [
            'pagetitle' => get_string('importexemptions', 'local_jobboard'),
            'formhtml' => $formhtml,
            'sample' => $sample,
            'str' => [
                'instructions_title' => get_string('importinstructions', 'local_jobboard'),
                'instructions_text' => get_string('importinstructionstext', 'local_jobboard'),
                'required_columns' => get_string('requiredcolumns', 'local_jobboard'),
                'optional_columns' => get_string('optionalcolumns', 'local_jobboard'),
                'sample_csv' => get_string('samplecsv', 'local_jobboard'),
                'or' => get_string('or'),
                'user_identifier' => get_string('useridentifier', 'local_jobboard'),
                'exemptiontype_desc' => get_string('exemptiontype_desc', 'local_jobboard'),
                'exempteddocs_desc' => get_string('exempteddocs_desc', 'local_jobboard'),
                'documentref_desc' => get_string('documentref_desc', 'local_jobboard'),
                'notes_desc' => get_string('notes_desc', 'local_jobboard'),
            ],
        ];
    }

    /**
     * Prepare import exemptions results page data.
     *
     * @param array $results Import results.
     * @param bool $ispreview Whether in preview mode.
     * @return array Template data.
     */
    public function prepare_import_exemptions_results_data(array $results, bool $ispreview): array {
        $errors = array_slice($results['errors'] ?? [], 0, 20);
        $morecount = max(0, count($results['errors'] ?? []) - 20);

        return [
            'pagetitle' => get_string('importresults', 'local_jobboard'),
            'ispreview' => $ispreview,
            'haspreview' => !empty($results['preview']),
            'previewitems' => $results['preview'] ?? [],
            'previewtotal' => count($results['preview'] ?? []),
            'successcount' => $results['success'] ?? 0,
            'skippedcount' => $results['skipped'] ?? 0,
            'hasskipped' => ($results['skipped'] ?? 0) > 0,
            'haserrors' => !empty($results['errors']),
            'errors' => $errors,
            'hasmoreerrors' => $morecount > 0,
            'moreerrors' => $morecount,
            'continueurl' => (new \moodle_url('/local/jobboard/admin/manage_exemptions.php'))->out(false),
            'str' => [
                'preview_notice' => get_string('previewmodenotice', 'local_jobboard'),
                'row' => get_string('row', 'local_jobboard'),
                'user' => get_string('user'),
                'email' => get_string('email'),
                'exemptiontype' => get_string('exemptiontype', 'local_jobboard'),
                'numdocs' => get_string('numdocs', 'local_jobboard'),
                'preview_total' => get_string('previewtotal', 'local_jobboard', count($results['preview'] ?? [])),
                'preview_confirm' => get_string('previewconfirm', 'local_jobboard'),
                'import_complete' => get_string('importcomplete', 'local_jobboard'),
                'imported_success' => get_string('importedsuccess', 'local_jobboard', $results['success'] ?? 0),
                'imported_skipped' => get_string('importedskipped', 'local_jobboard', $results['skipped'] ?? 0),
                'import_errors' => get_string('importerrors', 'local_jobboard'),
                'and_more' => get_string('andmore', 'local_jobboard', $morecount),
                'continue' => get_string('continue'),
            ],
        ];
    }

    /**
     * Prepare exemption form page data.
     *
     * @param bool $isedit Whether editing an existing exemption.
     * @param string $formhtml Rendered form HTML.
     * @return array Template data.
     */
    public function prepare_exemption_form_data(bool $isedit, string $formhtml): array {
        $strdata = [
            'back' => get_string('back'),
            'editexemption' => get_string('editexemption', 'local_jobboard'),
            'addexemption' => get_string('addexemption', 'local_jobboard'),
        ];

        return [
            'pagetitle' => $isedit ? get_string('editexemption', 'local_jobboard') : get_string('addexemption', 'local_jobboard'),
            'backurl' => (new \moodle_url('/local/jobboard/admin/manage_exemptions.php'))->out(false),
            'isedit' => $isedit,
            'formhtml' => $formhtml,
            'str' => $strdata,
        ];
    }

    /**
     * Prepare exemption revoke page data.
     *
     * @param int $id Exemption ID.
     * @param object $user User record.
     * @return array Template data.
     */
    public function prepare_exemption_revoke_data(int $id, object $user): array {
        $strdata = [
            'revokeexemption' => get_string('revokeexemption', 'local_jobboard'),
            'confirmrevokeexemption_prefix' => get_string('confirmrevokeexemption', 'local_jobboard', ''),
            'confirmrevokeexemption_suffix' => '',
            'revokereason' => get_string('revokereason', 'local_jobboard'),
            'confirm' => get_string('confirm', 'local_jobboard'),
            'cancel' => get_string('cancel', 'local_jobboard'),
        ];

        return [
            'id' => $id,
            'userfullname' => fullname($user),
            'sesskey' => sesskey(),
            'cancelurl' => (new \moodle_url('/local/jobboard/admin/manage_exemptions.php'))->out(false),
            'str' => $strdata,
        ];
    }

    /**
     * Prepare exemption view page data.
     *
     * @param object $exemption Exemption record.
     * @param object $user User record.
     * @param object $createdby Created by user record.
     * @param bool $isvalid Whether exemption is valid.
     * @param array $usages Usage history.
     * @return array Template data.
     */
    public function prepare_exemption_view_data(
        object $exemption,
        object $user,
        object $createdby,
        bool $isvalid,
        array $usages
    ): array {
        global $DB;

        // Determine status badge.
        if ($exemption->timerevoked) {
            $statusbadge = ['label' => get_string('revoked', 'local_jobboard'), 'class' => 'jb-badge-danger'];
        } else if ($isvalid) {
            $statusbadge = ['label' => get_string('active', 'local_jobboard'), 'class' => 'jb-badge-success'];
        } else {
            $statusbadge = ['label' => get_string('expired', 'local_jobboard'), 'class' => 'jb-badge-warning'];
        }

        // Build doctype badges.
        $doctypesbadges = [];
        $doctypes = explode(',', $exemption->exempteddoctypes);
        foreach ($doctypes as $dt) {
            $dt = trim($dt);
            if ($dt) {
                $doctypesbadges[] = ['label' => get_string('doctype_' . $dt, 'local_jobboard')];
            }
        }

        // Revoked info.
        $revokedbyinfo = '';
        $revokereason = '';
        if ($exemption->timerevoked) {
            $revokedby = $DB->get_record('user', ['id' => $exemption->revokedby]);
            $revokedbyinfo = fullname($revokedby) . ' - ' . userdate($exemption->timerevoked, '%Y-%m-%d %H:%M');
            $revokereason = format_text($exemption->revokereason);
        }

        // Usage history.
        $usagehistory = [];
        foreach ($usages as $usage) {
            $usagehistory[] = [
                'vacancy' => format_string($usage->code . ' - ' . $usage->title),
                'date' => userdate($usage->timecreated, '%Y-%m-%d %H:%M'),
            ];
        }

        // String data.
        $strdata = [
            'exemptiondetails' => get_string('exemptiondetails', 'local_jobboard'),
            'back' => get_string('back'),
            'user' => get_string('user'),
            'exemptiontype' => get_string('exemptiontype', 'local_jobboard'),
            'documentref' => get_string('documentref', 'local_jobboard'),
            'exempteddocs' => get_string('exempteddocs', 'local_jobboard'),
            'validfrom' => get_string('validfrom', 'local_jobboard'),
            'validuntil' => get_string('validuntil', 'local_jobboard'),
            'notes' => get_string('notes', 'local_jobboard'),
            'createdby' => get_string('createdby', 'local_jobboard'),
            'revokedby' => get_string('revokedby', 'local_jobboard'),
            'revokereason' => get_string('revokereason', 'local_jobboard'),
            'edit' => get_string('edit', 'local_jobboard'),
            'revoke' => get_string('revoke', 'local_jobboard'),
            'exemptionusagehistory' => get_string('exemptionusagehistory', 'local_jobboard'),
            'vacancy' => get_string('vacancy', 'local_jobboard'),
            'date' => get_string('date'),
            'noexemptionusage' => get_string('noexemptionusage', 'local_jobboard'),
        ];

        return [
            'id' => $exemption->id,
            'backurl' => (new \moodle_url('/local/jobboard/admin/manage_exemptions.php'))->out(false),
            'userfullname' => fullname($user),
            'useremail' => $user->email,
            'exemptiontype' => get_string('exemptiontype_' . $exemption->exemptiontype, 'local_jobboard'),
            'documentref' => $exemption->documentref ?: '',
            'doctypesbadges' => $doctypesbadges,
            'validfrom' => userdate($exemption->validfrom, '%Y-%m-%d'),
            'validuntil' => $exemption->validuntil
                ? userdate($exemption->validuntil, '%Y-%m-%d')
                : get_string('noexpiry', 'local_jobboard'),
            'notes' => $exemption->notes ?: '',
            'createdbyinfo' => fullname($createdby) . ' - ' . userdate($exemption->timecreated, '%Y-%m-%d %H:%M'),
            'statusbadge' => $statusbadge,
            'isrevoked' => (bool)$exemption->timerevoked,
            'revokedbyinfo' => $revokedbyinfo,
            'revokereason' => $revokereason,
            'canmodify' => !$exemption->timerevoked && $isvalid,
            'editurl' => (new \moodle_url('/local/jobboard/admin/manage_exemptions.php', ['action' => 'edit', 'id' => $exemption->id]))->out(false),
            'revokeurl' => (new \moodle_url('/local/jobboard/admin/manage_exemptions.php', ['action' => 'revoke', 'id' => $exemption->id]))->out(false),
            'usagehistory' => $usagehistory,
            'hasusagehistory' => !empty($usagehistory),
            'str' => $strdata,
        ];
    }
}
