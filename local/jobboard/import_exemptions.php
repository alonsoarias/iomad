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
 * CSV Import for ISER exemptions.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../config.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once($CFG->libdir . '/formslib.php');

use local_jobboard\exemption;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:manageexemptions', $context);

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/import_exemptions.php'));
$PAGE->set_context($context);
$PAGE->set_title(get_string('importexemptions', 'local_jobboard'));
$PAGE->set_heading(get_string('importexemptions', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Navbar.
$PAGE->navbar->add(get_string('pluginname', 'local_jobboard'), new moodle_url('/local/jobboard/'));
$PAGE->navbar->add(get_string('manageexemptions', 'local_jobboard'),
    new moodle_url('/local/jobboard/manage_exemptions.php'));
$PAGE->navbar->add(get_string('importexemptions', 'local_jobboard'));

/**
 * CSV import form.
 */
class import_exemptions_form extends moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        $mform = $this->_form;

        $mform->addElement('header', 'importheader', get_string('importexemptions', 'local_jobboard'));

        // File upload.
        $mform->addElement('filepicker', 'csvfile', get_string('csvfile', 'local_jobboard'),
            null, ['accepted_types' => '.csv']);
        $mform->addRule('csvfile', get_string('required'), 'required', null, 'client');

        // Delimiter.
        $delimiters = \csv_import_reader::get_delimiter_list();
        $mform->addElement('select', 'delimiter', get_string('csvdelimiter', 'local_jobboard'), $delimiters);
        $mform->setDefault('delimiter', 'comma');

        // Encoding.
        $encodings = core_text::get_encodings();
        $mform->addElement('select', 'encoding', get_string('encoding', 'local_jobboard'), $encodings);
        $mform->setDefault('encoding', 'UTF-8');

        // Default exemption type.
        $types = [
            'historico_iser' => get_string('exemptiontype_historico_iser', 'local_jobboard'),
            'documentos_recientes' => get_string('exemptiontype_documentos_recientes', 'local_jobboard'),
            'traslado_interno' => get_string('exemptiontype_traslado_interno', 'local_jobboard'),
            'recontratacion' => get_string('exemptiontype_recontratacion', 'local_jobboard'),
        ];
        $mform->addElement('select', 'defaulttype', get_string('defaultexemptiontype', 'local_jobboard'), $types);
        $mform->setDefault('defaulttype', 'historico_iser');
        $mform->addHelpButton('defaulttype', 'defaultexemptiontype', 'local_jobboard');

        // Validity period.
        $mform->addElement('date_selector', 'validfrom', get_string('defaultvalidfrom', 'local_jobboard'));
        $mform->setDefault('validfrom', time());

        $mform->addElement('date_selector', 'validuntil', get_string('defaultvaliduntil', 'local_jobboard'),
            ['optional' => true]);

        // Preview only checkbox.
        $mform->addElement('advcheckbox', 'previewonly', get_string('previewonly', 'local_jobboard'));
        $mform->setDefault('previewonly', 1);

        // Submit.
        $this->add_action_buttons(true, get_string('import'));
    }
}

