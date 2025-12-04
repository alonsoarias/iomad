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

use advanced_testcase;
use context_system;

/**
 * Unit tests for reviewer class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @category  test
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_jobboard\reviewer
 */
class reviewer_test extends advanced_testcase {

    /** @var \stdClass Test vacancy. */
    protected $vacancy;

    /** @var \stdClass Test applicant user. */
    protected $applicant;

    /** @var \stdClass Test reviewer user. */
    protected $reviewer1;

    /** @var \stdClass Test reviewer user. */
    protected $reviewer2;

    /** @var \stdClass Test application. */
    protected $application;

    /**
     * Set up test fixtures.
     */
    protected function setUp(): void {
        global $DB;
        parent::setUp();
        $this->resetAfterTest(true);

        // Create test users.
        $this->applicant = $this->getDataGenerator()->create_user([
            'username' => 'applicant1',
            'firstname' => 'John',
            'lastname' => 'Applicant',
        ]);

        $this->reviewer1 = $this->getDataGenerator()->create_user([
            'username' => 'reviewer1',
            'firstname' => 'Review',
            'lastname' => 'One',
        ]);

        $this->reviewer2 = $this->getDataGenerator()->create_user([
            'username' => 'reviewer2',
            'firstname' => 'Review',
            'lastname' => 'Two',
        ]);

        // Assign reviewer capability.
        $context = context_system::instance();
        $roleid = $this->getDataGenerator()->create_role();
        assign_capability('local/jobboard:reviewdocuments', CAP_ALLOW, $roleid, $context->id);
        role_assign($roleid, $this->reviewer1->id, $context->id);
        role_assign($roleid, $this->reviewer2->id, $context->id);

        // Create test vacancy.
        $this->vacancy = new \stdClass();
        $this->vacancy->code = 'TEST001';
        $this->vacancy->title = 'Test Vacancy';
        $this->vacancy->description = 'Test description';
        $this->vacancy->contracttype = 'catedra';
        $this->vacancy->status = 'published';
        $this->vacancy->opendate = time() - 86400;
        $this->vacancy->closedate = time() + (30 * 86400);
        $this->vacancy->positions = 1;
        $this->vacancy->createdby = 2;
        $this->vacancy->timecreated = time();
        $this->vacancy->id = $DB->insert_record('local_jobboard_vacancy', $this->vacancy);

        // Create test application.
        $this->application = new \stdClass();
        $this->application->vacancyid = $this->vacancy->id;
        $this->application->userid = $this->applicant->id;
        $this->application->status = 'submitted';
        $this->application->consentgiven = 1;
        $this->application->consenttimestamp = time();
        $this->application->digitalsignature = 'John Applicant';
        $this->application->timecreated = time();
        $this->application->id = $DB->insert_record('local_jobboard_application', $this->application);
    }

    /**
     * Test assigning a reviewer to an application.
     */
    public function test_assign_reviewer(): void {
        global $DB;

        $this->setUser($this->reviewer1);

        $result = reviewer::assign($this->application->id, $this->reviewer1->id);
        $this->assertTrue($result);

        // Check database.
        $app = $DB->get_record('local_jobboard_application', ['id' => $this->application->id]);
        $this->assertEquals($this->reviewer1->id, $app->reviewerid);
        $this->assertNotEmpty($app->reviewerassignedtime);
    }

    /**
     * Test assigning reviewer to non-existent application.
     */
    public function test_assign_reviewer_invalid_application(): void {
        $this->setAdminUser();

        $result = reviewer::assign(99999, $this->reviewer1->id);
        $this->assertFalse($result);
    }

