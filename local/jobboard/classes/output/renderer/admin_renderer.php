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
 * Admin renderer trait for Job Board plugin.
 *
 * Contains all admin-related rendering methods.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\output\renderer;

defined('MOODLE_INTERNAL') || die();

use moodle_url;

/**
 * Trait for admin rendering functionality.
 */
trait admin_renderer {

    /**
     * Render admin roles page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_admin_roles_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin/roles', $data);
    }

    /**
     * Render migrate page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_migrate_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin/migrate', $data);
    }

    /**
     * Render admin doctypes page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_admin_doctypes_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin/doctypes', $data);
    }

    /**
     * Render admin templates page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_admin_templates_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin/templates', $data);
    }

    /**
     * Render import vacancies page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_import_vacancies_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin/import_vacancies', $data);
    }

    /**
     * Render import vacancies results page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_import_vacancies_results_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin/import_vacancies_results', $data);
    }

    /**
     * Render admin template edit page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_admin_template_edit_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin/template_edit', $data);
    }

    /**
     * Render admin doctype form page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_admin_doctype_form_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin/doctype_form', $data);
    }

    /**
     * Render admin doctype confirm delete page.
     *
     * @param array $data Page data.
     * @return string HTML output.
     */
    public function render_admin_doctype_confirm_delete_page(array $data): string {
        return $this->render_from_template('local_jobboard/pages/admin/doctype_delete', $data);
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
            'committeeurl' => (new moodle_url('/local/jobboard/admin/manage_committee.php'))->out(false),
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
     * Prepare migration page data for template.
     *
     * @param array $exportcounts Export counts from local_jobboard_get_export_counts().
     * @param string $exportformhtml Rendered export form HTML.
     * @param string $importformhtml Rendered import form HTML.
     * @param array|null $dryrunresults Dry run results (if any).
     * @return array Template data.
     */
    public function prepare_migrate_page_data(
        array $exportcounts,
        string $exportformhtml,
        string $importformhtml,
        ?array $dryrunresults = null
    ): array {
        $data = [
            'pagetitle' => get_string('migrateplugin', 'local_jobboard'),
            'pagedesc' => get_string('migrateplugin_desc', 'local_jobboard'),
            'infotitle' => get_string('migrationinfo_title', 'local_jobboard'),
            'infodesc' => get_string('migrationinfo_desc', 'local_jobboard'),
            'exporttitle' => get_string('exportdata', 'local_jobboard'),
            'exportdesc' => get_string('exportdata_desc', 'local_jobboard'),
            'importtitle' => get_string('importdata', 'local_jobboard'),
            'importdesc' => get_string('importdata_desc', 'local_jobboard'),
            'importwarning' => get_string('importwarning', 'local_jobboard'),
            'backurl' => (new \moodle_url('/local/jobboard/index.php'))->out(false),
            'backtext' => get_string('backtodashboard', 'local_jobboard'),
            'hasdryrunresults' => false,

            // Export counts split into two columns.
            'exportcounts_left' => [
                ['label' => get_string('doctypes', 'local_jobboard'), 'count' => $exportcounts['doctypes']],
                ['label' => get_string('emailtemplates', 'local_jobboard'), 'count' => $exportcounts['email_templates']],
                ['label' => get_string('convocatorias', 'local_jobboard'), 'count' => $exportcounts['convocatorias']],
                ['label' => get_string('vacancies', 'local_jobboard'), 'count' => $exportcounts['vacancies']],
            ],
            'exportcounts_right' => [
                ['label' => get_string('applications', 'local_jobboard'), 'count' => $exportcounts['applications']],
                ['label' => get_string('documents', 'local_jobboard'), 'count' => $exportcounts['documents']],
                ['label' => get_string('exemptions', 'local_jobboard'), 'count' => $exportcounts['exemptions']],
                ['label' => get_string('files', 'local_jobboard'), 'count' => $exportcounts['files']],
            ],
            'hasfiles' => $exportcounts['files'] > 0,
            'filewarning' => get_string('exportwarning_files', 'local_jobboard'),
            'exportformhtml' => $exportformhtml,
            'importformhtml' => $importformhtml,
        ];

        // Handle dry run results.
        if ($dryrunresults !== null) {
            $data['hasdryrunresults'] = true;
            $data['dryrunmessages'] = [];
            foreach ($dryrunresults['messages'] as $msg) {
                $data['dryrunmessages'][] = ['message' => $msg];
            }
        }

        return $data;
    }

    /**
     * Prepare admin doctypes page data for template.
     *
     * @param array $doctypes Array of doctype records.
     * @param \moodle_url $pageurl Current page URL.
     * @return array Template data.
     */
    public function prepare_admin_doctypes_page_data(array $doctypes, \moodle_url $pageurl): array {
        global $DB;

        // Calculate stats.
        $stats = [
            'total' => count($doctypes),
            'enabled' => 0,
            'required' => 0,
            'conditional' => 0,
        ];

        foreach ($doctypes as $dt) {
            if ($dt->enabled) {
                $stats['enabled']++;
            }
            if (!empty($dt->isrequired)) {
                $stats['required']++;
            }
            if (!empty($dt->gender_condition) || !empty($dt->profession_exempt) || !empty($dt->age_exemption_threshold)) {
                $stats['conditional']++;
            }
        }

        // Prepare doctypes data.
        $doctypesdata = [];
        $totalcount = count($doctypes);
        $index = 0;

        foreach ($doctypes as $dt) {
            $index++;

            // Status badge.
            $statusbadge = $dt->enabled
                ? '<span class="jb-badge jb-badge-success">' . get_string('enabled', 'local_jobboard') . '</span>'
                : '<span class="jb-badge jb-badge-secondary">' . get_string('disabled', 'local_jobboard') . '</span>';

            // Required badge.
            $isrequired = $dt->isrequired ?? 0;
            $requiredbadge = $isrequired
                ? '<span class="jb-badge jb-badge-primary">' . get_string('yes') . '</span>'
                : '<span class="jb-badge jb-badge-light">' . get_string('no') . '</span>';

            // Build conditions display.
            $conditions = [];

            if (!empty($dt->gender_condition)) {
                if ($dt->gender_condition === 'M') {
                    $conditions[] = '<span class="jb-badge jb-badge-info">' . get_string('doc_condition_men_only', 'local_jobboard') . '</span>';
                } else if ($dt->gender_condition === 'F') {
                    $conditions[] = '<span class="jb-badge jb-badge-info">' . get_string('doc_condition_women_only', 'local_jobboard') . '</span>';
                }
            }

            if (!empty($dt->profession_exempt)) {
                $exemptlist = json_decode($dt->profession_exempt, true);
                if (is_array($exemptlist) && !empty($exemptlist)) {
                    $exemptnames = [];
                    foreach ($exemptlist as $edu) {
                        $stringkey = 'signup_edu_' . $edu;
                        if (get_string_manager()->string_exists($stringkey, 'local_jobboard')) {
                            $exemptnames[] = get_string($stringkey, 'local_jobboard');
                        } else {
                            $exemptnames[] = ucfirst($edu);
                        }
                    }
                    $conditions[] = '<span class="jb-badge jb-badge-warning">' .
                        get_string('doc_condition_profession_exempt', 'local_jobboard', implode(', ', $exemptnames)) . '</span>';
                }
            }

            if (!empty($dt->iserexempted)) {
                $conditions[] = '<span class="jb-badge jb-badge-secondary">' . get_string('doc_condition_iser_exempt', 'local_jobboard') . '</span>';
            }

            if (!empty($dt->age_exemption_threshold)) {
                $conditions[] = '<span class="jb-badge jb-badge-success">' .
                    get_string('age_exempt_notice', 'local_jobboard', (int) $dt->age_exemption_threshold) . '</span>';
            }

            if (!empty($dt->conditional_note)) {
                $conditions[] = '<span class="jb-badge jb-badge-light jb-border" title="' . s($dt->conditional_note) . '">' .
                    '<i class="fa fa-info-circle"></i> ' . get_string('hasnote', 'local_jobboard') . '</span>';
            }

            $conditionshtml = !empty($conditions) ? implode('<br>', $conditions) : '<span class="jb-text-muted">-</span>';

            // Name with translation.
            $displayname = get_string_manager()->string_exists('doctype_' . $dt->code, 'local_jobboard')
                ? get_string('doctype_' . $dt->code, 'local_jobboard')
                : $dt->name;

            // Category with translation.
            $categoryname = !empty($dt->category) && get_string_manager()->string_exists('doccategory_' . $dt->category, 'local_jobboard')
                ? get_string('doccategory_' . $dt->category, 'local_jobboard')
                : ($dt->category ?? '-');

            $doctypesdata[] = [
                'id' => $dt->id,
                'code' => format_string($dt->code),
                'displayname' => $displayname,
                'categoryname' => $categoryname,
                'sortorder' => $dt->sortorder,
                'statusbadge' => $statusbadge,
                'requiredbadge' => $requiredbadge,
                'conditionshtml' => $conditionshtml,
                'canmoveup' => $index > 1,
                'canmovedown' => $index < $totalcount,
                'moveupurl' => (new \moodle_url($pageurl, ['action' => 'moveup', 'id' => $dt->id, 'sesskey' => sesskey()]))->out(false),
                'movedownurl' => (new \moodle_url($pageurl, ['action' => 'movedown', 'id' => $dt->id, 'sesskey' => sesskey()]))->out(false),
                'editurl' => (new \moodle_url($pageurl, ['action' => 'edit', 'id' => $dt->id]))->out(false),
                'toggleurl' => (new \moodle_url($pageurl, ['action' => 'toggle', 'id' => $dt->id, 'sesskey' => sesskey()]))->out(false),
                'toggleicon' => $dt->enabled ? 'fa-eye-slash' : 'fa-eye',
                'toggletitle' => $dt->enabled ? get_string('disable') : get_string('enable'),
                'deleteurl' => (new \moodle_url($pageurl, ['action' => 'confirmdelete', 'id' => $dt->id]))->out(false),
            ];
        }

        return [
            'pagetitle' => get_string('managedoctypes', 'local_jobboard'),
            'stats' => $stats,
            'doctypes' => $doctypesdata,
            'hasdoctypes' => !empty($doctypesdata),
            'totalcount' => $totalcount,
            'addurl' => (new \moodle_url($pageurl, ['action' => 'add']))->out(false),
            'dashboardurl' => (new \moodle_url('/local/jobboard/index.php'))->out(false),
            'templatesurl' => (new \moodle_url('/local/jobboard/admin/templates.php'))->out(false),
            'pageurl' => $pageurl->out(false),
            'abouttitle' => get_string('aboutdoctypes', 'local_jobboard'),
            'aboutdesc' => get_string('doctypeshelp', 'local_jobboard'),
        ];
    }

    /**
     * Prepare admin templates page data for template.
     *
     * @param array $templates Array of template objects.
     * @param array $stats Statistics array.
     * @param array $categories All available categories.
     * @param string $currentcategory Currently selected category filter.
     * @param int $companyid Company ID for multi-tenant.
     * @param \moodle_url $pageurl Current page URL.
     * @return array Template data.
     */
    public function prepare_admin_templates_page_data(
        array $templates,
        array $stats,
        array $categories,
        string $currentcategory,
        int $companyid,
        \moodle_url $pageurl
    ): array {
        // Category badge class mapping.
        $badgeclasses = [
            'application' => 'jb-badge-primary',
            'documents' => 'jb-badge-info',
            'interview' => 'jb-badge-warning',
            'selection' => 'jb-badge-success',
            'system' => 'jb-badge-secondary',
        ];

        // Prepare categories data with counts.
        $categoriesdata = [];
        foreach ($categories as $cat) {
            $catTemplates = \local_jobboard\email_template::get_by_category($cat, $companyid);
            $categoriesdata[] = [
                'code' => $cat,
                'name' => \local_jobboard\email_template::get_category_name($cat),
                'count' => count($catTemplates),
                'url' => (new \moodle_url($pageurl, ['category' => $cat]))->out(false),
                'isactive' => ($currentcategory === $cat),
            ];
        }

        // Prepare templates data.
        $templatesdata = [];
        foreach ($templates as $tpl) {
            $categoryname = \local_jobboard\email_template::get_category_name($tpl->category);
            $categorybadgeclass = $badgeclasses[$tpl->category] ?? 'jb-badge-light';

            $templatesdata[] = [
                'id' => $tpl->id,
                'code' => $tpl->code,
                'name' => format_string($tpl->name),
                'categoryname' => $categoryname,
                'categorybadgeclass' => $categorybadgeclass,
                'subjectpreview' => shorten_text(strip_tags($tpl->subject), 40),
                'subjectfull' => format_string($tpl->subject),
                'enabled' => !empty($tpl->enabled),
                'isdisabled' => empty($tpl->enabled),
                'isdefault' => !empty($tpl->is_default),
                'hassavedid' => ($tpl->id > 0),
                'canreset' => ($tpl->id > 0 && empty($tpl->is_default)),
                'editurl' => (new \moodle_url($pageurl, [
                    'action' => 'edit',
                    'code' => $tpl->code,
                    'companyid' => $companyid,
                ]))->out(false),
                'toggleurl' => (new \moodle_url($pageurl, [
                    'action' => 'toggle',
                    'id' => $tpl->id,
                    'sesskey' => sesskey(),
                ]))->out(false),
                'toggleicon' => $tpl->enabled ? 'fa-toggle-on jb-text-success' : 'fa-toggle-off jb-text-secondary',
                'reseturl' => (new \moodle_url($pageurl, [
                    'action' => 'reset',
                    'code' => $tpl->code,
                    'companyid' => $companyid,
                    'sesskey' => sesskey(),
                ]))->out(false),
            ];
        }

        // Build string data.
        $strdata = [
            'emailtemplates' => get_string('email_templates', 'local_jobboard'),
            'dashboard' => get_string('dashboard', 'local_jobboard'),
            'installdefaults' => get_string('install_defaults', 'local_jobboard'),
            'totaltemplates' => get_string('total_templates', 'local_jobboard'),
            'templatesenabled' => get_string('templates_enabled', 'local_jobboard'),
            'templatesdisabled' => get_string('templates_disabled', 'local_jobboard'),
            'templatecategories' => get_string('template_categories', 'local_jobboard'),
            'all' => get_string('all', 'local_jobboard'),
            'notemplates' => get_string('no_templates', 'local_jobboard'),
            'templatename' => get_string('template_name', 'local_jobboard'),
            'templatecode' => get_string('template_code', 'local_jobboard'),
            'templatecategory' => get_string('template_category', 'local_jobboard'),
            'subject' => get_string('subject'),
            'status' => get_string('status'),
            'actions' => get_string('actions'),
            'default' => get_string('default'),
            'enabled' => get_string('enabled', 'local_jobboard'),
            'disabled' => get_string('disabled', 'local_jobboard'),
            'edit' => get_string('edit'),
            'togglestatus' => get_string('toggle_status', 'local_jobboard'),
            'resettodefault' => get_string('reset_to_default', 'local_jobboard'),
            'confirmreset' => get_string('confirm_reset', 'local_jobboard'),
            'templatehelptitle' => get_string('template_help_title', 'local_jobboard'),
            'placeholders' => get_string('placeholders', 'local_jobboard'),
            'templatehelpplaceholders' => get_string('template_help_placeholders', 'local_jobboard'),
            'htmlsupport' => get_string('html_support', 'local_jobboard'),
            'templatehelphtml' => get_string('template_help_html', 'local_jobboard'),
            'multitenant' => get_string('multi_tenant', 'local_jobboard'),
            'templatehelptenant' => get_string('template_help_tenant', 'local_jobboard'),
            'doctypes' => get_string('doctypes', 'local_jobboard'),
            'manageroles' => get_string('manageroles', 'local_jobboard'),
            'pluginsettings' => get_string('pluginsettings', 'local_jobboard'),
        ];

        return [
            'pageurl' => $pageurl->out(false),
            'stats' => [
                'total' => count($templates),
                'enabled' => $stats['enabled'] ?? 0,
                'disabled' => $stats['disabled'] ?? 0,
                'categories' => count($categories),
            ],
            'categories' => $categoriesdata,
            'currentcategory' => $currentcategory,
            'hastemplates' => !empty($templates),
            'templates' => $templatesdata,
            'installurl' => (new \moodle_url($pageurl, ['action' => 'install', 'sesskey' => sesskey()]))->out(false),
            'dashboardurl' => (new \moodle_url('/local/jobboard/index.php'))->out(false),
            'doctypesurl' => (new \moodle_url('/local/jobboard/admin/doctypes.php'))->out(false),
            'rolesurl' => (new \moodle_url('/local/jobboard/admin/roles.php'))->out(false),
            'settingsurl' => (new \moodle_url('/admin/settings.php', ['section' => 'local_jobboard']))->out(false),
            'str' => $strdata,
        ];
    }

    /**
     * Prepare import vacancies page data for template.
     *
     * @param int $convocatoriaid Convocatoria ID.
     * @param string $formhtml Rendered form HTML.
     * @return array Template data.
     */
    public function prepare_import_vacancies_page_data(int $convocatoriaid, string $formhtml): array {
        // CSV columns documentation.
        $csvcolumns = [
            ['name' => 'code', 'description' => get_string('csvcolumn_code', 'local_jobboard'), 'isrequired' => get_string('yes'), 'example' => 'FCAS-001'],
            ['name' => 'contracttype', 'description' => get_string('csvcolumn_contracttype', 'local_jobboard'), 'isrequired' => get_string('no'), 'example' => 'OCASIONAL TIEMPO COMPLETO'],
            ['name' => 'program', 'description' => get_string('csvcolumn_program', 'local_jobboard'), 'isrequired' => get_string('no'), 'example' => 'TECNOLOGÍA EN GESTIÓN COMUNITARIA'],
            ['name' => 'profile', 'description' => get_string('csvcolumn_profile', 'local_jobboard'), 'isrequired' => get_string('no'), 'example' => 'PROFESIONAL EN TRABAJO SOCIAL'],
            ['name' => 'courses', 'description' => get_string('csvcolumn_courses', 'local_jobboard'), 'isrequired' => get_string('no'), 'example' => 'CURSO1|CURSO2|CURSO3'],
            ['name' => 'location', 'description' => get_string('csvcolumn_location', 'local_jobboard'), 'isrequired' => get_string('no'), 'example' => 'PAMPLONA'],
            ['name' => 'modality', 'description' => get_string('csvcolumn_modality', 'local_jobboard'), 'isrequired' => get_string('no'), 'example' => 'PRESENCIAL'],
            ['name' => 'faculty', 'description' => get_string('csvcolumn_faculty', 'local_jobboard'), 'isrequired' => get_string('no'), 'example' => 'FCAS'],
        ];

        // CSV example content.
        $csvexample = "code,contracttype,program,profile,courses,location,modality,faculty\n" .
            "FCAS-001,OCASIONAL TIEMPO COMPLETO,TECNOLOGÍA EN GESTIÓN COMUNITARIA,PROFESIONAL EN TRABAJO SOCIAL,SISTEMATIZACIÓN DE EXPERIENCIAS|SUJETO Y FAMILIA|DIRECCIÓN DE TRABAJO DE GRADO,PAMPLONA,PRESENCIAL,FCAS\n" .
            "FCAS-002,CATEDRA,TECNOLOGÍA EN GESTIÓN EMPRESARIAL,ADMINISTRADOR DE EMPRESAS CON POSGRADO EN ÁREAS AFINES,EMPRENDIMIENTO|ADMINISTRACIÓN GENERAL,CUCUTA,A DISTANCIA,FCAS\n" .
            "FII-001,OCASIONAL TIEMPO COMPLETO,TECNOLOGÍA EN GESTIÓN INDUSTRIAL,INGENIERO INDUSTRIAL,ERGONOMÍA|GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO,PAMPLONA,PRESENCIAL,FII";

        // Build string data.
        $strdata = [
            'backtoconvocatorias' => get_string('backtoconvocatorias', 'local_jobboard'),
            'csvformat' => get_string('csvformat', 'local_jobboard'),
            'csvformatdesc' => get_string('csvformat_desc', 'local_jobboard'),
            'column' => get_string('column', 'local_jobboard'),
            'description' => get_string('description'),
            'required' => get_string('required', 'local_jobboard'),
            'example' => get_string('example', 'local_jobboard'),
            'csvexample' => get_string('csvexample', 'local_jobboard'),
            'csvexampledesc' => get_string('csvexample_desc', 'local_jobboard'),
            'csvexampletip' => get_string('csvexample_tip', 'local_jobboard'),
        ];

        return [
            'pagetitle' => get_string('importvacancies', 'local_jobboard'),
            'backurl' => (new \moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']))->out(false),
            'csvcolumns' => $csvcolumns,
            'csvexample' => $csvexample,
            'formhtml' => $formhtml,
            'str' => $strdata,
        ];
    }

    /**
     * Prepare import vacancies results data for template.
     *
     * @param array $results Import results array.
     * @param bool $ispreview Whether this is preview mode.
     * @param int $convocatoriaid Convocatoria ID.
     * @return array Template data.
     */
    public function prepare_import_vacancies_results_data(array $results, bool $ispreview, int $convocatoriaid): array {
        // Prepare preview rows.
        $previewrows = [];
        if ($ispreview && !empty($results['preview'])) {
            foreach ($results['preview'] as $row) {
                $isupdate = ($row['action'] === get_string('update'));
                $previewrows[] = [
                    'row' => $row['row'],
                    'code' => $row['code'],
                    'title' => $row['title'],
                    'location' => $row['location'],
                    'modality' => $row['modality'],
                    'contracttype' => $row['contracttype'],
                    'coursecount' => $row['courses'],
                    'action' => $row['action'],
                    'actionbadgeclass' => $isupdate ? 'jb-badge-warning' : 'jb-badge-success',
                ];
            }
        }

        // Prepare errors (max 20).
        $errors = [];
        $errorlist = array_slice($results['errors'] ?? [], 0, 20);
        foreach ($errorlist as $error) {
            $errors[] = ['message' => $error];
        }

        // Build string data.
        $strdata = [
            'importresults' => get_string('importresults', 'local_jobboard'),
            'previewmode' => get_string('previewmode', 'local_jobboard'),
            'row' => get_string('row', 'local_jobboard'),
            'code' => get_string('code', 'local_jobboard'),
            'title' => get_string('title', 'local_jobboard'),
            'location' => get_string('location'),
            'modality' => get_string('modality', 'local_jobboard'),
            'contracttype' => get_string('contracttype', 'local_jobboard'),
            'courses' => get_string('courses', 'local_jobboard'),
            'action' => get_string('action'),
            'previewconfirm' => get_string('previewconfirm', 'local_jobboard', count($previewrows)),
            'uploadnewfile' => get_string('uploadnewfile', 'local_jobboard'),
            'vacanciescreated' => get_string('vacancies_created', 'local_jobboard'),
            'vacanciesupdated' => get_string('vacancies_updated', 'local_jobboard'),
            'vacanciesskipped' => get_string('vacancies_skipped', 'local_jobboard'),
            'errors' => get_string('errors'),
            'andmore' => get_string('andmore', 'local_jobboard', count($results['errors'] ?? []) - 20),
            'backtoconvocatorias' => get_string('backtoconvocatorias', 'local_jobboard'),
        ];

        return [
            'pagetitle' => get_string('importresults', 'local_jobboard'),
            'ispreview' => $ispreview,
            'haspreviewrows' => !empty($previewrows),
            'previewrows' => $previewrows,
            'results' => [
                'created' => $results['created'] ?? 0,
                'updated' => $results['updated'] ?? 0,
                'skipped' => $results['skipped'] ?? 0,
            ],
            'convocatoriaid' => $convocatoriaid,
            'backurl' => (new \moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']))->out(false),
            'uploadurl' => (new \moodle_url('/local/jobboard/admin/import_vacancies.php', ['convocatoriaid' => $convocatoriaid]))->out(false),
            'haserrors' => !empty($errors),
            'errors' => $errors,
            'hasmoreerrors' => count($results['errors'] ?? []) > 20,
            'str' => $strdata,
        ];
    }

    /**
     * Prepare admin template edit page data.
     *
     * @param string $code Template code.
     * @param string $templatename Template display name.
     * @param string $formhtml Rendered form HTML.
     * @param \moodle_url $pageurl Page URL.
     * @return array Template data.
     */
    public function prepare_admin_template_edit_data(
        string $code,
        string $templatename,
        string $formhtml,
        \moodle_url $pageurl
    ): array {
        // Check if description exists.
        $desckey = 'template_' . $code . '_desc';
        $hasdesc = get_string_manager()->string_exists($desckey, 'local_jobboard');
        $description = $hasdesc ? get_string($desckey, 'local_jobboard') : '';

        $strdata = [
            'email_templates' => get_string('email_templates', 'local_jobboard'),
            'back_to_templates' => get_string('back_to_templates', 'local_jobboard'),
            'edit_template' => get_string('edit_template', 'local_jobboard'),
            'dashboard' => get_string('dashboard', 'local_jobboard'),
            'doctypes' => get_string('doctypes', 'local_jobboard'),
            'manageroles' => get_string('manageroles', 'local_jobboard'),
            'pluginsettings' => get_string('pluginsettings', 'local_jobboard'),
        ];

        return [
            'pagetitle' => get_string('edit_template', 'local_jobboard') . ': ' . $templatename,
            'backurl' => $pageurl->out(false),
            'templatename' => $templatename,
            'templatecode' => $code,
            'hasdescription' => $hasdesc,
            'description' => $description,
            'formhtml' => $formhtml,
            'navfooter' => [
                'dashboardurl' => (new \moodle_url('/local/jobboard/index.php'))->out(false),
                'doctypesurl' => (new \moodle_url('/local/jobboard/admin/doctypes.php'))->out(false),
                'rolesurl' => (new \moodle_url('/local/jobboard/admin/roles.php'))->out(false),
                'settingsurl' => (new \moodle_url('/admin/settings.php', ['section' => 'local_jobboard']))->out(false),
            ],
            'str' => $strdata,
        ];
    }

    /**
     * Prepare admin doctype form page data.
     *
     * @param bool $isedit Whether editing existing doctype.
     * @param string $formhtml Rendered form HTML.
     * @param \moodle_url $pageurl Page URL.
     * @return array Template data.
     */
    public function prepare_admin_doctype_form_data(bool $isedit, string $formhtml, \moodle_url $pageurl): array {
        $strdata = [
            'back' => get_string('back'),
        ];

        return [
            'pagetitle' => $isedit
                ? get_string('editdoctype', 'local_jobboard')
                : get_string('adddoctype', 'local_jobboard'),
            'backurl' => $pageurl->out(false),
            'isedit' => $isedit,
            'formhtml' => $formhtml,
            'str' => $strdata,
        ];
    }

    /**
     * Prepare admin doctype confirm delete page data.
     *
     * @param object $doctype Doctype record.
     * @param int $usagecount Number of documents using this type.
     * @param \moodle_url $pageurl Page URL.
     * @return array Template data.
     */
    public function prepare_admin_doctype_confirm_delete_data(
        object $doctype,
        int $usagecount,
        \moodle_url $pageurl
    ): array {
        // Get display name.
        $name = get_string_manager()->string_exists('doctype_' . $doctype->code, 'local_jobboard')
            ? get_string('doctype_' . $doctype->code, 'local_jobboard')
            : $doctype->name;

        $strdata = [
            'confirmdeletedoctype' => get_string('confirmdeletedoctype', 'local_jobboard'),
            'error_doctypeinuse' => get_string('error:doctypeinuse', 'local_jobboard', $usagecount),
            'confirmdeletedoctype_msg' => get_string('confirmdeletedoctype_msg', 'local_jobboard', $name),
            'code' => get_string('code', 'local_jobboard'),
            'back' => get_string('back'),
            'delete' => get_string('delete'),
            'cancel' => get_string('cancel'),
        ];

        return [
            'doctypename' => $name,
            'doctypecode' => $doctype->code,
            'inuse' => $usagecount > 0,
            'usagecount' => $usagecount,
            'deleteurl' => (new \moodle_url($pageurl, [
                'action' => 'delete',
                'id' => $doctype->id,
                'sesskey' => sesskey(),
            ]))->out(false),
            'cancelurl' => $pageurl->out(false),
            'str' => $strdata,
        ];
    }
}
