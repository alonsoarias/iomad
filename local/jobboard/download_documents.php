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
 * Download all documents for an application as a ZIP file.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educacion Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

$applicationid = required_param('applicationid', PARAM_INT);
$sesskey = required_param('sesskey', PARAM_RAW);

// Validate session key.
if (!confirm_sesskey($sesskey)) {
    throw new moodle_exception('invalidsesskey');
}

// Check capability.
$context = context_system::instance();
require_capability('local/jobboard:reviewdocuments', $context);

// Get application directly from DB to avoid class loading issues.
$application = $DB->get_record('local_jobboard_application', ['id' => $applicationid]);
if (!$application) {
    throw new moodle_exception('error:invalidapplication', 'local_jobboard');
}

// Get applicant info for filename and HTML content.
$applicant = $DB->get_record('user', ['id' => $application->userid],
    'id, firstname, lastname, idnumber, firstnamephonetic, lastnamephonetic, middlename, alternatename');
if (!$applicant) {
    throw new moodle_exception('error:invaliduser', 'local_jobboard');
}

// Get all documents for this application.
$documents = $DB->get_records('local_jobboard_document', [
    'applicationid' => $applicationid,
    'issuperseded' => 0
], 'timecreated ASC');

if (empty($documents)) {
    throw new moodle_exception('error:nodocuments', 'local_jobboard');
}

// Create ZIP file.
$lastname = clean_filename($applicant->lastname);
$firstname = clean_filename($applicant->firstname);
$idnumber = clean_filename($applicant->idnumber ?: (string)$applicant->id);

$zipfilename = "documentos_{$lastname}_{$firstname}_{$idnumber}_" . date('Ymd_His') . '.zip';

// Sanitize lastname for use in document filenames (same logic as document class).
$sanitizedlastname = \local_jobboard\document::sanitize_filename_part($applicant->lastname);

// Document type prefixes (institutional naming convention).
$fileprefixes = [
    'rut' => 'Rut',                           // Fotocopia del RUT actualizado.
    'sigep' => 'Hv',                          // Hoja de vida.
    'bienes_rentas' => 'FDR',                 // Formato de Declaración de Bienes y Rentas.
    'cedula' => 'Di',                         // Documento de identidad.
    'libreta_militar' => 'Lm',                // Libreta militar.
    'diploma_pregrado' => 'Preg',             // Diploma de pregrado.
    'acta_pregrado' => 'Apreg',               // Acta de grado pregrado.
    'tarjeta_profesional' => 'Tp',            // Tarjeta Profesional.
    'diploma_posgrado' => 'Posg',             // Diploma posgrado.
    'acta_posgrado' => 'Aposg',               // Acta de grado Posgrado.
    'experiencia_docente' => 'Edoc',          // Experiencia docente.
    'experiencia_profesional' => 'Epro',      // Experiencia profesional.
    'formacion_complementaria' => 'Cfc',      // Certificados de formación complementaria.
    'formacion_pedagogia' => 'Fped',          // Formación en pedagogía.
    'formacion_tic' => 'Tic',                 // Formación TIC.
    'antecedentes_disciplinarios' => 'Adis',  // Antecedentes disciplinarios.
    'antecedentes_fiscales' => 'Afis',        // Antecedentes fiscales.
    'antecedentes_judiciales' => 'Ajud',      // Antecedentes judiciales.
    'medidas_correctivas' => 'RNMC',          // Registro Nacional de Medidas Correctivas.
    'inhabilidades' => 'Cids',                // Consulta de Inhabilidades, Delitos sexuales.
    'redam' => 'RDAM',                        // Registro de Deudores Alimentarios Morosos.
    'carta_intencion' => 'CI',                // Carta de intención.
    'coverletter' => 'CP',                    // Carta de presentación.
];

// Create temporary file for ZIP.
$tempdir = make_temp_directory('local_jobboard_zip');
$tempzippath = $tempdir . '/' . $zipfilename;

