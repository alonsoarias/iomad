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
 * CSV Import for vacancies.
 *
 * Allows importing vacancies from a CSV file to a convocatoria.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/csvlib.class.php');
require_once($CFG->libdir . '/formslib.php');
require_once(__DIR__ . '/../lib.php');

use local_jobboard\output\ui_helper;

require_login();

$context = context_system::instance();
require_capability('local/jobboard:createvacancy', $context);

// Parameters.
$convocatoriaid = optional_param('convocatoriaid', 0, PARAM_INT);
$downloadtemplate = optional_param('downloadtemplate', 0, PARAM_BOOL);

// Download CSV template.
if ($downloadtemplate) {
    $filename = 'vacancies_template.csv';
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');

    $output = fopen('php://output', 'w');

    // BOM for UTF-8.
    fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

    // Header row.
    fputcsv($output, ['code', 'contracttype', 'program', 'profile', 'courses', 'location', 'modality', 'faculty']);

    // Example rows.
    fputcsv($output, [
        'FCAS-001',
        'OCASIONAL TIEMPO COMPLETO',
        'TECNOLOGÍA EN GESTIÓN COMUNITARIA',
        'PROFESIONAL EN TRABAJO SOCIAL',
        'SISTEMATIZACIÓN DE EXPERIENCIAS|SUJETO Y FAMILIA|DIRECCIÓN DE TRABAJO DE GRADO',
        'PAMPLONA',
        'PRESENCIAL',
        'FCAS'
    ]);
    fputcsv($output, [
        'FCAS-002',
        'CATEDRA',
        'TECNOLOGÍA EN GESTIÓN EMPRESARIAL',
        'ADMINISTRADOR DE EMPRESAS CON POSGRADO EN ÁREAS AFINES',
        'EMPRENDIMIENTO|ADMINISTRACIÓN GENERAL',
        'CUCUTA',
        'A DISTANCIA',
        'FCAS'
    ]);
    fputcsv($output, [
        'FII-001',
        'OCASIONAL TIEMPO COMPLETO',
        'TECNOLOGÍA EN GESTIÓN INDUSTRIAL',
        'INGENIERO INDUSTRIAL',
        'ERGONOMÍA|GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO',
        'PAMPLONA',
        'PRESENCIAL',
        'FII'
    ]);

    fclose($output);
    exit;
}

// Set up page.
$PAGE->set_url(new moodle_url('/local/jobboard/admin/import_vacancies.php', ['convocatoriaid' => $convocatoriaid]));
$PAGE->set_context($context);
$PAGE->set_title(get_string('importvacancies', 'local_jobboard'));
$PAGE->set_heading(get_string('importvacancies', 'local_jobboard'));
$PAGE->set_pagelayout('admin');

// Get convocatorias for dropdown.
$convocatorias = $DB->get_records_menu('local_jobboard_convocatoria', null, 'name ASC', 'id, name');

// Check if IOMAD is available for company selection.
$isiomad = local_jobboard_is_iomad_installed();

/**
 * CSV import form for vacancies.
 */
class import_vacancies_form extends moodleform {
    /**
     * Form definition.
     */
    protected function definition() {
        global $DB;

        $mform = $this->_form;
        $convocatorias = $this->_customdata['convocatorias'] ?? [];
        $convocatoriaid = $this->_customdata['convocatoriaid'] ?? 0;
        $isiomad = $this->_customdata['isiomad'] ?? false;

        $mform->addElement('header', 'importheader', get_string('importvacancies', 'local_jobboard'));

        // Help text.
        $mform->addElement('static', 'helptext', '',
            html_writer::div(
                html_writer::tag('strong', get_string('importvacancies_help', 'local_jobboard')) .
                html_writer::tag('br', '') .
                html_writer::link(
                    new moodle_url('/local/jobboard/admin/import_vacancies.php', ['downloadtemplate' => 1]),
                    html_writer::tag('i', '', ['class' => 'fa fa-download mr-1']) .
                    get_string('downloadcsvtemplate', 'local_jobboard'),
                    ['class' => 'btn btn-outline-secondary btn-sm mt-2']
                ),
                'alert alert-info'
            )
        );

        // Convocatoria selection.
        $convoptions = [0 => get_string('selectconvocatoria', 'local_jobboard')] + $convocatorias;
        $mform->addElement('select', 'convocatoriaid', get_string('convocatoria', 'local_jobboard'), $convoptions);
        $mform->addRule('convocatoriaid', get_string('required'), 'required', null, 'client');
        $mform->addRule('convocatoriaid', get_string('required'), 'nonzero', null, 'client');
        if ($convocatoriaid) {
            $mform->setDefault('convocatoriaid', $convocatoriaid);
        }

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

        // IOMAD options.
        if ($isiomad) {
            $mform->addElement('header', 'iomadheader', get_string('iomadoptions', 'local_jobboard'));

            // Auto-create companies.
            $mform->addElement('advcheckbox', 'createcompanies', get_string('createcompanies', 'local_jobboard'));
            $mform->setDefault('createcompanies', 0);
            $mform->addHelpButton('createcompanies', 'createcompanies', 'local_jobboard');
        }

        // Import options.
        $mform->addElement('header', 'optionsheader', get_string('importoptions', 'local_jobboard'));

        // Default status.
        $statuses = [
            'draft' => get_string('vacancy_status_draft', 'local_jobboard'),
            'published' => get_string('vacancy_status_published', 'local_jobboard'),
        ];
        $mform->addElement('select', 'status', get_string('defaultstatus', 'local_jobboard'), $statuses);
        $mform->setDefault('status', 'draft');

        // Update existing.
        $mform->addElement('advcheckbox', 'updateexisting', get_string('updateexisting', 'local_jobboard'));
        $mform->setDefault('updateexisting', 0);
        $mform->addHelpButton('updateexisting', 'updateexisting', 'local_jobboard');

        // Preview only checkbox.
        $mform->addElement('advcheckbox', 'previewonly', get_string('previewonly', 'local_jobboard'));
        $mform->setDefault('previewonly', 1);

        // Submit.
        $this->add_action_buttons(true, get_string('import'));
    }
}

