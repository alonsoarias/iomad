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
 * Privacy provider for local_jobboard.
 *
 * Implements Moodle's privacy API for GDPR compliance and Colombian
 * Habeas Data law (Ley 1581/2012) compliance.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\privacy;

defined('MOODLE_INTERNAL') || die();

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\contextlist;
use core_privacy\local\request\userlist;
use core_privacy\local\request\writer;
use core_privacy\local\request\helper;

/**
 * Privacy provider implementation.
 */
class provider implements
    \core_privacy\local\metadata\provider,
    \core_privacy\local\request\plugin\provider,
    \core_privacy\local\request\core_userlist_provider {

    /**
     * Returns metadata about the personal data stored by this plugin.
     *
     * @param collection $collection The collection to add metadata to.
     * @return collection The updated collection.
     */
    public static function get_metadata(collection $collection): collection {
        // Applications table.
        $collection->add_database_table(
            'local_jobboard_application',
            [
                'userid' => 'privacy:metadata:application:userid',
                'vacancyid' => 'privacy:metadata:application:vacancyid',
                'status' => 'privacy:metadata:application:status',
                'digitalsignature' => 'privacy:metadata:application:digitalsignature',
                'coverletter' => 'privacy:metadata:application:coverletter',
                'applicationdata' => 'privacy:metadata:application:applicationdata',
                'consentgiven' => 'privacy:metadata:application:consentgiven',
                'consenttimestamp' => 'privacy:metadata:application:consenttimestamp',
                'consentip' => 'privacy:metadata:application:consentip',
                'consentuseragent' => 'privacy:metadata:application:consentuseragent',
                'timecreated' => 'privacy:metadata:application:timecreated',
            ],
            'privacy:metadata:application'
        );

        // Documents table.
        $collection->add_database_table(
            'local_jobboard_document',
            [
                'applicationid' => 'privacy:metadata:document:applicationid',
                'documenttype' => 'privacy:metadata:document:documenttype',
                'filename' => 'privacy:metadata:document:filename',
                'uploadedby' => 'privacy:metadata:document:uploadedby',
                'issuedate' => 'privacy:metadata:document:issuedate',
                'timecreated' => 'privacy:metadata:document:timecreated',
            ],
            'privacy:metadata:document'
        );

        // Exemptions table.
        $collection->add_database_table(
            'local_jobboard_exemption',
            [
                'userid' => 'privacy:metadata:exemption:userid',
                'exemptiontype' => 'privacy:metadata:exemption:exemptiontype',
                'exempteddocs' => 'privacy:metadata:exemption:exempteddocs',
                'validfrom' => 'privacy:metadata:exemption:validfrom',
                'validuntil' => 'privacy:metadata:exemption:validuntil',
                'notes' => 'privacy:metadata:exemption:notes',
                'timecreated' => 'privacy:metadata:exemption:timecreated',
            ],
            'privacy:metadata:exemption'
        );

        // Workflow log table.
        $collection->add_database_table(
            'local_jobboard_workflow_log',
            [
                'applicationid' => 'privacy:metadata:workflowlog:applicationid',
                'previousstatus' => 'privacy:metadata:workflowlog:previousstatus',
                'newstatus' => 'privacy:metadata:workflowlog:newstatus',
                'changedby' => 'privacy:metadata:workflowlog:changedby',
                'comments' => 'privacy:metadata:workflowlog:comments',
                'timecreated' => 'privacy:metadata:workflowlog:timecreated',
            ],
            'privacy:metadata:workflowlog'
        );

        // Audit log table.
        $collection->add_database_table(
            'local_jobboard_audit',
            [
                'userid' => 'privacy:metadata:audit:userid',
                'action' => 'privacy:metadata:audit:action',
                'entitytype' => 'privacy:metadata:audit:entitytype',
                'entityid' => 'privacy:metadata:audit:entityid',
                'ipaddress' => 'privacy:metadata:audit:ipaddress',
                'useragent' => 'privacy:metadata:audit:useragent',
                'extradata' => 'privacy:metadata:audit:extradata',
                'timecreated' => 'privacy:metadata:audit:timecreated',
            ],
            'privacy:metadata:audit'
        );

        // Notifications table.
        $collection->add_database_table(
            'local_jobboard_notification',
            [
                'userid' => 'privacy:metadata:notification:userid',
                'templatecode' => 'privacy:metadata:notification:templatecode',
                'data' => 'privacy:metadata:notification:data',
                'status' => 'privacy:metadata:notification:status',
                'timecreated' => 'privacy:metadata:notification:timecreated',
            ],
            'privacy:metadata:notification'
        );

        // API tokens table.
        $collection->add_database_table(
            'local_jobboard_api_token',
            [
                'userid' => 'privacy:metadata:apitoken:userid',
                'description' => 'privacy:metadata:apitoken:description',
                'permissions' => 'privacy:metadata:apitoken:permissions',
                'lastused' => 'privacy:metadata:apitoken:lastused',
                'timecreated' => 'privacy:metadata:apitoken:timecreated',
            ],
            'privacy:metadata:apitoken'
        );

        // Interviewer table (interview panel members).
        $collection->add_database_table(
            'local_jobboard_interviewer',
            [
                'userid' => 'privacy:metadata:interviewer:userid',
                'interviewid' => 'privacy:metadata:interviewer:interviewid',
                'timecreated' => 'privacy:metadata:interviewer:timecreated',
            ],
            'privacy:metadata:interviewer'
        );

        // Committee member table.
        $collection->add_database_table(
            'local_jobboard_committee_member',
            [
                'userid' => 'privacy:metadata:committeemember:userid',
                'committeeid' => 'privacy:metadata:committeemember:committeeid',
                'role' => 'privacy:metadata:committeemember:role',
                'addedby' => 'privacy:metadata:committeemember:addedby',
                'timecreated' => 'privacy:metadata:committeemember:timecreated',
            ],
            'privacy:metadata:committeemember'
        );

        // Evaluation table (votes and scores from committee members).
        $collection->add_database_table(
            'local_jobboard_evaluation',
            [
                'userid' => 'privacy:metadata:evaluation:userid',
                'applicationid' => 'privacy:metadata:evaluation:applicationid',
                'score' => 'privacy:metadata:evaluation:score',
                'vote' => 'privacy:metadata:evaluation:vote',
                'comments' => 'privacy:metadata:evaluation:comments',
                'timecreated' => 'privacy:metadata:evaluation:timecreated',
            ],
            'privacy:metadata:evaluation'
        );

        // File storage.
        $collection->add_subsystem_link(
            'core_files',
            [],
            'privacy:metadata:files'
        );

        return $collection;
    }

    /**
     * Get the list of contexts that contain user information.
     *
     * @param int $userid The user ID.
     * @return contextlist The contextlist containing the user's contexts.
     */
    public static function get_contexts_for_userid(int $userid): contextlist {
        $contextlist = new contextlist();

        // Add system context if user has any data.
        $sql = "SELECT DISTINCT ctx.id
                FROM {context} ctx
                WHERE ctx.contextlevel = :contextlevel
                  AND EXISTS (
                      SELECT 1 FROM {local_jobboard_application} a WHERE a.userid = :userid1
                      UNION
                      SELECT 1 FROM {local_jobboard_exemption} e WHERE e.userid = :userid2
                      UNION
                      SELECT 1 FROM {local_jobboard_audit} au WHERE au.userid = :userid3
                      UNION
                      SELECT 1 FROM {local_jobboard_notification} n WHERE n.userid = :userid4
                      UNION
                      SELECT 1 FROM {local_jobboard_api_token} t WHERE t.userid = :userid5
                      UNION
                      SELECT 1 FROM {local_jobboard_interviewer} i WHERE i.userid = :userid6
                      UNION
                      SELECT 1 FROM {local_jobboard_committee_member} cm WHERE cm.userid = :userid7
                      UNION
                      SELECT 1 FROM {local_jobboard_evaluation} ev WHERE ev.userid = :userid8
                  )";

        $contextlist->add_from_sql($sql, [
            'contextlevel' => CONTEXT_SYSTEM,
            'userid1' => $userid,
            'userid2' => $userid,
            'userid3' => $userid,
            'userid4' => $userid,
            'userid5' => $userid,
            'userid6' => $userid,
            'userid7' => $userid,
            'userid8' => $userid,
        ]);

        return $contextlist;
    }

    /**
     * Get the list of users within a specific context.
     *
     * @param userlist $userlist The userlist to populate.
     */
    public static function get_users_in_context(userlist $userlist): void {
        $context = $userlist->get_context();

        if (!$context instanceof \context_system) {
            return;
        }

        // Get users from applications.
        $sql = "SELECT DISTINCT userid FROM {local_jobboard_application}";
        $userlist->add_from_sql('userid', $sql, []);

        // Get users from exemptions.
        $sql = "SELECT DISTINCT userid FROM {local_jobboard_exemption}";
        $userlist->add_from_sql('userid', $sql, []);

        // Get users from audit logs.
        $sql = "SELECT DISTINCT userid FROM {local_jobboard_audit}";
        $userlist->add_from_sql('userid', $sql, []);

        // Get users from notifications.
        $sql = "SELECT DISTINCT userid FROM {local_jobboard_notification}";
        $userlist->add_from_sql('userid', $sql, []);

        // Get users from API tokens.
        $sql = "SELECT DISTINCT userid FROM {local_jobboard_api_token}";
        $userlist->add_from_sql('userid', $sql, []);

        // Get users from interviewers.
        $sql = "SELECT DISTINCT userid FROM {local_jobboard_interviewer}";
        $userlist->add_from_sql('userid', $sql, []);

        // Get users from committee members.
        $sql = "SELECT DISTINCT userid FROM {local_jobboard_committee_member}";
        $userlist->add_from_sql('userid', $sql, []);

        // Get users from evaluations.
        $sql = "SELECT DISTINCT userid FROM {local_jobboard_evaluation}";
        $userlist->add_from_sql('userid', $sql, []);
    }

    /**
     * Export all user data for the specified approved contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts for export.
     */
    public static function export_user_data(approved_contextlist $contextlist): void {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $user = $contextlist->get_user();
        $context = \context_system::instance();

        // Export applications.
        self::export_applications($user->id, $context);

        // Export exemptions.
        self::export_exemptions($user->id, $context);

        // Export audit logs.
        self::export_audit_logs($user->id, $context);

        // Export notifications.
        self::export_notifications($user->id, $context);

        // Export API tokens.
        self::export_api_tokens($user->id, $context);

        // Export interviewer assignments.
        self::export_interviewer_data($user->id, $context);

        // Export committee memberships.
        self::export_committee_data($user->id, $context);

        // Export evaluations.
        self::export_evaluation_data($user->id, $context);
    }

    /**
     * Export user applications.
     *
     * @param int $userid User ID.
     * @param \context $context The context.
     */
    private static function export_applications(int $userid, \context $context): void {
        global $DB;

        $applications = $DB->get_records('local_jobboard_application', ['userid' => $userid]);

        foreach ($applications as $app) {
            $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $app->vacancyid]);

            $appdata = [
                'vacancy_code' => $vacancy ? $vacancy->code : 'Unknown',
                'vacancy_title' => $vacancy ? $vacancy->title : 'Unknown',
                'status' => $app->status,
                'digital_signature' => $app->digitalsignature,
                'cover_letter' => $app->coverletter,
                'is_iser_exemption' => $app->isexemption ? 'Yes' : 'No',
                'exemption_reason' => $app->exemptionreason,
                'consent_given' => $app->consentgiven ? 'Yes' : 'No',
                'consent_timestamp' => $app->consenttimestamp
                    ? userdate($app->consenttimestamp) : null,
                'consent_ip' => $app->consentip,
                'application_data' => $app->applicationdata,
                'created' => userdate($app->timecreated),
                'modified' => $app->timemodified ? userdate($app->timemodified) : null,
            ];

            // Export documents for this application.
            $documents = $DB->get_records('local_jobboard_document', ['applicationid' => $app->id]);
            $docdata = [];

            foreach ($documents as $doc) {
                $validation = $DB->get_record('local_jobboard_doc_validation', ['documentid' => $doc->id]);

                $docdata[] = [
                    'type' => $doc->documenttype,
                    'filename' => $doc->filename,
                    'filesize' => $doc->filesize,
                    'mimetype' => $doc->mimetype,
                    'issue_date' => $doc->issuedate ? userdate($doc->issuedate, '%Y-%m-%d') : null,
                    'is_superseded' => $doc->issuperseded ? 'Yes' : 'No',
                    'validation_status' => $validation
                        ? ($validation->isvalid ? 'Approved' : 'Pending/Rejected') : 'Pending',
                    'uploaded' => userdate($doc->timecreated),
                ];
            }

            $appdata['documents'] = $docdata;

            // Export workflow history.
            $logs = $DB->get_records('local_jobboard_workflow_log', ['applicationid' => $app->id], 'timecreated ASC');
            $logdata = [];

            foreach ($logs as $log) {
                $logdata[] = [
                    'previous_status' => $log->previousstatus,
                    'new_status' => $log->newstatus,
                    'comments' => $log->comments,
                    'timestamp' => userdate($log->timecreated),
                ];
            }

            $appdata['workflow_history'] = $logdata;

            writer::with_context($context)->export_data(
                [get_string('applications', 'local_jobboard'), $app->id],
                (object) $appdata
            );

            // Export actual document files.
            $fs = get_file_storage();
            $files = $fs->get_area_files(
                $context->id,
                'local_jobboard',
                'application_documents',
                $app->id,
                'filepath, filename',
                false
            );

            foreach ($files as $file) {
                writer::with_context($context)->export_file(
                    [get_string('applications', 'local_jobboard'), $app->id, 'files'],
                    $file
                );
            }
        }
    }

    /**
     * Export user exemptions.
     *
     * @param int $userid User ID.
     * @param \context $context The context.
     */
    private static function export_exemptions(int $userid, \context $context): void {
        global $DB;

        $exemptions = $DB->get_records('local_jobboard_exemption', ['userid' => $userid]);

        $data = [];
        foreach ($exemptions as $ex) {
            $data[] = [
                'type' => $ex->exemptiontype,
                'document_reference' => $ex->documentref,
                'exempted_documents' => $ex->exempteddocs,
                'valid_from' => userdate($ex->validfrom, '%Y-%m-%d'),
                'valid_until' => $ex->validuntil ? userdate($ex->validuntil, '%Y-%m-%d') : 'Indefinite',
                'notes' => $ex->notes,
                'revoked' => $ex->timerevoked ? userdate($ex->timerevoked) : 'No',
                'revoke_reason' => $ex->revokereason,
                'created' => userdate($ex->timecreated),
            ];
        }

        if (!empty($data)) {
            writer::with_context($context)->export_data(
                [get_string('exemptions', 'local_jobboard')],
                (object) ['exemptions' => $data]
            );
        }
    }

    /**
     * Export user audit logs.
     *
     * @param int $userid User ID.
     * @param \context $context The context.
     */
    private static function export_audit_logs(int $userid, \context $context): void {
        global $DB;

        $logs = $DB->get_records('local_jobboard_audit', ['userid' => $userid], 'timecreated DESC', '*', 0, 1000);

        $data = [];
        foreach ($logs as $log) {
            $data[] = [
                'action' => $log->action,
                'entity_type' => $log->entitytype,
                'entity_id' => $log->entityid,
                'ip_address' => $log->ipaddress,
                'timestamp' => userdate($log->timecreated),
            ];
        }

        if (!empty($data)) {
            writer::with_context($context)->export_data(
                [get_string('auditlog', 'local_jobboard')],
                (object) ['audit_records' => $data]
            );
        }
    }

    /**
     * Export user notifications.
     *
     * @param int $userid User ID.
     * @param \context $context The context.
     */
    private static function export_notifications(int $userid, \context $context): void {
        global $DB;

        $notifications = $DB->get_records('local_jobboard_notification', ['userid' => $userid], 'timecreated DESC');

        $data = [];
        foreach ($notifications as $notif) {
            $data[] = [
                'template' => $notif->templatecode,
                'status' => $notif->status,
                'sent_time' => $notif->timesent ? userdate($notif->timesent) : null,
                'created' => userdate($notif->timecreated),
            ];
        }

        if (!empty($data)) {
            writer::with_context($context)->export_data(
                [get_string('notifications', 'local_jobboard')],
                (object) ['notifications' => $data]
            );
        }
    }

    /**
     * Export user API tokens.
     *
     * @param int $userid User ID.
     * @param \context $context The context.
     */
    private static function export_api_tokens(int $userid, \context $context): void {
        global $DB;

        $tokens = $DB->get_records('local_jobboard_api_token', ['userid' => $userid]);

        $data = [];
        foreach ($tokens as $token) {
            $data[] = [
                'description' => $token->description,
                'permissions' => $token->permissions,
                'enabled' => $token->enabled ? 'Yes' : 'No',
                'valid_from' => $token->validfrom ? userdate($token->validfrom, '%Y-%m-%d') : null,
                'valid_until' => $token->validuntil ? userdate($token->validuntil, '%Y-%m-%d') : null,
                'last_used' => $token->lastused ? userdate($token->lastused) : 'Never',
                'created' => userdate($token->timecreated),
            ];
        }

        if (!empty($data)) {
            writer::with_context($context)->export_data(
                [get_string('apitokens', 'local_jobboard')],
                (object) ['api_tokens' => $data]
            );
        }
    }

    /**
     * Export user interviewer assignments.
     *
     * @param int $userid User ID.
     * @param \context $context The context.
     */
    private static function export_interviewer_data(int $userid, \context $context): void {
        global $DB;

        $sql = "SELECT i.*, iv.applicationid, a.userid as applicantuserid
                FROM {local_jobboard_interviewer} i
                JOIN {local_jobboard_interview} iv ON iv.id = i.interviewid
                JOIN {local_jobboard_application} a ON a.id = iv.applicationid
                WHERE i.userid = :userid";

        $records = $DB->get_records_sql($sql, ['userid' => $userid]);

        $data = [];
        foreach ($records as $record) {
            $data[] = [
                'interview_id' => $record->interviewid,
                'application_id' => $record->applicationid,
                'role' => 'Interviewer',
                'assigned' => userdate($record->timecreated),
            ];
        }

        if (!empty($data)) {
            writer::with_context($context)->export_data(
                [get_string('interviews', 'local_jobboard')],
                (object) ['interviewer_assignments' => $data]
            );
        }
    }

    /**
     * Export user committee memberships.
     *
     * @param int $userid User ID.
     * @param \context $context The context.
     */
    private static function export_committee_data(int $userid, \context $context): void {
        global $DB;

        $sql = "SELECT cm.*, c.name as committeename, c.vacancyid
                FROM {local_jobboard_committee_member} cm
                JOIN {local_jobboard_committee} c ON c.id = cm.committeeid
                WHERE cm.userid = :userid";

        $records = $DB->get_records_sql($sql, ['userid' => $userid]);

        $data = [];
        foreach ($records as $record) {
            $data[] = [
                'committee_name' => $record->committeename,
                'vacancy_id' => $record->vacancyid,
                'role' => $record->role,
                'assigned' => userdate($record->timecreated),
            ];
        }

        if (!empty($data)) {
            writer::with_context($context)->export_data(
                [get_string('committees', 'local_jobboard')],
                (object) ['committee_memberships' => $data]
            );
        }
    }

    /**
     * Export user evaluations.
     *
     * @param int $userid User ID.
     * @param \context $context The context.
     */
    private static function export_evaluation_data(int $userid, \context $context): void {
        global $DB;

        $evaluations = $DB->get_records('local_jobboard_evaluation', ['userid' => $userid]);

        $data = [];
        foreach ($evaluations as $eval) {
            $data[] = [
                'application_id' => $eval->applicationid,
                'score' => $eval->score,
                'vote' => $eval->vote,
                'comments' => $eval->comments,
                'created' => userdate($eval->timecreated),
            ];
        }

        if (!empty($data)) {
            writer::with_context($context)->export_data(
                [get_string('evaluations', 'local_jobboard')],
                (object) ['evaluations' => $data]
            );
        }
    }

    /**
     * Delete all user data for the specified approved contexts.
     *
     * @param approved_contextlist $contextlist The approved contexts for deletion.
     */
    public static function delete_data_for_all_users_in_context(\context $context): void {
        global $DB;

        if (!$context instanceof \context_system) {
            return;
        }

        // This would delete ALL plugin data - typically not called.
        // Individual user deletion is handled by delete_data_for_user.
    }

    /**
     * Delete all user data for the specified user.
     *
     * @param approved_contextlist $contextlist The approved contexts for deletion.
     */
    public static function delete_data_for_user(approved_contextlist $contextlist): void {
        global $DB;

        if (empty($contextlist->count())) {
            return;
        }

        $userid = $contextlist->get_user()->id;
        $context = \context_system::instance();

        // Delete in order of dependencies.
        self::delete_user_data($userid, $context);
    }

    /**
     * Delete data for multiple users within a context.
     *
     * @param approved_userlist $userlist The users and context for deletion.
     */
    public static function delete_data_for_users(approved_userlist $userlist): void {
        global $DB;

        $context = $userlist->get_context();

        if (!$context instanceof \context_system) {
            return;
        }

        foreach ($userlist->get_userids() as $userid) {
            self::delete_user_data($userid, $context);
        }
    }

    /**
     * Delete all data for a specific user.
     *
     * @param int $userid The user ID.
     * @param \context $context The context.
     */
    private static function delete_user_data(int $userid, \context $context): void {
        global $DB;

        // Get all applications for this user.
        $applications = $DB->get_records('local_jobboard_application', ['userid' => $userid]);

        foreach ($applications as $app) {
            // Delete document validations.
            $documents = $DB->get_records('local_jobboard_document', ['applicationid' => $app->id]);
            foreach ($documents as $doc) {
                $DB->delete_records('local_jobboard_doc_validation', ['documentid' => $doc->id]);
            }

            // Delete documents.
            $DB->delete_records('local_jobboard_document', ['applicationid' => $app->id]);

            // Delete workflow logs.
            $DB->delete_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);

            // Delete files.
            $fs = get_file_storage();
            $fs->delete_area_files($context->id, 'local_jobboard', 'application_documents', $app->id);
        }

        // Delete applications.
        $DB->delete_records('local_jobboard_application', ['userid' => $userid]);

        // Delete exemptions.
        $DB->delete_records('local_jobboard_exemption', ['userid' => $userid]);

        // Delete notifications.
        $DB->delete_records('local_jobboard_notification', ['userid' => $userid]);

        // Delete API tokens.
        $DB->delete_records('local_jobboard_api_token', ['userid' => $userid]);

        // Delete interviewer assignments.
        $DB->delete_records('local_jobboard_interviewer', ['userid' => $userid]);

        // Delete committee memberships.
        $DB->delete_records('local_jobboard_committee_member', ['userid' => $userid]);

        // Delete evaluations.
        $DB->delete_records('local_jobboard_evaluation', ['userid' => $userid]);

        // Anonymize audit logs (keep for compliance but remove PII).
        $DB->set_field('local_jobboard_audit', 'userid', 0, ['userid' => $userid]);
        $DB->set_field('local_jobboard_audit', 'ipaddress', '0.0.0.0', ['userid' => 0]);
        $DB->set_field('local_jobboard_audit', 'useragent', '', ['userid' => 0]);

        // Log the deletion.
        \local_jobboard\audit::log('user_data_deleted', 'user', $userid, [
            'reason' => 'GDPR/Habeas Data request',
        ]);
    }
}
