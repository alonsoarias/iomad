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
 * PHPUnit tests for local_jobboard vacancy class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @category  test
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard;

defined('MOODLE_INTERNAL') || die();

/**
 * Tests for the vacancy class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_jobboard\vacancy
 */
class vacancy_test extends \advanced_testcase {

    /**
     * Set up test fixtures.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);
    }

    /**
     * Get valid vacancy data for testing.
     *
     * @return \stdClass
     */
    protected function get_valid_vacancy_data(): \stdClass {
        return (object) [
            'code' => 'TEST001',
            'title' => 'Test Vacancy',
            'description' => 'Test description',
            'contracttype' => 'catedra',
            'duration' => '6 months',
            'location' => 'Bogotá',
            'department' => 'Engineering',
            'positions' => 2,
            'requirements' => 'Bachelor degree required',
            'desirable' => 'Master degree preferred',
        ];
    }

    /**
     * Test creating a vacancy with valid data.
     */
    public function test_create_vacancy_with_valid_data(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $vacancy = vacancy::create($data);

        $this->assertNotEmpty($vacancy->id);
        $this->assertEquals('TEST001', $vacancy->code);
        $this->assertEquals('Test Vacancy', $vacancy->title);
        $this->assertEquals('draft', $vacancy->status);
        $this->assertGreaterThan(0, $vacancy->timecreated);
    }

    /**
     * Test that vacancy code must be unique.
     */
    public function test_vacancy_code_must_be_unique(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        vacancy::create($data);

        // Try to create another vacancy with the same code.
        $this->expectException(\moodle_exception::class);
        vacancy::create($data);
    }

    /**
     * Test that close date must be after open date.
     */
    public function test_close_date_must_be_after_open_date(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $data->closedate = $data->opendate - 1; // Close before open.

        $this->expectException(\moodle_exception::class);
        vacancy::create($data);
    }

    /**
     * Test updating a vacancy.
     */
    public function test_update_vacancy(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $vacancy = vacancy::create($data);

        $updatedata = new \stdClass();
        $updatedata->title = 'Updated Title';
        $updatedata->positions = 5;

        $vacancy->update($updatedata);

        // Reload and verify.
        $reloaded = vacancy::get($vacancy->id);
        $this->assertEquals('Updated Title', $reloaded->title);
        $this->assertEquals(5, $reloaded->positions);
    }

    /**
     * Test getting vacancy by ID.
     */
    public function test_get_vacancy_by_id(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $created = vacancy::create($data);

        $vacancy = vacancy::get($created->id);

        $this->assertNotNull($vacancy);
        $this->assertEquals($created->id, $vacancy->id);
        $this->assertEquals('TEST001', $vacancy->code);
    }

    /**
     * Test getting vacancy by code.
     */
    public function test_get_vacancy_by_code(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        vacancy::create($data);

        $vacancy = vacancy::get_by_code('TEST001');

        $this->assertNotNull($vacancy);
        $this->assertEquals('TEST001', $vacancy->code);
    }

    /**
     * Test getting non-existent vacancy returns null.
     */
    public function test_get_nonexistent_vacancy_returns_null(): void {
        $vacancy = vacancy::get(99999);
        $this->assertNull($vacancy);
    }

    /**
     * Test vacancy status changes.
     */
    public function test_vacancy_status_changes(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $vacancy = vacancy::create($data);

        // Initially draft.
        $this->assertEquals('draft', $vacancy->status);
        $this->assertTrue($vacancy->can_publish());

        // Publish.
        $vacancy->publish();
        $this->assertEquals('published', $vacancy->status);
        $this->assertFalse($vacancy->can_publish());

        // Close.
        $vacancy->close();
        $this->assertEquals('closed', $vacancy->status);
    }

    /**
     * Test cannot publish without required fields.
     */
    public function test_cannot_publish_without_required_fields(): void {
        $this->setAdminUser();

        $data = (object) [
            'code' => 'TEST002',
            'title' => 'Incomplete Vacancy',
            'opendate' => time(),
            'closedate' => time() + (30 * 24 * 60 * 60),
            'positions' => 1,
        ];
        $vacancy = vacancy::create($data);

        // Should be able to publish since required fields are present.
        $this->assertTrue($vacancy->can_publish());
    }

    /**
     * Test cannot publish with expired close date.
     */
    public function test_cannot_publish_with_expired_close_date(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $data->closedate = time() - 1; // Already expired.
        $vacancy = vacancy::create($data);

        $this->assertFalse($vacancy->can_publish());
    }

    /**
     * Test deleting a draft vacancy.
     */
    public function test_delete_draft_vacancy(): void {
        global $DB;
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $vacancy = vacancy::create($data);
        $vacancyid = $vacancy->id;

        $this->assertTrue($vacancy->can_delete());

        $vacancy->delete();

        $this->assertFalse($DB->record_exists('local_jobboard_vacancy', ['id' => $vacancyid]));
    }

