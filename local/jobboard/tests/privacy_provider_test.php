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
 * Unit tests for the privacy provider.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\privacy;

use core_privacy\local\metadata\collection;
use core_privacy\local\request\approved_contextlist;
use core_privacy\local\request\approved_userlist;
use core_privacy\local\request\writer;
use core_privacy\tests\provider_testcase;

defined('MOODLE_INTERNAL') || die();

/**
 * Test class for privacy provider functionality.
 *
 * @coversDefaultClass \local_jobboard\privacy\provider
 */
class provider_test extends provider_testcase {

    /** @var \stdClass Test user. */
    protected $user;

    /** @var \stdClass Test vacancy. */
    protected $vacancy;

    /**
     * Set up before each test.
     */
    protected function setUp(): void {
        global $DB;

        parent::setUp();
        $this->resetAfterTest(true);
        $this->user = $this->getDataGenerator()->create_user();

        // Create a test vacancy.
        $this->vacancy = (object) [
            'code' => 'TEST001',
            'title' => 'Test Vacancy',
            'description' => 'Test description',
            'descriptionformat' => FORMAT_HTML,
            'contracttype' => 'catedra',
            'companyid' => 0,
            'opendate' => time() - 86400,
            'closedate' => time() + 86400,
            'positions' => 1,
            'status' => 'published',
            'createdby' => $this->user->id,
            'timecreated' => time(),
        ];
        $this->vacancy->id = $DB->insert_record('local_jobboard_vacancy', $this->vacancy);
    }

    /**
     * Test that provider implements required interfaces.
     */
    public function test_provider_implements_interfaces(): void {
        $this->assertInstanceOf(
            \core_privacy\local\metadata\provider::class,
            new provider()
        );
        $this->assertInstanceOf(
            \core_privacy\local\request\plugin\provider::class,
            new provider()
        );
        $this->assertInstanceOf(
            \core_privacy\local\request\core_userlist_provider::class,
            new provider()
        );
    }

    /**
     * Test get_metadata returns valid collection.
     *
     * @covers ::get_metadata
     */
    public function test_get_metadata(): void {
        $collection = new collection('local_jobboard');
        $result = provider::get_metadata($collection);

        $this->assertInstanceOf(collection::class, $result);

        $items = $result->get_collection();
        $this->assertNotEmpty($items);

        // Check that expected tables are included.
        $tableNames = [];
        foreach ($items as $item) {
            if ($item instanceof \core_privacy\local\metadata\types\database_table) {
                $tableNames[] = $item->get_name();
            }
        }

        $this->assertContains('local_jobboard_application', $tableNames);
        $this->assertContains('local_jobboard_document', $tableNames);
        $this->assertContains('local_jobboard_exemption', $tableNames);
        $this->assertContains('local_jobboard_audit', $tableNames);
        $this->assertContains('local_jobboard_api_token', $tableNames);
        $this->assertContains('local_jobboard_notification', $tableNames);
    }

    /**
     * Test get_contexts_for_userid returns empty for user with no data.
     *
     * @covers ::get_contexts_for_userid
     */
    public function test_get_contexts_for_userid_no_data(): void {
        $contextlist = provider::get_contexts_for_userid($this->user->id);

        $this->assertEmpty($contextlist->get_contextids());
    }

    /**
     * Test get_contexts_for_userid returns contexts for user with applications.
     *
     * @covers ::get_contexts_for_userid
     */
    public function test_get_contexts_for_userid_with_applications(): void {
        global $DB;

        // Create an application.
        $application = (object) [
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'timecreated' => time(),
        ];
        $DB->insert_record('local_jobboard_application', $application);

        $contextlist = provider::get_contexts_for_userid($this->user->id);

        $this->assertNotEmpty($contextlist->get_contextids());
        $this->assertContains(\context_system::instance()->id, $contextlist->get_contextids());
    }

    /**
     * Test get_users_in_context returns users with applications.
     *
     * @covers ::get_users_in_context
     */
    public function test_get_users_in_context(): void {
        global $DB;

        $context = \context_system::instance();

        // Create applications for multiple users.
        $user2 = $this->getDataGenerator()->create_user();

        $application1 = (object) [
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'timecreated' => time(),
        ];
        $DB->insert_record('local_jobboard_application', $application1);

        $application2 = (object) [
            'vacancyid' => $this->vacancy->id,
            'userid' => $user2->id,
            'status' => 'submitted',
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'timecreated' => time(),
        ];
        $DB->insert_record('local_jobboard_application', $application2);

        $userlist = new \core_privacy\local\request\userlist($context, 'local_jobboard');
        provider::get_users_in_context($userlist);

        $userids = $userlist->get_userids();
        $this->assertContains($this->user->id, $userids);
        $this->assertContains($user2->id, $userids);
    }

