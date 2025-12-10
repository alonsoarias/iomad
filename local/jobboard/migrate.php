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
 * Complete migration tool for local_jobboard.
 *
 * Exports and imports ALL plugin data including files between Moodle instances.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');
require_once(__DIR__ . '/lib.php');

use local_jobboard\output\ui_helper;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:configure', $context);

$action = optional_param('action', '', PARAM_ALPHA);

$PAGE->set_url(new moodle_url('/local/jobboard/migrate.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('migrateplugin', 'local_jobboard'));
$PAGE->set_heading(get_string('migrateplugin', 'local_jobboard'));
$PAGE->set_pagelayout('admin');
$PAGE->requires->css('/local/jobboard/styles.css');

// File areas used by the plugin.
define('JOBBOARD_FILE_AREAS', [
    'application_documents',
    'document',
    'converted',
    'preview',
    'vacancy_description',
    'convocatoria_description',
]);

/**
 * Export form class.
 */
class migrate_export_form extends moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'exporthdr', get_string('exportdata', 'local_jobboard'));

        $mform->addElement('static', 'exportinfo', '',
            '<div class="alert alert-info">' .
            '<i class="fa fa-info-circle mr-2"></i>' .
            get_string('fullexport_info', 'local_jobboard') .
            '</div>'
        );

        // What to export - all checked by default.
        $mform->addElement('advcheckbox', 'export_doctypes', '', get_string('doctypes', 'local_jobboard'));
        $mform->setDefault('export_doctypes', 1);

        $mform->addElement('advcheckbox', 'export_emailtemplates', '', get_string('emailtemplates', 'local_jobboard'));
        $mform->setDefault('export_emailtemplates', 1);

        $mform->addElement('advcheckbox', 'export_convocatorias', '', get_string('convocatorias', 'local_jobboard'));
        $mform->setDefault('export_convocatorias', 1);

        $mform->addElement('advcheckbox', 'export_vacancies', '', get_string('vacancies', 'local_jobboard'));
        $mform->setDefault('export_vacancies', 1);

        $mform->addElement('advcheckbox', 'export_settings', '', get_string('pluginsettings', 'local_jobboard'));
        $mform->setDefault('export_settings', 1);

        $mform->addElement('advcheckbox', 'export_exemptions', '', get_string('exemptions', 'local_jobboard'));
        $mform->setDefault('export_exemptions', 1);

        $mform->addElement('advcheckbox', 'export_applications', '',
            get_string('applications', 'local_jobboard') . ' ' .
            html_writer::tag('span', get_string('includesfiles', 'local_jobboard'), ['class' => 'badge badge-warning'])
        );
        $mform->setDefault('export_applications', 0);

        $mform->addElement('advcheckbox', 'export_files', '',
            get_string('allfiles', 'local_jobboard') . ' ' .
            html_writer::tag('span', get_string('largeexport', 'local_jobboard'), ['class' => 'badge badge-danger'])
        );
        $mform->setDefault('export_files', 0);

        $this->add_action_buttons(true, get_string('exportdownload', 'local_jobboard'));
    }
}

/**
 * Import form class.
 */
class migrate_import_form extends moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'importhdr', get_string('importdata', 'local_jobboard'));

        $mform->addElement('filepicker', 'importfile', get_string('migrationfile', 'local_jobboard'), null, [
            'accepted_types' => ['.zip', '.json'],
            'maxbytes' => 524288000, // 500MB for full exports with files.
        ]);
        $mform->addRule('importfile', null, 'required');

        $mform->addElement('advcheckbox', 'overwrite', '', get_string('overwriteexisting', 'local_jobboard'));
        $mform->setDefault('overwrite', 0);

        $mform->addElement('advcheckbox', 'dryrun', '', get_string('dryrunmode', 'local_jobboard'));
        $mform->setDefault('dryrun', 1);

        $this->add_action_buttons(true, get_string('importupload', 'local_jobboard'));
    }
}

/**
 * Export all plugin data to ZIP.
 *
 * @param array $options Export options.
 * @return string Path to the created ZIP file.
 */
