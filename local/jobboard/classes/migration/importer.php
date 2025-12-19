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
 * Data importer class for local_jobboard migration.
 *
 * Handles complete import of all plugin data including files.
 * Extracted from admin/migrate.php for better code organization.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\migration;

defined('MOODLE_INTERNAL') || die();

/**
 * Importer class for migration.
 */
class importer {

    /** @var bool Overwrite existing records. */
    protected $overwrite;

    /** @var bool Dry run mode. */
    protected $dryrun;

    /** @var array Results of import. */
    protected $results;

    /** @var array ID mapping for doctypes. */
    protected $doctypemap = [];

    /** @var array ID mapping for convocatorias. */
    protected $convocatoriamap = [];

    /** @var array ID mapping for vacancies. */
    protected $vacancymap = [];

    /** @var array ID mapping for applications. */
    protected $applicationmap = [];

    /** @var array ID mapping for faculties. */
    protected $facultymap = [];

    /** @var array ID mapping for programs. */
    protected $programmap = [];

    /** @var array ID mapping for committees. */
    protected $committeemap = [];

    /** @var array ID mapping for interviews. */
    protected $interviewmap = [];

    /** @var array ID mapping for email templates. */
    protected $templatemap = [];

    /** @var string|null Temporary directory. */
    protected $tempdir = null;

    /** @var string|null Files directory. */
    protected $filesdir = null;

    /**
     * Constructor.
     *
     * @param bool $overwrite Overwrite existing records.
     * @param bool $dryrun Dry run mode.
     */
    public function __construct(bool $overwrite = false, bool $dryrun = false) {
        $this->overwrite = $overwrite;
        $this->dryrun = $dryrun;
        $this->results = [
            'success' => true,
            'messages' => [],
            'counts' => [],
        ];
    }

    /**
     * Import plugin data from ZIP/JSON.
     *
     * @param string $filepath Path to the import file.
     * @return array Import results.
     */
    public function import_full(string $filepath): array {
        global $DB;

        // Extract data from file.
        $data = $this->extract_data($filepath);
        if (!$data) {
            return $this->results;
        }

        // Validate data.
        if (!$this->validate_data($data)) {
            return $this->results;
        }

        $this->results['messages'][] = get_string('importingfrom', 'local_jobboard', [
            'version' => $data['release'] ?? $data['version'],
            'date' => $data['export_date'],
            'site' => $data['site_name'] ?? 'Unknown',
        ]);

        $transaction = $this->dryrun ? null : $DB->start_delegated_transaction();

        try {
            // Import each data type in order (dependencies first).
            $this->import_doctypes($data['doctypes'] ?? []);
            $this->import_email_templates($data['email_templates'] ?? []);
            $this->import_email_strings($data['email_strings'] ?? []);
            $this->import_convocatorias($data['convocatorias'] ?? []);
            $this->import_vacancies($data['vacancies'] ?? []);
            $this->import_vacancy_fields($data['vacancy_fields'] ?? []);
            $this->import_doc_requirements($data['doc_requirements'] ?? []);
            $this->import_settings($data['settings'] ?? []);
            $this->import_plugin_config($data['config'] ?? []);
            $this->import_exemptions($data['exemptions'] ?? []);

            // Import organizational structure.
            $this->import_faculties($data['faculties'] ?? []);
            $this->import_programs($data['programs'] ?? []);
            $this->import_program_reviewers($data['program_reviewers'] ?? []);

            // Import committees and evaluation structures.
            $this->import_committees($data['committees'] ?? []);
            $this->import_committee_members($data['committee_members'] ?? []);
            $this->import_criteria($data['criteria'] ?? []);

            // Import applications (after vacancies are imported).
            $this->import_applications($data['applications'] ?? []);

            // Import evaluations and decisions (after applications are imported).
            $this->import_evaluations($data['evaluations'] ?? []);
            $this->import_decisions($data['decisions'] ?? []);

            // Import interviews.
            $this->import_interviews($data['interviews'] ?? []);
            $this->import_interviewers($data['interviewers'] ?? []);

            // Import workflow and history.
            $this->import_workflow_logs($data['workflow_logs'] ?? []);

            // Import profiles and consents.
            $this->import_applicant_profiles($data['applicant_profiles'] ?? []);
            $this->import_consents($data['consents'] ?? []);
            $this->import_notifications($data['notifications'] ?? []);

            if (!$this->dryrun && $transaction) {
                $transaction->allow_commit();
            }

            if ($this->dryrun) {
                array_unshift($this->results['messages'],
                    \html_writer::tag('strong', get_string('dryrunresults', 'local_jobboard')));
            }

        } catch (\Exception $e) {
            if ($transaction) {
                $transaction->rollback($e);
            }
            $this->results['success'] = false;
            $this->results['messages'][] = get_string('importerror', 'local_jobboard') . ': ' . $e->getMessage();
        }

        // Cleanup.
        $this->cleanup();

        return $this->results;
    }

    /**
     * Extract data from ZIP or JSON file.
     *
     * @param string $filepath Path to file.
     * @return array|null Data array or null on error.
     */
    protected function extract_data(string $filepath): ?array {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimetype = finfo_file($finfo, $filepath);
        finfo_close($finfo);

        if (strpos($mimetype, 'zip') !== false || pathinfo($filepath, PATHINFO_EXTENSION) === 'zip') {
            return $this->extract_zip($filepath);
        } else {
            return json_decode(file_get_contents($filepath), true);
        }
    }