// Location mapping (same as CLI).
$locationmap = [
    'PAMPLONA' => ['name' => 'ISER Sede Pamplona', 'city' => 'Pamplona'],
    'CUCUTA' => ['name' => 'ISER Centro Tutorial San José de Cúcuta', 'city' => 'Cúcuta'],
    'TIBU' => ['name' => 'ISER Centro Tutorial Tibú', 'city' => 'Tibú'],
    'SANVICENTE' => ['name' => 'ISER Centro Tutorial San Vicente del Chucurí', 'city' => 'San Vicente del Chucurí'],
    'ELTARRA' => ['name' => 'ISER Centro Tutorial El Tarra', 'city' => 'El Tarra'],
    'OCANA' => ['name' => 'ISER Centro Tutorial Ocaña', 'city' => 'Ocaña'],
    'PUEBLOBELLO' => ['name' => 'ISER Centro Tutorial Pueblo Bello', 'city' => 'Pueblo Bello'],
    'SANPABLO' => ['name' => 'ISER Centro Tutorial San Pablo', 'city' => 'San Pablo'],
    'SANTAROSA' => ['name' => 'ISER Centro Tutorial Santa Rosa del Sur', 'city' => 'Santa Rosa del Sur'],
    'TAME' => ['name' => 'ISER Centro Tutorial Tame', 'city' => 'Tame'],
    'FUNDACION' => ['name' => 'ISER Centro Tutorial Fundación', 'city' => 'Fundación'],
    'CIMITARRA' => ['name' => 'ISER Centro Tutorial Cimitarra', 'city' => 'Cimitarra'],
    'SALAZAR' => ['name' => 'ISER Centro Tutorial Salazar', 'city' => 'Salazar'],
    'TOLEDO' => ['name' => 'ISER Centro Tutorial Toledo', 'city' => 'Toledo'],
];

/**
 * Normalize location key.
 */
function normalize_location($location) {
    $location = strtoupper(trim($location));
    $location = preg_replace('/^(?:ISER\s+)?(?:SEDE\s+|CENTRO\s+TUTORIAL\s+)?/i', '', $location);
    $location = trim($location);

    $map = [
        'CÚCUTA' => 'CUCUTA', 'CUCUTA' => 'CUCUTA', 'SAN JOSÉ DE CÚCUTA' => 'CUCUTA',
        'TIBÚ' => 'TIBU', 'TIBU' => 'TIBU',
        'SAN VICENTE' => 'SANVICENTE', 'SANVICENTE' => 'SANVICENTE',
        'EL TARRA' => 'ELTARRA', 'ELTARRA' => 'ELTARRA', 'TARRA' => 'ELTARRA',
        'OCAÑA' => 'OCANA', 'OCANA' => 'OCANA',
        'PUEBLO BELLO' => 'PUEBLOBELLO', 'PUEBLOBELLO' => 'PUEBLOBELLO',
        'SAN PABLO' => 'SANPABLO', 'SANPABLO' => 'SANPABLO',
        'SANTA ROSA' => 'SANTAROSA', 'SANTAROSA' => 'SANTAROSA',
        'FUNDACIÓN' => 'FUNDACION', 'FUNDACION' => 'FUNDACION',
        'PAMPLONA' => 'PAMPLONA',
        'CIMITARRA' => 'CIMITARRA',
        'SALAZAR' => 'SALAZAR',
        'TOLEDO' => 'TOLEDO',
        'TAME' => 'TAME',
    ];

    return $map[$location] ?? 'PAMPLONA';
}

