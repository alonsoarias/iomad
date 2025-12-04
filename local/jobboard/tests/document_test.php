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
 * PHPUnit tests for document class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @category  test
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_jobboard\document
 */
class document_test extends \advanced_testcase {

    /** @var \stdClass Test user. */
    protected $user;

    /** @var \stdClass Test application. */
    protected $application;

    /** @var \local_jobboard_generator Plugin generator. */
    protected $generator;

    /**
     * Set up test fixtures.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);

        // Create test user.
        $this->user = $this->getDataGenerator()->create_user();
        $this->setUser($this->user);

        // Get plugin generator.
        $this->generator = $this->getDataGenerator()->get_plugin_generator('local_jobboard');

        // Create vacancy.
        $vacancy = $this->generator->create_vacancy([
            'code' => 'DOC001',
            'title' => 'Document Test Vacancy',
            'status' => 'published',
        ]);

        // Create application.
        $this->application = $this->generator->create_application([
            'vacancyid' => $vacancy->id,
            'userid' => $this->user->id,
        ]);
    }

    /**
     * Test creating a document record.
     */
    public function test_create_document(): void {
        $document = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula.pdf',
        ]);

        $this->assertNotNull($document);
        $this->assertEquals($this->application->id, $document->applicationid);
        $this->assertEquals('cedula', $document->documenttype);
        $this->assertEquals('cedula.pdf', $document->filename);
    }

    /**
     * Test getting document by ID.
     */
    public function test_get_document(): void {
        $created = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula.pdf',
        ]);

        $document = document::get($created->id);

        $this->assertNotNull($document);
        $this->assertEquals($created->id, $document->id);
    }

    /**
     * Test getting documents for an application.
     */
    public function test_get_documents_for_application(): void {
        $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula.pdf',
        ]);

        $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'rut',
            'filename' => 'rut.pdf',
        ]);

        $documents = document::get_for_application($this->application->id);

        $this->assertCount(2, $documents);
    }

    /**
     * Test document superseding.
     */
    public function test_document_superseding(): void {
        global $DB;

        $original = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula_v1.pdf',
        ]);

        // Create a new document of the same type.
        $new = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula_v2.pdf',
        ]);

        // Mark original as superseded.
        $DB->set_field('local_jobboard_document', 'issuperseded', 1, ['id' => $original->id]);

        // Get active documents (not superseded).
        $documents = document::get_for_application($this->application->id, false);

        // Should only return non-superseded documents.
        $this->assertCount(1, $documents);
        $this->assertEquals('cedula_v2.pdf', reset($documents)->filename);
    }

    /**
     * Test document validation.
     */
    public function test_document_validation(): void {
        $this->setAdminUser();

        $created = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula.pdf',
        ]);

        $document = document::get($created->id);

        // Validate the document.
        $document->validate(true, null, 'All checks passed');

        $validation = $document->get_validation();

        $this->assertNotNull($validation);
        $this->assertEquals(1, $validation->isvalid);
        $this->assertEquals('All checks passed', $validation->notes);
    }

    /**
     * Test document rejection.
     */
    public function test_document_rejection(): void {
        $this->setAdminUser();

        $created = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula.pdf',
        ]);

        $document = document::get($created->id);

        // Reject the document.
        $document->validate(false, 'Document is illegible', 'Please upload a clearer copy');

        $validation = $document->get_validation();

        $this->assertNotNull($validation);
        $this->assertEquals(0, $validation->isvalid);
        $this->assertEquals('Document is illegible', $validation->rejectreason);
    }

    /**
     * Test getting pending validation count.
     */
    public function test_pending_validation_count(): void {
        $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula.pdf',
        ]);

        $doc2 = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'rut',
            'filename' => 'rut.pdf',
        ]);

        // Initially 2 pending.
        $pending = document::get_pending_validation_count($this->application->id);
        $this->assertEquals(2, $pending);

        // Validate one.
        $this->setAdminUser();
        $document = document::get($doc2->id);
        $document->validate(true);

        // Now 1 pending.
        $pending = document::get_pending_validation_count($this->application->id);
        $this->assertEquals(1, $pending);
    }

    /**
     * Test document issue date.
     */
    public function test_document_issue_date(): void {
        $issuedate = time() - (30 * 24 * 60 * 60); // 30 days ago.

        $document = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'antecedentes_procuraduria',
            'filename' => 'antecedentes.pdf',
            'issuedate' => $issuedate,
        ]);

        $this->assertEquals($issuedate, $document->issuedate);
    }

    /**
     * Test checking if all required documents are validated.
     */
    public function test_all_documents_validated(): void {
        $this->setAdminUser();

        $doc1 = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula.pdf',
        ]);

        $doc2 = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'rut',
            'filename' => 'rut.pdf',
        ]);

        // Not all validated yet.
        $this->assertFalse(document::all_validated($this->application->id));

        // Validate first.
        $document1 = document::get($doc1->id);
        $document1->validate(true);

        // Still not all validated.
        $this->assertFalse(document::all_validated($this->application->id));

        // Validate second.
        $document2 = document::get($doc2->id);
        $document2->validate(true);

        // Now all validated.
        $this->assertTrue(document::all_validated($this->application->id));
    }

    /**
     * Test document has any rejected.
     */
    public function test_has_rejected_documents(): void {
        $this->setAdminUser();

        $doc1 = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'cedula',
            'filename' => 'cedula.pdf',
        ]);

        $doc2 = $this->generator->create_document([
            'applicationid' => $this->application->id,
            'documenttype' => 'rut',
            'filename' => 'rut.pdf',
        ]);

        // No rejections yet.
        $this->assertFalse(document::has_rejected($this->application->id));

        // Validate first.
        $document1 = document::get($doc1->id);
        $document1->validate(true);

        // Still no rejections.
        $this->assertFalse(document::has_rejected($this->application->id));

        // Reject second.
        $document2 = document::get($doc2->id);
        $document2->validate(false, 'Invalid');

        // Now has rejection.
        $this->assertTrue(document::has_rejected($this->application->id));
    }
}
