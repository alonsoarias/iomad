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

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * PHPUnit tests for application class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @category  test
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_jobboard\application
 */
class application_test extends \advanced_testcase {

    /** @var \stdClass Test user. */
    protected $user;

    /** @var \stdClass Test vacancy. */
    protected $vacancy;

    /** @var \local_jobboard_generator Plugin generator. */
    protected $generator;

    /**
     * Set up test fixtures.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);

        // Create a test user.
        $this->user = $this->getDataGenerator()->create_user([
            'firstname' => 'Test',
            'lastname' => 'User',
            'email' => 'testuser@example.com',
        ]);

        // Get plugin generator.
        $this->generator = $this->getDataGenerator()->get_plugin_generator('local_jobboard');

        // Create a test vacancy.
        $this->vacancy = $this->generator->create_vacancy([
            'code' => 'TEST001',
            'title' => 'Test Vacancy',
            'status' => 'published',
            'opendate' => time() - 86400,
            'closedate' => time() + (30 * 86400),
        ]);
    }

    /**
     * Test creating an application.
     */
    public function test_create_application(): void {
        $this->setUser($this->user);

        $application = application::create([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'digitalsignature' => 'Test User',
        ]);

        $this->assertNotNull($application);
        $this->assertIsInt($application->id);
        $this->assertEquals($this->vacancy->id, $application->vacancyid);
        $this->assertEquals($this->user->id, $application->userid);
        $this->assertEquals('submitted', $application->status);
    }

    /**
     * Test application creation requires consent.
     */
    public function test_create_application_requires_consent(): void {
        $this->setUser($this->user);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage('consentrequired');

        application::create([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'consentgiven' => 0,
            'digitalsignature' => 'Test User',
        ]);
    }