    /**
     * Extract ZIP file.
     *
     * @param string $filepath Path to ZIP file.
     * @return array|null Data array or null on error.
     */
    protected function extract_zip(string $filepath): ?array {
        $this->tempdir = make_temp_directory('jobboard_import_' . time());
        $zip = new \ZipArchive();

        if ($zip->open($filepath) !== true) {
            $this->results['success'] = false;
            $this->results['messages'][] = get_string('invalidmigrationfile', 'local_jobboard');
            return null;
        }

        $zip->extractTo($this->tempdir);
        $zip->close();

        $jsonpath = $this->tempdir . '/data.json';
        $this->filesdir = $this->tempdir . '/files';

        if (!file_exists($jsonpath)) {
            remove_dir($this->tempdir);
            $this->tempdir = null;
            $this->results['success'] = false;
            $this->results['messages'][] = get_string('invalidmigrationfile', 'local_jobboard');
            return null;
        }

        return json_decode(file_get_contents($jsonpath), true);
    }

    /**
     * Validate import data.
     *
     * @param array $data Data to validate.
     * @return bool True if valid.
     */
    protected function validate_data(array $data): bool {
        if (!$data || !isset($data['plugin']) || $data['plugin'] !== 'local_jobboard') {
            $this->cleanup();
            $this->results['success'] = false;
            $this->results['messages'][] = get_string('invalidmigrationfile', 'local_jobboard');
            return false;
        }
        return true;
    }

