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
 * Document validation page.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');

use local_jobboard\document;
use local_jobboard\application;
use local_jobboard\document_services;

$id = required_param('id', PARAM_INT);
$action = optional_param('action', '', PARAM_ALPHA);

require_login();

$context = context_system::instance();
require_capability('local/jobboard:reviewdocuments', $context);

// Get document.
$document = document::get($id);
if (!$document) {
    throw new moodle_exception('documentnotfound', 'local_jobboard');
}

// Get application.
$application = application::get($document->applicationid);
if (!$application) {
    throw new moodle_exception('applicationnotfound', 'local_jobboard');
}

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/validate_document.php', ['id' => $id]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('validatedocument', 'local_jobboard'));
$PAGE->set_heading(get_string('validatedocument', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('managevacancies', 'local_jobboard'), new moodle_url('/local/jobboard/index.php', ['view' => 'manage']));
$PAGE->navbar->add(get_string('viewapplication', 'local_jobboard'),
    new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id]));
$PAGE->navbar->add(get_string('validatedocument', 'local_jobboard'));

// Handle form submission.
if ($action === 'validate' || $action === 'reject') {
    require_sesskey();

    $isvalid = ($action === 'validate');
    $rejectreason = optional_param('rejectreason', '', PARAM_TEXT);
    $notes = optional_param('notes', '', PARAM_TEXT);

    $document->validate($isvalid, $rejectreason, $notes);

    redirect(
        new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id]),
        get_string($isvalid ? 'documentvalidated' : 'documentrejected', 'local_jobboard'),
        null,
        \core\output\notification::NOTIFY_SUCCESS
    );
}

// Get applicant.
$applicant = $DB->get_record('user', ['id' => $application->userid]);

echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('validatedocument', 'local_jobboard'));