    /**
     * Test bulk assignment of reviewers.
     */
    public function test_bulk_assign(): void {
        global $DB;

        $this->setAdminUser();

        // Create additional applications.
        $app2 = new \stdClass();
        $app2->vacancyid = $this->vacancy->id;
        $app2->userid = $this->getDataGenerator()->create_user()->id;
        $app2->status = 'submitted';
        $app2->consentgiven = 1;
        $app2->timecreated = time();
        $app2->id = $DB->insert_record('local_jobboard_application', $app2);

        $app3 = new \stdClass();
        $app3->vacancyid = $this->vacancy->id;
        $app3->userid = $this->getDataGenerator()->create_user()->id;
        $app3->status = 'submitted';
        $app3->consentgiven = 1;
        $app3->timecreated = time();
        $app3->id = $DB->insert_record('local_jobboard_application', $app3);

        // Bulk assign.
        $applicationids = [$this->application->id, $app2->id, $app3->id];
        $results = reviewer::bulk_assign($applicationids, $this->reviewer1->id);

        $this->assertEquals(3, $results['success']);
        $this->assertEquals(0, $results['failed']);

        // Verify all applications have reviewer assigned.
        foreach ($applicationids as $appid) {
            $app = $DB->get_record('local_jobboard_application', ['id' => $appid]);
            $this->assertEquals($this->reviewer1->id, $app->reviewerid);
        }
    }

    /**
     * Test getting reviewer workload.
     */
    public function test_get_reviewer_workload(): void {
        global $DB;

        $this->setAdminUser();

        // Initially no workload.
        $workload = reviewer::get_reviewer_workload($this->reviewer1->id);
        $this->assertEquals(0, $workload);

        // Assign application.
        reviewer::assign($this->application->id, $this->reviewer1->id);

        // Should now have workload of 1.
        $workload = reviewer::get_reviewer_workload($this->reviewer1->id);
        $this->assertEquals(1, $workload);

        // Create and assign more applications.
        for ($i = 0; $i < 5; $i++) {
            $app = new \stdClass();
            $app->vacancyid = $this->vacancy->id;
            $app->userid = $this->getDataGenerator()->create_user()->id;
            $app->status = 'submitted';
            $app->consentgiven = 1;
            $app->reviewerid = $this->reviewer1->id;
            $app->timecreated = time();
            $DB->insert_record('local_jobboard_application', $app);
        }

        // Should now have workload of 6.
        $workload = reviewer::get_reviewer_workload($this->reviewer1->id);
        $this->assertEquals(6, $workload);
    }

    /**
     * Test auto-assignment of reviewers.
     */
    public function test_auto_assign(): void {
        global $DB;

        $this->setAdminUser();

        // Create multiple unassigned applications.
        for ($i = 0; $i < 10; $i++) {
            $app = new \stdClass();
            $app->vacancyid = $this->vacancy->id;
            $app->userid = $this->getDataGenerator()->create_user()->id;
            $app->status = 'submitted';
            $app->consentgiven = 1;
            $app->timecreated = time();
            $DB->insert_record('local_jobboard_application', $app);
        }

        // Plus the one from setup.
        $unassigned = $DB->count_records_select('local_jobboard_application',
            'reviewerid IS NULL OR reviewerid = 0');
        $this->assertEquals(11, $unassigned);

        // Auto-assign with max 10 per reviewer.
        $assigned = reviewer::auto_assign($this->vacancy->id, 10);

        // All should be assigned (we have 2 reviewers).
        $this->assertGreaterThan(0, $assigned);

        // Check workloads are balanced.
        $workload1 = reviewer::get_reviewer_workload($this->reviewer1->id);
        $workload2 = reviewer::get_reviewer_workload($this->reviewer2->id);

        // Difference should be at most 1 (for odd number of applications).
        $this->assertLessThanOrEqual(1, abs($workload1 - $workload2));
    }