$mform = new import_exemptions_form();

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/jobboard/manage_exemptions.php'));
} else if ($data = $mform->get_data()) {
    // Process import.
    $content = $mform->get_file_content('csvfile');

    $iid = \csv_import_reader::get_new_iid('local_jobboard_exemptions');
    $csvreader = new \csv_import_reader($iid, 'local_jobboard_exemptions');

    $delimiter = $data->delimiter;
    $encoding = $data->encoding;

    $readcount = $csvreader->load_csv_content($content, $encoding, $delimiter);

    if ($readcount === false) {
        throw new moodle_exception('csvreaderror', 'local_jobboard');
    }

    $columns = $csvreader->get_columns();

    // Expected columns: email (or username or idnumber), exemptiontype (optional), exempteddocs (optional)
    $emailindex = array_search('email', array_map('strtolower', $columns));
    $usernameindex = array_search('username', array_map('strtolower', $columns));
    $idnumberindex = array_search('idnumber', array_map('strtolower', $columns));
    $typeindex = array_search('exemptiontype', array_map('strtolower', $columns));
    $docsindex = array_search('exempteddocs', array_map('strtolower', $columns));
    $refindex = array_search('documentref', array_map('strtolower', $columns));
    $notesindex = array_search('notes', array_map('strtolower', $columns));

    // We need at least one user identifier.
    if ($emailindex === false && $usernameindex === false && $idnumberindex === false) {
        $csvreader->close();
        $csvreader->cleanup();
        throw new moodle_exception('csvmissingcolumn', 'local_jobboard', '', 'email/username/idnumber');
    }

    // Default exempted docs (all common ones).
    $defaultdocs = ['cedula', 'rut', 'eps', 'pension', 'cuenta_bancaria', 'libreta_militar',
        'titulo_pregrado', 'tarjeta_profesional', 'sigep', 'antecedentes_procuraduria',
        'antecedentes_contraloria', 'antecedentes_policia', 'rnmc'];

    $csvreader->init();

    $results = [
        'success' => 0,
        'skipped' => 0,
        'errors' => [],
        'preview' => [],
    ];

    $rownum = 1;

    while ($row = $csvreader->next()) {
        $rownum++;

        // Find user.
        $user = null;
        if ($emailindex !== false && !empty($row[$emailindex])) {
            $user = $DB->get_record('user', ['email' => trim($row[$emailindex]), 'deleted' => 0]);
        }
        if (!$user && $usernameindex !== false && !empty($row[$usernameindex])) {
            $user = $DB->get_record('user', ['username' => trim($row[$usernameindex]), 'deleted' => 0]);
        }
        if (!$user && $idnumberindex !== false && !empty($row[$idnumberindex])) {
            $user = $DB->get_record('user', ['idnumber' => trim($row[$idnumberindex]), 'deleted' => 0]);
        }

        if (!$user) {
            $results['errors'][] = get_string('importerror_usernotfound', 'local_jobboard', $rownum);
            continue;
        }

        // Check if user already has active exemption.
        $existing = exemption::get_for_user($user->id);
        if ($existing) {
            $results['skipped']++;
            $results['errors'][] = get_string('importerror_alreadyexempt', 'local_jobboard',
                ['row' => $rownum, 'user' => fullname($user)]);
            continue;
        }

        // Determine exemption type.
        $type = $data->defaulttype;
        if ($typeindex !== false && !empty($row[$typeindex])) {
            $providedtype = strtolower(trim($row[$typeindex]));
            if (in_array($providedtype, ['historico_iser', 'documentos_recientes', 'traslado_interno', 'recontratacion'])) {
                $type = $providedtype;
            }
        }

        // Determine exempted docs.
        $docs = $defaultdocs;
        if ($docsindex !== false && !empty($row[$docsindex])) {
            $docs = array_map('trim', explode('|', $row[$docsindex]));
        }

        // Document reference.
        $docref = '';
        if ($refindex !== false && !empty($row[$refindex])) {
            $docref = trim($row[$refindex]);
        }

        // Notes.
        $notes = get_string('importednote', 'local_jobboard', userdate(time()));
        if ($notesindex !== false && !empty($row[$notesindex])) {
            $notes = trim($row[$notesindex]);
        }

        if ($data->previewonly) {
            // Preview mode.
            $results['preview'][] = [
                'row' => $rownum,
                'user' => fullname($user),
                'email' => $user->email,
                'type' => get_string('exemptiontype_' . $type, 'local_jobboard'),
                'docs' => count($docs),
            ];
        } else {
            // Actually create exemption.
            $result = exemption::create(
                $user->id,
                $type,
                $docs,
                $data->validfrom,
                $data->validuntil ?: null,
                $docref,
                $notes
            );

            if ($result) {
                $results['success']++;
            } else {
                $results['errors'][] = get_string('importerror_createfailed', 'local_jobboard',
                    ['row' => $rownum, 'user' => fullname($user)]);
            }
        }
    }

    $csvreader->close();
    $csvreader->cleanup();

    // Show results.
    echo $OUTPUT->header();
    echo $OUTPUT->heading(get_string('importresults', 'local_jobboard'));

    if ($data->previewonly) {
        echo $OUTPUT->notification(get_string('previewmodenotice', 'local_jobboard'), 'info');

        if (!empty($results['preview'])) {
            $table = new html_table();
            $table->head = [
                get_string('row', 'local_jobboard'),
                get_string('user'),
                get_string('email'),
                get_string('exemptiontype', 'local_jobboard'),
                get_string('numdocs', 'local_jobboard'),
            ];
            $table->attributes['class'] = 'table table-striped';

            foreach ($results['preview'] as $preview) {
                $table->data[] = [
                    $preview['row'],
                    $preview['user'],
                    $preview['email'],
                    $preview['type'],
                    $preview['docs'],
                ];
            }

            echo html_writer::table($table);
            echo '<p class="text-success font-weight-bold">' .
                get_string('previewtotal', 'local_jobboard', count($results['preview'])) . '</p>';
        }

        // Button to import for real.
        echo '<form method="post" action="">';
        echo '<input type="hidden" name="sesskey" value="' . sesskey() . '">';
        echo '<p>' . get_string('previewconfirm', 'local_jobboard') . '</p>';
        echo '</form>';

    } else {
        // Show actual results.
        echo '<div class="alert alert-success">';
        echo '<strong>' . get_string('importcomplete', 'local_jobboard') . '</strong><br>';
        echo get_string('importedsuccess', 'local_jobboard', $results['success']) . '<br>';
        if ($results['skipped'] > 0) {
            echo get_string('importedskipped', 'local_jobboard', $results['skipped']);
        }
        echo '</div>';
    }

    // Show errors.
    if (!empty($results['errors'])) {
        echo '<div class="alert alert-warning">';
        echo '<strong>' . get_string('importerrors', 'local_jobboard') . '</strong><br>';
        echo '<ul>';
        foreach (array_slice($results['errors'], 0, 20) as $error) {
            echo '<li>' . $error . '</li>';
        }
        if (count($results['errors']) > 20) {
            echo '<li>... ' . get_string('andmore', 'local_jobboard',
                count($results['errors']) - 20) . '</li>';
        }
        echo '</ul>';
        echo '</div>';
    }

    echo '<a href="' . new moodle_url('/local/jobboard/manage_exemptions.php') .
        '" class="btn btn-primary">' . get_string('continue') . '</a>';

    echo $OUTPUT->footer();
    exit;
}

