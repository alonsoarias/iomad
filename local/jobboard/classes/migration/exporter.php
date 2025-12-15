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
 * Data exporter class for local_jobboard migration.
 *
 * Handles complete export of all plugin data including files.
 * Extracted from admin/migrate.php for better code organization.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_jobboard\migration;

defined('MOODLE_INTERNAL') || die();

/**
 * Exporter class for migration.
 */
class exporter {

    /** @var array File areas used by the plugin. */
    const FILE_AREAS = [
        'application_documents',
        'document',
        'converted',
        'preview',
        'vacancy_description',
        'convocatoria_description',
    ];

    /** @var \context_system System context. */
    protected $context;

    /** @var \file_storage File storage instance. */
    protected $fs;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->context = \context_system::instance();
        $this->fs = get_file_storage();
    }

    /**
     * Get counts of data to be exported.
     *
     * @return array Counts by type.
     */
    public function get_export_counts(): array {
        global $DB;

        $counts = [
            'doctypes' => $DB->count_records('local_jobboard_doctype'),
            'email_templates' => $DB->count_records('local_jobboard_email_template'),
            'convocatorias' => $DB->count_records('local_jobboard_convocatoria'),
            'vacancies' => $DB->count_records('local_jobboard_vacancy'),
            'applications' => $DB->count_records('local_jobboard_application'),
            'documents' => $DB->count_records('local_jobboard_document'),
            'exemptions' => $DB->count_records('local_jobboard_exemption'),
            'files' => 0,
        ];

        // Count files in all areas.
        foreach (self::FILE_AREAS as $filearea) {
            $files = $this->fs->get_area_files($this->context->id, 'local_jobboard', $filearea, false, 'id', false);
            foreach ($files as $file) {
                if ($file->get_filename() !== '.') {
                    $counts['files']++;
                }
            }
        }

        return $counts;
    }

    /**
     * Export ALL plugin data to ZIP - complete migration package.
     *
     * @return string Path to the created ZIP file.
     * @throws \moodle_exception If export fails.
     */
    public function export_full(): string {
        global $DB, $CFG;

        $data = $this->prepare_export_metadata();

        // Create temp directory.
        $tempdir = make_temp_directory('jobboard_export_' . time());
        $filesdir = $tempdir . '/files';
        mkdir($filesdir, 0777, true);

        $filecount = 0;

        // Export all data types.
        $data['doctypes'] = $this->export_doctypes();
        $data['email_templates'] = $this->export_email_templates();
        $data['convocatorias'] = $this->export_convocatorias();
        $data['vacancies'] = $this->export_vacancies();
        $data['settings'] = $this->export_settings();
        $data['exemptions'] = $this->export_exemptions();

        // Export applications with documents and files.
        list($applications, $appfilecount) = $this->export_applications($filesdir);
        $data['applications'] = $applications;
        $filecount += $appfilecount;

        // Export all files from all areas.
        list($filesmetadata, $areafilecount) = $this->export_files($filesdir);
        $data['files_metadata'] = $filesmetadata;
        $filecount += $areafilecount;

        // Export profiles and consents.
        $data['applicant_profiles'] = $this->export_applicant_profiles();
        $data['consents'] = $this->export_consents();
        $data['audit_logs'] = $this->export_audit_logs();

        $data['file_count'] = $filecount;

        // Create ZIP archive.
        $zipfile = $this->create_zip_archive($tempdir, $data, $filesdir);

        // Cleanup temp directory.
        remove_dir($tempdir);

        return $zipfile;
    }

    /**
     * Prepare export metadata.
     *
     * @return array Metadata array.
     */
    protected function prepare_export_metadata(): array {
        global $CFG;

        return [
            'plugin' => 'local_jobboard',
            'version' => get_config('local_jobboard', 'version'),
            'release' => get_config('local_jobboard', 'release') ?: '2.1.0',
            'exported' => time(),
            'export_date' => userdate(time()),
            'moodle_version' => $CFG->version,
            'site_name' => $CFG->shortname,
            'full_export' => true,
        ];
    }

    /**
     * Export document types.
     *
     * @return array Exported doctypes.
     */
    protected function export_doctypes(): array {
        global $DB;

        $doctypes = $DB->get_records('local_jobboard_doctype', [], 'sortorder ASC');
        return array_values(array_map(function($dt) {
            $dt->original_id = $dt->id;
            unset($dt->id);
            return $dt;
        }, $doctypes));
    }

    /**
     * Export email templates.
     *
     * @return array Exported templates.
     */
    protected function export_email_templates(): array {
        global $DB;

        $templates = $DB->get_records('local_jobboard_email_template', [], 'code ASC');
        return array_values(array_map(function($tpl) {
            unset($tpl->id);
            return $tpl;
        }, $templates));
    }

    /**
     * Export convocatorias with exemptions.
     *
     * @return array Exported convocatorias.
     */
    protected function export_convocatorias(): array {
        global $DB;

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
        return array_values($convocatorias);
    }

    /**
     * Export vacancies.
     *
     * @return array Exported vacancies.
     */
    protected function export_vacancies(): array {
        global $DB;

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
        return array_values($vacancies);
    }

    /**
     * Export plugin settings.
     *
     * @return array Settings key-value pairs.
     */
    protected function export_settings(): array {
        global $DB;

        $allconfig = $DB->get_records('config_plugins', ['plugin' => 'local_jobboard']);
        $settings = [];
        foreach ($allconfig as $cfg) {
            $settings[$cfg->name] = $cfg->value;
        }
        return $settings;
    }

    /**
     * Export user exemptions.
     *
     * @return array Exported exemptions.
     */
    protected function export_exemptions(): array {
        global $DB;

        $exemptions = $DB->get_records_sql(
            "SELECT e.*, u.username, u.email, u.idnumber
               FROM {local_jobboard_exemption} e
               JOIN {user} u ON u.id = e.userid
              ORDER BY u.username"
        );
        foreach ($exemptions as &$ex) {
            $ex->original_id = $ex->id;
            unset($ex->id, $ex->userid);
        }
        return array_values($exemptions);
    }

    /**
     * Export applications with documents and files.
     *
     * @param string $filesdir Directory to copy files to.
     * @return array [applications, filecount]
     */
    protected function export_applications(string $filesdir): array {
        global $DB;

        $filecount = 0;
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

            foreach ($documents as &$doc) {
                $doc->original_id = $doc->id;

                // Get files for this document/application.
                $files = $this->fs->get_area_files(
                    $this->context->id,
                    'local_jobboard',
                    'application_documents',
                    $app->id,
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
                $val->original_id = $val->id;
                unset($val->id);
            }
            $app->validations = array_values($validations);

            unset($app->id, $app->userid, $app->vacancyid);
        }

        return [array_values($applications), $filecount];
    }

    /**
     * Export all files from all areas.
     *
     * @param string $filesdir Directory to copy files to.
     * @return array [files_metadata, filecount]
     */
    protected function export_files(string $filesdir): array {
        $filesmetadata = [];
        $filecount = 0;

        foreach (self::FILE_AREAS as $filearea) {
            $files = $this->fs->get_area_files($this->context->id, 'local_jobboard', $filearea, false, 'id', false);

            foreach ($files as $file) {
                if ($file->get_filename() !== '.') {
                    $filehash = $file->get_contenthash();
                    $filename = $filehash . '_' . $file->get_filename();

                    // Only copy if not already copied (avoid duplicates).
                    if (!file_exists($filesdir . '/' . $filename)) {
                        $file->copy_content_to($filesdir . '/' . $filename);
                        $filecount++;
                    }

                    $filesmetadata[] = [
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

        return [$filesmetadata, $filecount];
    }

    /**
     * Export applicant profiles.
     *
     * @return array Exported profiles.
     */
    protected function export_applicant_profiles(): array {
        global $DB;

        $profiles = $DB->get_records_sql(
            "SELECT p.*, u.username, u.email, u.idnumber
               FROM {local_jobboard_applicant_profile} p
               JOIN {user} u ON u.id = p.userid
              ORDER BY u.username"
        );
        foreach ($profiles as &$prof) {
            $prof->original_id = $prof->id;
            unset($prof->id, $prof->userid);
        }
        return array_values($profiles);
    }

    /**
     * Export consents.
     *
     * @return array Exported consents.
     */
    protected function export_consents(): array {
        global $DB;

        $consents = $DB->get_records_sql(
            "SELECT c.*, u.username, u.email, u.idnumber
               FROM {local_jobboard_consent} c
               JOIN {user} u ON u.id = c.userid
              ORDER BY c.timecreated"
        );
        foreach ($consents as &$con) {
            $con->original_id = $con->id;
            unset($con->id, $con->userid);
        }
        return array_values($consents);
    }

    /**
     * Export audit logs (for reference).
     *
     * @param int $limit Maximum number of records.
     * @return array Exported audit logs.
     */
    protected function export_audit_logs(int $limit = 10000): array {
        global $DB;

        $audits = $DB->get_records_sql(
            "SELECT a.*, u.username, u.email
               FROM {local_jobboard_audit} a
               LEFT JOIN {user} u ON u.id = a.userid
              ORDER BY a.timecreated DESC
              LIMIT " . $limit
        );
        foreach ($audits as &$audit) {
            unset($audit->id, $audit->userid);
        }
        return array_values($audits);
    }

    /**
     * Create ZIP archive from exported data.
     *
     * @param string $tempdir Temporary directory.
     * @param array $data Export data.
     * @param string $filesdir Files directory.
     * @return string Path to ZIP file.
     * @throws \moodle_exception If ZIP creation fails.
     */
    protected function create_zip_archive(string $tempdir, array $data, string $filesdir): string {
        global $CFG;

        // Write JSON data.
        $jsonfile = $tempdir . '/data.json';
        file_put_contents($jsonfile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Create ZIP archive.
        $zipfile = $CFG->tempdir . '/jobboard_migration_' . date('Y-m-d_His') . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($zipfile, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            throw new \moodle_exception('error:cannotcreatezip', 'local_jobboard');
        }

        // Add JSON file.
        $zip->addFile($jsonfile, 'data.json');

        // Add all files from files directory.
        if (is_dir($filesdir)) {
            $fileiterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($filesdir, \RecursiveDirectoryIterator::SKIP_DOTS)
            );
            foreach ($fileiterator as $file) {
                $relativepath = 'files/' . $file->getFilename();
                $zip->addFile($file->getPathname(), $relativepath);
            }
        }

        $zip->close();

        return $zipfile;
    }
}
