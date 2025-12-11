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
 * Data generator for local_jobboard tests.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Job Board data generator class.
 */
class local_jobboard_generator extends testing_module_generator {

    /** @var int Counter for vacancy codes. */
    protected $vacancycount = 0;

    /** @var int Counter for applications. */
    protected $applicationcount = 0;

    /**
     * Reset generator counters.
     */
    public function reset(): void {
        $this->vacancycount = 0;
        $this->applicationcount = 0;
        parent::reset();
    }

    /**
     * Create a vacancy.
     *
     * @param array|stdClass $record Data for the vacancy.
     * @return stdClass The created vacancy record.
     */
    public function create_vacancy($record = null): \stdClass {
        global $DB, $USER;

        $this->vacancycount++;
        $record = (array) $record;

        $defaults = [
            'code' => 'VAC' . str_pad($this->vacancycount, 5, '0', STR_PAD_LEFT),
            'title' => 'Test Vacancy ' . $this->vacancycount,
            'description' => 'This is a test vacancy description.',
            'descriptionformat' => FORMAT_HTML,
            'contracttype' => 'catedra',
            'companyid' => 0,
            'location' => 'Test Location',
            'department' => 'Test Department',
            'duration' => '6 months',
            'positions' => 1,
            'requirements' => 'Test requirements',
            'requirementsformat' => FORMAT_HTML,
            'desirable' => 'Test desirable qualifications',
            'desirableformat' => FORMAT_HTML,
            'status' => 'draft',
            'createdby' => $USER->id,
            'timecreated' => time(),
        ];

        $record = array_merge($defaults, $record);
        $record['id'] = $DB->insert_record('local_jobboard_vacancy', (object) $record);

        return (object) $record;
    }

    /**
     * Create a published vacancy.
     *
     * @param array|stdClass $record Data for the vacancy.
     * @return stdClass The created vacancy record.
     */
    public function create_published_vacancy($record = null): \stdClass {
        $record = (array) $record;
        $record['status'] = 'published';
        return $this->create_vacancy($record);
    }

    /**
     * Create an application.
     *
     * @param array|stdClass $record Data for the application.
     * @return stdClass The created application record.
     */
    public function create_application($record = null): \stdClass {
        global $DB, $USER;

        $this->applicationcount++;
        $record = (array) $record;

        if (empty($record['vacancyid'])) {
            $vacancy = $this->create_published_vacancy();
            $record['vacancyid'] = $vacancy->id;
        }

        $defaults = [
            'userid' => $USER->id,
            'status' => 'submitted',
            'coverletter' => 'This is my cover letter.',
            'digitalsignature' => 'Test User',
            'isexemption' => 0,
            'exemptionreason' => null,
            'consentgiven' => 1,
            'consenttimestamp' => time(),
            'consentip' => '127.0.0.1',
            'timecreated' => time(),
        ];

        $record = array_merge($defaults, $record);
        $record['id'] = $DB->insert_record('local_jobboard_application', (object) $record);

        return (object) $record;
    }

    /**
     * Create a document.
     *
     * @param array|stdClass $record Data for the document.
     * @return stdClass The created document record.
     */
    public function create_document($record = null): \stdClass {
        global $DB, $USER;

        $record = (array) $record;

        if (empty($record['applicationid'])) {
            $application = $this->create_application();
            $record['applicationid'] = $application->id;
        }

        $defaults = [
            'userid' => $USER->id,
            'documenttype' => 'cedula',
            'filename' => 'document.pdf',
            'filepath' => '/',
            'filesize' => 1024,
            'mimetype' => 'application/pdf',
            'contenthash' => sha1(random_bytes(20)),
            'issuedate' => time() - 86400,
            'isencrypted' => 0,
            'issuperseded' => 0,
            'timecreated' => time(),
        ];

        $record = array_merge($defaults, $record);
        $record['id'] = $DB->insert_record('local_jobboard_document', (object) $record);

        return (object) $record;
    }

    /**
     * Create an ISER exemption.
     *
     * @param array|stdClass $record Data for the exemption.
     * @return stdClass The created exemption record.
     */
    public function create_exemption($record = null): \stdClass {
        global $DB, $USER;

        $record = (array) $record;

        $defaults = [
            'userid' => $USER->id,
            'exemptiontype' => 'iser_historic',
            'documentref' => 'DOC-' . rand(1000, 9999),
            'exempteddocs' => json_encode(['libreta_militar', 'formacion_complementaria']),
            'validfrom' => time(),
            'validuntil' => null,
            'notes' => 'Test exemption',
            'createdby' => $USER->id,
            'timecreated' => time(),
        ];

        $record = array_merge($defaults, $record);
        $record['id'] = $DB->insert_record('local_jobboard_exemption', (object) $record);

        return (object) $record;
    }

