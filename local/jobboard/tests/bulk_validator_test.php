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

/**
 * Unit tests for bulk_validator class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @category  test
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_jobboard\bulk_validator
 */
class bulk_validator_test extends advanced_testcase {

    /** @var \stdClass Test vacancy. */
    protected $vacancy;

    /** @var \stdClass Test applicant user. */
    protected $applicant;

    /** @var \stdClass Test reviewer user. */
    protected $reviewer;

    /** @var \stdClass Test application. */
    protected $application;

    /** @var array Test documents. */
    protected $documents = [];

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

        $this->reviewer = $this->getDataGenerator()->create_user([
            'username' => 'reviewer1',
            'firstname' => 'Review',
            'lastname' => 'User',
        ]);

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
        $this->application->reviewerid = $this->reviewer->id;
        $this->application->timecreated = time();
        $this->application->id = $DB->insert_record('local_jobboard_application', $this->application);

        // Create test documents.
        $doctypes = ['cedula', 'rut', 'eps', 'antecedentes_policia', 'titulo_pregrado'];
        foreach ($doctypes as $doctype) {
            $doc = new \stdClass();
            $doc->applicationid = $this->application->id;
            $doc->documenttype = $doctype;
            $doc->filename = $doctype . '.pdf';
            $doc->issuperseded = 0;
            $doc->timecreated = time();
            $doc->id = $DB->insert_record('local_jobboard_document', $doc);
            $this->documents[$doctype] = $doc;
        }
    }

    /**
     * Test bulk validation of documents - approve.
     */
    public function test_validate_documents_approve(): void {
        global $DB;

        $this->setUser($this->reviewer);

        $docids = [
            $this->documents['cedula']->id,
            $this->documents['rut']->id,
        ];

        $results = bulk_validator::validate_documents($docids, true, null, 'Bulk approved');

        $this->assertEquals(2, $results['success']);
        $this->assertEquals(0, $results['failed']);

        // Verify validations in database.
        foreach ($docids as $docid) {
            $validation = $DB->get_record('local_jobboard_doc_validation', ['documentid' => $docid]);
            $this->assertNotEmpty($validation);
            $this->assertEquals(1, $validation->isvalid);
            $this->assertEquals($this->reviewer->id, $validation->validatedby);
            $this->assertEquals('Bulk approved', $validation->notes);
        }
    }

    /**
     * Test bulk validation of documents - reject.
     */
    public function test_validate_documents_reject(): void {
        global $DB;

        $this->setUser($this->reviewer);

        $docids = [
            $this->documents['eps']->id,
        ];

        $results = bulk_validator::validate_documents($docids, false, 'vencido', 'Document expired');

        $this->assertEquals(1, $results['success']);
        $this->assertEquals(0, $results['failed']);

        // Verify validation in database.
        $validation = $DB->get_record('local_jobboard_doc_validation', ['documentid' => $this->documents['eps']->id]);
        $this->assertNotEmpty($validation);
        $this->assertEquals(0, $validation->isvalid);
        $this->assertEquals('vencido', $validation->rejectreason);
    }

    /**
     * Test bulk validation with invalid document IDs.
     */
    public function test_validate_documents_invalid_ids(): void {
        $this->setUser($this->reviewer);

        $results = bulk_validator::validate_documents([99999, 99998], true, null, null);

        $this->assertEquals(0, $results['success']);
        $this->assertEquals(2, $results['failed']);
    }

    /**
     * Test bulk validation with mixed valid and invalid IDs.
     */
    public function test_validate_documents_mixed(): void {
        $this->setUser($this->reviewer);

        $docids = [
            $this->documents['cedula']->id,
            99999, // Invalid.
            $this->documents['rut']->id,
        ];

        $results = bulk_validator::validate_documents($docids, true, null, null);

        $this->assertEquals(2, $results['success']);
        $this->assertEquals(1, $results['failed']);
    }

    /**
     * Test getting pending documents by type.
     */
    public function test_get_pending_by_type(): void {
        $this->setUser($this->reviewer);

        $pending = bulk_validator::get_pending_by_type($this->vacancy->id, null);

        // All 5 documents should be pending.
        $this->assertArrayHasKey('cedula', $pending);
        $this->assertArrayHasKey('rut', $pending);
        $this->assertArrayHasKey('eps', $pending);
        $this->assertArrayHasKey('antecedentes_policia', $pending);
        $this->assertArrayHasKey('titulo_pregrado', $pending);

        // Validate one document.
        bulk_validator::validate_documents([$this->documents['cedula']->id], true, null, null);

        // Now should be 4 pending.
        $pending = bulk_validator::get_pending_by_type($this->vacancy->id, null);
        $this->assertArrayNotHasKey('cedula', $pending);
        $this->assertCount(4, $pending);
    }

    /**
     * Test getting pending documents filtered by reviewer.
     */
    public function test_get_pending_by_type_reviewer_filter(): void {
        global $DB;

        $this->setUser($this->reviewer);

        // Create another application with different reviewer.
        $otherreviewer = $this->getDataGenerator()->create_user();
        $app2 = new \stdClass();
        $app2->vacancyid = $this->vacancy->id;
        $app2->userid = $this->getDataGenerator()->create_user()->id;
        $app2->status = 'submitted';
        $app2->consentgiven = 1;
        $app2->reviewerid = $otherreviewer->id;
        $app2->timecreated = time();
        $app2->id = $DB->insert_record('local_jobboard_application', $app2);

        // Add document to app2.
        $doc = new \stdClass();
        $doc->applicationid = $app2->id;
        $doc->documenttype = 'cedula';
        $doc->filename = 'cedula2.pdf';
        $doc->issuperseded = 0;
        $doc->timecreated = time();
        $DB->insert_record('local_jobboard_document', $doc);

        // Get pending for our reviewer only.
        $pending = bulk_validator::get_pending_by_type($this->vacancy->id, $this->reviewer->id);

        // Should only have 5 documents (from our application).
        $totaldocs = 0;
        foreach ($pending as $docs) {
            $totaldocs += count($docs);
        }
        $this->assertEquals(5, $totaldocs);
    }

    /**
     * Test getting validation statistics.
     */
    public function test_get_validation_stats(): void {
        global $DB;

        $this->setUser($this->reviewer);

        // Initial stats - all pending.
        $stats = bulk_validator::get_validation_stats($this->vacancy->id);

        $this->assertEquals(5, $stats['pending']);
        $this->assertEquals(0, $stats['validated']);
        $this->assertEquals(0, $stats['rejected']);
        $this->assertEquals(5, $stats['total']);

        // Validate 3 documents.
        bulk_validator::validate_documents([
            $this->documents['cedula']->id,
            $this->documents['rut']->id,
            $this->documents['eps']->id,
        ], true, null, null);

        // Reject 1 document.
        bulk_validator::validate_documents([
            $this->documents['antecedentes_policia']->id,
        ], false, 'vencido', null);

        // Updated stats.
        $stats = bulk_validator::get_validation_stats($this->vacancy->id);

        $this->assertEquals(1, $stats['pending']);
        $this->assertEquals(3, $stats['validated']);
        $this->assertEquals(1, $stats['rejected']);
        $this->assertEquals(5, $stats['total']);
    }

    /**
     * Test getting rejection reasons statistics.
     */
    public function test_get_rejection_reasons_stats(): void {
        global $DB;

        $this->setUser($this->reviewer);

        // Reject documents with different reasons.
        $rejections = [
            ['id' => $this->documents['cedula']->id, 'reason' => 'ilegible'],
            ['id' => $this->documents['rut']->id, 'reason' => 'vencido'],
            ['id' => $this->documents['eps']->id, 'reason' => 'vencido'],
            ['id' => $this->documents['antecedentes_policia']->id, 'reason' => 'incompleto'],
        ];

        foreach ($rejections as $rejection) {
            bulk_validator::validate_documents([$rejection['id']], false, $rejection['reason'], null);
        }

        // Get rejection stats.
        $stats = bulk_validator::get_rejection_reasons_stats($this->vacancy->id);

        $this->assertNotEmpty($stats);

        // Find vencido - should have count of 2.
        $vencidoFound = false;
        foreach ($stats as $stat) {
            if ($stat->rejectreason === 'vencido') {
                $this->assertEquals(2, $stat->count);
                $vencidoFound = true;
            }
        }
        $this->assertTrue($vencidoFound);
    }

    /**
     * Test that already validated documents are skipped.
     */
    public function test_skip_already_validated(): void {
        global $DB;

        $this->setUser($this->reviewer);

        $docid = $this->documents['cedula']->id;

        // First validation.
        $results1 = bulk_validator::validate_documents([$docid], true, null, 'First validation');
        $this->assertEquals(1, $results1['success']);

        // Second validation should be skipped.
        $results2 = bulk_validator::validate_documents([$docid], false, 'ilegible', 'Second validation');
        $this->assertEquals(0, $results2['success']);
        $this->assertEquals(1, $results2['failed']);

        // Original validation should remain.
        $validation = $DB->get_record('local_jobboard_doc_validation', ['documentid' => $docid]);
        $this->assertEquals(1, $validation->isvalid);
        $this->assertEquals('First validation', $validation->notes);
    }

    /**
     * Test getting overall validation statistics (no vacancy filter).
     */
    public function test_get_validation_stats_all(): void {
        global $DB;

        $this->setUser($this->reviewer);

        // Create another vacancy with documents.
        $vacancy2 = new \stdClass();
        $vacancy2->code = 'TEST002';
        $vacancy2->title = 'Test Vacancy 2';
        $vacancy2->description = 'Test description 2';
        $vacancy2->contracttype = 'catedra';
        $vacancy2->status = 'published';
        $vacancy2->opendate = time() - 86400;
        $vacancy2->closedate = time() + (30 * 86400);
        $vacancy2->positions = 1;
        $vacancy2->createdby = 2;
        $vacancy2->timecreated = time();
        $vacancy2->id = $DB->insert_record('local_jobboard_vacancy', $vacancy2);

        $app2 = new \stdClass();
        $app2->vacancyid = $vacancy2->id;
        $app2->userid = $this->getDataGenerator()->create_user()->id;
        $app2->status = 'submitted';
        $app2->consentgiven = 1;
        $app2->timecreated = time();
        $app2->id = $DB->insert_record('local_jobboard_application', $app2);

        // Add 3 documents to vacancy2.
        for ($i = 0; $i < 3; $i++) {
            $doc = new \stdClass();
            $doc->applicationid = $app2->id;
            $doc->documenttype = 'cedula';
            $doc->filename = "doc{$i}.pdf";
            $doc->issuperseded = 0;
            $doc->timecreated = time();
            $DB->insert_record('local_jobboard_document', $doc);
        }

        // Get stats without vacancy filter.
        $stats = bulk_validator::get_validation_stats();

        // Should have 8 total documents (5 + 3).
        $this->assertEquals(8, $stats['total']);
        $this->assertEquals(8, $stats['pending']);
    }

    /**
     * Test validation stats with date filter.
     */
    public function test_get_validation_stats_with_date_filter(): void {
        global $DB;

        $this->setUser($this->reviewer);

        // Validate a document now.
        bulk_validator::validate_documents([$this->documents['cedula']->id], true, null, null);

        // Get stats since yesterday.
        $yesterday = strtotime('-1 day');
        $stats = bulk_validator::get_validation_stats(null, $yesterday);

        $this->assertEquals(1, $stats['validated']);

        // Get stats since tomorrow (should be 0).
        $tomorrow = strtotime('+1 day');
        $stats = bulk_validator::get_validation_stats(null, $tomorrow);

        $this->assertEquals(0, $stats['validated']);
    }
}
