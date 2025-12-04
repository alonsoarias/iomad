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
 * PHPUnit tests for exemption class.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @category  test
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers    \local_jobboard\exemption
 */
class exemption_test extends \advanced_testcase {

    /** @var \stdClass Test user. */
    protected $user;

    /**
     * Set up test fixtures.
     */
    protected function setUp(): void {
        parent::setUp();
        $this->resetAfterTest(true);

        $this->user = $this->getDataGenerator()->create_user();
        $this->setAdminUser();
    }

    /**
     * Test creating an exemption.
     */
    public function test_create_exemption(): void {
        $exemption = exemption::create($this->user->id, exemption::TYPE_HISTORICO_ISER, [
            'documentref' => 'RES-2024-001',
            'notes' => 'Historical ISER employee since 2010',
        ]);

        $this->assertNotNull($exemption);
        $this->assertIsInt($exemption->id);
        $this->assertEquals($this->user->id, $exemption->userid);
        $this->assertEquals(exemption::TYPE_HISTORICO_ISER, $exemption->exemptiontype);
        $this->assertEquals('RES-2024-001', $exemption->documentref);
    }

    /**
     * Test creating exemption with invalid type fails.
     */
    public function test_create_invalid_type(): void {
        $this->expectException(\moodle_exception::class);
        $this->expectExceptionMessage('invalidexemptiontype');

        exemption::create($this->user->id, 'invalid_type');
    }

    /**
     * Test getting exemption by ID.
     */
    public function test_get_exemption(): void {
        $created = exemption::create($this->user->id, exemption::TYPE_HISTORICO_ISER);

        $exemption = exemption::get($created->id);

        $this->assertNotNull($exemption);
        $this->assertEquals($created->id, $exemption->id);
    }

    /**
     * Test getting active exemption for user.
     */
    public function test_get_active_for_user(): void {
        exemption::create($this->user->id, exemption::TYPE_HISTORICO_ISER);

        $exemption = exemption::get_active_for_user($this->user->id);

        $this->assertNotNull($exemption);
        $this->assertEquals($this->user->id, $exemption->userid);
    }

    /**
     * Test user without exemption returns null.
     */
    public function test_no_active_exemption(): void {
        $user2 = $this->getDataGenerator()->create_user();

        $exemption = exemption::get_active_for_user($user2->id);

        $this->assertNull($exemption);
    }

    /**
     * Test checking if user has active exemption.
     */
    public function test_user_has_active_exemption(): void {
        $this->assertFalse(exemption::user_has_active_exemption($this->user->id));

        exemption::create($this->user->id, exemption::TYPE_HISTORICO_ISER);

        $this->assertTrue(exemption::user_has_active_exemption($this->user->id));
    }

    /**
     * Test exemption validity period.
     */
    public function test_exemption_validity_period(): void {
        // Create exemption valid from yesterday until tomorrow.
        $exemption = exemption::create($this->user->id, exemption::TYPE_DOCUMENTOS_RECIENTES, [
            'validfrom' => time() - 86400,
            'validuntil' => time() + 86400,
        ]);

        $this->assertTrue($exemption->is_active());
    }

    /**
     * Test expired exemption is not active.
     */
    public function test_expired_exemption(): void {
        // Create exemption that expired yesterday.
        $exemption = exemption::create($this->user->id, exemption::TYPE_DOCUMENTOS_RECIENTES, [
            'validfrom' => time() - (10 * 86400),
            'validuntil' => time() - 86400,
        ]);

        $this->assertFalse($exemption->is_active());
    }

    /**
     * Test future exemption is not active.
     */
    public function test_future_exemption(): void {
        // Create exemption that starts tomorrow.
        $exemption = exemption::create($this->user->id, exemption::TYPE_DOCUMENTOS_RECIENTES, [
            'validfrom' => time() + 86400,
        ]);

        $this->assertFalse($exemption->is_active());
    }

    /**
     * Test revoking an exemption.
     */
    public function test_revoke_exemption(): void {
        $exemption = exemption::create($this->user->id, exemption::TYPE_HISTORICO_ISER);

        $this->assertTrue($exemption->is_active());

        $result = $exemption->revoke('No longer employed');

        $this->assertTrue($result);
        $this->assertFalse($exemption->is_active());
        $this->assertNotNull($exemption->timerevoked);
        $this->assertEquals('No longer employed', $exemption->revokereason);
    }