    /**
     * Create an API token.
     *
     * @param array|stdClass $record Data for the token.
     * @return array The token info with raw token and api_token object.
     */
    public function create_api_token($record = null): array {
        global $USER;

        $record = (array) $record;

        $defaults = [
            'userid' => $USER->id,
            'description' => 'Test API Token',
            'permissions' => ['view_vacancies', 'view_vacancy_details'],
            'ipwhitelist' => [],
            'validfrom' => null,
            'validuntil' => null,
        ];

        $record = array_merge($defaults, $record);

        return \local_jobboard\api_token::create(
            $record['userid'],
            $record['description'],
            $record['permissions'],
            $record['ipwhitelist'],
            $record['validfrom'],
            $record['validuntil']
        );
    }

    /**
     * Create an audit log entry.
     *
     * @param array|stdClass $record Data for the audit log.
     * @return stdClass The created audit record.
     */
    public function create_audit_log($record = null): \stdClass {
        global $DB, $USER;

        $record = (array) $record;

        $defaults = [
            'userid' => $USER->id,
            'action' => 'test_action',
            'entitytype' => 'test',
            'entityid' => 1,
            'data' => json_encode(['test' => 'data']),
            'ipaddress' => '127.0.0.1',
            'useragent' => 'PHPUnit Test',
            'timecreated' => time(),
        ];

        $record = array_merge($defaults, $record);
        $record['id'] = $DB->insert_record('local_jobboard_audit', (object) $record);

        return (object) $record;
    }

    /**
     * Create a notification record.
     *
     * @param array|stdClass $record Data for the notification.
     * @return stdClass The created notification record.
     */
    public function create_notification($record = null): \stdClass {
        global $DB, $USER;

        $record = (array) $record;

        if (empty($record['applicationid'])) {
            $application = $this->create_application();
            $record['applicationid'] = $application->id;
        }

        $defaults = [
            'userid' => $USER->id,
            'templatecode' => 'application_received',
            'subject' => 'Test Notification',
            'body' => 'This is a test notification body.',
            'bodyformat' => FORMAT_HTML,
            'status' => 'pending',
            'retrycount' => 0,
            'timecreated' => time(),
        ];

        $record = array_merge($defaults, $record);
        $record['id'] = $DB->insert_record('local_jobboard_notification', (object) $record);

        return (object) $record;
    }

    /**
     * Create a document type configuration.
     *
     * @param array|stdClass $record Data for the document type.
     * @return stdClass The created document type record.
     */
    public function create_document_type($record = null): \stdClass {
        global $DB;

        $record = (array) $record;

        $defaults = [
            'code' => 'test_doc_' . rand(100, 999),
            'name' => 'Test Document Type',
            'description' => 'A test document type',
            'descriptionformat' => FORMAT_HTML,
            'isrequired' => 1,
            'maxvaliditydays' => 365,
            'allowedformats' => json_encode(['pdf']),
            'maxfilesize' => 5242880,
            'sortorder' => 0,
            'enabled' => 1,
            'externallink' => null,
            'checklistitems' => json_encode([]),
            'timecreated' => time(),
        ];

        $record = array_merge($defaults, $record);
        $record['id'] = $DB->insert_record('local_jobboard_doc_type', (object) $record);

        return (object) $record;
    }

    /**
     * Create a workflow log entry.
     *
     * @param array|stdClass $record Data for the workflow log.
     * @return stdClass The created workflow log record.
     */
    public function create_workflow_log($record = null): \stdClass {
        global $DB, $USER;

        $record = (array) $record;

        if (empty($record['applicationid'])) {
            $application = $this->create_application();
            $record['applicationid'] = $application->id;
        }

        $defaults = [
            'previousstatus' => 'submitted',
            'newstatus' => 'under_review',
            'comments' => 'Test workflow transition',
            'changedby' => $USER->id,
            'timecreated' => time(),
        ];

        $record = array_merge($defaults, $record);
        $record['id'] = $DB->insert_record('local_jobboard_workflow_log', (object) $record);

        return (object) $record;
    }
}
