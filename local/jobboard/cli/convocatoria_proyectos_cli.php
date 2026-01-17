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
 * CLI script for creating Convocatoria de Proyectos Interinstitucionales 2026.
 *
 * This script creates:
 * 1. The convocatoria (call for applications)
 * 2. All vacancies (15 profiles with 56 total positions)
 *
 * Usage:
 *   php convocatoria_proyectos_cli.php [options]
 *
 * Options:
 *   --help, -h          Show help message
 *   --dryrun, -d        Simulate without making changes
 *   --verbose, -v       Show detailed output
 *   --update, -u        Update existing records
 *   --publish, -p       Publish vacancies after creation
 *   --json=FILE, -j     Path to JSON data file (default: convocatoria_proyectos_2026.json)
 *   --reset             Delete existing convocatoria and vacancies before creating
 *
 * @package   local_jobboard
 * @copyright 2026 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// CLI options.
list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'dryrun' => false,
    'verbose' => false,
    'update' => false,
    'publish' => false,
    'json' => __DIR__ . '/convocatoria_proyectos_2026.json',
    'reset' => false,
], [
    'h' => 'help',
    'd' => 'dryrun',
    'v' => 'verbose',
    'u' => 'update',
    'p' => 'publish',
    'j' => 'json',
    'r' => 'reset',
]);

if (!empty($unrecognized)) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
============================================================
ISER Job Board - Convocatoria Proyectos Interinstitucionales CLI
============================================================

Creates the Convocatoria 01-2026 for Proyectos Interinstitucionales
with all 15 professional profiles (56 total positions).

USAGE:
  php convocatoria_proyectos_cli.php [options]

OPTIONS:
  -h, --help        Show this help message
  -d, --dryrun      Simulate execution without making changes
  -v, --verbose     Show detailed output during execution
  -u, --update      Update existing records if they exist
  -p, --publish     Publish vacancies after creation (default: draft)
  -j, --json=FILE   Path to JSON data file
                    (default: convocatoria_proyectos_2026.json)
  -r, --reset       Delete existing convocatoria and vacancies before creating

EXAMPLES:

  1. Create convocatoria and vacancies (dry run):
     php convocatoria_proyectos_cli.php --dryrun --verbose

  2. Create and publish all vacancies:
     php convocatoria_proyectos_cli.php --publish --verbose

  3. Update existing records:
     php convocatoria_proyectos_cli.php --update --verbose

  4. Reset and recreate everything:
     php convocatoria_proyectos_cli.php --reset --publish --verbose

PROFILES INCLUDED:
  DP-1   Director(a) del Programa (1 position)
  PM-2   Profesional de Monitoreo y Evaluación (2 positions)
  PC-3   Especialista en Planeación Curricular (2 positions)
  CP-4   Coordinador(a) de Programa (2 positions)
  DEP-5  Docente de recreación, actividad física y/o deportes (2 positions)
  ARC-6  Docente de artes y cultura (2 positions)
  EM-7   Especialista en Matemáticas (8 positions)
  EL-8   Especialista en Lenguaje (8 positions)
  PS-9   Profesional para el apoyo psicosocial (8 positions)
  PU-10  Docente Universitario (8 positions)
  ECD-11 Especialista en competencias digitales (2 positions)
  PDI-12 Profesional en Diseño Instruccional (2 positions)
  EEI-13 Especialista en educación inclusiva (2 positions)
  EXSA-14 Experto en plataformas tecnológicas (3 positions)
  EPAT-15 Experto plataforma de alertas tempranas (4 positions)

LOCATIONS:
  - Pamplona (Norte de Santander): 14 positions
  - Arauquita (Arauca): 17 positions
  - Cumaribo (Vichada): 25 positions

TOTAL: 56 positions across 15 professional profiles

EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

// Global variables.
$dryrun = $options['dryrun'];
$verbose = $options['verbose'];
$update = $options['update'];
$publish = $options['publish'];
$jsonpath = $options['json'];
$reset = $options['reset'];

// Load JSON data.
cli_heading('Loading JSON Data');

if (!file_exists($jsonpath)) {
    cli_error("JSON file not found: $jsonpath");
}

$jsondata = file_get_contents($jsonpath);
$data = json_decode($jsondata, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    cli_error("Error parsing JSON: " . json_last_error_msg());
}

echo "Loaded: {$data['source']}\n";
echo "Generated: {$data['generated']}\n";
echo "Total profiles: {$data['stats']['total_profiles']}\n";
echo "Total positions: {$data['stats']['total_positions']}\n\n";

// Get admin user.
$adminuser = get_admin();
$now = time();

