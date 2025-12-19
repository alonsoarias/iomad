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
 * Download a text document as HTML file.
 *
 * Text documents (like carta_intencion) are stored in applicationdata,
 * not as physical files. This script generates an HTML file on the fly.
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

require_login();

// Get parameters.
$documentid = required_param('documentid', PARAM_INT);
$forcedownload = optional_param('forcedownload', 1, PARAM_BOOL);

// Get document record.
$document = $DB->get_record('local_jobboard_document', ['id' => $documentid], '*', MUST_EXIST);

// Check if this is a text document.
if (!in_array($document->mimetype, ['text/plain', 'text/html'])) {
    throw new moodle_exception('error:nottextdocument', 'local_jobboard');
}

// Get application.
$application = $DB->get_record('local_jobboard_application', ['id' => $document->applicationid], '*', MUST_EXIST);

// Check capability - either owner or reviewer.
$context = context_system::instance();
$isowner = ($application->userid == $USER->id);
$isreviewer = has_capability('local/jobboard:reviewdocuments', $context);

if (!$isowner && !$isreviewer) {
    throw new moodle_exception('error:nopermission', 'local_jobboard');
}

// Get applicant info for the HTML wrapper.
$applicant = $DB->get_record('user', ['id' => $application->userid],
    'id, firstname, lastname, idnumber, firstnamephonetic, lastnamephonetic, middlename, alternatename');

if (!$applicant) {
    throw new moodle_exception('error:invaliduser', 'local_jobboard');
}

// Get text content from applicationdata.
$textcontent = '';
$appdata = json_decode($application->applicationdata ?? '{}', true);
$textdocuments = $appdata['text_documents'] ?? [];

if (!empty($textdocuments[$document->documenttype]['value'])) {
    $textcontent = $textdocuments[$document->documenttype]['value'];
} else if ($document->documenttype === 'carta_intencion' && !empty($application->coverletter)) {
    // Fallback to coverletter field.
    $textcontent = $application->coverletter;
}

if (empty($textcontent)) {
    throw new moodle_exception('error:nodocumentcontent', 'local_jobboard');
}

// Get document type name.
$doctype = $DB->get_record('local_jobboard_doctype', ['code' => $document->documenttype]);
$typename = $doctype ? $doctype->name : $document->documenttype;

// Check if content is HTML (from editor) or plain text.
$ishtml = !empty($textdocuments[$document->documenttype]['type']) &&
          $textdocuments[$document->documenttype]['type'] === 'editor';

// Format content.
if ($ishtml) {
    $formattedcontent = $textcontent;
} else {
    $formattedcontent = nl2br(htmlspecialchars($textcontent, ENT_QUOTES, 'UTF-8'));
}

// Generate HTML.
$fullname = fullname($applicant);
$html = '<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>' . htmlspecialchars($typename, ENT_QUOTES, 'UTF-8') . '</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 800px; margin: 40px auto; padding: 20px; }
        h1 { color: #333; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .meta { color: #666; margin-bottom: 20px; font-size: 0.9em; }
        .content { background: #f9f9f9; padding: 20px; border-radius: 5px; }
        .content p { margin: 0 0 1em 0; }
    </style>
</head>
<body>
    <h1>' . htmlspecialchars($typename, ENT_QUOTES, 'UTF-8') . '</h1>
    <div class="meta">Postulante: ' . htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8') . '</div>
    <div class="content">' . $formattedcontent . '</div>
</body>
</html>';

// Generate filename.
$sanitizedlastname = \local_jobboard\document::sanitize_filename_part($applicant->lastname);
$fileprefixes = [
    'carta_intencion' => 'CI',
    'coverletter' => 'CP',
];
$prefix = $fileprefixes[$document->documenttype] ?? ucfirst(substr($document->documenttype, 0, 4));
$filename = $prefix . '_' . $sanitizedlastname . '.html';

// Send headers.
if ($forcedownload) {
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
} else {
    header('Content-Type: text/html; charset=utf-8');
    header('Content-Disposition: inline; filename="' . $filename . '"');
}
header('Content-Length: ' . strlen($html));
header('Cache-Control: private, no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo $html;
exit;