$zip = new ZipArchive();
$result = $zip->open($tempzippath, ZipArchive::CREATE | ZipArchive::OVERWRITE);
if ($result !== true) {
    throw new moodle_exception('error:cannotcreatezip', 'local_jobboard');
}

$filesadded = 0;
$fs = get_file_storage();
$syscontext = context_system::instance();

// Get all files for this application once (more efficient).
$allfiles = $fs->get_area_files(
    $syscontext->id,
    'local_jobboard',
    'application_documents',
    $applicationid,
    'id',
    false
);

// Get document types for naming text files.
$doctypes = $DB->get_records('local_jobboard_doctype', [], '', 'code, name');

// Helper function to wrap text content in HTML.
$wraphtmlcontent = function($title, $content) use ($applicant) {
    $fullname = fullname($applicant);
    $formattedcontent = nl2br(htmlspecialchars($content, ENT_QUOTES, 'UTF-8'));
    return '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 800px; margin: 40px auto; padding: 20px; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .meta { color: #666; margin-bottom: 20px; font-size: 0.9em; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 5px; }
    </style>
</head>
<body>
    <h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>
    <div class="meta">Postulante: ' . htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8') . '</div>
    <div class="content">' . $formattedcontent . '</div>
</body>
</html>';
};

// Add cover letter if present.
if (!empty($application->coverletter)) {
    $coverlettertitle = get_string('coverletter', 'local_jobboard');
    // Use same naming convention: CP_{Lastname}.html
    $coverletterfilename = $fileprefixes['coverletter'] . '_' . $sanitizedlastname . '.html';
    $htmlcontent = $wraphtmlcontent($coverlettertitle, $application->coverletter);
    $zip->addFromString($coverletterfilename, $htmlcontent);
    $filesadded++;
}

foreach ($documents as $doc) {
    // Handle text documents (they don't have physical files in Moodle file storage).
    if ($doc->mimetype === 'text/plain') {
        // For text documents, get content from application data.
        $appdata = json_decode($application->applicationdata ?? '{}', true);
        if (is_array($appdata) && isset($appdata[$doc->documenttype])) {
            $textcontent = $appdata[$doc->documenttype];
            if (is_string($textcontent) && !empty(trim($textcontent))) {
                // Use document type name for the title.
                $typename = isset($doctypes[$doc->documenttype]) ?
                    $doctypes[$doc->documenttype]->name :
                    $doc->documenttype;
                // Use same naming convention: {Prefix}_{Lastname}.html
                $prefix = $fileprefixes[$doc->documenttype] ?? ucfirst(substr($doc->documenttype, 0, 4));
                $textfilename = $prefix . '_' . $sanitizedlastname . '.html';
                $htmlcontent = $wraphtmlcontent($typename, $textcontent);
                $zip->addFromString($textfilename, $htmlcontent);
                $filesadded++;
            }
        }
        continue;
    }

    // Handle physical files - use the stored filename directly (already has correct format).
    foreach ($allfiles as $file) {
        if ($file->get_filepath() === '/' . $doc->documenttype . '/' &&
            $file->get_filename() === $doc->filename) {

            // Get file content.
            $content = $file->get_content();
            if (!empty($content)) {
                // Use original filename directly (already follows {Prefix}_{Lastname}.ext format).
                $zip->addFromString($doc->filename, $content);
                $filesadded++;
            }
            break;
        }
    }
}

$zip->close();

if ($filesadded === 0) {
    @unlink($tempzippath);
    throw new moodle_exception('error:nodocumentstodownload', 'local_jobboard');
}

// Verify ZIP file exists and has content.
if (!file_exists($tempzippath) || filesize($tempzippath) === 0) {
    @unlink($tempzippath);
    throw new moodle_exception('error:cannotcreatezip', 'local_jobboard');
}

// Send the ZIP file to the browser.
header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="' . $zipfilename . '"');
header('Content-Length: ' . filesize($tempzippath));
header('Cache-Control: private, no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

readfile($tempzippath);

// Clean up.
@unlink($tempzippath);

exit;