// Show form.
echo $OUTPUT->header();

echo $OUTPUT->heading(get_string('importexemptions', 'local_jobboard'));

// Instructions.
echo '<div class="card mb-4">';
echo '<div class="card-header">';
echo '<h5>' . get_string('importinstructions', 'local_jobboard') . '</h5>';
echo '</div>';
echo '<div class="card-body">';
echo '<p>' . get_string('importinstructionstext', 'local_jobboard') . '</p>';
echo '<h6>' . get_string('requiredcolumns', 'local_jobboard') . '</h6>';
echo '<ul>';
echo '<li><code>email</code> ' . get_string('or') . ' <code>username</code> ' .
    get_string('or') . ' <code>idnumber</code> - ' . get_string('useridentifier', 'local_jobboard') . '</li>';
echo '</ul>';
echo '<h6>' . get_string('optionalcolumns', 'local_jobboard') . '</h6>';
echo '<ul>';
echo '<li><code>exemptiontype</code> - ' . get_string('exemptiontype_desc', 'local_jobboard') . '</li>';
echo '<li><code>exempteddocs</code> - ' . get_string('exempteddocs_desc', 'local_jobboard') . '</li>';
echo '<li><code>documentref</code> - ' . get_string('documentref_desc', 'local_jobboard') . '</li>';
echo '<li><code>notes</code> - ' . get_string('notes_desc', 'local_jobboard') . '</li>';
echo '</ul>';
echo '<h6>' . get_string('samplecsv', 'local_jobboard') . '</h6>';
echo '<pre class="bg-light p-2">email,exemptiontype,exempteddocs
juan.perez@example.com,historico_iser,cedula|rut|eps
maria.garcia@example.com,historico_iser,
pedro.lopez@example.com,documentos_recientes,antecedentes_procuraduria|antecedentes_contraloria</pre>';
echo '</div>';
echo '</div>';

$mform->display();

echo $OUTPUT->footer();
