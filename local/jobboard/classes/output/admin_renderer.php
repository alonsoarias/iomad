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
 * Admin renderer for Job Board plugin.
 *
 * Handles rendering of administrative settings, configuration pages, and management interfaces.
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
 * Admin renderer class.
 *
 * Responsible for rendering administrative UI components including
 * settings pages, document type management, and system configuration.
 */
class admin_renderer extends renderer_base {

    /**
     * Render admin dashboard/index page.
     *
     * @param array $stats System statistics.
     * @param array $pendingactions Pending administrative actions.
     * @param array $systemhealth System health checks.
     * @return string HTML output.
     */
    public function render_admin_dashboard(
        array $stats,
        array $pendingactions = [],
        array $systemhealth = []
    ): string {
        $data = [
            'stats' => $this->prepare_admin_stats($stats),
            'pendingactions' => $this->prepare_admin_actions($pendingactions),
            'haspendingactions' => !empty($pendingactions),
            'systemhealth' => $this->prepare_health_checks($systemhealth),
            'hassystemhealth' => !empty($systemhealth),
            'quicklinks' => $this->get_admin_quick_links(),
        ];

        return $this->render_from_template('local_jobboard/admin_dashboard', $data);
    }

    /**
     * Prepare admin statistics.
     *
     * @param array $stats Raw statistics.
     * @return array Formatted statistics.
     */
    protected function prepare_admin_stats(array $stats): array {
        return [
            'totalusers' => $stats['totalusers'] ?? 0,
            'activeapplicants' => $stats['activeapplicants'] ?? 0,
            'totalvacancies' => $stats['totalvacancies'] ?? 0,
            'totalapplications' => $stats['totalapplications'] ?? 0,
            'totaldocuments' => $stats['totaldocuments'] ?? 0,
            'pendingdocuments' => $stats['pendingdocuments'] ?? 0,
            'totalcompanies' => $stats['totalcompanies'] ?? 0,
        ];
    }

    /**
     * Prepare administrative actions.
     *
     * @param array $actions Raw actions.
     * @return array Formatted actions.
     */
    protected function prepare_admin_actions(array $actions): array {
        $items = [];
        foreach ($actions as $action) {
            $items[] = [
                'type' => $action['type'] ?? 'task',
                'title' => $action['title'] ?? '',
                'description' => $action['description'] ?? '',
                'count' => $action['count'] ?? 0,
                'priority' => $action['priority'] ?? 'normal',
                'priorityclass' => $this->get_priority_class($action['priority'] ?? 'normal'),
                'actionurl' => $action['url'] ?? '',
                'actionlabel' => $action['actionlabel'] ?? get_string('view', 'local_jobboard'),
            ];
        }
        return $items;
    }

    /**
     * Prepare system health checks.
     *
     * @param array $checks Health check results.
     * @return array Formatted health checks.
     */
    protected function prepare_health_checks(array $checks): array {
        $items = [];
        foreach ($checks as $check) {
            $items[] = [
                'name' => $check['name'] ?? '',
                'status' => $check['status'] ?? 'unknown',
                'statusclass' => $this->get_health_status_class($check['status'] ?? 'unknown'),
                'statusicon' => $this->get_health_status_icon($check['status'] ?? 'unknown'),
                'message' => $check['message'] ?? '',
                'details' => $check['details'] ?? '',
                'hasdetails' => !empty($check['details']),
            ];
        }
        return $items;
    }

    /**
     * Get health status CSS class.
     *
     * @param string $status Health status.
     * @return string CSS class.
     */
    protected function get_health_status_class(string $status): string {
        $classes = [
            'ok' => 'success',
            'warning' => 'warning',
            'error' => 'danger',
            'unknown' => 'secondary',
        ];
        return $classes[$status] ?? 'secondary';
    }

    /**
     * Get health status icon.
     *
     * @param string $status Health status.
     * @return string Icon name.
     */
    protected function get_health_status_icon(string $status): string {
        $icons = [
            'ok' => 'check-circle',
            'warning' => 'alert-triangle',
            'error' => 'x-circle',
            'unknown' => 'help-circle',
        ];
        return $icons[$status] ?? 'help-circle';
    }

