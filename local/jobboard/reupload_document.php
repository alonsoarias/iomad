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
 * Reupload a rejected document.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/formslib.php');

use local_jobboard\document;
use local_jobboard\application;

$applicationid = required_param('applicationid', PARAM_INT);
$documenttype = required_param('documenttype', PARAM_ALPHANUMEXT);

require_login();

$context = context_system::instance();

// Get application.
$application = application::get($applicationid);
if (!$application) {
    throw new moodle_exception('applicationnotfound', 'local_jobboard');
}

// Check user owns this application.
if ($application->userid != $USER->id) {
    throw new moodle_exception('noaccess', 'local_jobboard');
}

// Check application status allows re-upload.
if (!in_array($application->status, ['docs_rejected', 'submitted', 'under_review'])) {
    throw new moodle_exception('cannotreupload', 'local_jobboard');
}

// Get the rejected document.
$existingdoc = document::get_for_application_by_type($applicationid, $documenttype);
$validation = $existingdoc ? $existingdoc->get_validation() : null;

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/reupload_document.php', [
    'applicationid' => $applicationid,
    'documenttype' => $documenttype,
]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('reuploaddocument', 'local_jobboard'));
$PAGE->set_heading(get_string('reuploaddocument', 'local_jobboard'));
$PAGE->set_pagelayout('standard');

/**
 * Reupload form.
 */
class reupload_form extends moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;
        $documenttype = $this->_customdata['documenttype'];
        $validation = $this->_customdata['validation'];

        // Show rejection reason if applicable.
        if ($validation && !$validation->isvalid) {
            $reasontext = get_string('rejectreason_' . $validation->rejectreason, 'local_jobboard');
            if (!empty($validation->notes)) {
                $reasontext .= '<br><em>' . format_string($validation->notes) . '</em>';
            }

            $mform->addElement('html', '<div class="alert alert-warning">' .
                '<strong>' . get_string('rejectionreason', 'local_jobboard') . ':</strong><br>' .
                $reasontext . '</div>');
        }

        // Document type info.
        $doctypename = get_string('doctype_' . $documenttype, 'local_jobboard');
        $mform->addElement('static', 'doctypeinfo', get_string('documenttype', 'local_jobboard'), $doctypename);

        // File upload.
        $acceptedtypes = get_config('local_jobboard', 'acceptedfiletypes');
        if (empty($acceptedtypes)) {
            $acceptedtypes = '.pdf,.jpg,.jpeg,.png';
        }
        $maxsize = get_config('local_jobboard', 'maxfilesize');
        if (empty($maxsize)) {
            $maxsize = 10 * 1024 * 1024;
        }

        $fileoptions = [
            'subdirs' => 0,
            'maxbytes' => $maxsize,
            'maxfiles' => 1,
            'accepted_types' => explode(',', $acceptedtypes),
        ];

        $mform->addElement('filemanager', 'newdocument', get_string('newdocument', 'local_jobboard'),
            null, $fileoptions);
        $mform->addRule('newdocument', get_string('required'), 'required', null, 'client');

        // Issue date for certain document types.
        $datedoctypes = ['antecedentes_procuraduria', 'antecedentes_contraloria', 'antecedentes_policia',
            'rnmc', 'sijin', 'certificado_medico'];
        if (in_array($documenttype, $datedoctypes)) {
            $mform->addElement('date_selector', 'issuedate', get_string('documentissuedate', 'local_jobboard'));
            $mform->setDefault('issuedate', time());
        }

        // Hidden fields.
        $mform->addElement('hidden', 'applicationid', $this->_customdata['applicationid']);
        $mform->setType('applicationid', PARAM_INT);
        $mform->addElement('hidden', 'documenttype', $documenttype);
        $mform->setType('documenttype', PARAM_ALPHANUMEXT);

        $this->add_action_buttons(true, get_string('uploaddocument', 'local_jobboard'));
    }

    /**
     * Validation.
     *
     * @param array $data Form data.
     * @param array $files Files.
     * @return array Errors.
     */
    public function validation($data, $files) {
        global $USER;

        $errors = parent::validation($data, $files);

        // Check file was uploaded.
        $draftitemid = $data['newdocument'] ?? 0;
        if ($draftitemid) {
            $fs = get_file_storage();
            $usercontext = \context_user::instance($USER->id);
            $draftfiles = $fs->get_area_files($usercontext->id, 'user', 'draft', $draftitemid, 'id', false);

            if (empty($draftfiles)) {
                $errors['newdocument'] = get_string('required');
            }
        }

        return $errors;
    }
}

// Create form.
$customdata = [
    'applicationid' => $applicationid,
    'documenttype' => $documenttype,
    'validation' => $validation,
];

$mform = new reupload_form(null, $customdata);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $applicationid]));
}

if ($data = $mform->get_data()) {
    // Mark existing document as superseded.
    if ($existingdoc) {
        $existingdoc->supersede();
    }

    // Store new document.
    $newdoc = document::store_from_draft(
        $applicationid,
        $documenttype,
        $data->newdocument,
        $data->issuedate ?? null
    );

    if ($newdoc) {
        // If application was in docs_rejected status, move back to under_review.
        if ($application->status === 'docs_rejected') {
            $application->transition_to('under_review', get_string('documentreuploaded', 'local_jobboard'));
        }

        redirect(
            new moodle_url('/local/jobboard/index.php', ['view' => 'application', 'id' => $applicationid]),
            get_string('documentreuploaded', 'local_jobboard'),
            null,
            \core\output\notification::NOTIFY_SUCCESS
        );
    } else {
        \core\notification::error(get_string('uploadfailed', 'local_jobboard'));
    }
}

// Output page.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('reuploaddocument', 'local_jobboard'));

// Show help text.
echo '<div class="alert alert-info">';
echo get_string('reuploadhelp', 'local_jobboard');
echo '</div>';

$mform->display();

echo $OUTPUT->footer();