// ============================================================
// RESET MODE
// ============================================================
if ($reset) {
    cli_heading('Resetting Existing Data');

    $convcode = $data['convocatoria']['code'];

    // Find existing convocatoria.
    $existingconv = $DB->get_record('local_jobboard_convocatoria', ['code' => $convcode]);

    if ($existingconv) {
        echo "Found existing convocatoria: {$existingconv->name} (ID: {$existingconv->id})\n";

        // Check for applications.
        $sql = "SELECT COUNT(a.id) as cnt
                FROM {local_jobboard_application} a
                JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
                WHERE v.convocatoriaid = ?";
        $appcount = $DB->count_records_sql($sql, [$existingconv->id]);

        if ($appcount > 0) {
            cli_error("Cannot reset: Found $appcount application(s). Delete applications first.");
        }

        if (!$dryrun) {
            // Delete vacancies.
            $vacancies = $DB->get_records('local_jobboard_vacancy', ['convocatoriaid' => $existingconv->id]);
            foreach ($vacancies as $v) {
                // Delete document requirements.
                $DB->delete_records('local_jobboard_doc_requirement', ['vacancyid' => $v->id]);
                // Delete vacancy.
                $DB->delete_records('local_jobboard_vacancy', ['id' => $v->id]);
                if ($verbose) {
                    echo "  Deleted vacancy: {$v->code} - {$v->location}\n";
                }
            }

            // Delete convocatoria.
            $DB->delete_records('local_jobboard_convocatoria', ['id' => $existingconv->id]);
            echo "Deleted convocatoria: {$existingconv->code}\n";
        } else {
            echo "DRY RUN: Would delete convocatoria and " . count($vacancies ?? []) . " vacancies\n";
        }
    } else {
        echo "No existing convocatoria found with code: $convcode\n";
    }

    echo "\n";
}

// ============================================================
// CREATE CONVOCATORIA
// ============================================================
cli_heading('Creating Convocatoria');

$convdata = $data['convocatoria'];
$convcode = $convdata['code'];
$convname = $convdata['name'];

// Check if exists.
$existingconv = $DB->get_record('local_jobboard_convocatoria', ['code' => $convcode]);

if ($existingconv && !$update) {
    echo "Convocatoria already exists: {$existingconv->name} (ID: {$existingconv->id})\n";
    echo "Use --update to modify existing records.\n";
    $convocatoriaid = $existingconv->id;
} else {
    // Build description HTML.
    $deschtml = build_convocatoria_description($data);

    // Build terms HTML.
    $termshtml = build_convocatoria_terms($data);

    // Parse dates.
    $startdate = strtotime($convdata['startdate']);
    $enddate = strtotime($convdata['enddate'] . ' 23:59:59');

    $convrecord = new stdClass();
    $convrecord->code = $convcode;
    $convrecord->name = $convname;
    $convrecord->brief_description = $convdata['brief_description'];
    $convrecord->description = $deschtml;
    $convrecord->startdate = $startdate;
    $convrecord->enddate = $enddate;
    $convrecord->status = $convdata['status'];
    $convrecord->companyid = null;
    $convrecord->departmentid = null;
    $convrecord->publicationtype = $convdata['publicationtype'];
    $convrecord->terms = $termshtml;
    $convrecord->allow_multiple_applications = 1;
    $convrecord->max_applications_per_user = 3;
    $convrecord->createdby = $adminuser->id;
    $convrecord->timecreated = $now;

    if (!$dryrun) {
        if ($existingconv && $update) {
            $convrecord->id = $existingconv->id;
            $convrecord->modifiedby = $adminuser->id;
            $convrecord->timemodified = $now;
            $DB->update_record('local_jobboard_convocatoria', $convrecord);
            $convocatoriaid = $existingconv->id;
            echo "Updated convocatoria: $convname (ID: $convocatoriaid)\n";
        } else {
            $convocatoriaid = $DB->insert_record('local_jobboard_convocatoria', $convrecord);
            echo "Created convocatoria: $convname (ID: $convocatoriaid)\n";
        }
    } else {
        echo "DRY RUN: Would create convocatoria: $convname\n";
        $convocatoriaid = 0;
    }

    echo "  Code: $convcode\n";
    echo "  Status: {$convdata['status']}\n";
    echo "  Period: {$convdata['startdate']} to {$convdata['enddate']}\n";
    echo "  Publication: {$convdata['publicationtype']}\n";
}

echo "\n";

// ============================================================
// CREATE VACANCIES
// ============================================================
cli_heading('Creating Vacancies');