    /**
     * Test export_user_data exports applications.
     *
     * @covers ::export_user_data
     */
    public function test_export_user_data(): void {
        global $DB;

        // Create an application with documents.
        $application = (object) [
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
            'coverletter' => 'My cover letter',
            'digitalsignature' => 'John Doe',
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'timecreated' => time(),
        ];
        $applicationId = $DB->insert_record('local_jobboard_application', $application);

        // Create a document.
        $document = (object) [
            'applicationid' => $applicationId,
            'userid' => $this->user->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula.pdf',
            'filepath' => '/test/',
            'filesize' => 1024,
            'mimetype' => 'application/pdf',
            'timecreated' => time(),
        ];
        $DB->insert_record('local_jobboard_document', $document);

        // Create an API token.
        $token = (object) [
            'token' => hash('sha256', 'test_token'),
            'userid' => $this->user->id,
            'description' => 'Test token',
            'permissions' => json_encode(['view_vacancies']),
            'ipwhitelist' => json_encode([]),
            'enabled' => 1,
            'timecreated' => time(),
        ];
        $DB->insert_record('local_jobboard_api_token', $token);

        // Export data.
        $context = \context_system::instance();
        $contextlist = new approved_contextlist($this->user, 'local_jobboard', [$context->id]);

        provider::export_user_data($contextlist);

        $writer = writer::with_context($context);
        $this->assertTrue($writer->has_any_data());

        // Check applications were exported.
        $data = $writer->get_data(['jobboard', 'applications']);
        $this->assertNotNull($data);

        // Check API tokens were exported.
        $tokenData = $writer->get_data(['jobboard', 'api_tokens']);
        $this->assertNotNull($tokenData);
    }

    /**
     * Test delete_data_for_all_users_in_context.
     *
     * @covers ::delete_data_for_all_users_in_context
     */
    public function test_delete_data_for_all_users_in_context(): void {
        global $DB;

        $context = \context_system::instance();

        // Create applications for multiple users.
        $user2 = $this->getDataGenerator()->create_user();

        $application1 = (object) [
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'timecreated' => time(),
        ];
        $app1Id = $DB->insert_record('local_jobboard_application', $application1);

        $application2 = (object) [
            'vacancyid' => $this->vacancy->id,
            'userid' => $user2->id,
            'status' => 'submitted',
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'timecreated' => time(),
        ];
        $app2Id = $DB->insert_record('local_jobboard_application', $application2);

        // Create documents.
        $document1 = (object) [
            'applicationid' => $app1Id,
            'userid' => $this->user->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula.pdf',
            'filepath' => '/test/',
            'filesize' => 1024,
            'mimetype' => 'application/pdf',
            'timecreated' => time(),
        ];
        $DB->insert_record('local_jobboard_document', $document1);

        $this->assertTrue($DB->record_exists('local_jobboard_application', ['id' => $app1Id]));
        $this->assertTrue($DB->record_exists('local_jobboard_application', ['id' => $app2Id]));
        $this->assertTrue($DB->record_exists('local_jobboard_document', ['applicationid' => $app1Id]));

        // Delete all data in context.
        provider::delete_data_for_all_users_in_context($context);

        // All data should be deleted.
        $this->assertFalse($DB->record_exists('local_jobboard_application', ['id' => $app1Id]));
        $this->assertFalse($DB->record_exists('local_jobboard_application', ['id' => $app2Id]));
        $this->assertFalse($DB->record_exists('local_jobboard_document', ['applicationid' => $app1Id]));
    }

    /**
     * Test delete_data_for_user.
     *
     * @covers ::delete_data_for_user
     */
    public function test_delete_data_for_user(): void {
        global $DB;

        $context = \context_system::instance();
        $user2 = $this->getDataGenerator()->create_user();

        // Create applications for both users.
        $application1 = (object) [
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'timecreated' => time(),
        ];
        $app1Id = $DB->insert_record('local_jobboard_application', $application1);

        $application2 = (object) [
            'vacancyid' => $this->vacancy->id,
            'userid' => $user2->id,
            'status' => 'submitted',
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'timecreated' => time(),
        ];
        $app2Id = $DB->insert_record('local_jobboard_application', $application2);

        // Create exemptions.
        $exemption = (object) [
            'userid' => $this->user->id,
            'exemptiontype' => 'iser_historic',
            'validfrom' => time(),
            'createdby' => 2,
            'timecreated' => time(),
        ];
        $DB->insert_record('local_jobboard_exemption', $exemption);

        // Create API tokens.
        $token = (object) [
            'token' => hash('sha256', 'test_token'),
            'userid' => $this->user->id,
            'description' => 'Test token',
            'permissions' => json_encode(['view_vacancies']),
            'ipwhitelist' => json_encode([]),
            'enabled' => 1,
            'timecreated' => time(),
        ];
        $DB->insert_record('local_jobboard_api_token', $token);

        // Verify data exists.
        $this->assertTrue($DB->record_exists('local_jobboard_application', ['userid' => $this->user->id]));
        $this->assertTrue($DB->record_exists('local_jobboard_exemption', ['userid' => $this->user->id]));
        $this->assertTrue($DB->record_exists('local_jobboard_api_token', ['userid' => $this->user->id]));
        $this->assertTrue($DB->record_exists('local_jobboard_application', ['userid' => $user2->id]));

        // Delete data for first user only.
        $contextlist = new approved_contextlist($this->user, 'local_jobboard', [$context->id]);
        provider::delete_data_for_user($contextlist);

        // First user's data should be deleted.
        $this->assertFalse($DB->record_exists('local_jobboard_application', ['userid' => $this->user->id]));
        $this->assertFalse($DB->record_exists('local_jobboard_exemption', ['userid' => $this->user->id]));
        $this->assertFalse($DB->record_exists('local_jobboard_api_token', ['userid' => $this->user->id]));

        // Second user's data should remain.
        $this->assertTrue($DB->record_exists('local_jobboard_application', ['userid' => $user2->id]));
    }