    /**
     * Get admin quick links.
     *
     * @return array Quick links.
     */
    protected function get_admin_quick_links(): array {
        return [
            [
                'label' => get_string('managedoctypes', 'local_jobboard'),
                'url' => $this->get_url('admin', ['section' => 'doctypes']),
                'icon' => 'file-text',
            ],
            [
                'label' => get_string('manageexemptions', 'local_jobboard'),
                'url' => $this->get_url('admin', ['section' => 'exemptions']),
                'icon' => 'shield',
            ],
            [
                'label' => get_string('emailtemplates', 'local_jobboard'),
                'url' => $this->get_url('admin', ['section' => 'emails']),
                'icon' => 'mail',
            ],
            [
                'label' => get_string('workflowsettings', 'local_jobboard'),
                'url' => $this->get_url('admin', ['section' => 'workflow']),
                'icon' => 'git-branch',
            ],
            [
                'label' => get_string('iomadsettings', 'local_jobboard'),
                'url' => $this->get_url('admin', ['section' => 'iomad']),
                'icon' => 'building',
            ],
            [
                'label' => get_string('reports', 'local_jobboard'),
                'url' => $this->get_url('reports'),
                'icon' => 'bar-chart-2',
            ],
        ];
    }

    /**
     * Render document type management page.
     *
     * @param array $doctypes Document types.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_doctype_management(array $doctypes, string $pagination = ''): string {
        $rows = $this->prepare_doctype_rows($doctypes);

        $data = [
            'doctypes' => $rows,
            'hasdoctypes' => !empty($rows),
            'count' => count($rows),
            'pagination' => $pagination,
            'createurl' => $this->get_url('admin', ['section' => 'doctypes', 'action' => 'create']),
        ];

        return $this->render_from_template('local_jobboard/admin_doctypes', $data);
    }

    /**
     * Prepare document type rows.
     *
     * @param array $doctypes Raw document types.
     * @return array Formatted rows.
     */
    protected function prepare_doctype_rows(array $doctypes): array {
        global $DB;

        $rows = [];
        foreach ($doctypes as $doctype) {
            $documentcount = $DB->count_records('local_jobboard_document', ['doctypeid' => $doctype->id]);

            $rows[] = [
                'id' => $doctype->id,
                'code' => $doctype->code,
                'name' => $doctype->name,
                'description' => $this->shorten_text($doctype->description ?? '', 100),
                'isrequired' => !empty($doctype->required),
                'validityperiod' => $doctype->validityperiod ?? 0,
                'hasvalidityperiod' => ($doctype->validityperiod ?? 0) > 0,
                'documentcount' => $documentcount,
                'enabled' => !empty($doctype->enabled),
                'enabledclass' => !empty($doctype->enabled) ? 'success' : 'secondary',
                'editurl' => $this->get_url('admin', [
                    'section' => 'doctypes',
                    'action' => 'edit',
                    'id' => $doctype->id,
                ]),
                'deleteurl' => $this->get_url('admin', [
                    'section' => 'doctypes',
                    'action' => 'delete',
                    'id' => $doctype->id,
                ]),
                'candelete' => $documentcount === 0,
            ];
        }
        return $rows;
    }

    /**
     * Render document type form page.
     *
     * @param \moodleform $form The document type form.
     * @param bool $isedit Whether editing existing type.
     * @param object|null $doctype Existing type if editing.
     * @return string HTML output.
     */
    public function render_doctype_form($form, bool $isedit = false, $doctype = null): string {
        $data = [
            'formhtml' => $form->render(),
            'isedit' => $isedit,
            'title' => $isedit ?
                get_string('editdoctype', 'local_jobboard') :
                get_string('createdoctype', 'local_jobboard'),
            'backurl' => $this->get_url('admin', ['section' => 'doctypes']),
        ];

        if ($isedit && $doctype) {
            $data['doctypecode'] = $doctype->code;
            $data['doctypename'] = $doctype->name;
        }

        return $this->render_from_template('local_jobboard/admin_doctype_form', $data);
    }