$profiles = $data['profiles'];
$stats = [
    'created' => 0,
    'updated' => 0,
    'skipped' => 0,
    'errors' => 0,
];

$totalvacancies = 0;
foreach ($profiles as $profile) {
    $totalvacancies += count($profile['locations']);
}

$current = 0;

foreach ($profiles as $profile) {
    $code = $profile['code'];
    $role = $profile['role'];
    $positions = $profile['positions'];

    foreach ($profile['locations'] as $location) {
        $current++;
        $prefix = "[$current/$totalvacancies]";

        $locationName = $location['name'];
        $locationPositions = $location['positions'];
        $locationNotes = $location['notes'] ?? '';
        $locationDept = $location['department'] ?? '';

        // Build full location string.
        $fullLocation = $locationName;
        if (!empty($locationDept)) {
            $fullLocation .= " ($locationDept)";
        }

        // Check if exists.
        $existing = $DB->get_record_sql(
            "SELECT * FROM {local_jobboard_vacancy}
             WHERE code = :code AND location = :location AND convocatoriaid = :convid",
            ['code' => $code, 'location' => $fullLocation, 'convid' => $convocatoriaid]
        );

        if ($existing && !$update) {
            if ($verbose) {
                echo "$prefix SKIP: $code @ $fullLocation (exists)\n";
            }
            $stats['skipped']++;
            continue;
        }

        // Build vacancy record.
        $vacancy = new stdClass();
        $vacancy->code = $code;
        $vacancy->title = "$role - $fullLocation";
        $vacancy->description = build_vacancy_description_proyectos($profile, $location);
        $vacancy->contracttype = $profile['contracttype'];
        $vacancy->duration = 'Según proyecto interinstitucional';
        $vacancy->location = $fullLocation;
        $vacancy->modality = 'presencial';
        $vacancy->department = 'Proyectos Interinstitucionales';
        $vacancy->convocatoriaid = $convocatoriaid;
        $vacancy->positions = $locationPositions;
        $vacancy->requirements = build_vacancy_requirements_proyectos($profile);
        $vacancy->desirable = build_vacancy_desirable_proyectos($profile);
        $vacancy->status = $publish ? 'published' : 'draft';
        $vacancy->publicationtype = 'public';
        $vacancy->createdby = $adminuser->id;
        $vacancy->timecreated = $now;

        if (!$dryrun) {
            try {
                if ($existing && $update) {
                    $vacancy->id = $existing->id;
                    $vacancy->modifiedby = $adminuser->id;
                    $vacancy->timemodified = $now;
                    $DB->update_record('local_jobboard_vacancy', $vacancy);
                    $stats['updated']++;
                    if ($verbose) {
                        echo "$prefix UPDATED: $code @ $fullLocation ($locationPositions pos)\n";
                    }
                } else {
                    $vacancyid = $DB->insert_record('local_jobboard_vacancy', $vacancy);
                    $stats['created']++;
                    if ($verbose) {
                        echo "$prefix CREATED: $code @ $fullLocation ($locationPositions pos) [ID: $vacancyid]\n";
                    }
                }
            } catch (Exception $e) {
                $stats['errors']++;
                echo "$prefix ERROR: $code @ $fullLocation - " . $e->getMessage() . "\n";
            }
        } else {
            $stats['created']++;
            if ($verbose) {
                echo "$prefix DRY RUN: Would create $code @ $fullLocation ($locationPositions pos)\n";
            }
        }
    }
}

// ============================================================
// SUMMARY
// ============================================================
cli_heading('Summary');

echo "Convocatoria: {$data['convocatoria']['name']}\n";
echo "Convocatoria ID: $convocatoriaid\n\n";

echo str_repeat('-', 50) . "\n";
echo "Vacancies created:  {$stats['created']}\n";
echo "Vacancies updated:  {$stats['updated']}\n";
echo "Vacancies skipped:  {$stats['skipped']}\n";
echo "Errors:             {$stats['errors']}\n";
echo str_repeat('-', 50) . "\n";

$totalprocessed = $stats['created'] + $stats['updated'] + $stats['skipped'];
echo "Total processed: $totalprocessed / $totalvacancies\n";

if ($dryrun) {
    echo "\n*** DRY RUN - No changes were made ***\n";
    echo "Run without --dryrun to apply changes.\n";
}

echo "\n=== DONE ===\n";

exit(0);

// ============================================================
// HELPER FUNCTIONS
// ============================================================

/**
 * Build convocatoria description HTML.
 */
