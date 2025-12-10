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
 * This file contains database schema upgrades from Phase 9 onwards.
 * Earlier phases (1-8) were consolidated into install.xml for fresh installations.
 *
 * Version history:
 * - 2025120901: Phase 9 - Consent tracking table
 * - 2025120902: Phase 9 - Applicant profile table
 * - 2025120903: Phase 9 - Document type conditional fields
 * - 2025120904: Phase 9 - Department integration for vacancies
 * - 2025120905: Phase 9 - Convocatoria (call for applications) system
 * - 2025120906: Phase 9 - Remove legacy vacancy fields (courseid, categoryid)
 * - 2025120907: Phase 9 - Create custom roles for document reviewers
 * - 2025120908: Phase 9 - Vacancy modality field
 * - 2025120910: Phase 10 - Audit table enhancements
 * - 2025120911: Phase 10 - Document type age exemption and notes
 * - 2025120912: Phase 10 - Convocatoria application restrictions
 * - 2025120913: Phase 10 - Document validation observations
 * - 2025120914: Phase 10 - Email templates table
 * - 2025120915: Phase 10 - Remove deprecated vacancy fields
 * - 2025120916: Phase 10 - Email template multi-tenant support
 * - 2025120917: Phase 10 - Convocatoria document exemptions
 * - 2025120918: Phase 10 - Cleanup deprecated global settings
 * - 2025120920: Phase 10 - Final optimizations and cache purge
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
    $dbman = $DB->get_manager();

    // =========================================================================
    // PHASE 9: Alternative Registration and Convocatoria System
    // =========================================================================

    // 2025120901: Create consent table for tracking user consents.
    if ($oldversion < 2025120901) {
        $table = new xmldb_table('local_jobboard_consent');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('consenttype', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('consentgiven', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('consentversion', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('ipaddress', XMLDB_TYPE_CHAR, '45', null, null, null, null);
        $table->add_field('useragent', XMLDB_TYPE_CHAR, '512', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        $table->add_index('user_type_idx', XMLDB_INDEX_NOTUNIQUE, ['userid', 'consenttype']);
        $table->add_index('consenttype_idx', XMLDB_INDEX_NOTUNIQUE, ['consenttype']);
        $table->add_index('time_idx', XMLDB_INDEX_NOTUNIQUE, ['timecreated']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025120901, 'local', 'jobboard');
    }

    // 2025120902: Create applicant profile table for extended user data.
    if ($oldversion < 2025120902) {
        $table = new xmldb_table('local_jobboard_applicant_profile');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('doctype', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('birthdate', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('gender', XMLDB_TYPE_CHAR, '1', null, null, null, null);
        $table->add_field('education_level', XMLDB_TYPE_CHAR, '30', null, null, null, null);
        $table->add_field('degree_title', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('expertise_area', XMLDB_TYPE_CHAR, '255', null, null, null, null);
        $table->add_field('experience_years', XMLDB_TYPE_CHAR, '10', null, null, null, null);
        $table->add_field('profile_complete', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('userid_fk', XMLDB_KEY_FOREIGN_UNIQUE, ['userid'], 'user', ['id']);

        $table->add_index('education_idx', XMLDB_INDEX_NOTUNIQUE, ['education_level']);
        $table->add_index('experience_idx', XMLDB_INDEX_NOTUNIQUE, ['experience_years']);
        $table->add_index('profile_complete_idx', XMLDB_INDEX_NOTUNIQUE, ['profile_complete']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025120902, 'local', 'jobboard');
    }

    // 2025120903: Add conditional fields to doctype table.
    if ($oldversion < 2025120903) {
        $table = new xmldb_table('local_jobboard_doctype');

        // Gender condition: 'M' = men only, 'F' = women only, null = all.
        $field = new xmldb_field('gender_condition', XMLDB_TYPE_CHAR, '1', null, null, null, null, 'iserexempted');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Profession exemptions (JSON array of education types exempt).
        $field = new xmldb_field('profession_exempt', XMLDB_TYPE_TEXT, null, null, null, null, null, 'gender_condition');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Category grouping for organization.
        $field = new xmldb_field('category', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'profession_exempt');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Set libreta_militar as men only.
        $DB->set_field('local_jobboard_doctype', 'gender_condition', 'M', ['code' => 'libreta_militar']);

        // Set categories for document organization.
        $categories = [
            'identification' => ['cedula', 'libreta_militar'],
            'academic' => ['titulo_academico', 'tarjeta_profesional', 'formacion_complementaria'],
            'employment' => ['sigep', 'bienes_rentas', 'certificacion_laboral'],
            'financial' => ['rut', 'cuenta_bancaria'],
            'health' => ['eps', 'pension'],
            'legal' => ['antecedentes_disciplinarios', 'antecedentes_fiscales', 'antecedentes_judiciales',
                        'medidas_correctivas', 'inhabilidades', 'redam'],
        ];
        foreach ($categories as $category => $codes) {
            foreach ($codes as $code) {
                $DB->set_field('local_jobboard_doctype', 'category', $category, ['code' => $code]);
            }
        }

        upgrade_plugin_savepoint(true, 2025120903, 'local', 'jobboard');
    }

    // 2025120904: Add departmentid field to vacancy table for IOMAD integration.
    if ($oldversion < 2025120904) {
        $table = new xmldb_table('local_jobboard_vacancy');

        $field = new xmldb_field('departmentid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'companyid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $index = new xmldb_index('departmentid_idx', XMLDB_INDEX_NOTUNIQUE, ['departmentid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2025120904, 'local', 'jobboard');
    }

    // 2025120905: Create convocatoria table and link vacancies.
    if ($oldversion < 2025120905) {
        $table = new xmldb_table('local_jobboard_convocatoria');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('code', XMLDB_TYPE_CHAR, '50', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('startdate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('enddate', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'draft');
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('departmentid', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('publicationtype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'internal');
        $table->add_field('terms', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('createdby_fk', XMLDB_KEY_FOREIGN, ['createdby'], 'user', ['id']);
        $table->add_key('modifiedby_fk', XMLDB_KEY_FOREIGN, ['modifiedby'], 'user', ['id']);

        $table->add_index('code_unique', XMLDB_INDEX_UNIQUE, ['code']);
        $table->add_index('status_idx', XMLDB_INDEX_NOTUNIQUE, ['status']);
        $table->add_index('companyid_idx', XMLDB_INDEX_NOTUNIQUE, ['companyid']);
        $table->add_index('dates_idx', XMLDB_INDEX_NOTUNIQUE, ['startdate', 'enddate']);
        $table->add_index('publicationtype_idx', XMLDB_INDEX_NOTUNIQUE, ['publicationtype']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Add convocatoriaid field to vacancy table.
        $vacancytable = new xmldb_table('local_jobboard_vacancy');

        $field = new xmldb_field('convocatoriaid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'companyid');
        if (!$dbman->field_exists($vacancytable, $field)) {
            $dbman->add_field($vacancytable, $field);
        }

        $index = new xmldb_index('convocatoriaid_idx', XMLDB_INDEX_NOTUNIQUE, ['convocatoriaid']);
        if (!$dbman->index_exists($vacancytable, $index)) {
            $dbman->add_index($vacancytable, $index);
        }

        upgrade_plugin_savepoint(true, 2025120905, 'local', 'jobboard');
    }

    // 2025120906: Remove legacy courseid and categoryid fields from vacancy.
    if ($oldversion < 2025120906) {
        $table = new xmldb_table('local_jobboard_vacancy');

        // Drop indexes first.
        $index = new xmldb_index('courseid', XMLDB_INDEX_NOTUNIQUE, ['courseid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        $index = new xmldb_index('categoryid', XMLDB_INDEX_NOTUNIQUE, ['categoryid']);
        if ($dbman->index_exists($table, $index)) {
            $dbman->drop_index($table, $index);
        }

        // Drop fields.
        $field = new xmldb_field('courseid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        $field = new xmldb_field('categoryid');
        if ($dbman->field_exists($table, $field)) {
            $dbman->drop_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025120906, 'local', 'jobboard');
    }

    // 2025120907: Create custom roles for document reviewers.
    if ($oldversion < 2025120907) {
        require_once(__DIR__ . '/install.php');

        if (function_exists('local_jobboard_create_roles')) {
            local_jobboard_create_roles();
        }

        purge_all_caches();
        upgrade_plugin_savepoint(true, 2025120907, 'local', 'jobboard');
    }

    // 2025120908: Add modality field to vacancy table.
    if ($oldversion < 2025120908) {
        $table = new xmldb_table('local_jobboard_vacancy');

        $field = new xmldb_field('modality', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'location');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025120908, 'local', 'jobboard');
    }

    // =========================================================================
    // PHASE 10: Optimization and Regulatory Compliance (v2.1.0)
    // =========================================================================

    // 2025120910: Enhance audit table with previousvalue and newvalue fields.
    if ($oldversion < 2025120910) {
        $table = new xmldb_table('local_jobboard_audit');

        $field = new xmldb_field('previousvalue', XMLDB_TYPE_TEXT, null, null, null, null, null, 'extradata');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('newvalue', XMLDB_TYPE_TEXT, null, null, null, null, null, 'previousvalue');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025120910, 'local', 'jobboard');
    }

    // 2025120911: Add age exemption and conditional note fields to doctype.
    if ($oldversion < 2025120911) {
        $table = new xmldb_table('local_jobboard_doctype');

        // Age exemption threshold (e.g., 50 for libreta_militar).
        $field = new xmldb_field('age_exemption_threshold', XMLDB_TYPE_INTEGER, '3', null, null, null, null, 'category');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Conditional note for explaining requirements.
        $field = new xmldb_field('conditional_note', XMLDB_TYPE_TEXT, null, null, null, null, null, 'age_exemption_threshold');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Set age exemption for libreta_militar (men over 50 are exempt).
        $DB->set_field('local_jobboard_doctype', 'age_exemption_threshold', 50, ['code' => 'libreta_militar']);

        // Mark tarjeta_profesional as optional.
        $DB->set_field('local_jobboard_doctype', 'isrequired', 0, ['code' => 'tarjeta_profesional']);

        upgrade_plugin_savepoint(true, 2025120911, 'local', 'jobboard');
    }

    // 2025120912: Add application restriction fields to convocatoria.
    if ($oldversion < 2025120912) {
        $table = new xmldb_table('local_jobboard_convocatoria');

        $field = new xmldb_field('allow_multiple_applications', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'terms');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('max_applications_per_user', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '1', 'allow_multiple_applications');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Migrate global settings to existing convocatorias.
        $globalAllowMultiple = get_config('local_jobboard', 'allowmultipleapplications') ? 1 : 0;
        $globalMaxApps = get_config('local_jobboard', 'maxactiveapplications') ?: 1;

        $DB->execute(
            "UPDATE {local_jobboard_convocatoria}
             SET allow_multiple_applications = ?, max_applications_per_user = ?
             WHERE allow_multiple_applications = 0",
            [$globalAllowMultiple, $globalMaxApps]
        );

        upgrade_plugin_savepoint(true, 2025120912, 'local', 'jobboard');
    }

    // 2025120913: Add observations and requires_reupload fields to doc_validation.
    if ($oldversion < 2025120913) {
        $table = new xmldb_table('local_jobboard_doc_validation');

        $field = new xmldb_field('observations', XMLDB_TYPE_TEXT, null, null, null, null, null, 'notes');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('requires_reupload', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'observations');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2025120913, 'local', 'jobboard');
    }

    // 2025120914: Create email template table for customizable notifications.
    if ($oldversion < 2025120914) {
        $table = new xmldb_table('local_jobboard_email_template');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('companyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('code', XMLDB_TYPE_CHAR, '100', null, XMLDB_NOTNULL, null, null);
        $table->add_field('subject', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('body', XMLDB_TYPE_TEXT, null, null, XMLDB_NOTNULL, null, null);
        $table->add_field('bodyformat', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('enabled', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('lang', XMLDB_TYPE_CHAR, '30', null, XMLDB_NOTNULL, null, 'es');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);

        $table->add_index('companyid_code_lang_idx', XMLDB_INDEX_UNIQUE, ['companyid', 'code', 'lang']);
        $table->add_index('enabled_idx', XMLDB_INDEX_NOTUNIQUE, ['enabled']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025120914, 'local', 'jobboard');
    }

    // 2025120915: Remove deprecated vacancy fields (dates now from convocatoria).
    if ($oldversion < 2025120915) {
        $table = new xmldb_table('local_jobboard_vacancy');

        // Ensure all vacancies have a convocatoriaid before removing date fields.
        $orphanCount = $DB->count_records_select('local_jobboard_vacancy', 'convocatoriaid IS NULL OR convocatoriaid = 0');
        if ($orphanCount > 0) {
            // Create a default convocatoria for orphan vacancies.
            $defaultConv = new \stdClass();
            $defaultConv->code = 'LEGACY-' . date('Ymd');
            $defaultConv->name = get_string('legacyconvocatoria', 'local_jobboard');
            $defaultConv->description = get_string('legacyconvocatoria_desc', 'local_jobboard');
            $defaultConv->startdate = time() - (365 * 24 * 3600);
            $defaultConv->enddate = time() + (365 * 24 * 3600);
            $defaultConv->status = 'open';
            $defaultConv->publicationtype = 'public';
            $defaultConv->createdby = 2;
            $defaultConv->timecreated = time();

            $defaultConvId = $DB->insert_record('local_jobboard_convocatoria', $defaultConv);

            $DB->execute(
                "UPDATE {local_jobboard_vacancy} SET convocatoriaid = ? WHERE convocatoriaid IS NULL OR convocatoriaid = 0",
                [$defaultConvId]
            );
        }

        // Drop deprecated fields.
        $fieldsToRemove = ['salary', 'isextemporaneous', 'extemporaneousreason', 'opendate', 'closedate'];
        foreach ($fieldsToRemove as $fieldname) {
            $index = new xmldb_index($fieldname . '_idx', XMLDB_INDEX_NOTUNIQUE, [$fieldname]);
            if ($dbman->index_exists($table, $index)) {
                $dbman->drop_index($table, $index);
            }

            $field = new xmldb_field($fieldname);
            if ($dbman->field_exists($table, $field)) {
                $dbman->drop_field($table, $field);
            }
        }

        upgrade_plugin_savepoint(true, 2025120915, 'local', 'jobboard');
    }

    // 2025120916: Reinstall User Tours with improved selectors.
    if ($oldversion < 2025120916) {
        // Delete existing jobboard tours.
        $likepath = $DB->sql_like('pathmatch', ':pathmatch');
        $tours = $DB->get_records_select('tool_usertours_tours', $likepath, ['pathmatch' => '%/local/jobboard/%']);
        foreach ($tours as $tour) {
            $DB->delete_records('tool_usertours_steps', ['tourid' => $tour->id]);
            $DB->delete_records('tool_usertours_tours', ['id' => $tour->id]);
        }

        // Reinstall tours.
        require_once(__DIR__ . '/install.php');
        if (function_exists('local_jobboard_install_tours')) {
            local_jobboard_install_tours();
        }

        upgrade_plugin_savepoint(true, 2025120916, 'local', 'jobboard');
    }

    // 2025120917: Create convocatoria document exemptions table.
    if ($oldversion < 2025120917) {
        $table = new xmldb_table('local_jobboard_conv_docexempt');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('convocatoriaid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('doctypeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('exemptionreason', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('convocatoriaid_fk', XMLDB_KEY_FOREIGN, ['convocatoriaid'], 'local_jobboard_convocatoria', ['id']);
        $table->add_key('doctypeid_fk', XMLDB_KEY_FOREIGN, ['doctypeid'], 'local_jobboard_doctype', ['id']);
        $table->add_key('createdby_fk', XMLDB_KEY_FOREIGN, ['createdby'], 'user', ['id']);

        $table->add_index('conv_doctype_unique', XMLDB_INDEX_UNIQUE, ['convocatoriaid', 'doctypeid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2025120917, 'local', 'jobboard');
    }

    // 2025120918: Remove deprecated global application limit settings.
    if ($oldversion < 2025120918) {
        unset_config('allowmultipleapplications', 'local_jobboard');
        unset_config('maxactiveapplications', 'local_jobboard');

        upgrade_plugin_savepoint(true, 2025120918, 'local', 'jobboard');
    }

    // 2025120920: Final optimizations and cache purge.
    if ($oldversion < 2025120920) {
        purge_all_caches();

        upgrade_plugin_savepoint(true, 2025120920, 'local', 'jobboard');
    }

    return true;
}