// Document information.
echo '<div class="card mb-3">';
echo '<div class="card-header"><h5>' . get_string('documentinfo', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';
echo '<p><strong>' . get_string('documenttype', 'local_jobboard') . ':</strong> ' .
    get_string('doctype_' . $document->documenttype, 'local_jobboard') . '</p>';
echo '<p><strong>' . get_string('filename', 'local_jobboard') . ':</strong> ' .
    format_string($document->filename) . '</p>';
echo '<p><strong>' . get_string('uploaded', 'local_jobboard') . ':</strong> ' .
    userdate($document->timecreated, get_string('strftimedatetime', 'langconfig')) . '</p>';
if (!empty($document->issuedate)) {
    echo '<p><strong>' . get_string('documentissuedate', 'local_jobboard') . ':</strong> ' .
        userdate($document->issuedate, get_string('strftimedate', 'langconfig')) . '</p>';
}

// Document preview and download.
$downloadurl = $document->get_download_url();
$previewinfo = document_services::get_preview_info($document);

if ($downloadurl) {
    // Inline preview button.
    echo '<div class="mb-3">';

    // Use converted URL if available, otherwise original.
    $previewurl = $previewinfo['url'] ?: $downloadurl;
    $previewmime = $previewinfo['mimetype'];

    echo '<button type="button" class="btn btn-primary mr-2" ' .
        'data-preview-url="' . $previewurl . '" ' .
        'data-preview-filename="' . s($document->filename) . '" ' .
        'data-preview-mimetype="' . s($previewmime) . '">' .
        '<i class="fa fa-eye mr-1"></i>' . get_string('previewdocument', 'local_jobboard') . '</button>';
    echo '<a href="' . $downloadurl . '" class="btn btn-outline-secondary" target="_blank">' .
        '<i class="fa fa-download mr-1"></i>' . get_string('download') . '</a>';

    // Show conversion status if applicable.
    if ($previewinfo['can_convert']) {
        echo '<span class="ml-3 badge badge-' .
            ($previewinfo['status'] === 'ready' ? 'success' :
            ($previewinfo['status'] === 'converting' ? 'info' :
            ($previewinfo['status'] === 'failed' ? 'danger' : 'secondary'))) . '" ' .
            'id="conversion-status" data-documentid="' . $document->id . '">' .
            document_services::get_status_message($previewinfo['status']) . '</span>';
    }
    echo '</div>';

    // Inline preview container.
    $canpreview = ($previewinfo['status'] === 'ready' && $previewinfo['url']);
    $directpreview = in_array($document->mimetype, document_services::DIRECT_PREVIEW_MIMETYPES);

    if ($canpreview || $directpreview) {
        echo '<div class="document-inline-preview mb-3">';
        echo '<div class="card">';
        echo '<div class="card-header d-flex justify-content-between align-items-center">';
        echo '<span>' . get_string('documentpreview', 'local_jobboard') . '</span>';
        echo '<button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-preview">' .
            get_string('togglepreview', 'local_jobboard') . '</button>';
        echo '</div>';
        echo '<div class="card-body preview-content" id="preview-container" style="max-height: 500px; overflow: auto;">';

        if ($previewmime === 'application/pdf' && $previewinfo['url']) {
            // Use iframe for PDF preview (original or converted).
            echo '<iframe src="' . $previewinfo['url'] . '#toolbar=0&navpanes=0" ' .
                'style="width: 100%; height: 450px; border: none;" ' .
                'title="' . get_string('documentpreview', 'local_jobboard') . '"></iframe>';
        } else if (strpos($document->mimetype, 'image/') === 0) {
            // Direct image display.
            echo '<img src="' . $downloadurl . '" class="img-fluid" ' .
                'alt="' . s($document->filename) . '" style="max-width: 100%;">';
        }

        echo '</div>';
        echo '</div>';
        echo '</div>';
    } else if ($previewinfo['status'] === 'converting') {
        // Show converting message with polling.
        echo '<div class="document-inline-preview mb-3">';
        echo '<div class="card">';
        echo '<div class="card-header">' . get_string('documentpreview', 'local_jobboard') . '</div>';
        echo '<div class="card-body text-center" id="preview-container">';
        echo '<div class="conversion-progress">';
        echo '<i class="fa fa-spinner fa-spin fa-3x mb-3"></i>';
        echo '<p>' . get_string('conversioninprogress', 'local_jobboard') . '</p>';
        echo '<p class="text-muted">' . get_string('conversionwait', 'local_jobboard') . '</p>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    } else if ($previewinfo['can_convert'] && $previewinfo['status'] !== 'ready') {
        // Show message that conversion is possible but not yet started or failed.
        echo '<div class="document-inline-preview mb-3">';
        echo '<div class="card">';
        echo '<div class="card-header">' . get_string('documentpreview', 'local_jobboard') . '</div>';
        echo '<div class="card-body text-center" id="preview-container">';
        if ($previewinfo['status'] === 'failed') {
            echo '<div class="alert alert-warning">';
            echo '<i class="fa fa-exclamation-triangle mr-2"></i>';
            echo get_string('conversionfailed', 'local_jobboard');
            echo '</div>';
        } else {
            echo '<p class="text-muted">' . get_string('previewunavailable', 'local_jobboard') . '</p>';
        }
        echo '<p><a href="' . $downloadurl . '" class="btn btn-outline-primary" target="_blank">' .
            '<i class="fa fa-download mr-1"></i>' . get_string('downloadtoview', 'local_jobboard') . '</a></p>';
        echo '</div>';
        echo '</div>';
        echo '</div>';
    }
}
echo '</div>';
echo '</div>';

// Initialize document preview module.
$PAGE->requires->js_call_amd('local_jobboard/document_preview', 'init');

// Applicant information.
echo '<div class="card mb-3">';
echo '<div class="card-header"><h5>' . get_string('applicantinfo', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';
echo '<p><strong>' . get_string('name') . ':</strong> ' . fullname($applicant) . '</p>';
echo '<p><strong>' . get_string('email') . ':</strong> ' . $applicant->email . '</p>';
if (!empty($applicant->idnumber)) {
    echo '<p><strong>' . get_string('idnumber') . ':</strong> ' . format_string($applicant->idnumber) . '</p>';
}
echo '</div>';
echo '</div>';

// Validation checklist based on document type.
echo '<div class="card mb-3">';
echo '<div class="card-header"><h5>' . get_string('validationchecklist', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';

$checklist = get_validation_checklist($document->documenttype);
if (!empty($checklist)) {
    echo '<ul class="list-group mb-3">';
    foreach ($checklist as $item) {
        echo '<li class="list-group-item">';
        echo '<div class="form-check">';
        echo '<input class="form-check-input checklist-item" type="checkbox" id="check_' . md5($item) . '">';
        echo '<label class="form-check-label" for="check_' . md5($item) . '">' . $item . '</label>';
        echo '</div>';
        echo '</li>';
    }
    echo '</ul>';
}
echo '</div>';
echo '</div>';

// Validation form.
echo '<div class="card mb-3">';
echo '<div class="card-header"><h5>' . get_string('validationdecision', 'local_jobboard') . '</h5></div>';
echo '<div class="card-body">';

echo '<div class="row">';

// Approve form.
echo '<div class="col-md-6">';
echo '<form method="post" action="' . new moodle_url('/local/jobboard/validate_document.php',
    ['id' => $id, 'action' => 'validate']) . '">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
echo '<div class="form-group">';
echo '<label for="notes_approve">' . get_string('notes', 'local_jobboard') . '</label>';
echo '<textarea name="notes" id="notes_approve" class="form-control" rows="3"></textarea>';
echo '</div>';
echo '<button type="submit" class="btn btn-success btn-lg btn-block">';
echo '<i class="fa fa-check"></i> ' . get_string('approvedocument', 'local_jobboard');
echo '</button>';
echo '</form>';
echo '</div>';

// Reject form.
echo '<div class="col-md-6">';
echo '<form method="post" action="' . new moodle_url('/local/jobboard/validate_document.php',
    ['id' => $id, 'action' => 'reject']) . '">';
echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
echo '<div class="form-group">';
echo '<label for="rejectreason">' . get_string('rejectreason', 'local_jobboard') . ' <span class="text-danger">*</span></label>';
echo '<select name="rejectreason" id="rejectreason" class="form-control" required>';
echo '<option value="">' . get_string('selectreason', 'local_jobboard') . '</option>';
$reasons = [
    'illegible' => get_string('rejectreason_illegible', 'local_jobboard'),
    'expired' => get_string('rejectreason_expired', 'local_jobboard'),
    'incomplete' => get_string('rejectreason_incomplete', 'local_jobboard'),
    'wrongtype' => get_string('rejectreason_wrongtype', 'local_jobboard'),
    'mismatch' => get_string('rejectreason_mismatch', 'local_jobboard'),
    'other' => get_string('other'),
];
foreach ($reasons as $code => $label) {
    echo '<option value="' . $code . '">' . $label . '</option>';
}
echo '</select>';
echo '</div>';
echo '<div class="form-group">';
echo '<label for="notes_reject">' . get_string('additionalnotes', 'local_jobboard') . '</label>';
echo '<textarea name="notes" id="notes_reject" class="form-control" rows="3"></textarea>';
echo '</div>';
echo '<button type="submit" class="btn btn-danger btn-lg btn-block">';
echo '<i class="fa fa-times"></i> ' . get_string('rejectdocument', 'local_jobboard');
echo '</button>';
echo '</form>';
echo '</div>';

echo '</div>'; // row.
echo '</div>';
echo '</div>';

// Back button.
echo '<a href="' . new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $application->id]) .
    '" class="btn btn-secondary">' . get_string('back') . '</a>';

echo $OUTPUT->footer();

/**
 * Get validation checklist for a document type.
 *
 * @param string $doctype Document type code.
 * @return array Checklist items.
 */
function get_validation_checklist(string $doctype): array {
    $common = [
        get_string('checklist_legible', 'local_jobboard'),
        get_string('checklist_complete', 'local_jobboard'),
        get_string('checklist_namematch', 'local_jobboard'),
    ];

    $specific = [];
    switch ($doctype) {
        case 'cedula':
            $specific = [
                get_string('checklist_cedula_number', 'local_jobboard'),
                get_string('checklist_cedula_photo', 'local_jobboard'),
            ];
            break;
        case 'antecedentes_procuraduria':
        case 'antecedentes_contraloria':
        case 'antecedentes_policia':
        case 'rnmc':
        case 'sijin':
            $specific = [
                get_string('checklist_background_date', 'local_jobboard'),
                get_string('checklist_background_status', 'local_jobboard'),
            ];
            break;
        case 'titulo_pregrado':
        case 'titulo_postgrado':
        case 'titulo_especializacion':
        case 'titulo_maestria':
        case 'titulo_doctorado':
            $specific = [
                get_string('checklist_title_institution', 'local_jobboard'),
                get_string('checklist_title_date', 'local_jobboard'),
                get_string('checklist_title_program', 'local_jobboard'),
            ];
            break;
        case 'acta_grado':
            $specific = [
                get_string('checklist_acta_number', 'local_jobboard'),
                get_string('checklist_acta_date', 'local_jobboard'),
            ];
            break;
        case 'tarjeta_profesional':
            $specific = [
                get_string('checklist_tarjeta_number', 'local_jobboard'),
                get_string('checklist_tarjeta_profession', 'local_jobboard'),
            ];
            break;
        case 'rut':
            $specific = [
                get_string('checklist_rut_nit', 'local_jobboard'),
                get_string('checklist_rut_updated', 'local_jobboard'),
            ];
            break;
        case 'eps':
            $specific = [
                get_string('checklist_eps_active', 'local_jobboard'),
                get_string('checklist_eps_entity', 'local_jobboard'),
            ];
            break;
        case 'pension':
            $specific = [
                get_string('checklist_pension_fund', 'local_jobboard'),
                get_string('checklist_pension_active', 'local_jobboard'),
            ];
            break;
        case 'certificado_medico':
            $specific = [
                get_string('checklist_medical_date', 'local_jobboard'),
                get_string('checklist_medical_aptitude', 'local_jobboard'),
            ];
            break;
        case 'libreta_militar':
            $specific = [
                get_string('checklist_military_class', 'local_jobboard'),
                get_string('checklist_military_number', 'local_jobboard'),
            ];
            break;
    }

    return array_merge($common, $specific);
}