function build_convocatoria_description($data) {
    $conv = $data['convocatoria'];
    $stats = $data['stats'];

    $html = '<div class="convocatoria-description">';

    // Header.
    $html .= '<div class="alert alert-primary">';
    $html .= '<h3 class="alert-heading">CONVOCATORIA 01-2026</h3>';
    $html .= '<p class="lead">Para Conformar el Repositorio de Hojas de Vida de Proyectos Interinstitucionales</p>';
    $html .= '</div>';

    // Objective.
    $html .= '<h4>Objetivo</h4>';
    $html .= '<p>' . htmlspecialchars($conv['brief_description']) . '</p>';

    // Statistics.
    $html .= '<h4>Resumen de Vacantes</h4>';
    $html .= '<table class="table table-bordered">';
    $html .= '<thead><tr><th>Ubicación</th><th>Posiciones</th></tr></thead>';
    $html .= '<tbody>';
    $html .= '<tr><td>Pamplona (Norte de Santander)</td><td>' . $stats['locations']['pamplona'] . '</td></tr>';
    $html .= '<tr><td>Arauquita (Arauca)</td><td>' . $stats['locations']['arauquita'] . '</td></tr>';
    $html .= '<tr><td>Cumaribo (Vichada)</td><td>' . $stats['locations']['cumaribo'] . '</td></tr>';
    $html .= '<tr class="table-primary"><td><strong>TOTAL</strong></td><td><strong>' . $stats['total_positions'] . '</strong></td></tr>';
    $html .= '</tbody></table>';

    // Timeline.
    $html .= '<h4>Cronograma</h4>';
    $html .= '<ul>';
    $html .= '<li><strong>Publicación de la convocatoria:</strong> ' . $conv['publication_date'] . '</li>';
    $html .= '<li><strong>Plazo para remitir hojas de vida:</strong> ' . $conv['startdate'] . ' al ' . $conv['enddate'] . '</li>';
    $html .= '<li><strong>Publicación del listado del repositorio:</strong> ' . $conv['results_date'] . '</li>';
    $html .= '</ul>';

    // Required documents.
    $html .= '<h4>Documentos Requeridos</h4>';
    $html .= '<ol>';
    foreach ($data['required_documents'] as $doc) {
        $required = $doc['required'] ? '' : ' <em>(si aplica)</em>';
        $html .= '<li>' . htmlspecialchars($doc['name']) . $required . '</li>';
    }
    $html .= '</ol>';

    // Contact.
    $html .= '<h4>Información de Contacto</h4>';
    $html .= '<p><strong>PROCESO DE DIRECCIONAMIENTO ESTRATÉGICO Y PLANEACIÓN</strong></p>';
    $html .= '<ul>';
    foreach ($conv['contact_emails'] as $email) {
        $html .= '<li><a href="mailto:' . htmlspecialchars($email) . '">' . htmlspecialchars($email) . '</a></li>';
    }
    $html .= '</ul>';

    $html .= '</div>';

    return $html;
}

/**
 * Build convocatoria terms HTML.
 */
function build_convocatoria_terms($data) {
    $clarifications = $data['clarifications'];

    $html = '<div class="convocatoria-terms">';
    $html .= '<h4>Términos y Condiciones</h4>';

    $html .= '<h5>1. Aclaraciones Finales al Proceso de Convocatoria</h5>';
    $html .= '<ul>';
    foreach ($clarifications as $clarification) {
        $html .= '<li>' . htmlspecialchars($clarification) . '</li>';
    }
    $html .= '</ul>';

    $html .= '<h5>2. Requisitos y Compromisos</h5>';
    $html .= '<ul>';
    $html .= '<li>Cumplir con los mínimos de formación educativa y experiencia específica requeridos.</li>';
    $html .= '<li>Cada una de las experiencias relacionadas debe estar claramente delimitadas, especificando fecha de inicio y terminación, actividades/funciones desarrolladas, y municipio de ejecución.</li>';
    $html .= '<li>Residir en los municipios donde prestará los servicios o tener disponibilidad de desplazamiento.</li>';
    $html .= '</ul>';

    $html .= '<h5>3. Aceptación de Términos</h5>';
    $html .= '<p>Al enviar mi postulación, confirmo haber leído, entendido y aceptado todas las condiciones establecidas en esta convocatoria.</p>';

    $html .= '</div>';

    return $html;
}

/**
 * Build vacancy description HTML for proyectos interinstitucionales.
 */