    /**
     * Render exemption management page.
     *
     * @param array $exemptions Exemptions.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_exemption_management(array $exemptions, string $pagination = ''): string {
        $rows = $this->prepare_exemption_rows($exemptions);

        $data = [
            'exemptions' => $rows,
            'hasexemptions' => !empty($rows),
            'count' => count($rows),
            'pagination' => $pagination,
            'createurl' => $this->get_url('admin', ['section' => 'exemptions', 'action' => 'create']),
        ];

        return $this->render_from_template('local_jobboard/admin_exemptions', $data);
    }

    /**
     * Prepare exemption rows.
     *
     * @param array $exemptions Raw exemptions.
     * @return array Formatted rows.
     */
    protected function prepare_exemption_rows(array $exemptions): array {
        global $DB;

        $rows = [];
        foreach ($exemptions as $exemption) {
            $doctype = $DB->get_record('local_jobboard_doctype', ['id' => $exemption->doctypeid]);

            $rows[] = [
                'id' => $exemption->id,
                'username' => $this->get_user_fullname($exemption->userid),
                'userid' => $exemption->userid,
                'doctypename' => $doctype ? $doctype->name : '',
                'reason' => $this->shorten_text($exemption->reason ?? '', 100),
                'grantedby' => $this->get_user_fullname($exemption->grantedby),
                'timegranted' => $this->format_datetime($exemption->timecreated),
                'expirydate' => $exemption->expirydate ? $this->format_date($exemption->expirydate) : '',
                'hasexpiry' => !empty($exemption->expirydate),
                'isexpired' => !empty($exemption->expirydate) && $exemption->expirydate < time(),
                'viewurl' => $this->get_url('admin', [
                    'section' => 'exemptions',
                    'action' => 'view',
                    'id' => $exemption->id,
                ]),
                'revokeurl' => $this->get_url('admin', [
                    'section' => 'exemptions',
                    'action' => 'revoke',
                    'id' => $exemption->id,
                ]),
            ];
        }
        return $rows;
    }

    /**
     * Render exemption form page.
     *
     * @param \moodleform $form The exemption form.
     * @param bool $isedit Whether editing existing exemption.
     * @return string HTML output.
     */
    public function render_exemption_form($form, bool $isedit = false): string {
        $data = [
            'formhtml' => $form->render(),
            'isedit' => $isedit,
            'title' => $isedit ?
                get_string('editexemption', 'local_jobboard') :
                get_string('createexemption', 'local_jobboard'),
            'backurl' => $this->get_url('admin', ['section' => 'exemptions']),
        ];

        return $this->render_from_template('local_jobboard/admin_exemption_form', $data);
    }

    /**
     * Render email template management page.
     *
     * @param array $templates Email templates.
     * @return string HTML output.
     */
    public function render_email_template_management(array $templates): string {
        $rows = $this->prepare_email_template_rows($templates);

        $data = [
            'templates' => $rows,
            'hastemplates' => !empty($rows),
            'count' => count($rows),
        ];

        return $this->render_from_template('local_jobboard/admin_email_templates', $data);
    }

    /**
     * Prepare email template rows.
     *
     * @param array $templates Raw templates.
     * @return array Formatted rows.
     */
    protected function prepare_email_template_rows(array $templates): array {
        $rows = [];
        foreach ($templates as $key => $template) {
            $rows[] = [
                'key' => $key,
                'name' => get_string('emailtemplate:' . $key, 'local_jobboard'),
                'description' => get_string('emailtemplate:' . $key . ':desc', 'local_jobboard'),
                'subject' => $template['subject'] ?? '',
                'enabled' => $template['enabled'] ?? true,
                'editurl' => $this->get_url('admin', [
                    'section' => 'emails',
                    'action' => 'edit',
                    'template' => $key,
                ]),
                'previewurl' => $this->get_url('admin', [
                    'section' => 'emails',
                    'action' => 'preview',
                    'template' => $key,
                ]),
            ];
        }
        return $rows;
    }