    /**
     * Test duplicate application prevention.
     */
    public function test_duplicate_application_prevented(): void {
        $this->setUser($this->user);

        // Create first application.
        $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
        ]);

        // Verify user has applied.
        $this->assertTrue(application::user_has_applied($this->user->id, $this->vacancy->id));

        // Attempt to create duplicate should fail.
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage('alreadyapplied');

        application::create([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'digitalsignature' => 'Test User',
        ]);
    }

    /**
     * Test application to closed vacancy fails.
     */
    public function test_cannot_apply_to_closed_vacancy(): void {
        // Create closed vacancy.
        $closedvacancy = $this->generator->create_vacancy([
            'code' => 'CLOSED001',
            'title' => 'Closed Vacancy',
            'status' => 'closed',
        ]);

        $this->setUser($this->user);

        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage('vacancyclosed');

        application::create([
            'vacancyid' => $closedvacancy->id,
            'userid' => $this->user->id,
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'digitalsignature' => 'Test User',
        ]);
    }

    /**
     * Test getting application by ID.
     */
    public function test_get_application(): void {
        $created = $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
        ]);

        $application = application::get($created->id);

        $this->assertNotNull($application);
        $this->assertEquals($created->id, $application->id);
        $this->assertEquals($this->vacancy->id, $application->vacancyid);
    }

    /**
     * Test getting non-existent application returns null.
     */
    public function test_get_nonexistent_application(): void {
        $application = application::get(99999);
        $this->assertNull($application);
    }

    /**
     * Test application status transitions.
     */
    public function test_status_transitions(): void {
        $this->setAdminUser();

        $created = $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
        ]);

        $application = application::get($created->id);

        // submitted -> under_review is valid.
        $this->assertTrue($application->can_transition_to('under_review'));

        $result = $application->transition_to('under_review');
        $this->assertTrue($result);
        $this->assertEquals('under_review', $application->status);

        // under_review -> docs_validated is valid.
        $this->assertTrue($application->can_transition_to('docs_validated'));

        $result = $application->transition_to('docs_validated');
        $this->assertTrue($result);
        $this->assertEquals('docs_validated', $application->status);
    }

    /**
     * Test invalid status transition fails.
     */
    public function test_invalid_status_transition(): void {
        $this->setAdminUser();

        $created = $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
        ]);

        $application = application::get($created->id);

        // submitted -> selected is not valid.
        $this->assertFalse($application->can_transition_to('selected'));

        $result = $application->transition_to('selected');
        $this->assertFalse($result);
        $this->assertEquals('submitted', $application->status);
    }

    /**
     * Test application withdrawal.
     */
    public function test_withdraw_application(): void {
        $this->setUser($this->user);

        $created = $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
        ]);

        $application = application::get($created->id);

        $result = $application->withdraw();
        $this->assertTrue($result);
        $this->assertEquals('withdrawn', $application->status);
    }

    /**
     * Test cannot withdraw application in non-withdrawable status.
     */
    public function test_cannot_withdraw_selected_application(): void {
        $this->setAdminUser();

        $created = $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'selected',
        ]);

        $application = application::get($created->id);

        $result = $application->withdraw();
        $this->assertFalse($result);
        $this->assertEquals('selected', $application->status);
    }

    /**
     * Test getting application list with filters.
     */
    public function test_get_application_list(): void {
        // Create multiple users and applications.
        $user2 = $this->getDataGenerator()->create_user();

        $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
        ]);

        $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $user2->id,
            'status' => 'under_review',
        ]);

        // Get all applications for vacancy.
        $result = application::get_list(['vacancyid' => $this->vacancy->id]);
        $this->assertEquals(2, $result['total']);

        // Filter by status.
        $result = application::get_list([
            'vacancyid' => $this->vacancy->id,
            'status' => 'submitted',
        ]);
        $this->assertEquals(1, $result['total']);
    }

    /**
     * Test getting applications for a user.
     */
    public function test_get_applications_for_user(): void {
        // Create second vacancy.
        $vacancy2 = $this->generator->create_vacancy([
            'code' => 'TEST002',
            'title' => 'Second Vacancy',
            'status' => 'published',
        ]);

        $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
        ]);

        $this->generator->create_application([
            'vacancyid' => $vacancy2->id,
            'userid' => $this->user->id,
        ]);

        $result = application::get_list(['userid' => $this->user->id]);
        $this->assertEquals(2, $result['total']);
    }

    /**
     * Test getting vacancy stats.
     */
    public function test_get_vacancy_stats(): void {
        $user2 = $this->getDataGenerator()->create_user();
        $user3 = $this->getDataGenerator()->create_user();

        $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
        ]);

        $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $user2->id,
            'status' => 'under_review',
        ]);

        $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $user3->id,
            'status' => 'selected',
        ]);

        $stats = application::get_stats_for_vacancy($this->vacancy->id);

        $this->assertEquals(1, $stats['submitted']);
        $this->assertEquals(1, $stats['under_review']);
        $this->assertEquals(1, $stats['selected']);
    }

    /**
     * Test status history is recorded.
     */
    public function test_status_history(): void {
        $this->setAdminUser();

        $created = $this->generator->create_application([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'status' => 'submitted',
        ]);

        $application = application::get($created->id);
        $application->transition_to('under_review', 'Starting review');
        $application->transition_to('docs_validated', 'All documents OK');

        $history = $application->get_status_history();

        $this->assertCount(2, $history);
        $this->assertEquals('under_review', $history[0]->newstatus);
        $this->assertEquals('docs_validated', $history[1]->newstatus);
    }

    /**
     * Test exemption flag on application.
     */
    public function test_exemption_application(): void {
        $this->setUser($this->user);

        // Create exemption for user.
        exemption::create($this->user->id, exemption::TYPE_HISTORICO_ISER, [
            'documentref' => 'RES-2024-001',
        ]);

        $application = application::create([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'digitalsignature' => 'Test User',
            'isexemption' => 1,
        ]);

        $this->assertEquals(1, $application->isexemption);
    }

    /**
     * Test application with cover letter.
     */
    public function test_application_with_cover_letter(): void {
        $this->setUser($this->user);

        $coverletter = "Estimados señores,\n\nMe dirijo a ustedes para expresar mi interés en la vacante.";

        $application = application::create([
            'vacancyid' => $this->vacancy->id,
            'userid' => $this->user->id,
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'digitalsignature' => 'Test User',
            'coverletter' => $coverletter,
        ]);

        $this->assertEquals($coverletter, $application->coverletter);
    }
}