function build_vacancy_description_proyectos($profile, $location) {
    $locationName = $location['name'];
    $locationDept = $location['department'] ?? '';
    $locationNotes = $location['notes'] ?? '';
    $positions = $location['positions'];

    $fullLocation = $locationName;
    if (!empty($locationDept)) {
        $fullLocation .= " ($locationDept)";
    }

    $html = '<div class="vacancy-description">';

    // Header.
    $html .= '<div class="alert alert-info">';
    $html .= '<strong>Código:</strong> ' . htmlspecialchars($profile['code']) . ' | ';
    $html .= '<strong>Posiciones:</strong> ' . $positions . ' | ';
    $html .= '<strong>Ubicación:</strong> ' . htmlspecialchars($fullLocation);
    $html .= '</div>';

    // Role.
    $html .= '<h4>Rol</h4>';
    $html .= '<p class="lead"><strong>' . htmlspecialchars($profile['role']) . '</strong></p>';

    // Location notes.
    if (!empty($locationNotes)) {
        $html .= '<div class="alert alert-warning">';
        $html .= '<i class="fa fa-info-circle"></i> ' . htmlspecialchars($locationNotes);
        $html .= '</div>';
    }

    // Education.
    $html .= '<h4>Formación Académica</h4>';
    $html .= '<ul>';
    $html .= '<li><strong>Pregrado:</strong> ' . htmlspecialchars($profile['education']['degree']) . '</li>';
    if (!empty($profile['education']['postgraduate'])) {
        $html .= '<li><strong>Posgrado:</strong> ' . htmlspecialchars($profile['education']['postgraduate']) . '</li>';
    }
    $html .= '</ul>';

    // Experience.
    $html .= '<h4>Experiencia Requerida</h4>';
    $html .= '<p>' . htmlspecialchars($profile['experience']['description']) . '</p>';
    $html .= '<p><strong>Años mínimos de experiencia:</strong> ' . $profile['experience']['years'] . ' año(s)</p>';

    // Contract info.
    $html .= '<h4>Información del Contrato</h4>';
    $html .= '<table class="table table-sm">';
    $html .= '<tr><th>Tipo de Vinculación</th><td>Prestación de Servicios Profesionales</td></tr>';
    $html .= '<tr><th>Ubicación</th><td>' . htmlspecialchars($fullLocation) . '</td></tr>';
    $html .= '<tr><th>Posiciones Disponibles</th><td>' . $positions . '</td></tr>';
    $html .= '</table>';

    $html .= '</div>';

    return $html;
}

/**
 * Build vacancy requirements HTML.
 */
function build_vacancy_requirements_proyectos($profile) {
    $html = '<div class="vacancy-requirements">';

    $html .= '<h5>Requisitos Mínimos</h5>';
    $html .= '<ul>';
    $html .= '<li><strong>Formación:</strong> ' . htmlspecialchars($profile['education']['degree']) . '</li>';
    if (!empty($profile['education']['postgraduate'])) {
        $html .= '<li><strong>Posgrado:</strong> ' . htmlspecialchars($profile['education']['postgraduate']) . '</li>';
    }
    $html .= '<li><strong>Experiencia:</strong> Mínimo ' . $profile['experience']['years'] . ' año(s) de experiencia específica</li>';
    $html .= '<li>No tener inhabilidades ni incompatibilidades para contratar con el Estado</li>';
    $html .= '<li>Residir en el municipio de trabajo o tener disponibilidad de desplazamiento</li>';
    $html .= '</ul>';

    $html .= '<h5>Documentos Requeridos</h5>';
    $html .= '<ol>';
    $html .= '<li>Formato de Hoja de vida de función pública diligenciada</li>';
    $html .= '<li>Fotocopia de la cédula de ciudadanía</li>';
    $html .= '<li>Fotocopia de Libreta Militar (si aplica)</li>';
    $html .= '<li>Certificación de Experiencia reportada en la hoja de vida</li>';
    $html .= '<li>Tarjeta profesional (si aplica)</li>';
    $html .= '<li>Certificados de formación académica</li>';
    $html .= '<li>RUT actualizado</li>';
    $html .= '</ol>';

    $html .= '</div>';

    return $html;
}

/**
 * Build vacancy desirable requirements HTML.
 */
function build_vacancy_desirable_proyectos($profile) {
    $html = '<div class="vacancy-desirable">';

    $html .= '<h5>Requisitos Deseables</h5>';
    $html .= '<ul>';
    $html .= '<li>Experiencia adicional en proyectos sociales o educativos</li>';
    $html .= '<li>Conocimiento de metodologías de intervención comunitaria</li>';
    $html .= '<li>Manejo de herramientas tecnológicas para educación</li>';
    $html .= '<li>Experiencia de trabajo con poblaciones vulnerables</li>';
    $html .= '</ul>';

    $html .= '</div>';

    return $html;
}
