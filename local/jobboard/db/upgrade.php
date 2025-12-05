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
    $dbman = $DB->get_manager();

    // Phase 2 upgrades: Application System and Documents.
    if ($oldversion < 2024120301) {

        // Add new fields to local_jobboard_application table.
        $table = new xmldb_table('local_jobboard_application');

        // Add statusnotes field.
        $field = new xmldb_field('statusnotes', XMLDB_TYPE_TEXT, null, null, null, null, null, 'status');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add consentgiven field.
        $field = new xmldb_field('consentgiven', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'statusnotes');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add consenttimestamp field.
        $field = new xmldb_field('consenttimestamp', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'consentgiven');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add digitalsignature field.
        $field = new xmldb_field('digitalsignature', XMLDB_TYPE_CHAR, '255', null, null, null, null, 'consenttimestamp');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add coverletter field.
        $field = new xmldb_field('coverletter', XMLDB_TYPE_TEXT, null, null, null, null, null, 'digitalsignature');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024120301, 'local', 'jobboard');
    }

    if ($oldversion < 2024120302) {

        // Modify local_jobboard_doc_validation table.
        $table = new xmldb_table('local_jobboard_doc_validation');

        // Change isvalid to integer type if it exists as char.
        $field = new xmldb_field('isvalid', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        if ($dbman->field_exists($table, $field)) {
            // Drop existing field and recreate with correct type.
            $dbman->change_field_type($table, $field);
        }

        // Add validatedby field.
        $field = new xmldb_field('validatedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'isvalid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add rejectreason field.
        $field = new xmldb_field('rejectreason', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'validatedby');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add notes field.
        $field = new xmldb_field('notes', XMLDB_TYPE_TEXT, null, null, null, null, null, 'rejectreason');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024120302, 'local', 'jobboard');
    }

    if ($oldversion < 2024120303) {

        // Modify local_jobboard_exemption table.
        $table = new xmldb_table('local_jobboard_exemption');

        // Add documentref field.
        $field = new xmldb_field('documentref', XMLDB_TYPE_CHAR, '100', null, null, null, null, 'exemptiontype');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add timerevoked field.
        $field = new xmldb_field('timerevoked', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'documentref');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add revokedby field.
        $field = new xmldb_field('revokedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'timerevoked');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add revokereason field.
        $field = new xmldb_field('revokereason', XMLDB_TYPE_TEXT, null, null, null, null, null, 'revokedby');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024120303, 'local', 'jobboard');
    }

    if ($oldversion < 2024120304) {

        // Modify local_jobboard_notification table.
        $table = new xmldb_table('local_jobboard_notification');

        // Add templatecode field.
        $field = new xmldb_field('templatecode', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'userid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add data field (JSON data).
        $field = new xmldb_field('data', XMLDB_TYPE_TEXT, null, null, null, null, null, 'templatecode');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add status field.
        $field = new xmldb_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'pending', 'data');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add attempts field.
        $field = new xmldb_field('attempts', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '0', 'status');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add lasterror field.
        $field = new xmldb_field('lasterror', XMLDB_TYPE_TEXT, null, null, null, null, null, 'attempts');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024120304, 'local', 'jobboard');
    }

    if ($oldversion < 2024120305) {

        // Add isrequired field to local_jobboard_doctype table.
        $table = new xmldb_table('local_jobboard_doctype');

        $field = new xmldb_field('isrequired', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '1', 'validationrules');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024120305, 'local', 'jobboard');
    }

    // Phase 3 upgrades: Workflow and Document Validation.
    if ($oldversion < 2024120401) {

        // Add reviewerid field to local_jobboard_application table.
        $table = new xmldb_table('local_jobboard_application');

        $field = new xmldb_field('reviewerid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'coverletter');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add reviewerassignedtime field.
        $field = new xmldb_field('reviewerassignedtime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'reviewerid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add reviewerassignedby field.
        $field = new xmldb_field('reviewerassignedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'reviewerassignedtime');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add index for reviewer queries.
        $index = new xmldb_index('reviewerid_idx', XMLDB_INDEX_NOTUNIQUE, ['reviewerid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2024120401, 'local', 'jobboard');
    }

    if ($oldversion < 2024120402) {

        // Add issuperseded field to local_jobboard_document table.
        $table = new xmldb_table('local_jobboard_document');

        $field = new xmldb_field('issuperseded', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'fileid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add supersededby field (reference to new document version).
        $field = new xmldb_field('supersededby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'issuperseded');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add supersededtime field.
        $field = new xmldb_field('supersededtime', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'supersededby');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024120402, 'local', 'jobboard');
    }

    if ($oldversion < 2024120403) {

        // Add checklist field to local_jobboard_doc_validation table.
        $table = new xmldb_table('local_jobboard_doc_validation');

        $field = new xmldb_field('checklist', XMLDB_TYPE_TEXT, null, null, null, null, null, 'notes');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024120403, 'local', 'jobboard');
    }

    // Phase 4 upgrades: ISER Exemptions and Advanced Management.
    if ($oldversion < 2024120501) {

        // Create interview table.
        $table = new xmldb_table('local_jobboard_interview');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('applicationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('scheduledtime', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('duration', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '30');
        $table->add_field('interviewtype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'inperson');
        $table->add_field('location', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'scheduled');
        $table->add_field('notes', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('rating', XMLDB_TYPE_INTEGER, '1', null, null, null, null);
        $table->add_field('feedback', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('recommendation', XMLDB_TYPE_CHAR, '20', null, null, null, null);
        $table->add_field('completedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('timecompleted', XMLDB_TYPE_INTEGER, '10', null, null, null, null);
        $table->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('applicationid_fk', XMLDB_KEY_FOREIGN, ['applicationid'], 'local_jobboard_application', ['id']);

        $table->add_index('scheduledtime_idx', XMLDB_INDEX_NOTUNIQUE, ['scheduledtime']);
        $table->add_index('status_idx', XMLDB_INDEX_NOTUNIQUE, ['status']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2024120501, 'local', 'jobboard');
    }

    if ($oldversion < 2024120502) {

        // Create interviewer (interview panel members) table.
        $table = new xmldb_table('local_jobboard_interviewer');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('interviewid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('interviewid_fk', XMLDB_KEY_FOREIGN, ['interviewid'], 'local_jobboard_interview', ['id']);
        $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        $table->add_index('interview_user_idx', XMLDB_INDEX_UNIQUE, ['interviewid', 'userid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2024120502, 'local', 'jobboard');
    }

    if ($oldversion < 2024120503) {

        // Create selection committee table.
        $table = new xmldb_table('local_jobboard_committee');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('vacancyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'active');
        $table->add_field('createdby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('vacancyid_fk', XMLDB_KEY_FOREIGN, ['vacancyid'], 'local_jobboard_vacancy', ['id']);

        $table->add_index('vacancyid_idx', XMLDB_INDEX_UNIQUE, ['vacancyid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2024120503, 'local', 'jobboard');
    }

    if ($oldversion < 2024120504) {

        // Create committee member table.
        $table = new xmldb_table('local_jobboard_committee_member');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('committeeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('role', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'evaluator');
        $table->add_field('addedby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('committeeid_fk', XMLDB_KEY_FOREIGN, ['committeeid'], 'local_jobboard_committee', ['id']);
        $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        $table->add_index('committee_user_idx', XMLDB_INDEX_UNIQUE, ['committeeid', 'userid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2024120504, 'local', 'jobboard');
    }

    if ($oldversion < 2024120505) {

        // Create evaluation table.
        $table = new xmldb_table('local_jobboard_evaluation');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('committeeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('applicationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('userid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('score', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('vote', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('comments', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('criteriaratings', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('committeeid_fk', XMLDB_KEY_FOREIGN, ['committeeid'], 'local_jobboard_committee', ['id']);
        $table->add_key('applicationid_fk', XMLDB_KEY_FOREIGN, ['applicationid'], 'local_jobboard_application', ['id']);
        $table->add_key('userid_fk', XMLDB_KEY_FOREIGN, ['userid'], 'user', ['id']);

        $table->add_index('evaluation_unique_idx', XMLDB_INDEX_UNIQUE, ['committeeid', 'applicationid', 'userid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2024120505, 'local', 'jobboard');
    }

    if ($oldversion < 2024120506) {

        // Create evaluation criteria table.
        $table = new xmldb_table('local_jobboard_criteria');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('vacancyid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('name', XMLDB_TYPE_CHAR, '255', null, XMLDB_NOTNULL, null, null);
        $table->add_field('description', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('weight', XMLDB_TYPE_INTEGER, '2', null, XMLDB_NOTNULL, null, '1');
        $table->add_field('maxscore', XMLDB_TYPE_INTEGER, '3', null, XMLDB_NOTNULL, null, '10');
        $table->add_field('sortorder', XMLDB_TYPE_INTEGER, '4', null, XMLDB_NOTNULL, null, '0');
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('vacancyid_fk', XMLDB_KEY_FOREIGN, ['vacancyid'], 'local_jobboard_vacancy', ['id']);

        $table->add_index('vacancy_sort_idx', XMLDB_INDEX_NOTUNIQUE, ['vacancyid', 'sortorder']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2024120506, 'local', 'jobboard');
    }

    if ($oldversion < 2024120507) {

        // Create decision table for final selection decisions.
        $table = new xmldb_table('local_jobboard_decision');

        $table->add_field('id', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, XMLDB_SEQUENCE, null);
        $table->add_field('committeeid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('applicationid', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('decision', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, null);
        $table->add_field('reason', XMLDB_TYPE_TEXT, null, null, null, null, null);
        $table->add_field('decidedby', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);
        $table->add_field('timecreated', XMLDB_TYPE_INTEGER, '10', null, XMLDB_NOTNULL, null, null);

        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('committeeid_fk', XMLDB_KEY_FOREIGN, ['committeeid'], 'local_jobboard_committee', ['id']);
        $table->add_key('applicationid_fk', XMLDB_KEY_FOREIGN, ['applicationid'], 'local_jobboard_application', ['id']);

        $table->add_index('applicationid_idx', XMLDB_INDEX_UNIQUE, ['applicationid']);

        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        upgrade_plugin_savepoint(true, 2024120507, 'local', 'jobboard');
    }

    if ($oldversion < 2024120508) {

        // Add modifiedby field to exemption table.
        $table = new xmldb_table('local_jobboard_exemption');

        $field = new xmldb_field('modifiedby', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'revokereason');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        $field = new xmldb_field('timemodified', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'modifiedby');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024120508, 'local', 'jobboard');
    }

    // Phase 5 upgrades: Advanced Security and REST API.
    if ($oldversion < 2024120509) {

        // Enable API by default.
        set_config('api_enabled', 1, 'local_jobboard');

        // Set default data retention period (5 years for Habeas Data compliance).
        set_config('applicationretentiondays', 1825, 'local_jobboard');

        // Ensure api_token table has all necessary fields.
        $table = new xmldb_table('local_jobboard_api_token');

        // Add ipwhitelist field if missing.
        $field = new xmldb_field('ipwhitelist', XMLDB_TYPE_TEXT, null, null, null, null, null, 'permissions');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        upgrade_plugin_savepoint(true, 2024120509, 'local', 'jobboard');
    }

    // Phase 6 upgrades: UI/UX Improvements and Production Ready.
    if ($oldversion < 2024120510) {

        // Purge caches to ensure new templates and JS are loaded.
        purge_all_caches();

        // Set default UI preferences.
        set_config('vacancies_per_page', 12, 'local_jobboard');
        set_config('show_dashboard_widgets', 1, 'local_jobboard');
        set_config('enable_ajax_filters', 1, 'local_jobboard');

        upgrade_plugin_savepoint(true, 2024120510, 'local', 'jobboard');
    }

    // Phase 7 upgrades: Public Vacancies, API Standards, Application Limits.
    if ($oldversion < 2024120520) {

        // Add publicationtype field to local_jobboard_vacancy table.
        $table = new xmldb_table('local_jobboard_vacancy');

        // publicationtype: 'public' or 'internal'.
        $field = new xmldb_field('publicationtype', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'internal', 'status');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add index for publicationtype.
        $index = new xmldb_index('publicationtype_idx', XMLDB_INDEX_NOTUNIQUE, ['publicationtype']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Add compound index for status and publicationtype.
        $index = new xmldb_index('status_pubtype_idx', XMLDB_INDEX_NOTUNIQUE, ['status', 'publicationtype']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Set default configuration for public vacancies and application limits.
        set_config('enable_public_page', 1, 'local_jobboard');
        set_config('public_page_title', get_string('publicpagetitle', 'local_jobboard'), 'local_jobboard');
        set_config('allow_multiple_applications', 1, 'local_jobboard');
        set_config('max_active_applications', 0, 'local_jobboard'); // 0 = unlimited.
        set_config('show_menu_to_guests', 1, 'local_jobboard');
        set_config('menu_item_text', get_string('vacancies', 'local_jobboard'), 'local_jobboard');

        // Purge caches.
        purge_all_caches();

        upgrade_plugin_savepoint(true, 2024120520, 'local', 'jobboard');
    }

    // Phase 7.4 upgrades: Add status column to doc_validation table.
    if ($oldversion < 2024120524) {

        // Add status field to local_jobboard_doc_validation table.
        $table = new xmldb_table('local_jobboard_doc_validation');

        // status: 'pending', 'approved', or 'rejected'.
        $field = new xmldb_field('status', XMLDB_TYPE_CHAR, '20', null, XMLDB_NOTNULL, null, 'pending', 'documentid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add index for status.
        $index = new xmldb_index('status_idx', XMLDB_INDEX_NOTUNIQUE, ['status']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        // Migrate existing data: set status based on isvalid.
        // isvalid = 1 -> status = 'approved'
        // isvalid = 0 AND validatedby IS NOT NULL -> status = 'rejected'
        // isvalid = 0 AND validatedby IS NULL -> status = 'pending'
        $DB->execute("UPDATE {local_jobboard_doc_validation} SET status = 'approved' WHERE isvalid = 1");
        $DB->execute("UPDATE {local_jobboard_doc_validation} SET status = 'rejected' WHERE isvalid = 0 AND validatedby IS NOT NULL");
        $DB->execute("UPDATE {local_jobboard_doc_validation} SET status = 'pending' WHERE isvalid = 0 AND validatedby IS NULL");

        // Purge caches.
        purge_all_caches();

        upgrade_plugin_savepoint(true, 2024120524, 'local', 'jobboard');
    }

    // Phase 8.7: Add User Tours for plugin guidance.
    if ($oldversion < 2025120407) {
        // Import User Tours from the tours directory.
        require_once(__DIR__ . '/install.php');
        if (function_exists('local_jobboard_install_tours')) {
            local_jobboard_install_tours();
        }

        upgrade_plugin_savepoint(true, 2025120407, 'local', 'jobboard');
    }

    // Phase 9.3: Add consent table for alternative signup form.
    if ($oldversion < 2025120516) {

        // Create consent table for tracking user consents.
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

        upgrade_plugin_savepoint(true, 2025120516, 'local', 'jobboard');
    }

    // Phase 9.4: Add applicant profile table for extended user data.
    if ($oldversion < 2025120517) {

        // Create applicant profile table for storing extended user data.
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

        upgrade_plugin_savepoint(true, 2025120517, 'local', 'jobboard');
    }

    // Phase 9.5: Add conditional fields to doctype table for gender/profession conditions.
    if ($oldversion < 2025120518) {

        // Add new fields to local_jobboard_doctype table.
        $table = new xmldb_table('local_jobboard_doctype');

        // Field for gender condition: 'M' = men only, 'F' = women only, null = all.
        $field = new xmldb_field('gender_condition', XMLDB_TYPE_CHAR, '1', null, null, null, null, 'iserexempted');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Field for profession exemptions (JSON array of education types exempt).
        $field = new xmldb_field('profession_exempt', XMLDB_TYPE_TEXT, null, null, null, null, null, 'gender_condition');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Field for category grouping.
        $field = new xmldb_field('category', XMLDB_TYPE_CHAR, '50', null, null, null, null, 'profession_exempt');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Update existing document types with correct conditions.
        // libreta_militar: only for men.
        $DB->set_field('local_jobboard_doctype', 'gender_condition', 'M', ['code' => 'libreta_militar']);

        // tarjeta_profesional: not required for licenciados.
        $DB->set_field('local_jobboard_doctype', 'profession_exempt', json_encode(['licenciatura']), ['code' => 'tarjeta_profesional']);

        // Set iserexempted flag for documents that ISER previous employees don't need.
        $iserexempteddocs = ['cedula', 'titulo_academico', 'tarjeta_profesional', 'libreta_militar', 'certificacion_laboral'];
        foreach ($iserexempteddocs as $code) {
            $DB->set_field('local_jobboard_doctype', 'iserexempted', 1, ['code' => $code]);
        }

        // Set categories for better organization.
        $categories = [
            'identification' => ['cedula', 'libreta_militar'],
            'academic' => ['titulo_academico', 'tarjeta_profesional', 'formacion_complementaria'],
            'employment' => ['sigep', 'bienes_rentas', 'certificacion_laboral'],
            'financial' => ['rut', 'cuenta_bancaria'],
            'health' => ['eps', 'pension'],
            'legal' => ['antecedentes_disciplinarios', 'antecedentes_fiscales', 'antecedentes_judiciales', 'medidas_correctivas', 'inhabilidades', 'redam'],
        ];
        foreach ($categories as $category => $codes) {
            foreach ($codes as $code) {
                $DB->set_field('local_jobboard_doctype', 'category', $category, ['code' => $code]);
            }
        }

        upgrade_plugin_savepoint(true, 2025120518, 'local', 'jobboard');
    }

    // Version 2025120519: Add departmentid field to vacancy table for IOMAD integration.
    if ($oldversion < 2025120519) {

        // Add departmentid field to local_jobboard_vacancy table.
        $table = new xmldb_table('local_jobboard_vacancy');

        $field = new xmldb_field('departmentid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'companyid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add index for departmentid.
        $index = new xmldb_index('departmentid_idx', XMLDB_INDEX_NOTUNIQUE, ['departmentid']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2025120519, 'local', 'jobboard');
    }

    // Version 2025120520: Add convocatoria table and link vacancies to convocatorias.
    if ($oldversion < 2025120520) {

        // Define table local_jobboard_convocatoria.
        $table = new xmldb_table('local_jobboard_convocatoria');

        // Adding fields to table local_jobboard_convocatoria.
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

        // Adding keys to table local_jobboard_convocatoria.
        $table->add_key('primary', XMLDB_KEY_PRIMARY, ['id']);
        $table->add_key('createdby_fk', XMLDB_KEY_FOREIGN, ['createdby'], 'user', ['id']);
        $table->add_key('modifiedby_fk', XMLDB_KEY_FOREIGN, ['modifiedby'], 'user', ['id']);

        // Adding indexes to table local_jobboard_convocatoria.
        $table->add_index('code_unique', XMLDB_INDEX_UNIQUE, ['code']);
        $table->add_index('status_idx', XMLDB_INDEX_NOTUNIQUE, ['status']);
        $table->add_index('companyid_idx', XMLDB_INDEX_NOTUNIQUE, ['companyid']);
        $table->add_index('dates_idx', XMLDB_INDEX_NOTUNIQUE, ['startdate', 'enddate']);
        $table->add_index('publicationtype_idx', XMLDB_INDEX_NOTUNIQUE, ['publicationtype']);

        // Create table if it doesn't exist.
        if (!$dbman->table_exists($table)) {
            $dbman->create_table($table);
        }

        // Add convocatoriaid field to vacancy table.
        $vacancytable = new xmldb_table('local_jobboard_vacancy');
        $field = new xmldb_field('convocatoriaid', XMLDB_TYPE_INTEGER, '10', null, null, null, null, 'companyid');
        if (!$dbman->field_exists($vacancytable, $field)) {
            $dbman->add_field($vacancytable, $field);
        }

        // Add index on convocatoriaid.
        $index = new xmldb_index('convocatoriaid_idx', XMLDB_INDEX_NOTUNIQUE, ['convocatoriaid']);
        if (!$dbman->index_exists($vacancytable, $index)) {
            $dbman->add_index($vacancytable, $index);
        }

        // Add foreign key (as index since Moodle doesn't enforce foreign keys).
        // Note: Foreign keys are not strictly enforced in Moodle DB, but we document the relationship.

        upgrade_plugin_savepoint(true, 2025120520, 'local', 'jobboard');
    }

    // Version 2025120527: Add extemporaneous vacancy support.
    if ($oldversion < 2025120527) {

        // Add isextemporaneous and extemporaneousreason fields to vacancy table.
        $table = new xmldb_table('local_jobboard_vacancy');

        // isextemporaneous: 0 = uses convocatoria dates, 1 = uses custom dates.
        $field = new xmldb_field('isextemporaneous', XMLDB_TYPE_INTEGER, '1', null, XMLDB_NOTNULL, null, '0', 'convocatoriaid');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Reason for using extemporaneous dates.
        $field = new xmldb_field('extemporaneousreason', XMLDB_TYPE_TEXT, null, null, null, null, null, 'isextemporaneous');
        if (!$dbman->field_exists($table, $field)) {
            $dbman->add_field($table, $field);
        }

        // Add index for extemporaneous filter.
        $index = new xmldb_index('isextemporaneous_idx', XMLDB_INDEX_NOTUNIQUE, ['isextemporaneous']);
        if (!$dbman->index_exists($table, $index)) {
            $dbman->add_index($table, $index);
        }

        upgrade_plugin_savepoint(true, 2025120527, 'local', 'jobboard');
    }

    return true;
}