    /**
     * Test delete_data_for_users (userlist).
     *
     * @covers ::delete_data_for_users
     */
    public function test_delete_data_for_users(): void {
        global $DB;

        $context = \context_system::instance();
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        // Create applications for all users.
        foreach ([$this->user, $user2, $user3] as $user) {
            $application = (object) [
                'vacancyid' => $this->vacancy->id,
                'userid' => $user->id,
                'status' => 'submitted',
                'consentgiven' => 1,
                'consenttimestamp' => time(),
                'consentip' => '127.0.0.1',
                'timecreated' => time(),
            ];
            $DB->insert_record('local_jobboard_application', $application);
        }

        // Delete data for first two users.
        $userlist = new approved_userlist($context, 'local_jobboard', [$this->user->id, $user2->id]);
        provider::delete_data_for_users($userlist);

        // First two users' data should be deleted.
        $this->assertFalse($DB->record_exists('local_jobboard_application', ['userid' => $this->user->id]));
        $this->assertFalse($DB->record_exists('local_jobboard_application', ['userid' => $user2->id]));

        // Third user's data should remain.
        $this->assertTrue($DB->record_exists('local_jobboard_application', ['userid' => $user3->id]));
    }

    /**
     * Test that document files are deleted with user data.
     *
     * @covers ::delete_data_for_user
     */
    public function test_document_files_deleted(): void {
        global $DB;

        $context = \context_system::instance();

        // Create application with document.
        $application = (object) [
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'timecreated' => time(),
        ];
        $appId = $DB->insert_record('local_jobboard_application', $application);

        // Create a document record.
        $document = (object) [
            'applicationid' => $appId,
            'userid' => $this->user->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula.pdf',
            'filepath' => '/test/',
            'filesize' => 1024,
            'mimetype' => 'application/pdf',
            'timecreated' => time(),
        ];
        $docId = $DB->insert_record('local_jobboard_document', $document);

        $this->assertTrue($DB->record_exists('local_jobboard_document', ['id' => $docId]));

        // Delete user data.
        $contextlist = new approved_contextlist($this->user, 'local_jobboard', [$context->id]);
        provider::delete_data_for_user($contextlist);

        // Document record should be deleted.
        $this->assertFalse($DB->record_exists('local_jobboard_document', ['id' => $docId]));
    }

    /**
     * Test audit logs are deleted with user data.
     *
     * @covers ::delete_data_for_user
     */
    public function test_audit_logs_deleted(): void {
        global $DB;

        $context = \context_system::instance();

        // Create audit log entries.
        $audit = (object) [
            'userid' => $this->user->id,
            'action' => 'application_submitted',
            'entitytype' => 'application',
            'entityid' => 1,
            'ipaddress' => '127.0.0.1',
            'useragent' => 'PHPUnit',
            'timecreated' => time(),
        ];
        $auditId = $DB->insert_record('local_jobboard_audit', $audit);

        $this->assertTrue($DB->record_exists('local_jobboard_audit', ['id' => $auditId]));

        // Delete user data.
        $contextlist = new approved_contextlist($this->user, 'local_jobboard', [$context->id]);
        provider::delete_data_for_user($contextlist);

        // Audit logs should be deleted.
        $this->assertFalse($DB->record_exists('local_jobboard_audit', ['id' => $auditId]));
    }

    /**
     * Test notifications are deleted with user data.
     *
     * @covers ::delete_data_for_user
     */
    public function test_notifications_deleted(): void {
        global $DB;

        $context = \context_system::instance();

        // Create notification records.
        $notification = (object) [
            'userid' => $this->user->id,
            'applicationid' => 1,
            'templatecode' => 'application_received',
            'status' => 'sent',
            'timecreated' => time(),
            'timesent' => time(),
        ];
        $notifId = $DB->insert_record('local_jobboard_notification', $notification);

        $this->assertTrue($DB->record_exists('local_jobboard_notification', ['id' => $notifId]));

        // Delete user data.
        $contextlist = new approved_contextlist($this->user, 'local_jobboard', [$context->id]);
        provider::delete_data_for_user($contextlist);

        // Notifications should be deleted.
        $this->assertFalse($DB->record_exists('local_jobboard_notification', ['id' => $notifId]));
    }
}
