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
 * Uses refactored classes for better code organization:
 * - local_jobboard\migration\exporter - Export functionality
 * - local_jobboard\migration\importer - Import functionality
 * - local_jobboard\forms\migrate_export_form - Export form
 * - local_jobboard\forms\migrate_import_form - Import form
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once(__DIR__ . '/../lib.php');

use local_jobboard\migration\exporter;
use local_jobboard\migration\importer;
use local_jobboard\forms\migrate_export_form;
use local_jobboard\forms\migrate_import_form;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:configure', $context);

$action = optional_param('action', '', PARAM_ALPHA);

$PAGE->set_url(new moodle_url('/local/jobboard/admin/migrate.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('migrateplugin', 'local_jobboard'));
$PAGE->set_heading(get_string('migrateplugin', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Create exporter instance for counts.
$exporterinstance = new exporter();

// Handle export action - exports ALL data, no options.
if ($action === 'export') {
    $exportform = new migrate_export_form(
        new moodle_url('/local/jobboard/admin/migrate.php', ['action' => 'export']),
        ['counts' => $exporterinstance->get_export_counts()]
    );

    if ($exportform->get_data()) {
        try {
            // Export everything - full migration package.
            $zipfile = $exporterinstance->export_full();

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
                new moodle_url('/local/jobboard/admin/migrate.php'),
                get_string('exporterror', 'local_jobboard') . ': ' . $e->getMessage(),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }
    }
}

// Handle import action.
if ($action === 'import') {
    $importform = new migrate_import_form(
        new moodle_url('/local/jobboard/admin/migrate.php', ['action' => 'import'])
    );

    if ($data = $importform->get_data()) {
        $tempfile = $importform->save_temp_file('importfile');

        if ($tempfile) {
            $importerinstance = new importer(!empty($data->overwrite), !empty($data->dryrun));
            $results = $importerinstance->import_full($tempfile);
            unlink($tempfile);

            if (!empty($data->dryrun)) {
                // Show dry run results using template.
                echo $OUTPUT->header();
                $renderer = $PAGE->get_renderer('local_jobboard');
                $templatedata = $renderer->prepare_migrate_page_data([], '', '', $results);
                echo $renderer->render_migrate_page($templatedata);
                echo $OUTPUT->footer();
                exit;
            }

            $type = $results['success'] ? \core\output\notification::NOTIFY_SUCCESS : \core\output\notification::NOTIFY_ERROR;
            redirect(
                new moodle_url('/local/jobboard/admin/migrate.php'),
                implode('<br>', $results['messages']),
                null,
                $type
            );
        } else {
            redirect(
                new moodle_url('/local/jobboard/admin/migrate.php'),
                get_string('invalidmigrationfile', 'local_jobboard'),
                null,
                \core\output\notification::NOTIFY_ERROR
            );
        }
    }
}

// Display page using renderer + template pattern.
echo $OUTPUT->header();

// Get renderer.
$renderer = $PAGE->get_renderer('local_jobboard');

// Get export counts.
$exportcounts = $exporterinstance->get_export_counts();

// Render forms to HTML.
$exportform = new migrate_export_form(
    new moodle_url('/local/jobboard/admin/migrate.php', ['action' => 'export']),
    ['counts' => $exportcounts]
);
ob_start();
$exportform->display();
$exportformhtml = ob_get_clean();

$importform = new migrate_import_form(
    new moodle_url('/local/jobboard/admin/migrate.php', ['action' => 'import'])
);
ob_start();
$importform->display();
$importformhtml = ob_get_clean();

// Prepare template data.
$data = $renderer->prepare_migrate_page_data($exportcounts, $exportformhtml, $importformhtml);

// Render page.
echo $renderer->render_migrate_page($data);

echo $OUTPUT->footer();