    /**
     * Render email template edit form.
     *
     * @param string $templatekey Template key.
     * @param \moodleform $form The template form.
     * @param array $placeholders Available placeholders.
     * @return string HTML output.
     */
    public function render_email_template_form(
        string $templatekey,
        $form,
        array $placeholders = []
    ): string {
        $placeholderdata = [];
        foreach ($placeholders as $key => $description) {
            $placeholderdata[] = [
                'placeholder' => '{{' . $key . '}}',
                'description' => $description,
            ];
        }

        $data = [
            'formhtml' => $form->render(),
            'templatename' => get_string('emailtemplate:' . $templatekey, 'local_jobboard'),
            'placeholders' => $placeholderdata,
            'hasplaceholders' => !empty($placeholderdata),
            'backurl' => $this->get_url('admin', ['section' => 'emails']),
        ];

        return $this->render_from_template('local_jobboard/admin_email_template_form', $data);
    }

    /**
     * Render workflow settings page.
     *
     * @param \moodleform $form The workflow settings form.
     * @param array $currentworkflow Current workflow configuration.
     * @return string HTML output.
     */
    public function render_workflow_settings($form, array $currentworkflow = []): string {
        $stages = $this->prepare_workflow_stages($currentworkflow);

        $data = [
            'formhtml' => $form->render(),
            'stages' => $stages,
            'hasstages' => !empty($stages),
        ];

        return $this->render_from_template('local_jobboard/admin_workflow_settings', $data);
    }

    /**
     * Prepare workflow stages for visualization.
     *
     * @param array $workflow Workflow configuration.
     * @return array Stage data.
     */
    protected function prepare_workflow_stages(array $workflow): array {
        $stages = $workflow['stages'] ?? [
            'submitted' => ['next' => ['reviewing', 'rejected'], 'label' => 'Submitted'],
            'reviewing' => ['next' => ['interview', 'approved', 'rejected'], 'label' => 'Under Review'],
            'interview' => ['next' => ['approved', 'rejected'], 'label' => 'Interview'],
            'approved' => ['next' => ['hired'], 'label' => 'Approved'],
            'rejected' => ['next' => [], 'label' => 'Rejected'],
            'hired' => ['next' => [], 'label' => 'Hired'],
        ];

        $result = [];
        foreach ($stages as $key => $stage) {
            $result[] = [
                'key' => $key,
                'label' => $stage['label'] ?? $key,
                'nextstages' => implode(', ', $stage['next'] ?? []),
                'hasnext' => !empty($stage['next']),
                'isfinal' => empty($stage['next']),
            ];
        }
        return $result;
    }

    /**
     * Render IOMAD integration settings.
     *
     * @param \moodleform $form The IOMAD settings form.
     * @param array $companies Available companies.
     * @return string HTML output.
     */
    public function render_iomad_settings($form, array $companies = []): string {
        $companydata = [];
        foreach ($companies as $company) {
            $companydata[] = [
                'id' => $company->id,
                'name' => $company->name,
                'shortname' => $company->shortname,
                'enabled' => !empty($company->jobboard_enabled),
                'vacancycount' => $company->vacancycount ?? 0,
                'settingsurl' => $this->get_url('admin', [
                    'section' => 'iomad',
                    'action' => 'company',
                    'companyid' => $company->id,
                ]),
            ];
        }

        $data = [
            'formhtml' => $form->render(),
            'companies' => $companydata,
            'hascompanies' => !empty($companydata),
        ];

        return $this->render_from_template('local_jobboard/admin_iomad_settings', $data);
    }

    /**
     * Render settings tabs navigation.
     *
     * @param string $activesection Currently active section.
     * @return string HTML output.
     */
    public function render_settings_tabs(string $activesection = 'general'): string {
        $tabs = [
            'general' => get_string('generalsettings', 'local_jobboard'),
            'doctypes' => get_string('documenttypes', 'local_jobboard'),
            'exemptions' => get_string('exemptions', 'local_jobboard'),
            'emails' => get_string('emailtemplates', 'local_jobboard'),
            'workflow' => get_string('workflowsettings', 'local_jobboard'),
            'iomad' => get_string('iomadsettings', 'local_jobboard'),
        ];

        $tabdata = [];
        foreach ($tabs as $key => $label) {
            $tabdata[] = [
                'key' => $key,
                'label' => $label,
                'url' => $this->get_url('admin', ['section' => $key]),
                'isactive' => $key === $activesection,
            ];
        }

        return $this->render_from_template('local_jobboard/admin_tabs', [
            'tabs' => $tabdata,
        ]);
    }

