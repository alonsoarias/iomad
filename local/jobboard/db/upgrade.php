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

/**
 * Upgrade script for local_jobboard.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Upgrade the local_jobboard plugin.
 *
 * @param int $oldversion The old version of the plugin.
 * @return bool True on success.
 */
function xmldb_local_jobboard_upgrade($oldversion) {
    global $DB;

    // Version 2.2.0 - Create custom roles for the plugin.
    if ($oldversion < 2025121100) {
        // Create the plugin custom roles if they don't exist.
        local_jobboard_upgrade_create_roles();

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121100, 'local', 'jobboard');
    }

    // Version 2.2.1 - Update role capabilities and ensure completeness.
    if ($oldversion < 2025121101) {
        // Update existing roles with any missing capabilities.
        local_jobboard_upgrade_update_role_capabilities();

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121101, 'local', 'jobboard');
    }

    // Version 2.3.0 - Major structural changes:
    // - Committee by faculty (company) instead of vacancy
    // - Add input_type field to document types
    // - Add PDF and brief description to convocatoria
    if ($oldversion < 2025121103) {
        $dbman = $DB->get_manager();

        // ====================================================================
        // 1. COMMITTEE: Add companyid field and make vacancyid nullable
        // ====================================================================
        $table = new xmldb_table('local_jobboard_committee');

        // Add companyid field.
        $field = new xmldb_field('companyid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Drop the unique index on vacancyid (we'll create a non-unique one).
        $index = new xmldb_index('vacancyid_idx', XMLDB_INDEX_UNIQUE, ['vacancyid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Change vacancyid to allow null (for faculty-wide committees).
        $field = new xmldb_field('vacancyid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'companyid');
        $dbman->change_field_notnull($table, $field);

        // Create new indexes.
        $index = new xmldb_index('companyid_idx', XMLDB_INDEX_NOTUNIQUE, ['companyid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        $index = new xmldb_index('vacancyid_nonunique_idx', XMLDB_INDEX_NOTUNIQUE, ['vacancyid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Migrate existing committees: set companyid from vacancy.
        $sql = "UPDATE {local_jobboard_committee} c
                   SET companyid = (
                       SELECT v.companyid
                         FROM {local_jobboard_vacancy} v
                        WHERE v.id = c.vacancyid
                   )
                 WHERE c.companyid IS NULL";
        $DB->execute($sql);

        // ====================================================================
        // 2. DOCTYPE: Add input_type field
        // ====================================================================
        $table = new xmldb_table('local_jobboard_doctype');

        $field = new xmldb_field('input_type', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'file', 'conditional_note');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add index on input_type.
        $index = new xmldb_index('input_type_idx', XMLDB_INDEX_NOTUNIQUE, ['input_type']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // ====================================================================
        // 3. CONVOCATORIA: Add PDF and brief description fields
        // ====================================================================
        $table = new xmldb_table('local_jobboard_convocatoria');

        // Add brief_description field.
        $field = new xmldb_field('brief_description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'description');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add pdf_contenthash field.
        $field = new xmldb_field('pdf_contenthash', XMLDB_TYPE_CHAR, '40', null, null, null, null, 'brief_description');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add pdf_filename field.
        $field = new xmldb_field('pdf_filename', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'pdf_contenthash');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // ====================================================================
        // 4. Update default file formats setting to PDF only
        // ====================================================================
        set_config('allowedformats', 'pdf', 'local_jobboard');
        set_config('acceptedfiletypes', '.pdf', 'local_jobboard');

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121103, 'local', 'jobboard');
    }

    // Version 2.3.1 - Remove API token functionality.
    if ($oldversion < 2025121104) {
        $dbman = $DB->get_manager();

        // Drop the API token table if it exists.
        $table = new xmldb_table('local_jobboard_api_token');
        if ($dbman->table_exists($table)) {
            $dbman->drop_table($table);
        }

        // Remove API-related configuration settings.
        unset_config('api_enabled', 'local_jobboard');
        unset_config('api_rate_limit', 'local_jobboard');

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121104, 'local', 'jobboard');
    }

    // Version 2.4.0 - Add faculty reviewers table.
    if ($oldversion < 2025121105) {
        $dbman = $DB->get_manager();

        // Define table local_jobboard_faculty_reviewer.
        $table = new xmldb_table('local_jobboard_faculty_reviewer');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('role', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'reviewer');
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'active');
        $table->add_field('addedby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('addedby_fk', XMLDB_KEY_FOREIGN, ['addedby'], 'user', ['id']);

        $table->add_index('companyid_idx', XMLDB_INDEX_NOTUNIQUE, ['companyid']);
        $table->add_index('company_user_idx', XMLDB_INDEX_UNIQUE, ['companyid', 'userid']);
        $table->add_index('status_idx', XMLDB_INDEX_NOTUNIQUE, ['status']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121105, 'local', 'jobboard');
    }

    // Version 3.0.0 - Email Templates Refactoring.
    // Add new fields for multi-tenant support and categories.
    if ($oldversion < 2025121106) {
        $dbman = $DB->get_manager();
        $table = new xmldb_table('local_jobboard_email_template');

        // Add companyid field for multi-tenant support (0 = global).
        $field = new xmldb_field('companyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0', 'code');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add category field.
        $field = new xmldb_field('category', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'application', 'companyid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add description field.
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add is_default field.
        $field = new xmldb_field('is_default', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'enabled');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add priority field.
        $field = new xmldb_field('priority', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '0', 'is_default');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add createdby field.
        $field = new xmldb_field('createdby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'priority');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add modifiedby field.
        $field = new xmldb_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'createdby');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Drop old unique index on code (will create new composite index).
        $index = new xmldb_index('code_unique', XMLDB_INDEX_UNIQUE, ['code']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Create new composite unique index on code + companyid.
        $index = new xmldb_index('code_company_unique', XMLDB_INDEX_UNIQUE, ['code', 'companyid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Create index on category.
        $index = new xmldb_index('category_idx', XMLDB_INDEX_NOTUNIQUE, ['category']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Create index on companyid.
        $index = new xmldb_index('companyid_idx', XMLDB_INDEX_NOTUNIQUE, ['companyid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Migrate existing templates: determine category from code.
        $categorymapping = [
            'application_received' => 'application',
            'under_review' => 'application',
            'docs_validated' => 'documents',
            'docs_rejected' => 'documents',
            'review_complete' => 'documents',
            'interview_scheduled' => 'interview',
            'interview_reminder' => 'interview',
            'interview_completed' => 'interview',
            'selected' => 'selection',
            'rejected' => 'selection',
            'waitlist' => 'selection',
            'vacancy_closing' => 'system',
            'new_vacancy' => 'system',
            'reviewer_assigned' => 'system',
        ];

        foreach ($categorymapping as $code => $category) {
            $DB->execute(
                "UPDATE {local_jobboard_email_template} SET category = :category WHERE code = :code",
                ['category' => $category, 'code' => $code]
            );
        }

        // Mark existing templates as defaults.
        $DB->execute("UPDATE {local_jobboard_email_template} SET is_default = 1 WHERE companyid = 0");

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121106, 'local', 'jobboard');
    }

    // Version 3.0.1 - Program Reviewers (replacing faculty reviewers).
    // Reviewers are now assigned to academic programs (course categories) instead of companies/faculties.
    if ($oldversion < 2025121107) {
        $dbman = $DB->get_manager();

        // ====================================================================
        // 1. Create program_reviewer table (linked to course_categories).
        // ====================================================================
        $table = new xmldb_table('local_jobboard_program_reviewer');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('role', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'reviewer');
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'active');
        $table->add_field('addedby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('categoryid_fk', XMLDB_KEY_FOREIGN, ['categoryid'], 'course_categories', ['id']);
        $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);
        $table->add_key('addedby_fk', XMLDB_KEY_FOREIGN, ['addedby'], 'user', ['id']);

        $table->add_index('categoryid_idx', XMLDB_INDEX_NOTUNIQUE, ['categoryid']);
        $table->add_index('category_user_idx', XMLDB_INDEX_UNIQUE, ['categoryid', 'userid']);
        $table->add_index('userid_idx', XMLDB_INDEX_NOTUNIQUE, ['userid']);
        $table->add_index('status_idx', XMLDB_INDEX_NOTUNIQUE, ['status']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // ====================================================================
        // 2. Migrate data from faculty_reviewer to program_reviewer.
        // Map company departments to course categories (programs).
        // ====================================================================
        $oldtable = new xmldb_table('local_jobboard_faculty_reviewer');
        if ($dbman->table_exists($oldtable)) {
            // Get all faculty reviewers.
            $facultyreviewers = $DB->get_records('local_jobboard_faculty_reviewer');

            foreach ($facultyreviewers as $fr) {
                // Try to find course categories linked to this company.
                // In IOMAD, departments can be linked to course categories.
                // We'll try to find matching categories by looking at department names.
                $company = $DB->get_record('company', ['id' => $fr->companyid]);
                if ($company) {
                    // Find categories that might match this company's programs.
                    // This is a best-effort migration - manual review may be needed.
                    $sql = "SELECT cc.id
                              FROM {course_categories} cc
                              JOIN {department} d ON d.name = cc.name
                             WHERE d.company = :companyid
                               AND d.parent > 0
                             LIMIT 10";
                    $categories = $DB->get_records_sql($sql, ['companyid' => $fr->companyid]);

                    foreach ($categories as $cat) {
                        // Check if this assignment already exists.
                        if (!$DB->record_exists('local_jobboard_program_reviewer', [
                            'categoryid' => $cat->id,
                            'userid' => $fr->userid,
                        ])) {
                            $newrecord = new stdClass();
                            $newrecord->categoryid = $cat->id;
                            $newrecord->userid = $fr->userid;
                            $newrecord->role = $fr->role;
                            $newrecord->status = $fr->status;
                            $newrecord->addedby = $fr->addedby;
                            $newrecord->timecreated = $fr->timecreated;
                            $newrecord->timemodified = time();
                            $DB->insert_record('local_jobboard_program_reviewer', $newrecord);
                        }
                    }
                }
            }

            // Drop the old faculty_reviewer table.
            $dbman->drop_table($oldtable);
        }

        // ====================================================================
        // 3. Create email_strings table for multilingual email support.
        // ====================================================================
        $table = new xmldb_table('local_jobboard_email_strings');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('templateid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('lang', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subject', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('body', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('signature', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('templateid_fk', XMLDB_KEY_FOREIGN, ['templateid'], 'local_jobboard_email_template', ['id']);

        $table->add_index('templateid_idx', XMLDB_INDEX_NOTUNIQUE, ['templateid']);
        $table->add_index('lang_idx', XMLDB_INDEX_NOTUNIQUE, ['lang']);
        $table->add_index('template_lang_idx', XMLDB_INDEX_UNIQUE, ['templateid', 'lang']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Migrate existing template content to email_strings table.
        $templates = $DB->get_records('local_jobboard_email_template');
        foreach ($templates as $template) {
            // Check if strings already exist for this template.
            if (!$DB->record_exists('local_jobboard_email_strings', ['templateid' => $template->id])) {
                // Detect language from template content or use default.
                $lang = !empty($template->lang) ? $template->lang : 'en';

                $strings = new stdClass();
                $strings->templateid = $template->id;
                $strings->lang = $lang;
                $strings->subject = $template->subject;
                $strings->body = $template->body;
                $strings->signature = !empty($template->signature) ? $template->signature : '';
                $strings->timecreated = time();

                $DB->insert_record('local_jobboard_email_strings', $strings);
            }
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121107, 'local', 'jobboard');
    }

    return true;
}

/**
 * Create custom roles for the Job Board plugin during upgrade.
 *
 * This function is called during upgrade to create the three specialized roles
 * for existing installations that didn't have them created during initial install.
 *
 * @return void
 */
function local_jobboard_upgrade_create_roles(): void {
    global $DB;

    // Ensure capabilities are loaded.
    update_capabilities('local_jobboard');

    $systemcontext = context_system::instance();

    // Role: Document Reviewer.
    $reviewerrole = $DB->get_record('role', ['shortname' => 'jobboard_reviewer']);
    if (!$reviewerrole) {
        $reviewerroleid = create_role(
            get_string('role_reviewer', 'local_jobboard'),
            'jobboard_reviewer',
            get_string('role_reviewer_desc', 'local_jobboard'),
            'teacher'
        );

        // Assign capabilities for reviewer role.
        $reviewercaps = [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:review',
            'local/jobboard:validatedocuments',
            'local/jobboard:reviewdocuments',
            'local/jobboard:downloadanydocument',
        ];

        foreach ($reviewercaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $reviewerroleid, $systemcontext->id);
        }

        // Set contexts where this role can be assigned.
        set_role_contextlevels($reviewerroleid, [CONTEXT_SYSTEM]);
    }

    // Role: Selection Coordinator.
    $coordinatorrole = $DB->get_record('role', ['shortname' => 'jobboard_coordinator']);
    if (!$coordinatorrole) {
        $coordinatorroleid = create_role(
            get_string('role_coordinator', 'local_jobboard'),
            'jobboard_coordinator',
            get_string('role_coordinator_desc', 'local_jobboard'),
            'editingteacher'
        );

        // Assign capabilities for coordinator role.
        $coordinatorcaps = [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:manage',
            'local/jobboard:createvacancy',
            'local/jobboard:editvacancy',
            'local/jobboard:publishvacancy',
            'local/jobboard:viewallvacancies',
            'local/jobboard:viewallapplications',
            'local/jobboard:changeapplicationstatus',
            'local/jobboard:assignreviewers',
            'local/jobboard:viewreports',
            'local/jobboard:viewevaluations',
            'local/jobboard:manageworkflow',
        ];

        foreach ($coordinatorcaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $coordinatorroleid, $systemcontext->id);
        }

        set_role_contextlevels($coordinatorroleid, [CONTEXT_SYSTEM]);
    }

    // Role: Selection Committee Member.
    $committeerole = $DB->get_record('role', ['shortname' => 'jobboard_committee']);
    if (!$committeerole) {
        $committeeroleid = create_role(
            get_string('role_committee', 'local_jobboard'),
            'jobboard_committee',
            get_string('role_committee_desc', 'local_jobboard'),
            'teacher'
        );

        // Assign capabilities for committee role.
        $committeecaps = [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:evaluate',
            'local/jobboard:viewevaluations',
            'local/jobboard:downloadanydocument',
        ];

        foreach ($committeecaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $committeeroleid, $systemcontext->id);
        }

        set_role_contextlevels($committeeroleid, [CONTEXT_SYSTEM]);
    }
}

/**
 * Update existing role capabilities for the Job Board plugin.
 *
 * This function ensures all plugin roles have the complete set of required capabilities,
 * adding any that might be missing from previous versions.
 *
 * @return void
 */
function local_jobboard_upgrade_update_role_capabilities(): void {
    global $DB;

    // Ensure capabilities are loaded.
    update_capabilities('local_jobboard');

    $systemcontext = context_system::instance();

    // Define complete capability sets for each role.
    $roleconfigs = [
        'jobboard_reviewer' => [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:review',
            'local/jobboard:validatedocuments',
            'local/jobboard:reviewdocuments',
            'local/jobboard:downloadanydocument',
        ],
        'jobboard_coordinator' => [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:manage',
            'local/jobboard:createvacancy',
            'local/jobboard:editvacancy',
            'local/jobboard:publishvacancy',
            'local/jobboard:viewallvacancies',
            'local/jobboard:viewallapplications',
            'local/jobboard:changeapplicationstatus',
            'local/jobboard:assignreviewers',
            'local/jobboard:viewreports',
            'local/jobboard:viewevaluations',
            'local/jobboard:manageworkflow',
        ],
        'jobboard_committee' => [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:evaluate',
            'local/jobboard:viewevaluations',
            'local/jobboard:downloadanydocument',
        ],
    ];

    foreach ($roleconfigs as $shortname => $requiredcaps) {
        $role = $DB->get_record('role', ['shortname' => $shortname]);
        if (!$role) {
            // Role doesn't exist, will be created by local_jobboard_upgrade_create_roles().
            continue;
        }

        // Get currently assigned capabilities for this role at system context.
        $currentcaps = $DB->get_records('role_capabilities', [
            'roleid' => $role->id,
            'contextid' => $systemcontext->id,
        ], '', 'capability, permission');

        $currentcapnames = array_keys($currentcaps);

        // Assign any missing capabilities.
        foreach ($requiredcaps as $cap) {
            if (!in_array($cap, $currentcapnames)) {
                assign_capability($cap, CAP_ALLOW, $role->id, $systemcontext->id);
            }
        }
    }
}