function local_jobboard_export_full(array $options): string {
    global $DB, $CFG;

    $data = [
        'plugin' => 'local_jobboard',
        'version' => get_config('local_jobboard', 'version'),
        'release' => get_config('local_jobboard', 'release') ?: '2.1.0',
        'exported' => time(),
        'export_date' => userdate(time()),
        'moodle_version' => $CFG->version,
        'site_name' => $CFG->shortname,
    ];

    // Create temp directory.
    $tempdir = make_temp_directory('jobboard_export_' . time());
    $filesdir = $tempdir . '/files';
    mkdir($filesdir, 0777, true);

    $filecount = 0;

    // Export document types.
    if (!empty($options['export_doctypes'])) {
        $doctypes = $DB->get_records('local_jobboard_doctype', [], 'sortorder ASC');
        $data['doctypes'] = array_values(array_map(function($dt) {
            unset($dt->id);
            return $dt;
        }, $doctypes));
    }

    // Export email templates.
    if (!empty($options['export_emailtemplates'])) {
        $templates = $DB->get_records('local_jobboard_email_template', [], 'code ASC');
        $data['email_templates'] = array_values(array_map(function($tpl) {
            unset($tpl->id);
            return $tpl;
        }, $templates));
    }

    // Export convocatorias with exemptions.
    if (!empty($options['export_convocatorias'])) {
        $convocatorias = $DB->get_records('local_jobboard_convocatoria', [], 'code ASC');
        foreach ($convocatorias as &$conv) {
            // Get exemptions with doctype codes.
            $exemptions = $DB->get_records_sql(
                "SELECT e.*, d.code as doctype_code
                   FROM {local_jobboard_conv_docexempt} e
                   JOIN {local_jobboard_doctype} d ON d.id = e.doctypeid
                  WHERE e.convocatoriaid = ?",
                [$conv->id]
            );
            $conv->exemptions = array_values($exemptions);
            $conv->original_id = $conv->id;
            unset($conv->id);
        }
        $data['convocatorias'] = array_values($convocatorias);
    }

    // Export vacancies with convocatoria codes.
    if (!empty($options['export_vacancies'])) {
        $vacancies = $DB->get_records_sql(
            "SELECT v.*, c.code as convocatoria_code
               FROM {local_jobboard_vacancy} v
               LEFT JOIN {local_jobboard_convocatoria} c ON c.id = v.convocatoriaid
              ORDER BY v.code ASC"
        );
        foreach ($vacancies as &$vac) {
            $vac->original_id = $vac->id;
            unset($vac->id);
        }
        $data['vacancies'] = array_values($vacancies);
    }

    // Export plugin settings.
    if (!empty($options['export_settings'])) {
        $allconfig = $DB->get_records('config_plugins', ['plugin' => 'local_jobboard']);
        $settings = [];
        foreach ($allconfig as $cfg) {
            $settings[$cfg->name] = $cfg->value;
        }
        $data['settings'] = $settings;
    }

    // Export user exemptions with user identifiers.
    if (!empty($options['export_exemptions'])) {
        $exemptions = $DB->get_records_sql(
            "SELECT e.*, u.username, u.email, u.idnumber
               FROM {local_jobboard_exemption} e
               JOIN {user} u ON u.id = e.userid
              ORDER BY u.username"
        );
        foreach ($exemptions as &$ex) {
            unset($ex->id, $ex->userid);
        }
        $data['exemptions'] = array_values($exemptions);
    }

    // Export applications with documents and files.
    if (!empty($options['export_applications'])) {
        $applications = $DB->get_records_sql(
            "SELECT a.*, u.username, u.email, u.idnumber,
                    v.code as vacancy_code, c.code as convocatoria_code
               FROM {local_jobboard_application} a
               JOIN {user} u ON u.id = a.userid
               JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
               LEFT JOIN {local_jobboard_convocatoria} c ON c.id = v.convocatoriaid
              ORDER BY a.id"
        );

        foreach ($applications as &$app) {
            $app->original_id = $app->id;

            // Get documents for this application.
            $documents = $DB->get_records_sql(
                "SELECT d.*, dt.code as doctype_code
                   FROM {local_jobboard_document} d
                   JOIN {local_jobboard_doctype} dt ON dt.id = d.doctypeid
                  WHERE d.applicationid = ?",
                [$app->id]
            );

            // Get files for documents.
            $fs = get_file_storage();
            $context = context_system::instance();

            foreach ($documents as &$doc) {
                $doc->original_id = $doc->id;

                // Get the file.
                $files = $fs->get_area_files(
                    $context->id,
                    'local_jobboard',
                    'application_documents',
                    $doc->applicationid,
                    'itemid, filepath, filename',
                    false
                );

                $doc->files = [];
                foreach ($files as $file) {
                    if ($file->get_filename() !== '.') {
                        $filehash = $file->get_contenthash();
                        $filename = $filehash . '_' . $file->get_filename();

                        // Copy file to export directory.
                        $file->copy_content_to($filesdir . '/' . $filename);
                        $filecount++;

                        $doc->files[] = [
                            'filename' => $file->get_filename(),
                            'filepath' => $file->get_filepath(),
                            'mimetype' => $file->get_mimetype(),
                            'filesize' => $file->get_filesize(),
                            'export_filename' => $filename,
                        ];
                    }
                }

                unset($doc->id);
            }

            $app->documents = array_values($documents);

            // Get validations.
            $validations = $DB->get_records('local_jobboard_doc_validation', ['applicationid' => $app->id]);
            foreach ($validations as &$val) {
                unset($val->id);
            }
            $app->validations = array_values($validations);

            unset($app->id, $app->userid, $app->vacancyid);
        }

        $data['applications'] = array_values($applications);
    }

    // Export all files if requested (without applications).
    if (!empty($options['export_files']) && empty($options['export_applications'])) {
        $fs = get_file_storage();
        $context = context_system::instance();

        $data['files_metadata'] = [];

        foreach (JOBBOARD_FILE_AREAS as $filearea) {
            $files = $fs->get_area_files($context->id, 'local_jobboard', $filearea, false, 'id', false);

            foreach ($files as $file) {
                if ($file->get_filename() !== '.') {
                    $filehash = $file->get_contenthash();
                    $filename = $filehash . '_' . $file->get_filename();

                    $file->copy_content_to($filesdir . '/' . $filename);
                    $filecount++;

                    $data['files_metadata'][] = [
                        'filearea' => $filearea,
                        'itemid' => $file->get_itemid(),
                        'filename' => $file->get_filename(),
                        'filepath' => $file->get_filepath(),
                        'mimetype' => $file->get_mimetype(),
                        'filesize' => $file->get_filesize(),
                        'export_filename' => $filename,
                    ];
                }
            }
        }
    }

    // Export applicant profiles.
    if (!empty($options['export_applications'])) {
        $profiles = $DB->get_records_sql(
            "SELECT p.*, u.username, u.email, u.idnumber
               FROM {local_jobboard_applicant_profile} p
               JOIN {user} u ON u.id = p.userid
              ORDER BY u.username"
        );
        foreach ($profiles as &$prof) {
            unset($prof->id, $prof->userid);
        }
        $data['applicant_profiles'] = array_values($profiles);
    }

    // Export consents.
    if (!empty($options['export_applications'])) {
        $consents = $DB->get_records_sql(
            "SELECT c.*, u.username, u.email, u.idnumber
               FROM {local_jobboard_consent} c
               JOIN {user} u ON u.id = c.userid
              ORDER BY c.timecreated"
        );
        foreach ($consents as &$con) {
            unset($con->id, $con->userid);
        }
        $data['consents'] = array_values($consents);
    }

    $data['file_count'] = $filecount;

    // Write JSON data.
    $jsonfile = $tempdir . '/data.json';
    file_put_contents($jsonfile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // Create ZIP archive.
    $zipfile = $CFG->tempdir . '/jobboard_migration_' . date('Y-m-d_His') . '.zip';
    $zip = new ZipArchive();

    if ($zip->open($zipfile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        throw new moodle_exception('error:cannotcreatezip', 'local_jobboard');
    }

    // Add JSON file.
    $zip->addFile($jsonfile, 'data.json');

    // Add all files from files directory.
    if (is_dir($filesdir)) {
        $fileiterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($filesdir, RecursiveDirectoryIterator::SKIP_DOTS)
        );
        foreach ($fileiterator as $file) {
            $relativepath = 'files/' . $file->getFilename();
            $zip->addFile($file->getPathname(), $relativepath);
        }
    }

    $zip->close();

    // Cleanup temp directory.
    remove_dir($tempdir);

    return $zipfile;
}

/**
 * Import plugin data from ZIP/JSON.
 *
 * @param string $filepath Path to the import file.
 * @param bool $overwrite Overwrite existing records.
 * @param bool $dryrun Dry run mode (no changes).
 * @return array Import results.
 */
function local_jobboard_import_full(string $filepath, bool $overwrite = false, bool $dryrun = false): array {
    global $DB, $CFG, $USER;

    $results = [
        'success' => true,
        'messages' => [],
        'counts' => [],
    ];

    // Determine file type and extract data.
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimetype = finfo_file($finfo, $filepath);
    finfo_close($finfo);

    $tempdir = null;
    $filesdir = null;

    if (strpos($mimetype, 'zip') !== false || pathinfo($filepath, PATHINFO_EXTENSION) === 'zip') {
        // Extract ZIP.
        $tempdir = make_temp_directory('jobboard_import_' . time());
        $zip = new ZipArchive();

        if ($zip->open($filepath) !== true) {
            return ['success' => false, 'messages' => [get_string('invalidmigrationfile', 'local_jobboard')]];
        }

        $zip->extractTo($tempdir);
        $zip->close();

        $jsonpath = $tempdir . '/data.json';
        $filesdir = $tempdir . '/files';

        if (!file_exists($jsonpath)) {
            remove_dir($tempdir);
            return ['success' => false, 'messages' => [get_string('invalidmigrationfile', 'local_jobboard')]];
        }

        $data = json_decode(file_get_contents($jsonpath), true);
    } else {
        // Direct JSON file.
        $data = json_decode(file_get_contents($filepath), true);
    }

    if (!$data || !isset($data['plugin']) || $data['plugin'] !== 'local_jobboard') {
        if ($tempdir) {
            remove_dir($tempdir);
        }
        return ['success' => false, 'messages' => [get_string('invalidmigrationfile', 'local_jobboard')]];
    }

    $results['messages'][] = get_string('importingfrom', 'local_jobboard', [
        'version' => $data['release'] ?? $data['version'],
        'date' => $data['export_date'],
        'site' => $data['site_name'] ?? 'Unknown',
    ]);

    $transaction = $dryrun ? null : $DB->start_delegated_transaction();

    try {
        // Maps for ID translation.
        $doctypemap = [];
        $convocatoriamap = [];
        $vacancymap = [];
        $applicationmap = [];

        // Import document types.
        if (!empty($data['doctypes'])) {
            $results['counts']['doctypes'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

            foreach ($data['doctypes'] as $doctype) {
                $doctype = (object) $doctype;
                $code = $doctype->code;

                $existing = $DB->get_record('local_jobboard_doctype', ['code' => $code]);

                if ($existing) {
                    $doctypemap[$code] = $existing->id;
                    if ($overwrite) {
                        $doctype->id = $existing->id;
                        if (!$dryrun) {
                            $DB->update_record('local_jobboard_doctype', $doctype);
                        }
                        $results['counts']['doctypes']['updated']++;
                    } else {
                        $results['counts']['doctypes']['skipped']++;
                    }
                } else {
                    $doctype->timecreated = time();
                    if (!$dryrun) {
                        $newid = $DB->insert_record('local_jobboard_doctype', $doctype);
                        $doctypemap[$code] = $newid;
                    }
                    $results['counts']['doctypes']['inserted']++;
                }
            }
            $results['messages'][] = get_string('importeddoctypes', 'local_jobboard', $results['counts']['doctypes']);
        }

        // Import email templates.
        if (!empty($data['email_templates'])) {
            $results['counts']['email_templates'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

            foreach ($data['email_templates'] as $template) {
                $template = (object) $template;

                $existing = $DB->get_record('local_jobboard_email_template', [
                    'code' => $template->code,
                    'companyid' => $template->companyid ?? 0,
                ]);

                if ($existing) {
                    if ($overwrite) {
                        $template->id = $existing->id;
                        $template->timemodified = time();
                        if (!$dryrun) {
                            $DB->update_record('local_jobboard_email_template', $template);
                        }
                        $results['counts']['email_templates']['updated']++;
                    } else {
                        $results['counts']['email_templates']['skipped']++;
                    }
                } else {
                    $template->timecreated = time();
                    if (!$dryrun) {
                        $DB->insert_record('local_jobboard_email_template', $template);
                    }
                    $results['counts']['email_templates']['inserted']++;
                }
            }
            $results['messages'][] = get_string('importedemails', 'local_jobboard', $results['counts']['email_templates']);
        }

        // Import convocatorias.
        if (!empty($data['convocatorias'])) {
            $results['counts']['convocatorias'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

            foreach ($data['convocatorias'] as $conv) {
                $conv = (object) $conv;
                $originalid = $conv->original_id ?? null;
                $exemptions = $conv->exemptions ?? [];
                unset($conv->original_id, $conv->exemptions);

                $existing = $DB->get_record('local_jobboard_convocatoria', ['code' => $conv->code]);

                if ($existing) {
                    $convocatoriamap[$conv->code] = $existing->id;
                    if ($originalid) {
                        $convocatoriamap[$originalid] = $existing->id;
                    }

                    if ($overwrite) {
                        $conv->id = $existing->id;
                        $conv->timemodified = time();
                        $conv->modifiedby = $USER->id;
                        if (!$dryrun) {
                            $DB->update_record('local_jobboard_convocatoria', $conv);
                        }
                        $results['counts']['convocatorias']['updated']++;
                    } else {
                        $results['counts']['convocatorias']['skipped']++;
                    }
                } else {
                    $conv->timecreated = time();
                    $conv->createdby = $USER->id;
                    $conv->companyid = null;
                    if (!$dryrun) {
                        $newid = $DB->insert_record('local_jobboard_convocatoria', $conv);
                        $convocatoriamap[$conv->code] = $newid;
                        if ($originalid) {
                            $convocatoriamap[$originalid] = $newid;
                        }
                    }
                    $results['counts']['convocatorias']['inserted']++;
                }

                // Import exemptions.
                if (!empty($exemptions) && !$dryrun) {
                    $convid = $convocatoriamap[$conv->code] ?? null;
                    if ($convid) {
                        foreach ($exemptions as $exempt) {
                            $exempt = (object) $exempt;
                            $doctypecode = $exempt->doctype_code ?? null;
                            if ($doctypecode && isset($doctypemap[$doctypecode])) {
                                $existingexempt = $DB->get_record('local_jobboard_conv_docexempt', [
                                    'convocatoriaid' => $convid,
                                    'doctypeid' => $doctypemap[$doctypecode],
                                ]);
                                if (!$existingexempt) {
                                    $newexempt = new stdClass();
                                    $newexempt->convocatoriaid = $convid;
                                    $newexempt->doctypeid = $doctypemap[$doctypecode];
                                    $newexempt->exemptionreason = $exempt->exemptionreason ?? '';
                                    $newexempt->createdby = $USER->id;
                                    $newexempt->timecreated = time();
                                    $DB->insert_record('local_jobboard_conv_docexempt', $newexempt);
                                }
                            }
                        }
                    }
                }
            }
            $results['messages'][] = get_string('importedconvocatorias', 'local_jobboard', $results['counts']['convocatorias']);
        }

        // Import vacancies.
        if (!empty($data['vacancies'])) {
            $results['counts']['vacancies'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

            foreach ($data['vacancies'] as $vacancy) {
                $vacancy = (object) $vacancy;
                $originalid = $vacancy->original_id ?? null;
                $convcode = $vacancy->convocatoria_code ?? null;
                unset($vacancy->original_id, $vacancy->convocatoria_code);

                // Map convocatoria.
                if ($convcode && isset($convocatoriamap[$convcode])) {
                    $vacancy->convocatoriaid = $convocatoriamap[$convcode];
                }

                $existing = $DB->get_record('local_jobboard_vacancy', ['code' => $vacancy->code]);

                if ($existing) {
                    $vacancymap[$vacancy->code] = $existing->id;
                    if ($originalid) {
                        $vacancymap[$originalid] = $existing->id;
                    }

                    if ($overwrite) {
                        $vacancy->id = $existing->id;
                        $vacancy->timemodified = time();
                        $vacancy->modifiedby = $USER->id;
                        if (!$dryrun) {
                            $DB->update_record('local_jobboard_vacancy', $vacancy);
                        }
                        $results['counts']['vacancies']['updated']++;
                    } else {
                        $results['counts']['vacancies']['skipped']++;
                    }
                } else {
                    $vacancy->timecreated = time();
                    $vacancy->createdby = $USER->id;
                    $vacancy->companyid = null;
                    if (!$dryrun) {
                        $newid = $DB->insert_record('local_jobboard_vacancy', $vacancy);
                        $vacancymap[$vacancy->code] = $newid;
                        if ($originalid) {
                            $vacancymap[$originalid] = $newid;
                        }
                    }
                    $results['counts']['vacancies']['inserted']++;
                }
            }
            $results['messages'][] = get_string('importedvacancies', 'local_jobboard', $results['counts']['vacancies']);
        }

        // Import settings.
        if (!empty($data['settings'])) {
            $count = 0;
            foreach ($data['settings'] as $key => $value) {
                if (!$dryrun) {
                    set_config($key, $value, 'local_jobboard');
                }
                $count++;
            }
            $results['counts']['settings'] = $count;
            $results['messages'][] = get_string('importedsettings', 'local_jobboard', $count);
        }

        // Import user exemptions.
        if (!empty($data['exemptions'])) {
            $results['counts']['exemptions'] = ['inserted' => 0, 'updated' => 0, 'skipped' => 0];

            foreach ($data['exemptions'] as $exemption) {
                $exemption = (object) $exemption;

                // Find user.
                $user = null;
                if (!empty($exemption->username)) {
                    $user = $DB->get_record('user', ['username' => $exemption->username]);
                }
                if (!$user && !empty($exemption->email)) {
                    $user = $DB->get_record('user', ['email' => $exemption->email]);
                }
                if (!$user && !empty($exemption->idnumber)) {
                    $user = $DB->get_record('user', ['idnumber' => $exemption->idnumber]);
                }

                if ($user) {
                    unset($exemption->username, $exemption->email, $exemption->idnumber);
                    $exemption->userid = $user->id;

                    $existing = $DB->get_record('local_jobboard_exemption', [
                        'userid' => $user->id,
                        'exemptiontype' => $exemption->exemptiontype,
                    ]);

                    if ($existing) {
                        if ($overwrite) {
                            $exemption->id = $existing->id;
                            $exemption->timemodified = time();
                            if (!$dryrun) {
                                $DB->update_record('local_jobboard_exemption', $exemption);
                            }
                            $results['counts']['exemptions']['updated']++;
                        } else {
                            $results['counts']['exemptions']['skipped']++;
                        }
                    } else {
                        $exemption->timecreated = time();
                        $exemption->createdby = $USER->id;
                        if (!$dryrun) {
                            $DB->insert_record('local_jobboard_exemption', $exemption);
                        }
                        $results['counts']['exemptions']['inserted']++;
                    }
                } else {
                    $results['counts']['exemptions']['skipped']++;
                }
            }
            $results['messages'][] = get_string('importedexemptions', 'local_jobboard', $results['counts']['exemptions']);
        }

        // Import applications with documents and files.
        if (!empty($data['applications']) && $filesdir && is_dir($filesdir)) {
            $results['counts']['applications'] = ['inserted' => 0, 'skipped' => 0];
            $results['counts']['documents'] = ['inserted' => 0, 'skipped' => 0];
            $results['counts']['files'] = ['inserted' => 0, 'skipped' => 0];

            $fs = get_file_storage();
            $context = context_system::instance();

            foreach ($data['applications'] as $app) {
                $app = (object) $app;

                // Find user.
                $user = null;
                if (!empty($app->username)) {
                    $user = $DB->get_record('user', ['username' => $app->username]);
                }
                if (!$user && !empty($app->email)) {
                    $user = $DB->get_record('user', ['email' => $app->email]);
                }

                // Find vacancy.
                $vacancyid = null;
                if (!empty($app->vacancy_code) && isset($vacancymap[$app->vacancy_code])) {
                    $vacancyid = $vacancymap[$app->vacancy_code];
                }

                if (!$user || !$vacancyid) {
                    $results['counts']['applications']['skipped']++;
                    continue;
                }

                $documents = $app->documents ?? [];
                $validations = $app->validations ?? [];
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
                    $applicationmap[$app->original_id ?? 0] = $existing->id;
                    $results['counts']['applications']['skipped']++;
                    continue;
                }

                if (!$dryrun) {
                    $app->timecreated = time();
                    $newappid = $DB->insert_record('local_jobboard_application', $app);
                    $applicationmap[$app->original_id ?? 0] = $newappid;
                    $results['counts']['applications']['inserted']++;

                    // Import documents.
                    foreach ($documents as $doc) {
                        $doc = (object) $doc;
                        $doctypecode = $doc->doctype_code ?? null;
                        $files = $doc->files ?? [];
                        unset($doc->doctype_code, $doc->files, $doc->original_id);

                        if ($doctypecode && isset($doctypemap[$doctypecode])) {
                            $doc->doctypeid = $doctypemap[$doctypecode];
                            $doc->applicationid = $newappid;
                            $doc->timecreated = time();

                            $newdocid = $DB->insert_record('local_jobboard_document', $doc);
                            $results['counts']['documents']['inserted']++;

                            // Import files.
                            foreach ($files as $fileinfo) {
                                $exportfilename = $fileinfo['export_filename'] ?? null;
                                if ($exportfilename && file_exists($filesdir . '/' . $exportfilename)) {
                                    $filerecord = [
                                        'contextid' => $context->id,
                                        'component' => 'local_jobboard',
                                        'filearea' => 'application_documents',
                                        'itemid' => $newappid,
                                        'filepath' => $fileinfo['filepath'] ?? '/',
                                        'filename' => $fileinfo['filename'],
                                    ];

                                    $fs->create_file_from_pathname($filerecord, $filesdir . '/' . $exportfilename);
                                    $results['counts']['files']['inserted']++;
                                }
                            }
                        }
                    }
                } else {
                    $results['counts']['applications']['inserted']++;
                }
            }

            $results['messages'][] = get_string('importedapplications', 'local_jobboard', $results['counts']['applications']);
            $results['messages'][] = get_string('importeddocuments', 'local_jobboard', $results['counts']['documents']);
            $results['messages'][] = get_string('importedfiles', 'local_jobboard', $results['counts']['files']);
        }

        if (!$dryrun && $transaction) {
            $transaction->allow_commit();
        }

        if ($dryrun) {
            array_unshift($results['messages'], html_writer::tag('strong', get_string('dryrunresults', 'local_jobboard')));
        }

    } catch (Exception $e) {
        if ($transaction) {
            $transaction->rollback($e);
        }
        $results['success'] = false;
        $results['messages'][] = get_string('importerror', 'local_jobboard') . ': ' . $e->getMessage();
    }

    // Cleanup.
    if ($tempdir) {
        remove_dir($tempdir);
    }

    return $results;
}

// Handle export action.
if ($action === 'export') {
    $exportform = new migrate_export_form();

    if ($data = $exportform->get_data()) {
        try {
            $zipfile = local_jobboard_export_full((array) $data);

            // Send file for download.
            $filename = basename($zipfile);
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($zipfile));
            header('Pragma: no-cache');
            header('Expires: 0');

            readfile($zipfile);
            unlink($zipfile);
            exit;
        } catch (Exception $e) {
            redirect(
                new moodle_url('/local/jobboard/migrate.php'),
                get_string('exporterror', 'local_jobboard') . ': ' . $e->getMessage(),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }
    }
}

// Handle import action.
if ($action === 'import') {
    $importform = new migrate_import_form();

    if ($data = $importform->get_data()) {
        $tempfile = $importform->save_temp_file('importfile');

        if ($tempfile) {
            $results = local_jobboard_import_full($tempfile, !empty($data->overwrite), !empty($data->dryrun));
            unlink($tempfile);

            if (!empty($data->dryrun)) {
                // Show dry run results.
                echo $OUTPUT->header();
                echo html_writer::start_div('local-jobboard-dashboard');
                echo html_writer::tag('h3', get_string('dryrunresults', 'local_jobboard'));
                echo html_writer::start_div('card shadow-sm');
                echo html_writer::start_div('card-body');

                foreach ($results['messages'] as $msg) {
                    echo html_writer::tag('p', $msg);
                }

                echo html_writer::end_div();
                echo html_writer::end_div();

                echo html_writer::link(
                    new moodle_url('/local/jobboard/migrate.php'),
                    '<i class="fa fa-arrow-left mr-2"></i>' . get_string('back'),
                    ['class' => 'btn btn-primary mt-3']
                );
                echo html_writer::end_div();
                echo $OUTPUT->footer();
                exit;
            }

            $type = $results['success'] ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_ERROR;
            redirect(
                new moodle_url('/local/jobboard/migrate.php'),
                implode('<br>', $results['messages']),
                null,
                $type
            );
        } else {
            redirect(
                new moodle_url('/local/jobboard/migrate.php'),
                get_string('invalidmigrationfile', 'local_jobboard'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }
    }
}

// Display page.
echo $OUTPUT->header();
echo ui_helper::get_inline_styles();

echo html_writer::start_div('local-jobboard-dashboard');

// Page header.
echo html_writer::start_div('jb-welcome-section bg-gradient-primary text-white rounded-lg p-4 mb-4');
echo html_writer::start_div('d-flex justify-content-between align-items-center');
echo html_writer::start_div();
echo html_writer::tag('h2', get_string('migrateplugin', 'local_jobboard'), ['class' => 'mb-1 font-weight-bold']);
echo html_writer::tag('p', get_string('migrateplugin_desc', 'local_jobboard'), ['class' => 'mb-0 opacity-75']);
echo html_writer::end_div();
echo html_writer::tag('i', '', ['class' => 'fa fa-exchange-alt fa-3x opacity-25']);
echo html_writer::end_div();
echo html_writer::end_div();

// Info box.
echo html_writer::start_div('alert alert-info mb-4');
echo html_writer::tag('i', '', ['class' => 'fa fa-info-circle fa-lg mr-2']);
echo html_writer::tag('strong', get_string('migrationinfo_title', 'local_jobboard'));
echo html_writer::tag('p', get_string('migrationinfo_desc', 'local_jobboard'), ['class' => 'mb-0 mt-2']);
echo html_writer::end_div();

// Two column layout.
echo html_writer::start_div('row');

// Export Section.
echo html_writer::start_div('col-lg-6 mb-4');
echo html_writer::start_div('card shadow-sm h-100');
echo html_writer::start_div('card-header bg-primary text-white');
echo html_writer::tag('h5', '<i class="fa fa-download mr-2"></i>' . get_string('exportdata', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

echo html_writer::tag('p', get_string('exportdata_desc', 'local_jobboard'), ['class' => 'text-muted mb-3']);

$exportform = new migrate_export_form(new moodle_url('/local/jobboard/migrate.php', ['action' => 'export']));
$exportform->display();

echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

// Import Section.
echo html_writer::start_div('col-lg-6 mb-4');
echo html_writer::start_div('card shadow-sm h-100');
echo html_writer::start_div('card-header bg-success text-white');
echo html_writer::tag('h5', '<i class="fa fa-upload mr-2"></i>' . get_string('importdata', 'local_jobboard'), ['class' => 'mb-0']);
echo html_writer::end_div();
echo html_writer::start_div('card-body');

echo html_writer::tag('p', get_string('importdata_desc', 'local_jobboard'), ['class' => 'text-muted mb-3']);

// Warning box.
echo html_writer::start_div('alert alert-warning');
echo html_writer::tag('i', '', ['class' => 'fa fa-exclamation-triangle mr-2']);
echo get_string('importwarning', 'local_jobboard');
echo html_writer::end_div();

$importform = new migrate_import_form(new moodle_url('/local/jobboard/migrate.php', ['action' => 'import']));
$importform->display();

echo html_writer::end_div();
echo html_writer::end_div();
echo html_writer::end_div();

echo html_writer::end_div(); // row

// Back to dashboard.
echo html_writer::start_div('mt-3');
echo html_writer::link(
    new moodle_url('/local/jobboard/index.php'),
    '<i class="fa fa-arrow-left mr-2"></i>' . get_string('backtodashboard', 'local_jobboard'),
    ['class' => 'btn btn-outline-secondary']
);
echo html_writer::end_div();

echo html_writer::end_div(); // local-jobboard-dashboard

echo $OUTPUT->footer();