    /**
     * Import document types.
     *
     * @param array $doctypes Doctypes to import.
     */
    protected function import_doctypes(array $doctypes): void {
        global $DB;

        if (empty($doctypes)) {
            return;
        }

        $this->results['counts']['doctypes'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($doctypes as $doctype) {
            $doctype = (object) $doctype;
            $code = $doctype->code;

            $existing = $DB->get_record('local_jobboard_doctype', ['code' => $code]);

            if ($existing) {
                $this->doctypemap[$code] = $existing->id;
                if ($this->overwrite) {
                    $doctype->id = $existing->id;
                    if (!$this->dryrun) {
                        $DB->update_record('local_jobboard_doctype', $doctype);
                    }
                    $this->results['counts']['doctypes']['updated']++;
                } else {
                    $this->results['counts']['doctypes']['skipped']++;
                }
            } else {
                $doctype->timecreated = time();
                if (!$this->dryrun) {
                    $newid = $DB->insert_record('local_jobboard_doctype', $doctype);
                    $this->doctypemap[$code] = $newid;
                }
                $this->results['counts']['doctypes']['inserted']++;
            }
        }
        $this->results['messages'][] = get_string('importeddoctypes', 'local_jobboard', $this->results['counts']['doctypes']);
    }

    /**
     * Import email templates.
     *
     * @param array $templates Templates to import.
     */
    protected function import_email_templates(array $templates): void {
        global $DB;

        if (empty($templates)) {
            return;
        }

        $this->results['counts']['email_templates'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($templates as $template) {
            $template = (object) $template;
            $code = $template->code;

            $existing = $DB->get_record('local_jobboard_email_template', [
                'code' => $code,
                'companyid' => $template->companyid ?? 0,
            ]);

            if ($existing) {
                $this->templatemap[$code] = $existing->id;
                if ($this->overwrite) {
                    $template->id = $existing->id;
                    $template->timemodified = time();
                    if (!$this->dryrun) {
                        $DB->update_record('local_jobboard_email_template', $template);
                    }
                    $this->results['counts']['email_templates']['updated']++;
                } else {
                    $this->results['counts']['email_templates']['skipped']++;
                }
            } else {
                $template->timecreated = time();
                if (!$this->dryrun) {
                    $newid = $DB->insert_record('local_jobboard_email_template', $template);
                    $this->templatemap[$code] = $newid;
                }
                $this->results['counts']['email_templates']['inserted']++;
            }
        }
        $this->results['messages'][] = get_string('importedemails', 'local_jobboard', $this->results['counts']['email_templates']);
    }

    /**
     * Import convocatorias.
     *
     * @param array $convocatorias Convocatorias to import.
     */
    protected function import_convocatorias(array $convocatorias): void {
        global $DB, $USER;

        if (empty($convocatorias)) {
            return;
        }

        $this->results['counts']['convocatorias'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($convocatorias as $conv) {
            $conv = (object) $conv;
            $originalid = $conv->original_id ?? null;
            $exemptions = $conv->exemptions ?? [];
            unset($conv->original_id, $conv->exemptions);

            $existing = $DB->get_record('local_jobboard_convocatoria', ['code' => $conv->code]);

            if ($existing) {
                $this->convocatoriamap[$conv->code] = $existing->id;
                if ($originalid) {
                    $this->convocatoriamap[$originalid] = $existing->id;
                }

                if ($this->overwrite) {
                    $conv->id = $existing->id;
                    $conv->timemodified = time();
                    $conv->modifiedby = $USER->id;
                    if (!$this->dryrun) {
                        $DB->update_record('local_jobboard_convocatoria', $conv);
                    }
                    $this->results['counts']['convocatorias']['updated']++;
                } else {
                    $this->results['counts']['convocatorias']['skipped']++;
                }
            } else {
                $conv->timecreated = time();
                $conv->createdby = $USER->id;
                $conv->companyid = null;
                if (!$this->dryrun) {
                    $newid = $DB->insert_record('local_jobboard_convocatoria', $conv);
                    $this->convocatoriamap[$conv->code] = $newid;
                    if ($originalid) {
                        $this->convocatoriamap[$originalid] = $newid;
                    }
                }
                $this->results['counts']['convocatorias']['inserted']++;
            }

            // Import exemptions.
            $this->import_convocatoria_exemptions($conv->code, $exemptions);
        }
        $this->results['messages'][] = get_string('importedconvocatorias', 'local_jobboard', $this->results['counts']['convocatorias']);
    }

    /**
     * Import convocatoria exemptions.
     *
     * @param string $convcode Convocatoria code.
     * @param array $exemptions Exemptions to import.
     */
    protected function import_convocatoria_exemptions(string $convcode, array $exemptions): void {
        global $DB, $USER;

        if (empty($exemptions) || $this->dryrun) {
            return;
        }

        $convid = $this->convocatoriamap[$convcode] ?? null;
        if (!$convid) {
            return;
        }

        foreach ($exemptions as $exempt) {
            $exempt = (object) $exempt;
            $doctypecode = $exempt->doctype_code ?? null;
            if ($doctypecode && isset($this->doctypemap[$doctypecode])) {
                $existingexempt = $DB->get_record('local_jobboard_conv_docexempt', [
                    'convocatoriaid' => $convid,
                    'doctypeid' => $this->doctypemap[$doctypecode],
                ]);
                if (!$existingexempt) {
                    $newexempt = new \stdClass();
                    $newexempt->convocatoriaid = $convid;
                    $newexempt->doctypeid = $this->doctypemap[$doctypecode];
                    $newexempt->exemptionreason = $exempt->exemptionreason ?? '';
                    $newexempt->createdby = $USER->id;
                    $newexempt->timecreated = time();
                    $DB->insert_record('local_jobboard_conv_docexempt', $newexempt);
                }
            }
        }
    }

    /**
     * Import vacancies.
     *
     * @param array $vacancies Vacancies to import.
     */
    protected function import_vacancies(array $vacancies): void {
        global $DB, $USER;

        if (empty($vacancies)) {
            return;
        }

        $this->results['counts']['vacancies'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($vacancies as $vacancy) {
            $vacancy = (object) $vacancy;
            $originalid = $vacancy->original_id ?? null;
            $convcode = $vacancy->convocatoria_code ?? null;
            unset($vacancy->original_id, $vacancy->convocatoria_code);

            // Map convocatoria.
            if ($convcode && isset($this->convocatoriamap[$convcode])) {
                $vacancy->convocatoriaid = $this->convocatoriamap[$convcode];
            }

            $existing = $DB->get_record('local_jobboard_vacancy', ['code' => $vacancy->code]);

            if ($existing) {
                $this->vacancymap[$vacancy->code] = $existing->id;
                if ($originalid) {
                    $this->vacancymap[$originalid] = $existing->id;
                }

                if ($this->overwrite) {
                    $vacancy->id = $existing->id;
                    $vacancy->timemodified = time();
                    $vacancy->modifiedby = $USER->id;
                    if (!$this->dryrun) {
                        $DB->update_record('local_jobboard_vacancy', $vacancy);
                    }
                    $this->results['counts']['vacancies']['updated']++;
                } else {
                    $this->results['counts']['vacancies']['skipped']++;
                }
            } else {
                $vacancy->timecreated = time();
                $vacancy->createdby = $USER->id;
                $vacancy->companyid = null;
                if (!$this->dryrun) {
                    $newid = $DB->insert_record('local_jobboard_vacancy', $vacancy);
                    $this->vacancymap[$vacancy->code] = $newid;
                    if ($originalid) {
                        $this->vacancymap[$originalid] = $newid;
                    }
                }
                $this->results['counts']['vacancies']['inserted']++;
            }
        }
        $this->results['messages'][] = get_string('importedvacancies', 'local_jobboard', $this->results['counts']['vacancies']);
    }

    /**
     * Import settings.
     *
     * @param array $settings Settings to import.
     */
    protected function import_settings(array $settings): void {
        if (empty($settings)) {
            return;
        }

        $count = 0;
        foreach ($settings as $key => $value) {
            if (!$this->dryrun) {
                set_config($key, $value, 'local_jobboard');
            }
            $count++;
        }
        $this->results['counts']['settings'] = $count;
        $this->results['messages'][] = get_string('importedsettings', 'local_jobboard', $count);
    }

    /**
     * Import user exemptions.
     *
     * @param array $exemptions Exemptions to import.
     */
    protected function import_exemptions(array $exemptions): void {
        global $DB, $USER;

        if (empty($exemptions)) {
            return;
        }

        $this->results['counts']['exemptions'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($exemptions as $exemption) {
            $exemption = (object) $exemption;

            // Find user.
            $user = $this->find_user($exemption);

            if ($user) {
                unset($exemption->username, $exemption->email, $exemption->idnumber);
                $exemption->userid = $user->id;

                $existing = $DB->get_record('local_jobboard_exemption', [
                    'userid' => $user->id,
                    'exemptiontype' => $exemption->exemptiontype,
                ]);

                if ($existing) {
                    if ($this->overwrite) {
                        $exemption->id = $existing->id;
                        $exemption->timemodified = time();
                        if (!$this->dryrun) {
                            $DB->update_record('local_jobboard_exemption', $exemption);
                        }
                        $this->results['counts']['exemptions']['updated']++;
                    } else {
                        $this->results['counts']['exemptions']['skipped']++;
                    }
                } else {
                    $exemption->timecreated = time();
                    $exemption->createdby = $USER->id;
                    if (!$this->dryrun) {
                        $DB->insert_record('local_jobboard_exemption', $exemption);
                    }
                    $this->results['counts']['exemptions']['inserted']++;
                }
            } else {
                $this->results['counts']['exemptions']['skipped']++;
            }
        }
        $this->results['messages'][] = get_string('importedexemptions', 'local_jobboard', $this->results['counts']['exemptions']);
    }

    /**
     * Import applications with documents and files.
     *
     * @param array $applications Applications to import.
     */
    protected function import_applications(array $applications): void {
        global $DB;

        if (empty($applications) || !$this->filesdir || !is_dir($this->filesdir)) {
            return;
        }

        $this->results['counts']['applications'] = ['inserted' => 0, 'skipped' => 0];
        $this->results['counts']['documents'] = ['inserted' => 0, 'skipped' => 0];
        $this->results['counts']['files'] = ['inserted' => 0, 'skipped' => 0];

        $fs = get_file_storage();
        $context = \context_system::instance();

        foreach ($applications as $app) {
            $app = (object) $app;

            // Find user.
            $user = $this->find_user($app);

            // Find vacancy.
            $vacancyid = null;
            if (!empty($app->vacancy_code) && isset($this->vacancymap[$app->vacancy_code])) {
                $vacancyid = $this->vacancymap[$app->vacancy_code];
            }

            if (!$user || !$vacancyid) {
                $this->results['counts']['applications']['skipped']++;
                continue;
            }

            $documents = $app->documents ?? [];
            $originalid = $app->original_id ?? 0;
            unset($app->documents, $app->validations, $app->username, $app->email, $app->idnumber);
            unset($app->vacancy_code, $app->convocatoria_code, $app->original_id);

            $app->userid = $user->id;
            $app->vacancyid = $vacancyid;

            // Check for existing application.
            $existing = $DB->get_record('local_jobboard_application', [
                'userid' => $user->id,
                'vacancyid' => $vacancyid,
            ]);

            if ($existing) {
                $this->applicationmap[$originalid] = $existing->id;
                $this->results['counts']['applications']['skipped']++;
                continue;
            }

            if (!$this->dryrun) {
                $app->timecreated = time();
                $newappid = $DB->insert_record('local_jobboard_application', $app);
                $this->applicationmap[$originalid] = $newappid;
                $this->results['counts']['applications']['inserted']++;

                // Import documents and files.
                $this->import_application_documents($newappid, $documents, $fs, $context);
            } else {
                $this->results['counts']['applications']['inserted']++;
            }
        }

        $this->results['messages'][] = get_string('importedapplications', 'local_jobboard', $this->results['counts']['applications']);
        $this->results['messages'][] = get_string('importeddocuments', 'local_jobboard', $this->results['counts']['documents']);
        $this->results['messages'][] = get_string('importedfiles', 'local_jobboard', $this->results['counts']['files']);
    }

    /**
     * Import documents for an application.
     *
     * @param int $applicationid Application ID.
     * @param array $documents Documents to import.
     * @param \file_storage $fs File storage.
     * @param \context $context Context.
     */
    protected function import_application_documents(int $applicationid, array $documents, \file_storage $fs, \context $context): void {
        global $DB;

        foreach ($documents as $doc) {
            $doc = (object) $doc;
            $doctypecode = $doc->doctype_code ?? $doc->documenttype ?? null;
            $files = $doc->files ?? [];
            unset($doc->doctype_code, $doc->files, $doc->original_id);

            if ($doctypecode) {
                // Document table uses 'documenttype' (code), not 'doctypeid' (foreign key).
                $doc->documenttype = $doctypecode;
                $doc->applicationid = $applicationid;
                $doc->timecreated = time();

                $newdocid = $DB->insert_record('local_jobboard_document', $doc);
                $this->results['counts']['documents']['inserted']++;

                // Import files.
                foreach ($files as $fileinfo) {
                    $exportfilename = $fileinfo['export_filename'] ?? null;
                    if ($exportfilename && file_exists($this->filesdir . '/' . $exportfilename)) {
                        $filerecord = [
                            'contextid' => $context->id,
                            'component' => 'local_jobboard',
                            'filearea' => 'application_documents',
                            'itemid' => $applicationid,
                            'filepath' => $fileinfo['filepath'] ?? '/',
                            'filename' => $fileinfo['filename'],
                        ];

                        $fs->create_file_from_pathname($filerecord, $this->filesdir . '/' . $exportfilename);
                        $this->results['counts']['files']['inserted']++;
                    }
                }
            }
        }
    }

    /**
     * Find user by username, email, or idnumber.
     *
     * @param object $data Object with username/email/idnumber properties.
     * @return object|null User record or null.
     */
    protected function find_user(object $data): ?object {
        global $DB;

        $user = null;
        if (!empty($data->username)) {
            $user = $DB->get_record('user', ['username' => $data->username]);
        }
        if (!$user && !empty($data->email)) {
            $user = $DB->get_record('user', ['email' => $data->email]);
        }
        if (!$user && !empty($data->idnumber)) {
            $user = $DB->get_record('user', ['idnumber' => $data->idnumber]);
        }
        return $user ?: null;
    }

    /**
     * Cleanup temporary files.
     */
    protected function cleanup(): void {
        if ($this->tempdir) {
            remove_dir($this->tempdir);
            $this->tempdir = null;
        }
    }

    /**
     * Import email strings.
     *
     * @param array $strings Email strings to import.
     */
    protected function import_email_strings(array $strings): void {
        global $DB;

        if (empty($strings)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_email_strings')) {
            return;
        }

        $this->results['counts']['email_strings'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($strings as $str) {
            $str = (object) $str;
            $templatecode = $str->template_code ?? null;
            unset($str->template_code, $str->original_id);

            if ($templatecode && isset($this->templatemap[$templatecode])) {
                $str->templateid = $this->templatemap[$templatecode];

                $existing = $DB->get_record('local_jobboard_email_strings', [
                    'templateid' => $str->templateid,
                    'lang' => $str->lang,
                ]);

                if ($existing) {
                    if ($this->overwrite) {
                        $str->id = $existing->id;
                        if (!$this->dryrun) {
                            $DB->update_record('local_jobboard_email_strings', $str);
                        }
                        $this->results['counts']['email_strings']['updated']++;
                    } else {
                        $this->results['counts']['email_strings']['skipped']++;
                    }
                } else {
                    if (!$this->dryrun) {
                        $DB->insert_record('local_jobboard_email_strings', $str);
                    }
                    $this->results['counts']['email_strings']['inserted']++;
                }
            }
        }
    }

    /**
     * Import vacancy fields.
     *
     * @param array $fields Vacancy fields to import.
     */
    protected function import_vacancy_fields(array $fields): void {
        global $DB;

        if (empty($fields)) {
            return;
        }

        $this->results['counts']['vacancy_fields'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($fields as $field) {
            $field = (object) $field;
            $vacancycode = $field->vacancy_code ?? null;
            unset($field->vacancy_code, $field->original_id);

            if ($vacancycode && isset($this->vacancymap[$vacancycode])) {
                $field->vacancyid = $this->vacancymap[$vacancycode];
                if (!$this->dryrun) {
                    $DB->insert_record('local_jobboard_vacancy_field', $field);
                }
                $this->results['counts']['vacancy_fields']['inserted']++;
            } else {
                $this->results['counts']['vacancy_fields']['skipped']++;
            }
        }
    }

    /**
     * Import document requirements.
     *
     * @param array $requirements Document requirements to import.
     */
    protected function import_doc_requirements(array $requirements): void {
        global $DB;

        if (empty($requirements)) {
            return;
        }

        $this->results['counts']['doc_requirements'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($requirements as $req) {
            $req = (object) $req;
            $vacancycode = $req->vacancy_code ?? null;
            unset($req->vacancy_code, $req->original_id);

            if ($vacancycode && isset($this->vacancymap[$vacancycode])) {
                $req->vacancyid = $this->vacancymap[$vacancycode];
                if (!$this->dryrun) {
                    $DB->insert_record('local_jobboard_doc_requirement', $req);
                }
                $this->results['counts']['doc_requirements']['inserted']++;
            } else {
                $this->results['counts']['doc_requirements']['skipped']++;
            }
        }
    }

    /**
     * Import plugin config.
     *
     * @param array $config Config to import.
     */
    protected function import_plugin_config(array $config): void {
        global $DB;

        if (empty($config)) {
            return;
        }

        $count = 0;
        foreach ($config as $cfg) {
            $cfg = (object) $cfg;
            unset($cfg->original_id);

            $existing = $DB->get_record('local_jobboard_config', ['name' => $cfg->name]);
            if ($existing) {
                if ($this->overwrite && !$this->dryrun) {
                    $cfg->id = $existing->id;
                    $DB->update_record('local_jobboard_config', $cfg);
                }
            } else {
                if (!$this->dryrun) {
                    $DB->insert_record('local_jobboard_config', $cfg);
                }
            }
            $count++;
        }
        $this->results['counts']['config'] = $count;
    }

    /**
     * Import faculties.
     *
     * @param array $faculties Faculties to import.
     */
    protected function import_faculties(array $faculties): void {
        global $DB;

        if (empty($faculties)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_faculty')) {
            return;
        }

        $this->results['counts']['faculties'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($faculties as $fac) {
            $fac = (object) $fac;
            $originalid = $fac->original_id ?? null;
            unset($fac->original_id);

            $existing = $DB->get_record('local_jobboard_faculty', ['code' => $fac->code]);

            if ($existing) {
                $this->facultymap[$fac->code] = $existing->id;
                if ($originalid) {
                    $this->facultymap[$originalid] = $existing->id;
                }

                if ($this->overwrite) {
                    $fac->id = $existing->id;
                    if (!$this->dryrun) {
                        $DB->update_record('local_jobboard_faculty', $fac);
                    }
                    $this->results['counts']['faculties']['updated']++;
                } else {
                    $this->results['counts']['faculties']['skipped']++;
                }
            } else {
                if (!$this->dryrun) {
                    $newid = $DB->insert_record('local_jobboard_faculty', $fac);
                    $this->facultymap[$fac->code] = $newid;
                    if ($originalid) {
                        $this->facultymap[$originalid] = $newid;
                    }
                }
                $this->results['counts']['faculties']['inserted']++;
            }
        }
    }

    /**
     * Import programs.
     *
     * @param array $programs Programs to import.
     */
    protected function import_programs(array $programs): void {
        global $DB;

        if (empty($programs)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_program')) {
            return;
        }

        $this->results['counts']['programs'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($programs as $prog) {
            $prog = (object) $prog;
            $originalid = $prog->original_id ?? null;
            $facultycode = $prog->faculty_code ?? null;
            unset($prog->original_id, $prog->faculty_code);

            // Map faculty.
            if ($facultycode && isset($this->facultymap[$facultycode])) {
                $prog->facultyid = $this->facultymap[$facultycode];
            }

            $existing = $DB->get_record('local_jobboard_program', ['code' => $prog->code]);

            if ($existing) {
                $this->programmap[$prog->code] = $existing->id;
                if ($originalid) {
                    $this->programmap[$originalid] = $existing->id;
                }

                if ($this->overwrite) {
                    $prog->id = $existing->id;
                    if (!$this->dryrun) {
                        $DB->update_record('local_jobboard_program', $prog);
                    }
                    $this->results['counts']['programs']['updated']++;
                } else {
                    $this->results['counts']['programs']['skipped']++;
                }
            } else {
                if (!$this->dryrun) {
                    $newid = $DB->insert_record('local_jobboard_program', $prog);
                    $this->programmap[$prog->code] = $newid;
                    if ($originalid) {
                        $this->programmap[$originalid] = $newid;
                    }
                }
                $this->results['counts']['programs']['inserted']++;
            }
        }
    }

    /**
     * Import program reviewers.
     *
     * @param array $reviewers Program reviewers to import.
     */
    protected function import_program_reviewers(array $reviewers): void {
        global $DB;

        if (empty($reviewers)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_program_reviewer')) {
            return;
        }

        $this->results['counts']['program_reviewers'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($reviewers as $rev) {
            $rev = (object) $rev;
            $programcode = $rev->program_code ?? null;
            unset($rev->original_id, $rev->program_code, $rev->addedby_username);

            $user = $this->find_user($rev);
            unset($rev->username, $rev->email, $rev->idnumber);

            if ($user && $programcode && isset($this->programmap[$programcode])) {
                $rev->userid = $user->id;
                $rev->programid = $this->programmap[$programcode];

                $existing = $DB->get_record('local_jobboard_program_reviewer', [
                    'userid' => $user->id,
                    'programid' => $rev->programid,
                ]);

                if (!$existing) {
                    if (!$this->dryrun) {
                        $DB->insert_record('local_jobboard_program_reviewer', $rev);
                    }
                    $this->results['counts']['program_reviewers']['inserted']++;
                } else {
                    $this->results['counts']['program_reviewers']['skipped']++;
                }
            } else {
                $this->results['counts']['program_reviewers']['skipped']++;
            }
        }
    }

    /**
     * Import committees.
     *
     * @param array $committees Committees to import.
     */
    protected function import_committees(array $committees): void {
        global $DB, $USER;

        if (empty($committees)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_committee')) {
            return;
        }

        $this->results['counts']['committees'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($committees as $com) {
            $com = (object) $com;
            $originalid = $com->original_id ?? null;
            $facultycode = $com->faculty_code ?? null;
            $vacancycode = $com->vacancy_code ?? null;
            unset($com->original_id, $com->faculty_code, $com->vacancy_code, $com->createdby_username);

            // Map foreign keys.
            if ($facultycode && isset($this->facultymap[$facultycode])) {
                $com->facultyid = $this->facultymap[$facultycode];
            }
            if ($vacancycode && isset($this->vacancymap[$vacancycode])) {
                $com->vacancyid = $this->vacancymap[$vacancycode];
            }
            $com->createdby = $USER->id;

            $existing = $DB->get_record('local_jobboard_committee', ['name' => $com->name]);

            if ($existing) {
                $this->committeemap[$originalid] = $existing->id;

                if ($this->overwrite) {
                    $com->id = $existing->id;
                    if (!$this->dryrun) {
                        $DB->update_record('local_jobboard_committee', $com);
                    }
                    $this->results['counts']['committees']['updated']++;
                } else {
                    $this->results['counts']['committees']['skipped']++;
                }
            } else {
                $com->timecreated = time();
                if (!$this->dryrun) {
                    $newid = $DB->insert_record('local_jobboard_committee', $com);
                    if ($originalid) {
                        $this->committeemap[$originalid] = $newid;
                    }
                }
                $this->results['counts']['committees']['inserted']++;
            }
        }
    }

    /**
     * Import committee members.
     *
     * @param array $members Committee members to import.
     */
    protected function import_committee_members(array $members): void {
        global $DB, $USER;

        if (empty($members)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_committee_member')) {
            return;
        }

        $this->results['counts']['committee_members'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($members as $mem) {
            $mem = (object) $mem;
            $committeeoriginalid = $mem->committee_original_id ?? null;
            unset($mem->original_id, $mem->committee_original_id, $mem->committee_name, $mem->addedby_username);

            $user = $this->find_user($mem);
            unset($mem->username, $mem->email, $mem->idnumber);

            if ($user && $committeeoriginalid && isset($this->committeemap[$committeeoriginalid])) {
                $mem->userid = $user->id;
                $mem->committeeid = $this->committeemap[$committeeoriginalid];
                $mem->addedby = $USER->id;

                $existing = $DB->get_record('local_jobboard_committee_member', [
                    'userid' => $user->id,
                    'committeeid' => $mem->committeeid,
                ]);

                if (!$existing) {
                    $mem->timecreated = time();
                    if (!$this->dryrun) {
                        $DB->insert_record('local_jobboard_committee_member', $mem);
                    }
                    $this->results['counts']['committee_members']['inserted']++;
                } else {
                    $this->results['counts']['committee_members']['skipped']++;
                }
            } else {
                $this->results['counts']['committee_members']['skipped']++;
            }
        }
    }

    /**
     * Import evaluation criteria.
     *
     * @param array $criteria Criteria to import.
     */
    protected function import_criteria(array $criteria): void {
        global $DB;

        if (empty($criteria)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_criteria')) {
            return;
        }

        $this->results['counts']['criteria'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($criteria as $crit) {
            $crit = (object) $crit;
            $vacancycode = $crit->vacancy_code ?? null;
            unset($crit->original_id, $crit->vacancy_code);

            if ($vacancycode && isset($this->vacancymap[$vacancycode])) {
                $crit->vacancyid = $this->vacancymap[$vacancycode];
                if (!$this->dryrun) {
                    $DB->insert_record('local_jobboard_criteria', $crit);
                }
                $this->results['counts']['criteria']['inserted']++;
            } else {
                $this->results['counts']['criteria']['skipped']++;
            }
        }
    }

    /**
     * Import evaluations.
     *
     * @param array $evaluations Evaluations to import.
     */
    protected function import_evaluations(array $evaluations): void {
        global $DB;

        if (empty($evaluations)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_evaluation')) {
            return;
        }

        $this->results['counts']['evaluations'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($evaluations as $eval) {
            $eval = (object) $eval;
            $committeeoriginalid = $eval->committee_original_id ?? null;
            $applicationoriginalid = $eval->application_original_id ?? null;
            unset($eval->original_id, $eval->committee_original_id, $eval->application_original_id);
            unset($eval->committee_name, $eval->vacancy_code, $eval->applicant_username);

            $user = $this->find_user($eval);
            unset($eval->username, $eval->email, $eval->idnumber);

            if ($user && $committeeoriginalid && isset($this->committeemap[$committeeoriginalid])
                && $applicationoriginalid && isset($this->applicationmap[$applicationoriginalid])) {

                $eval->userid = $user->id;
                $eval->committeeid = $this->committeemap[$committeeoriginalid];
                $eval->applicationid = $this->applicationmap[$applicationoriginalid];

                if (!$this->dryrun) {
                    $DB->insert_record('local_jobboard_evaluation', $eval);
                }
                $this->results['counts']['evaluations']['inserted']++;
            } else {
                $this->results['counts']['evaluations']['skipped']++;
            }
        }
    }

    /**
     * Import decisions.
     *
     * @param array $decisions Decisions to import.
     */
    protected function import_decisions(array $decisions): void {
        global $DB, $USER;

        if (empty($decisions)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_decision')) {
            return;
        }

        $this->results['counts']['decisions'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($decisions as $dec) {
            $dec = (object) $dec;
            $committeeoriginalid = $dec->committee_original_id ?? null;
            $applicationoriginalid = $dec->application_original_id ?? null;
            unset($dec->original_id, $dec->committee_original_id, $dec->application_original_id);
            unset($dec->committee_name, $dec->vacancy_code, $dec->applicant_username, $dec->decidedby_username);

            if ($committeeoriginalid && isset($this->committeemap[$committeeoriginalid])
                && $applicationoriginalid && isset($this->applicationmap[$applicationoriginalid])) {

                $dec->committeeid = $this->committeemap[$committeeoriginalid];
                $dec->applicationid = $this->applicationmap[$applicationoriginalid];
                $dec->decidedby = $USER->id;

                if (!$this->dryrun) {
                    $DB->insert_record('local_jobboard_decision', $dec);
                }
                $this->results['counts']['decisions']['inserted']++;
            } else {
                $this->results['counts']['decisions']['skipped']++;
            }
        }
    }

    /**
     * Import interviews.
     *
     * @param array $interviews Interviews to import.
     */
    protected function import_interviews(array $interviews): void {
        global $DB, $USER;

        if (empty($interviews)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_interview')) {
            return;
        }

        $this->results['counts']['interviews'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($interviews as $int) {
            $int = (object) $int;
            $originalid = $int->original_id ?? null;
            $applicationoriginalid = $int->application_original_id ?? null;
            unset($int->original_id, $int->application_original_id);
            unset($int->createdby_username, $int->completedby_username, $int->vacancy_code, $int->applicant_username);

            if ($applicationoriginalid && isset($this->applicationmap[$applicationoriginalid])) {
                $int->applicationid = $this->applicationmap[$applicationoriginalid];
                $int->createdby = $USER->id;

                if (!$this->dryrun) {
                    $newid = $DB->insert_record('local_jobboard_interview', $int);
                    if ($originalid) {
                        $this->interviewmap[$originalid] = $newid;
                    }
                }
                $this->results['counts']['interviews']['inserted']++;
            } else {
                $this->results['counts']['interviews']['skipped']++;
            }
        }
    }

    /**
     * Import interviewers.
     *
     * @param array $interviewers Interviewers to import.
     */
    protected function import_interviewers(array $interviewers): void {
        global $DB;

        if (empty($interviewers)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_interviewer')) {
            return;
        }

        $this->results['counts']['interviewers'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($interviewers as $iv) {
            $iv = (object) $iv;
            $intervieworiginalid = $iv->interview_original_id ?? null;
            unset($iv->original_id, $iv->interview_original_id);

            $user = $this->find_user($iv);
            unset($iv->username, $iv->email, $iv->idnumber);

            if ($user && $intervieworiginalid && isset($this->interviewmap[$intervieworiginalid])) {
                $iv->userid = $user->id;
                $iv->interviewid = $this->interviewmap[$intervieworiginalid];

                if (!$this->dryrun) {
                    $DB->insert_record('local_jobboard_interviewer', $iv);
                }
                $this->results['counts']['interviewers']['inserted']++;
            } else {
                $this->results['counts']['interviewers']['skipped']++;
            }
        }
    }

    /**
     * Import workflow logs.
     *
     * @param array $logs Workflow logs to import.
     */
    protected function import_workflow_logs(array $logs): void {
        global $DB, $USER;

        if (empty($logs)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_workflow_log')) {
            return;
        }

        $this->results['counts']['workflow_logs'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($logs as $log) {
            $log = (object) $log;
            $applicationoriginalid = $log->application_original_id ?? null;
            unset($log->original_id, $log->application_original_id);
            unset($log->changedby_username, $log->vacancy_code, $log->applicant_username);

            if ($applicationoriginalid && isset($this->applicationmap[$applicationoriginalid])) {
                $log->applicationid = $this->applicationmap[$applicationoriginalid];
                $log->changedby = $USER->id;

                if (!$this->dryrun) {
                    $DB->insert_record('local_jobboard_workflow_log', $log);
                }
                $this->results['counts']['workflow_logs']['inserted']++;
            } else {
                $this->results['counts']['workflow_logs']['skipped']++;
            }
        }
    }

    /**
     * Import applicant profiles.
     *
     * @param array $profiles Profiles to import.
     */
    protected function import_applicant_profiles(array $profiles): void {
        global $DB;

        if (empty($profiles)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_applicant_profile')) {
            return;
        }

        $this->results['counts']['profiles'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

        foreach ($profiles as $profile) {
            $profile = (object) $profile;
            unset($profile->original_id);

            $user = $this->find_user($profile);
            unset($profile->username, $profile->email, $profile->idnumber);

            if ($user) {
                $profile->userid = $user->id;

                $existing = $DB->get_record('local_jobboard_applicant_profile', ['userid' => $user->id]);

                if ($existing) {
                    if ($this->overwrite) {
                        $profile->id = $existing->id;
                        if (!$this->dryrun) {
                            $DB->update_record('local_jobboard_applicant_profile', $profile);
                        }
                        $this->results['counts']['profiles']['updated']++;
                    } else {
                        $this->results['counts']['profiles']['skipped']++;
                    }
                } else {
                    if (!$this->dryrun) {
                        $DB->insert_record('local_jobboard_applicant_profile', $profile);
                    }
                    $this->results['counts']['profiles']['inserted']++;
                }
            } else {
                $this->results['counts']['profiles']['skipped']++;
            }
        }
    }

    /**
     * Import consents.
     *
     * @param array $consents Consents to import.
     */
    protected function import_consents(array $consents): void {
        global $DB;

        if (empty($consents)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_consent')) {
            return;
        }

        $this->results['counts']['consents'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($consents as $consent) {
            $consent = (object) $consent;
            unset($consent->original_id);

            $user = $this->find_user($consent);
            unset($consent->username, $consent->email, $consent->idnumber);

            if ($user) {
                $consent->userid = $user->id;

                $existing = $DB->get_record('local_jobboard_consent', [
                    'userid' => $user->id,
                    'consenttype' => $consent->consenttype ?? '',
                ]);

                if (!$existing) {
                    if (!$this->dryrun) {
                        $DB->insert_record('local_jobboard_consent', $consent);
                    }
                    $this->results['counts']['consents']['inserted']++;
                } else {
                    $this->results['counts']['consents']['skipped']++;
                }
            } else {
                $this->results['counts']['consents']['skipped']++;
            }
        }
    }

    /**
     * Import notifications.
     *
     * @param array $notifications Notifications to import.
     */
    protected function import_notifications(array $notifications): void {
        global $DB;

        if (empty($notifications)) {
            return;
        }

        $dbman = $DB->get_manager();
        if (!$dbman->table_exists('local_jobboard_notification')) {
            return;
        }

        // Notifications are typically not imported to avoid duplicates, but we support it.
        $this->results['counts']['notifications'] = ['inserted' => 0, 'skipped' => 0];

        foreach ($notifications as $notif) {
            $notif = (object) $notif;
            unset($notif->original_id);

            $user = $this->find_user($notif);
            unset($notif->username, $notif->email);

            if ($user) {
                $notif->userid = $user->id;
                if (!$this->dryrun) {
                    $DB->insert_record('local_jobboard_notification', $notif);
                }
                $this->results['counts']['notifications']['inserted']++;
            } else {
                $this->results['counts']['notifications']['skipped']++;
            }
        }
    }
}