    /**
     * Test getting reviewer statistics.
     */
    public function test_get_reviewer_stats(): void {
        global $DB;

        $this->setAdminUser();

        // Assign application.
        reviewer::assign($this->application->id, $this->reviewer1->id);

        // Create a document and validation.
        $doc = new \stdClass();
        $doc->applicationid = $this->application->id;
        $doc->documenttype = 'cedula';
        $doc->filename = 'cedula.pdf';
        $doc->timecreated = time();
        $doc->id = $DB->insert_record('local_jobboard_document', $doc);

        // Validate the document.
        $validation = new \stdClass();
        $validation->documentid = $doc->id;
        $validation->isvalid = 1;
        $validation->validatedby = $this->reviewer1->id;
        $validation->timecreated = time();
        $DB->insert_record('local_jobboard_doc_validation', $validation);

        // Create another document and reject it.
        $doc2 = new \stdClass();
        $doc2->applicationid = $this->application->id;
        $doc2->documenttype = 'rut';
        $doc2->filename = 'rut.pdf';
        $doc2->timecreated = time();
        $doc2->id = $DB->insert_record('local_jobboard_document', $doc2);

        $validation2 = new \stdClass();
        $validation2->documentid = $doc2->id;
        $validation2->isvalid = 0;
        $validation2->validatedby = $this->reviewer1->id;
        $validation2->rejectreason = 'ilegible';
        $validation2->timecreated = time();
        $DB->insert_record('local_jobboard_doc_validation', $validation2);

        // Get stats.
        $stats = reviewer::get_reviewer_stats($this->reviewer1->id);

        $this->assertEquals(2, $stats['total']);
        $this->assertEquals(1, $stats['validated']);
        $this->assertEquals(1, $stats['rejected']);
        $this->assertArrayHasKey('avg_time_hours', $stats);
    }

    /**
     * Test reassigning applications between reviewers.
     */
    public function test_reassign(): void {
        global $DB;

        $this->setAdminUser();

        // Assign to reviewer1.
        reviewer::assign($this->application->id, $this->reviewer1->id);

        // Create more applications for reviewer1.
        $apps = [$this->application->id];
        for ($i = 0; $i < 4; $i++) {
            $app = new \stdClass();
            $app->vacancyid = $this->vacancy->id;
            $app->userid = $this->getDataGenerator()->create_user()->id;
            $app->status = 'submitted';
            $app->consentgiven = 1;
            $app->reviewerid = $this->reviewer1->id;
            $app->timecreated = time();
            $apps[] = $DB->insert_record('local_jobboard_application', $app);
        }

        // Verify reviewer1 has 5 applications.
        $this->assertEquals(5, reviewer::get_reviewer_workload($this->reviewer1->id));
        $this->assertEquals(0, reviewer::get_reviewer_workload($this->reviewer2->id));

        // Reassign 3 specific applications.
        $reassigned = reviewer::reassign($this->reviewer1->id, $this->reviewer2->id, array_slice($apps, 0, 3));

        $this->assertEquals(3, $reassigned);
        $this->assertEquals(2, reviewer::get_reviewer_workload($this->reviewer1->id));
        $this->assertEquals(3, reviewer::get_reviewer_workload($this->reviewer2->id));
    }

    /**
     * Test reassigning all applications from one reviewer to another.
     */
    public function test_reassign_all(): void {
        global $DB;

        $this->setAdminUser();

        // Create applications for reviewer1.
        for ($i = 0; $i < 5; $i++) {
            $app = new \stdClass();
            $app->vacancyid = $this->vacancy->id;
            $app->userid = $this->getDataGenerator()->create_user()->id;
            $app->status = 'submitted';
            $app->consentgiven = 1;
            $app->reviewerid = $this->reviewer1->id;
            $app->timecreated = time();
            $DB->insert_record('local_jobboard_application', $app);
        }

        // Verify initial state.
        $this->assertEquals(5, reviewer::get_reviewer_workload($this->reviewer1->id));

        // Reassign all (empty array means all).
        $reassigned = reviewer::reassign($this->reviewer1->id, $this->reviewer2->id, []);

        $this->assertEquals(5, $reassigned);
        $this->assertEquals(0, reviewer::get_reviewer_workload($this->reviewer1->id));
        $this->assertEquals(5, reviewer::get_reviewer_workload($this->reviewer2->id));
    }

    /**
     * Test getting available reviewers.
     */
    public function test_get_available_reviewers(): void {
        $this->setAdminUser();

        $reviewers = reviewer::get_available_reviewers();

        // Should have at least our 2 test reviewers.
        $this->assertGreaterThanOrEqual(2, count($reviewers));

        // Check our reviewers are in the list.
        $reviewerids = array_column($reviewers, 'id');
        $this->assertContains($this->reviewer1->id, $reviewerids);
        $this->assertContains($this->reviewer2->id, $reviewerids);
    }
}