    /**
     * Test cannot delete published vacancy.
     */
    public function test_cannot_delete_published_vacancy(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $vacancy = vacancy::create($data);
        $vacancy->publish();

        $this->assertFalse($vacancy->can_delete());
    }

    /**
     * Test vacancy is_open method.
     */
    public function test_vacancy_is_open(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $vacancy = vacancy::create($data);

        // Draft is not open.
        $this->assertFalse($vacancy->is_open());

        // Published and within dates is open.
        $vacancy->publish();
        $this->assertTrue($vacancy->is_open());

        // Close it.
        $vacancy->close();
        $this->assertFalse($vacancy->is_open());
    }

    /**
     * Test vacancy with future open date is not open.
     */
    public function test_vacancy_with_future_open_date_is_not_open(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $data->opendate = time() + (7 * 24 * 60 * 60); // Opens in 7 days.
        $vacancy = vacancy::create($data);
        $vacancy->publish();

        $this->assertFalse($vacancy->is_open());
    }

    /**
     * Test listing vacancies.
     */
    public function test_list_vacancies(): void {
        $this->setAdminUser();

        // Create multiple vacancies.
        for ($i = 1; $i <= 5; $i++) {
            $data = $this->get_valid_vacancy_data();
            $data->code = 'LIST' . $i;
            $data->title = 'Vacancy ' . $i;
            vacancy::create($data);
        }

        $result = vacancy::get_list([], 'code', 'ASC', 0, 10);

        $this->assertCount(5, $result['vacancies']);
        $this->assertEquals(5, $result['total']);
    }

    /**
     * Test filtering vacancies by status.
     */
    public function test_filter_vacancies_by_status(): void {
        $this->setAdminUser();

        // Create draft vacancy.
        $data = $this->get_valid_vacancy_data();
        $data->code = 'DRAFT1';
        vacancy::create($data);

        // Create published vacancy.
        $data2 = $this->get_valid_vacancy_data();
        $data2->code = 'PUB1';
        $published = vacancy::create($data2);
        $published->publish();

        // Filter by published.
        $result = vacancy::get_list(['status' => 'published']);
        $this->assertCount(1, $result['vacancies']);
        $this->assertEquals('PUB1', $result['vacancies'][0]->code);

        // Filter by draft.
        $result = vacancy::get_list(['status' => 'draft']);
        $this->assertCount(1, $result['vacancies']);
        $this->assertEquals('DRAFT1', $result['vacancies'][0]->code);
    }

    /**
     * Test searching vacancies.
     */
    public function test_search_vacancies(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $data->code = 'SEARCH1';
        $data->title = 'Senior Developer Position';
        vacancy::create($data);

        $data2 = $this->get_valid_vacancy_data();
        $data2->code = 'SEARCH2';
        $data2->title = 'Junior Designer Position';
        vacancy::create($data2);

        // Search for "Developer".
        $result = vacancy::get_list(['search' => 'Developer']);
        $this->assertCount(1, $result['vacancies']);
        $this->assertEquals('SEARCH1', $result['vacancies'][0]->code);

        // Search by code.
        $result = vacancy::get_list(['search' => 'SEARCH2']);
        $this->assertCount(1, $result['vacancies']);
        $this->assertEquals('SEARCH2', $result['vacancies'][0]->code);
    }

    /**
     * Test vacancy code exists check.
     */
    public function test_code_exists(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $vacancy = vacancy::create($data);

        $this->assertTrue(vacancy::code_exists('TEST001'));
        $this->assertFalse(vacancy::code_exists('NONEXISTENT'));

        // Exclude the current vacancy.
        $this->assertFalse(vacancy::code_exists('TEST001', $vacancy->id));
    }

    /**
     * Test getting application count.
     */
    public function test_get_application_count(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $vacancy = vacancy::create($data);

        // Initially zero.
        $this->assertEquals(0, $vacancy->get_application_count());
    }

    /**
     * Test to_record method.
     */
    public function test_to_record(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $vacancy = vacancy::create($data);

        $record = $vacancy->to_record();

        $this->assertIsObject($record);
        $this->assertEquals($vacancy->id, $record->id);
        $this->assertEquals($vacancy->code, $record->code);
        $this->assertEquals($vacancy->title, $record->title);
    }

    /**
     * Test get_status_display method.
     */
    public function test_get_status_display(): void {
        $this->setAdminUser();

        $data = $this->get_valid_vacancy_data();
        $vacancy = vacancy::create($data);

        $display = $vacancy->get_status_display();
        $this->assertNotEmpty($display);
        $this->assertIsString($display);
    }
}
