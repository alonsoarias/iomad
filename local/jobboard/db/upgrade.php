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

        // Note: Foreign keys automatically create indexes, so we only add additional indexes.
        $table->add_index('category_user_idx', XMLDB_INDEX_UNIQUE, ['categoryid', 'userid']);
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

        // Note: templateid_fk creates an index automatically, so we only add additional indexes.
        $table->add_index('template_lang_idx', XMLDB_INDEX_UNIQUE, ['templateid', 'lang']);
        $table->add_index('lang_idx', XMLDB_INDEX_NOTUNIQUE, ['lang']);

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

    // Version 3.1.2 - Faculty and Program tables for IOMAD architecture.
    // Per AGENTS.md: Committees are per FACULTY, Reviewers are per PROGRAM.
    if ($oldversion < 2025121117) {
        $dbman = $DB->get_manager();

        // ====================================================================
        // 1. Create local_jobboard_faculty table.
        // ====================================================================
        $table = new xmldb_table('local_jobboard_faculty');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('code', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        $table->add_index('companyid_idx', XMLDB_INDEX_NOTUNIQUE, ['companyid']);
        $table->add_index('company_code_unique', XMLDB_INDEX_UNIQUE, ['companyid', 'code']);
        $table->add_index('enabled_idx', XMLDB_INDEX_NOTUNIQUE, ['enabled']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // ====================================================================
        // 2. Create local_jobboard_program table.
        // ====================================================================
        $table = new xmldb_table('local_jobboard_program');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('facultyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('code', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('shortname', XMLDB_TYPE_CHAR, '100', null, null, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('modality', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('level', XMLDB_TYPE_CHAR, '50', null, null, null, null);
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '5', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('facultyid_fk', XMLDB_KEY_FOREIGN, ['facultyid'], 'local_jobboard_faculty', ['id']);
        $table->add_key('categoryid_fk', XMLDB_KEY_FOREIGN, ['categoryid'], 'course_categories', ['id']);

        $table->add_index('faculty_code_unique', XMLDB_INDEX_UNIQUE, ['facultyid', 'code']);
        $table->add_index('categoryid_idx', XMLDB_INDEX_NOTUNIQUE, ['categoryid']);
        $table->add_index('enabled_idx', XMLDB_INDEX_NOTUNIQUE, ['enabled']);
        $table->add_index('modality_idx', XMLDB_INDEX_NOTUNIQUE, ['modality']);
        $table->add_index('level_idx', XMLDB_INDEX_NOTUNIQUE, ['level']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // ====================================================================
        // 3. Update local_jobboard_committee: add facultyid and description.
        // ====================================================================
        $table = new xmldb_table('local_jobboard_committee');

        // Add facultyid field.
        $field = new xmldb_field('facultyid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add description field.
        $field = new xmldb_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null, 'name');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add foreign key for facultyid.
        $key = new xmldb_key('facultyid_fk', XMLDB_KEY_FOREIGN, ['facultyid'], 'local_jobboard_faculty', ['id']);
        $dbman->add_key($table, $key);

        // Add index for facultyid.
        $index = new xmldb_index('facultyid_idx', XMLDB_INDEX_NOTUNIQUE, ['facultyid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Add status index.
        $index = new xmldb_index('status_idx', XMLDB_INDEX_NOTUNIQUE, ['status']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // ====================================================================
        // 4. Update local_jobboard_program_reviewer: add programid.
        // ====================================================================
        $table = new xmldb_table('local_jobboard_program_reviewer');

        // Add programid field.
        $field = new xmldb_field('programid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'id');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add foreign key for programid.
        $key = new xmldb_key('programid_fk', XMLDB_KEY_FOREIGN, ['programid'], 'local_jobboard_program', ['id']);
        $dbman->add_key($table, $key);

        // Add unique index for program_user.
        $index = new xmldb_index('program_user_idx', XMLDB_INDEX_UNIQUE, ['programid', 'userid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Change category_user_idx to non-unique (for backwards compatibility).
        $index = new xmldb_index('category_user_idx', XMLDB_INDEX_UNIQUE, ['categoryid', 'userid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }
        $index = new xmldb_index('category_user_idx', XMLDB_INDEX_NOTUNIQUE, ['categoryid', 'userid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Make categoryid nullable (it's legacy now).
        $field = new xmldb_field('categoryid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $dbman->change_field_notnull($table, $field);

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121117, 'local', 'jobboard');
    }

    // Version 3.2.1 - Add opendate and closedate to vacancy table.
    // These fields allow individual vacancy deadlines that can override convocatoria dates.
    if ($oldversion < 2025121241) {
        $dbman = $DB->get_manager();
        $table = new xmldb_table('local_jobboard_vacancy');

        // Add opendate field.
        $field = new xmldb_field('opendate', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'publicationtype');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add closedate field.
        $field = new xmldb_field('closedate', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'opendate');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Populate closedate from convocatoria.enddate for existing vacancies.
        $sql = "UPDATE {local_jobboard_vacancy} v
                   SET closedate = (
                       SELECT c.enddate
                         FROM {local_jobboard_convocatoria} c
                        WHERE c.id = v.convocatoriaid
                   ),
                   opendate = (
                       SELECT c.startdate
                         FROM {local_jobboard_convocatoria} c
                        WHERE c.id = v.convocatoriaid
                   )
                 WHERE v.convocatoriaid IS NOT NULL
                   AND v.closedate IS NULL";
        $DB->execute($sql);

        // Add index for closedate.
        $index = new xmldb_index('closedate_idx', XMLDB_INDEX_NOTUNIQUE, ['closedate']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Add index for opendate.
        $index = new xmldb_index('opendate_idx', XMLDB_INDEX_NOTUNIQUE, ['opendate']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121241, 'local', 'jobboard');
    }

    // Version 3.6.38 - Update document types with new structure.
    // Add new document types and reorganize sortorder.
    if ($oldversion < 2025121624) {
        local_jobboard_upgrade_doctypes();

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121624, 'local', 'jobboard');
    }

    // Version 3.6.43 - Change vacancy unique index to composite key.
    // Allow same vacancy code with different locations/modalities.
    if ($oldversion < 2025121629) {
        $dbman = $DB->get_manager();
        $table = new xmldb_table('local_jobboard_vacancy');

        // Drop old unique index on code only (if it exists).
        $oldindex = new xmldb_index('code_unique', XMLDB_INDEX_UNIQUE, ['code']);
        if ($dbman->index_exists($table, $oldindex)) {
            $dbman->drop_index($table, $oldindex);
        }

        // Create new composite unique index on code + location + modality.
        $newindex = new xmldb_index('code_location_modality_unique', XMLDB_INDEX_UNIQUE, ['code', 'location', 'modality']);
        if (!$dbman->index_exists($table, $newindex)) {
            $dbman->add_index($table, $newindex);
        }

        // Create non-unique index on code for search performance.
        $codeindex = new xmldb_index('code_idx', XMLDB_INDEX_NOTUNIQUE, ['code']);
        if (!$dbman->index_exists($table, $codeindex)) {
            $dbman->add_index($table, $codeindex);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121629, 'local', 'jobboard');
    }

    // Version 3.6.51 - Make tarjeta_profesional document optional.
    // Remove profession exemption filter - instead tarjeta profesional is now simply optional.
    if ($oldversion < 2025121803) {
        // Set tarjeta_profesional as optional (isrequired = 0).
        $DB->set_field('local_jobboard_doctype', 'isrequired', 0, ['code' => 'tarjeta_profesional']);

        // Clear profession_exempt field since we no longer use it for filtering.
        $DB->set_field('local_jobboard_doctype', 'profession_exempt', null, ['code' => 'tarjeta_profesional']);

        // Update timemodified.
        $DB->set_field('local_jobboard_doctype', 'timemodified', time(), ['code' => 'tarjeta_profesional']);

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121803, 'local', 'jobboard');
    }

    // Version 3.6.67 - Change carta_intencion from textarea to editor (Moodle HTML editor).
    if ($oldversion < 2025121819) {
        // Update carta_intencion to use the Moodle HTML editor instead of plain textarea.
        $DB->set_field('local_jobboard_doctype', 'input_type', 'editor', ['code' => 'carta_intencion']);

        // Update timemodified.
        $DB->set_field('local_jobboard_doctype', 'timemodified', time(), ['code' => 'carta_intencion']);

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025121819, 'local', 'jobboard');
    }

    // Version 4.0.0 - Major workflow restructuring:
    // - Add dean/HR review date fields to convocatoria
    // - Add userrole field to audit table
    // - Create new roles (dean, hr)
    // - Remove committee-related tables and role
    // - Migrate application statuses to new workflow
    if ($oldversion < 2025122100) {
        $dbman = $DB->get_manager();

        // ====================================================================
        // 1. Add review date fields to convocatoria table
        // ====================================================================
        $table = new xmldb_table('local_jobboard_convocatoria');

        $fields = [
            new xmldb_field('dean_review_startdate', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'max_applications_per_user'),
            new xmldb_field('dean_review_enddate', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'dean_review_startdate'),
            new xmldb_field('hr_review_startdate', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'dean_review_enddate'),
            new xmldb_field('hr_review_enddate', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'hr_review_startdate'),
        ];

        foreach ($fields as $field) {
            if (!$dbman->field_exists($table, $field)) {
                $dbman->add_field($table, $field);
            }
        }

        // ====================================================================
        // 2. Add userrole field to audit table
        // ====================================================================
        $audittable = new xmldb_table('local_jobboard_audit');
        $rolefield = new xmldb_field('userrole', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'userid');

        if (!$dbman->field_exists($audittable, $rolefield)) {
            $dbman->add_field($audittable, $rolefield);

            // Add index for userrole.
            $index = new xmldb_index('userrole_idx', XMLDB_INDEX_NOTUNIQUE, ['userrole']);
            if (!$dbman->index_exists($audittable, $index)) {
                $dbman->add_index($audittable, $index);
            }
        }

        // ====================================================================
        // 3. Create new roles: jobboard_dean and jobboard_hr
        // ====================================================================
        local_jobboard_upgrade_create_dean_hr_roles();

        // ====================================================================
        // 4. Migrate application statuses to new workflow
        // ====================================================================
        $statusmap = [
            'under_review' => 'pending_dean_review',
            'docs_validated' => 'dean_approved',
            'docs_rejected' => 'pending_dean_review', // Give another chance
            'interview' => 'pending_hr_validation',
            'selected' => 'hr_validated',
            // 'rejected' stays as is or maps based on context
        ];

        foreach ($statusmap as $oldstatus => $newstatus) {
            $DB->execute(
                "UPDATE {local_jobboard_application} SET status = :newstatus WHERE status = :oldstatus",
                ['newstatus' => $newstatus, 'oldstatus' => $oldstatus]
            );
        }

        // ====================================================================
        // 5. Delete committee-related tables (in correct order for FK)
        // ====================================================================
        $tablestodrop = [
            'local_jobboard_decision',
            'local_jobboard_evaluation',
            'local_jobboard_criteria',
            'local_jobboard_committee_member',
            'local_jobboard_committee',
        ];

        foreach ($tablestodrop as $tablename) {
            $table = new xmldb_table($tablename);
            if ($dbman->table_exists($table)) {
                $dbman->drop_table($table);
            }
        }

        // ====================================================================
        // 6. Delete committee role and its capabilities
        // ====================================================================
        $committeerole = $DB->get_record('role', ['shortname' => 'jobboard_committee']);
        if ($committeerole) {
            // Remove role assignments.
            $DB->delete_records('role_assignments', ['roleid' => $committeerole->id]);
            // Remove role capabilities.
            $DB->delete_records('role_capabilities', ['roleid' => $committeerole->id]);
            // Delete the role.
            delete_role($committeerole->id);
        }

        // ====================================================================
        // 7. Delete obsolete capabilities
        // ====================================================================
        $obsoletecaps = [
            'local/jobboard:evaluate',
            'local/jobboard:viewevaluations',
        ];

        foreach ($obsoletecaps as $capname) {
            $DB->delete_records('capabilities', ['name' => $capname]);
            $DB->delete_records('role_capabilities', ['capability' => $capname]);
        }

        // Savepoint reached.
        upgrade_plugin_savepoint(true, 2025122100, 'local', 'jobboard');
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

/**
 * Upgrade document types to the new structure.
 *
 * This upgrade:
 * - Adds new document types (carta_intencion, experiencia_docente, experiencia_profesional, formacion_pedagogia, formacion_tic)
 * - Reorganizes sortorder for all document types
 * - Disables obsolete document types (eps, pension, cuenta_bancaria, certificacion_laboral)
 * - Updates existing document types with new properties
 *
 * @return void
 */
function local_jobboard_upgrade_doctypes(): void {
    global $DB;

    $now = time();

    // Define the new document type structure with correct order.
    // Include function from install.php if available, otherwise define here.
    require_once(__DIR__ . '/install.php');

    if (function_exists('local_jobboard_get_default_doctypes')) {
        $newdoctypes = local_jobboard_get_default_doctypes();
    } else {
        // Fallback: Define doctypes inline.
        return;
    }

    // Codes of obsolete document types to disable.
    $obsoletecodes = ['eps', 'pension', 'cuenta_bancaria', 'certificacion_laboral'];

    // Disable obsolete document types.
    foreach ($obsoletecodes as $code) {
        $existing = $DB->get_record('local_jobboard_doctype', ['code' => $code]);
        if ($existing && $existing->enabled) {
            $DB->set_field('local_jobboard_doctype', 'enabled', 0, ['id' => $existing->id]);
            $DB->set_field('local_jobboard_doctype', 'timemodified', $now, ['id' => $existing->id]);
        }
    }

    // Process each new document type.
    foreach ($newdoctypes as $doctype) {
        $existing = $DB->get_record('local_jobboard_doctype', ['code' => $doctype['code']]);

        if ($existing) {
            // Update existing document type.
            $update = new stdClass();
            $update->id = $existing->id;
            $update->name = $doctype['name'];
            $update->description = $doctype['description'];
            $update->requirements = $doctype['requirements'];
            $update->checklistitems = $doctype['checklistitems'];
            $update->externalurl = $doctype['externalurl'] ?? '';
            $update->isrequired = $doctype['isrequired'] ?? 1;
            $update->iserexempted = $doctype['iserexempted'] ?? 0;
            $update->gender_condition = $doctype['gender_condition'] ?? null;
            $update->age_exemption_threshold = $doctype['age_exemption_threshold'] ?? null;
            $update->profession_exempt = $doctype['profession_exempt'] ?? null;
            $update->conditional_note = $doctype['conditional_note'] ?? '';
            $update->input_type = $doctype['input_type'] ?? 'file';
            $update->category = $doctype['category'] ?? '';
            $update->defaultmaxagedays = $doctype['defaultmaxagedays'] ?? null;
            $update->sortorder = $doctype['sortorder'];
            $update->enabled = 1;
            $update->timemodified = $now;

            $DB->update_record('local_jobboard_doctype', $update);
        } else {
            // Insert new document type.
            $insert = (object) $doctype;
            $insert->timecreated = $now;
            $insert->timemodified = $now;
            $DB->insert_record('local_jobboard_doctype', $insert);
        }
    }
}

/**
 * Create new Dean and HR roles for the workflow restructuring.
 *
 * Creates two new roles:
 * - jobboard_dean: Reviews applicant profiles and approves/rejects them
 * - jobboard_hr: Validates documents for dean-approved applicants
 *
 * @return void
 */
function local_jobboard_upgrade_create_dean_hr_roles(): void {
    global $DB;

    // First, ensure all new capabilities are registered in the database.
    // This is required because we're adding new capabilities (reviewprofiles,
    // approveprofile, validatehr) that need to exist before we can assign them.
    update_capabilities('local_jobboard');

    $systemcontext = context_system::instance();

    // Role: Dean Reviewer.
    $deanrole = $DB->get_record('role', ['shortname' => 'jobboard_dean']);
    if (!$deanrole) {
        $deanroleid = create_role(
            get_string('role_dean', 'local_jobboard'),
            'jobboard_dean',
            get_string('role_dean_desc', 'local_jobboard'),
            'teacher'
        );

        $deancaps = [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:viewallapplications',
            'local/jobboard:downloadanydocument',
            'local/jobboard:reviewprofiles',
            'local/jobboard:approveprofile',
        ];

        foreach ($deancaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $deanroleid, $systemcontext->id);
        }

        set_role_contextlevels($deanroleid, [CONTEXT_SYSTEM]);
    }

    // Role: HR Validator.
    $hrrole = $DB->get_record('role', ['shortname' => 'jobboard_hr']);
    if (!$hrrole) {
        $hrroleid = create_role(
            get_string('role_hr', 'local_jobboard'),
            'jobboard_hr',
            get_string('role_hr_desc', 'local_jobboard'),
            'teacher'
        );

        $hrcaps = [
            'local/jobboard:view',
            'local/jobboard:viewinternal',
            'local/jobboard:viewallapplications',
            'local/jobboard:downloadanydocument',
            'local/jobboard:validatedocuments',
            'local/jobboard:reviewdocuments',
            'local/jobboard:validatehr',
        ];

        foreach ($hrcaps as $cap) {
            assign_capability($cap, CAP_ALLOW, $hrroleid, $systemcontext->id);
        }

        set_role_contextlevels($hrroleid, [CONTEXT_SYSTEM]);
    }
}