$mform = new import_vacancies_form(null, [
    'convocatorias' => $convocatorias,
    'convocatoriaid' => $convocatoriaid,
    'isiomad' => $isiomad,
]);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/local/jobboard/index.php', ['view' => 'convocatorias']));
} else if ($data = $mform->get_data()) {
    // Process import.
    $content = $mform->get_file_content('csvfile');

    $iid = \csv_import_reader::get_new_iid('local_jobboard_vacancies');
    $csvreader = new \csv_import_reader($iid, 'local_jobboard_vacancies');

    $delimiter = $data->delimiter;
    $encoding = $data->encoding;

    $readcount = $csvreader->load_csv_content($content, $encoding, $delimiter);

    if ($readcount === false) {
        throw new moodle_exception('csvreaderror', 'local_jobboard');
    }

    $columns = $csvreader->get_columns();
    $columns_lower = array_map('strtolower', $columns);

    // Map columns.
    $colmap = [
        'code' => array_search('code', $columns_lower),
        'contracttype' => array_search('contracttype', $columns_lower),
        'program' => array_search('program', $columns_lower),
        'profile' => array_search('profile', $columns_lower),
        'courses' => array_search('courses', $columns_lower),
        'location' => array_search('location', $columns_lower),
        'modality' => array_search('modality', $columns_lower),
        'faculty' => array_search('faculty', $columns_lower),
    ];

    // Check required column.
    if ($colmap['code'] === false) {
        $csvreader->close();
        $csvreader->cleanup();
        throw new moodle_exception('csvmissingcolumn', 'local_jobboard', '', 'code');
    }

    // Get convocatoria.
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $data->convocatoriaid], '*', MUST_EXIST);

    $csvreader->init();

    $results = [
        'created' => 0,
        'updated' => 0,
        'skipped' => 0,
        'errors' => [],
        'preview' => [],
    ];

    $rownum = 1;
    $now = time();

    while ($row = $csvreader->next()) {
        $rownum++;

        // Get code.
        $code = trim($row[$colmap['code']] ?? '');
        if (empty($code)) {
            continue;
        }

        // Normalize code.
        $code = preg_replace('/[\s-]+/', '-', strtoupper($code));
        $code = preg_replace('/-+/', '-', $code);

        // Get other fields.
        $contracttype = $colmap['contracttype'] !== false ? strtoupper(trim($row[$colmap['contracttype']] ?? '')) : '';
        $program = $colmap['program'] !== false ? trim($row[$colmap['program']] ?? '') : '';
        $profile = $colmap['profile'] !== false ? trim($row[$colmap['profile']] ?? '') : '';
        $coursesstr = $colmap['courses'] !== false ? trim($row[$colmap['courses']] ?? '') : '';
        $locationkey = $colmap['location'] !== false ? normalize_location($row[$colmap['location']] ?? '') : 'PAMPLONA';
        $modality = $colmap['modality'] !== false ? trim($row[$colmap['modality']] ?? '') : 'PRESENCIAL';
        $faculty = $colmap['faculty'] !== false ? strtoupper(trim($row[$colmap['faculty']] ?? '')) : '';

        // Auto-detect faculty.
        if (empty($faculty)) {
            $faculty = strpos($code, 'FCAS') === 0 ? 'FCAS' : (strpos($code, 'FII') === 0 ? 'FII' : '');
        }

        // Parse courses.
        $courses = [];
        if (!empty($coursesstr)) {
            $courses = array_map('trim', explode('|', $coursesstr));
            $courses = array_filter($courses);
        }

        // Check if exists.
        $existing = $DB->get_record('local_jobboard_vacancy', ['code' => $code]);

        if ($existing && !$data->updateexisting) {
            $results['skipped']++;
            $results['errors'][] = get_string('importerror_vacancyexists', 'local_jobboard',
                ['row' => $rownum, 'code' => $code]);
            continue;
        }

        // Build vacancy data.
        $locationinfo = $locationmap[$locationkey] ?? $locationmap['PAMPLONA'];
        $modalityname = stripos($modality, 'DISTANCIA') !== false ? 'A Distancia' : 'Presencial';
        $facultyname = $faculty === 'FCAS' ? 'Ciencias Administrativas y Sociales' :
                      ($faculty === 'FII' ? 'Ingenierías e Informática' : $faculty);

        $isocasional = stripos($contracttype, 'OCASIONAL') !== false;

        // Title.
        $title = $program ?: "Docente {$faculty}";
        if ($profile && strlen($profile) < 60) {
            $title .= " - " . $profile;
        }
        if (strlen($title) > 250) {
            $title = substr($title, 0, 247) . '...';
        }

        // Build description HTML.
        $deschtml = "<div class=\"vacancy-description\">\n";
        $deschtml .= "<div class=\"alert alert-secondary\">\n";
        $deschtml .= "<strong>Código:</strong> {$code} | ";
        $deschtml .= "<strong>Facultad:</strong> {$facultyname} | ";
        $deschtml .= "<strong>Modalidad:</strong> {$modalityname}\n";
        $deschtml .= "</div>\n";

        if ($program) {
            $deschtml .= "<h4>Programa Académico</h4>\n";
            $deschtml .= "<p><strong>{$program}</strong></p>\n";
        }

        if (!empty($courses)) {
            $deschtml .= "<h4>Cursos/Asignaturas a Orientar</h4>\n";
            $deschtml .= "<ul class=\"list-group list-group-flush mb-3\">\n";
            foreach ($courses as $course) {
                $deschtml .= "<li class=\"list-group-item\"><i class=\"fa fa-book mr-2\"></i>" . s($course) . "</li>\n";
            }
            $deschtml .= "</ul>\n";
        }
        $deschtml .= "</div>\n";

        // Build requirements HTML.
        $reqhtml = "<div class=\"vacancy-requirements\">\n";
        $reqhtml .= "<h5>Perfil Profesional Requerido</h5>\n";
        if ($profile) {
            $reqhtml .= "<p class=\"lead\">" . s($profile) . "</p>\n";
        }
        $reqhtml .= "<h5>Requisitos Mínimos</h5>\n";
        $reqhtml .= "<ul>\n";
        $reqhtml .= "<li>Título profesional universitario acorde al perfil solicitado</li>\n";
        $reqhtml .= "<li>No tener inhabilidades ni incompatibilidades para contratar con el Estado</li>\n";
        $reqhtml .= "<li>Disponibilidad para la sede {$locationinfo['name']} en modalidad {$modalityname}</li>\n";
        $reqhtml .= "</ul>\n";
        $reqhtml .= "</div>\n";

        if ($data->previewonly) {
            // Preview mode.
            $results['preview'][] = [
                'row' => $rownum,
                'code' => $code,
                'title' => $title,
                'location' => $locationinfo['name'],
                'modality' => $modalityname,
                'contracttype' => $contracttype ?: '-',
                'courses' => count($courses),
                'action' => $existing ? get_string('update') : get_string('create'),
            ];
        } else {
            // Build record.
            $record = new stdClass();
            $record->code = $code;
            $record->title = $title;
            $record->description = $deschtml;
            $record->requirements = $reqhtml;
            $record->contracttype = $contracttype;
            $record->duration = $isocasional ? 'Período académico (semestral)' : 'Por horas según programación académica';
            $record->location = $locationinfo['name'];
            $record->department = $program ?: $facultyname;
            $record->convocatoriaid = $data->convocatoriaid;
            // Note: opendate and closedate are inherited from convocatoria.
            $record->positions = 1;
            $record->status = $data->status;
            $record->publicationtype = $convocatoria->publicationtype ?? 'internal';
            $record->createdby = $USER->id;
            $record->timecreated = $now;

            try {
                if ($existing) {
                    $record->id = $existing->id;
                    $record->modifiedby = $USER->id;
                    $record->timemodified = $now;
                    $DB->update_record('local_jobboard_vacancy', $record);
                    $results['updated']++;
                } else {
                    $DB->insert_record('local_jobboard_vacancy', $record);
                    $results['created']++;
                }
            } catch (Exception $e) {
                $results['errors'][] = get_string('importerror_createfailed', 'local_jobboard',
                    ['row' => $rownum, 'code' => $code]);
            }
        }
    }

    $csvreader->close();
    $csvreader->cleanup();

    // Audit log for actual imports.
    if (!$data->previewonly) {
        \local_jobboard\audit::log('vacancies_imported', 'convocatoria', $data->convocatoriaid, [
            'created' => $results['created'],
            'updated' => $results['updated'],
            'skipped' => $results['skipped'],
        ]);
    }

    // Show results using renderer + template pattern.
    echo $OUTPUT->header();

    $renderer = $PAGE->get_renderer('local_jobboard');
    $resultsdata = $renderer->prepare_import_vacancies_results_data(
        $results,
        (bool) $data->previewonly,
        (int) $data->convocatoriaid
    );
    echo $renderer->render_import_vacancies_results_page($resultsdata);

    echo $OUTPUT->footer();
    exit;
}

// Show form using renderer + template pattern.
echo $OUTPUT->header();

// Capture form HTML.
ob_start();
$mform->display();
$formhtml = ob_get_clean();

// Use renderer.
$renderer = $PAGE->get_renderer('local_jobboard');
$pagedata = $renderer->prepare_import_vacancies_page_data($convocatoriaid, $formhtml);
echo $renderer->render_import_vacancies_page($pagedata);

echo $OUTPUT->footer();