    /**
     * Test cannot revoke already revoked exemption.
     */
    public function test_cannot_revoke_twice(): void {
        $exemption = exemption::create($this->user->id, exemption::TYPE_HISTORICO_ISER);
        $exemption->revoke('First revocation');

        $result = $exemption->revoke('Second revocation');

        $this->assertFalse($result);
    }

    /**
     * Test getting required document codes for historico_iser.
     */
    public function test_historico_iser_required_docs(): void {
        $exemption = exemption::create($this->user->id, exemption::TYPE_HISTORICO_ISER);

        $requiredcodes = $exemption->get_required_document_codes();

        $this->assertContains('antecedentes_procuraduria', $requiredcodes);
        $this->assertContains('antecedentes_contraloria', $requiredcodes);
        $this->assertContains('antecedentes_policia', $requiredcodes);
        $this->assertContains('rnmc', $requiredcodes);
        $this->assertContains('certificado_medico', $requiredcodes);

        // Should NOT require cedula, etc.
        $this->assertNotContains('cedula', $requiredcodes);
        $this->assertNotContains('rut', $requiredcodes);
    }

    /**
     * Test getting required document codes for traslado_interno.
     */
    public function test_traslado_interno_required_docs(): void {
        $exemption = exemption::create($this->user->id, exemption::TYPE_TRASLADO_INTERNO);

        $requiredcodes = $exemption->get_required_document_codes();

        $this->assertContains('antecedentes_procuraduria', $requiredcodes);
        $this->assertContains('antecedentes_contraloria', $requiredcodes);
        $this->assertCount(2, $requiredcodes); // Only 2 required.
    }

    /**
     * Test getting all exemptions for a user.
     */
    public function test_get_all_for_user(): void {
        exemption::create($this->user->id, exemption::TYPE_HISTORICO_ISER);
        exemption::create($this->user->id, exemption::TYPE_DOCUMENTOS_RECIENTES, [
            'validfrom' => time() - (365 * 86400),
            'validuntil' => time() - 86400, // Expired.
        ]);

        // Get all exemptions.
        $all = exemption::get_all_for_user($this->user->id, false);
        $this->assertCount(2, $all);

        // Get only active.
        $active = exemption::get_all_for_user($this->user->id, true);
        $this->assertCount(1, $active);
    }

    /**
     * Test exemption type names.
     */
    public function test_get_type_name(): void {
        $exemption = exemption::create($this->user->id, exemption::TYPE_HISTORICO_ISER);

        $name = $exemption->get_type_name();

        $this->assertNotEmpty($name);
        $this->assertIsString($name);
    }

    /**
     * Test getting all exemption types.
     */
    public function test_get_all_types(): void {
        $types = exemption::get_all_types();

        $this->assertIsArray($types);
        $this->assertArrayHasKey(exemption::TYPE_HISTORICO_ISER, $types);
        $this->assertArrayHasKey(exemption::TYPE_DOCUMENTOS_RECIENTES, $types);
        $this->assertArrayHasKey(exemption::TYPE_TRASLADO_INTERNO, $types);
        $this->assertArrayHasKey(exemption::TYPE_RECONTRATACION, $types);
    }

    /**
     * Test exemption list with filters.
     */
    public function test_get_exemption_list(): void {
        $user2 = $this->getDataGenerator()->create_user();

        exemption::create($this->user->id, exemption::TYPE_HISTORICO_ISER);
        exemption::create($user2->id, exemption::TYPE_TRASLADO_INTERNO);

        // Get all.
        $result = exemption::get_list();
        $this->assertEquals(2, $result['total']);

        // Filter by user.
        $result = exemption::get_list(['userid' => $this->user->id]);
        $this->assertEquals(1, $result['total']);

        // Filter by type.
        $result = exemption::get_list(['exemptiontype' => exemption::TYPE_TRASLADO_INTERNO]);
        $this->assertEquals(1, $result['total']);
    }
}