    /**
     * Render committee management page.
     *
     * @param array $committees Selection committees.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_committee_management(array $committees, string $pagination = ''): string {
        $rows = $this->prepare_committee_rows($committees);

        $data = [
            'committees' => $rows,
            'hascommittees' => !empty($rows),
            'count' => count($rows),
            'pagination' => $pagination,
            'createurl' => $this->get_url('admin', ['section' => 'committees', 'action' => 'create']),
        ];

        return $this->render_from_template('local_jobboard/admin_committees', $data);
    }

    /**
     * Prepare committee rows.
     *
     * @param array $committees Raw committees.
     * @return array Formatted rows.
     */
    protected function prepare_committee_rows(array $committees): array {
        global $DB;

        $rows = [];
        foreach ($committees as $committee) {
            $membercount = $DB->count_records('local_jobboard_committee_member', ['committeeid' => $committee->id]);

            $rows[] = [
                'id' => $committee->id,
                'name' => $committee->name,
                'description' => $this->shorten_text($committee->description ?? '', 100),
                'membercount' => $membercount,
                'convocatorianame' => $committee->convocatorianame ?? '',
                'status' => $committee->status ?? 'active',
                'statusclass' => ($committee->status ?? 'active') === 'active' ? 'success' : 'secondary',
                'editurl' => $this->get_url('admin', [
                    'section' => 'committees',
                    'action' => 'edit',
                    'id' => $committee->id,
                ]),
                'membersurl' => $this->get_url('admin', [
                    'section' => 'committees',
                    'action' => 'members',
                    'id' => $committee->id,
                ]),
                'deleteurl' => $this->get_url('admin', [
                    'section' => 'committees',
                    'action' => 'delete',
                    'id' => $committee->id,
                ]),
            ];
        }
        return $rows;
    }

    /**
     * Render bulk operations page.
     *
     * @param string $entitytype Entity type (applications, documents, etc.).
     * @param array $entities Entities to operate on.
     * @param array $availableactions Available bulk actions.
     * @return string HTML output.
     */
    public function render_bulk_operations(
        string $entitytype,
        array $entities,
        array $availableactions
    ): string {
        $actionoptions = [];
        foreach ($availableactions as $key => $label) {
            $actionoptions[] = [
                'value' => $key,
                'label' => $label,
            ];
        }

        $data = [
            'entitytype' => $entitytype,
            'entities' => $entities,
            'hasentities' => !empty($entities),
            'count' => count($entities),
            'actions' => $actionoptions,
            'hasactions' => !empty($actionoptions),
            'submiturl' => $this->get_url('admin', ['section' => 'bulk', 'type' => $entitytype]),
        ];

        return $this->render_from_template('local_jobboard/admin_bulk_operations', $data);
    }

    /**
     * Render audit log page.
     *
     * @param array $logs Audit log entries.
     * @param array $filters Current filters.
     * @param string $pagination Pagination HTML.
     * @return string HTML output.
     */
    public function render_audit_log(
        array $logs,
        array $filters = [],
        string $pagination = ''
    ): string {
        $rows = [];
        foreach ($logs as $log) {
            $rows[] = [
                'id' => $log->id,
                'action' => $log->action,
                'actionlabel' => get_string('auditaction:' . $log->action, 'local_jobboard'),
                'entitytype' => $log->entitytype ?? '',
                'entityid' => $log->entityid ?? 0,
                'username' => isset($log->userid) ? $this->get_user_fullname($log->userid) : '',
                'ipaddress' => $log->ipaddress ?? '',
                'details' => $log->details ?? '',
                'timecreated' => $this->format_datetime($log->timecreated),
            ];
        }

        $data = [
            'logs' => $rows,
            'haslogs' => !empty($rows),
            'count' => count($rows),
            'filters' => $filters,
            'pagination' => $pagination,
            'exporturl' => $this->get_url('admin', ['section' => 'audit', 'action' => 'export']),
        ];

        return $this->render_from_template('local_jobboard/admin_audit_log', $data);
    }
}
