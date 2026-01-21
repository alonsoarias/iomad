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
 * CLI script for adding additional vacancies to existing Convocatoria de Proyectos 2026.
 *
 * This script adds the 14 new profiles (INGS-16 to INV-29) with 38 positions
 * to an existing convocatoria. It also updates the deadline if needed.
 *
 * Usage:
 *   php convocatoria_proyectos_adicionales_cli.php --convocatoria-id=9 [options]
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
    'publish' => false,
    'json' => __DIR__ . '/convocatoria_proyectos_2026_adicionales.json',
    'convocatoria-id' => null,
    'update-deadline' => false,
], [
    'h' => 'help',
    'd' => 'dryrun',
    'v' => 'verbose',
    'p' => 'publish',
    'j' => 'json',
    'i' => 'convocatoria-id',
    'u' => 'update-deadline',
]);

if (!empty($unrecognized)) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
============================================================
ISER Job Board - Adicionar Vacantes a Convocatoria Existente
============================================================

Añade los 14 nuevos perfiles profesionales (INGS-16 a INV-29) con 38 posiciones
adicionales a una convocatoria existente de Proyectos Interinstitucionales.

NUEVOS PERFILES:
  INGS-16  Profesional OVA Ingeniería de Sistemas (3 pos)
  INGTC-17 Profesional OVA Desarrollo de Software (3 pos)
  INGC-18  Profesional OVA Ingeniería Civil - Básicas (2 pos)
  INGC-19  Profesional OVA Ingeniería Civil - Construcción (3 pos)
  ARQ-20   Profesional OVA Arquitectura (1 pos)
  INGI-21  Profesional OVA Ingeniería Industrial (3 pos)
  INGIC-22 Profesional OVA Ingeniería Industrial - Automatización (1 pos)
  INGI-23  Profesional OVA Ingeniería de Alimentos - Alimentaria (3 pos)
  INGI-24  Profesional OVA Ingeniería de Alimentos - No Alimentaria (3 pos)
  SSTM-25  Profesional OVA SST - Medicina del Trabajo (3 pos)
  SSTS-26  Profesional OVA SST - Seguridad Industrial (3 pos)
  FORS-27  Profesional OVA Ingeniería Forestal (3 pos)
  FORS-28  Profesional OVA Ingeniería Forestal - Silvicultura (3 pos)
  INV-29   Profesional OVA Investigación y Desarrollo (4 pos)

TOTAL: 14 nuevos perfiles, 38 posiciones adicionales (todas en Pamplona)

USAGE:
  php convocatoria_proyectos_adicionales_cli.php --convocatoria-id=9 [options]

OPTIONS:
  -h, --help             Show this help message
  -d, --dryrun           Simulate execution without making changes
  -v, --verbose          Show detailed output during execution
  -p, --publish          Publish vacancies after creation (default: draft)
  -j, --json=FILE        Path to JSON data file
                         (default: convocatoria_proyectos_2026_adicionales.json)
  -i, --convocatoria-id=ID  REQUIRED: ID of the existing convocatoria
  -u, --update-deadline  Update the convocatoria deadline to the new date (Jan 25)

EXAMPLES:

  1. Add new vacancies (dry run):
     php convocatoria_proyectos_adicionales_cli.php -i 9 --dryrun --verbose

  2. Add and publish new vacancies:
     php convocatoria_proyectos_adicionales_cli.php -i 9 --publish --verbose

  3. Add vacancies and update deadline:
     php convocatoria_proyectos_adicionales_cli.php -i 9 --publish --update-deadline --verbose

EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

// Validate required parameter.
if (empty($options['convocatoria-id'])) {
    cli_error("ERROR: --convocatoria-id is required.\n\nUse --help for usage information.");
}

// Global variables.
$dryrun = $options['dryrun'];
$verbose = $options['verbose'];
$publish = $options['publish'];
$jsonpath = $options['json'];
$convocatoriaid = (int) $options['convocatoria-id'];
$updatedeadline = $options['update-deadline'];

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

echo "Source: {$data['source']}\n";
echo "New profiles: {$data['stats']['new_profiles']}\n";
echo "New positions: {$data['stats']['new_positions']}\n\n";

// Get admin user.
$adminuser = get_admin();
$now = time();

// ============================================================
// PHASE 1: VERIFY CONVOCATORIA EXISTS
// ============================================================
cli_heading('Phase 1: Verifying Convocatoria');

$convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);

if (!$convocatoria) {
    cli_error("Convocatoria with ID $convocatoriaid not found.");
}

echo "Found convocatoria:\n";
echo "  ID: {$convocatoria->id}\n";
echo "  Code: {$convocatoria->code}\n";
echo "  Name: {$convocatoria->name}\n";
echo "  Status: {$convocatoria->status}\n";
echo "  Current deadline: " . date('Y-m-d', $convocatoria->enddate) . "\n";

// Count existing vacancies.
$existingcount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoriaid]);
echo "  Existing vacancies: $existingcount\n";

echo "\n";

// ============================================================
// PHASE 2: UPDATE DEADLINE (optional)
// ============================================================
if ($updatedeadline && !empty($data['convocatoria_update']['new_enddate'])) {
    cli_heading('Phase 2: Updating Deadline');

    $newenddate = strtotime($data['convocatoria_update']['new_enddate'] . ' 23:59:59');

    echo "Current deadline: " . date('Y-m-d', $convocatoria->enddate) . "\n";
    echo "New deadline: " . date('Y-m-d', $newenddate) . "\n";

    if (!$dryrun) {
        $convocatoria->enddate = $newenddate;
        $convocatoria->timemodified = $now;
        $convocatoria->modifiedby = $adminuser->id;
        $DB->update_record('local_jobboard_convocatoria', $convocatoria);
        echo "Deadline UPDATED successfully.\n";
    } else {
        echo "DRY RUN: Would update deadline.\n";
    }
    echo "\n";
} else {
    echo "Skipping deadline update (use --update-deadline to enable)\n\n";
}

// ============================================================
// PHASE 3: GET COMPANY/DEPARTMENT FOR PAMPLONA
// ============================================================
cli_heading('Phase 3: Getting IOMAD Company Info');

// All new vacancies are in Pamplona.
$pamplonacompany = $DB->get_record_sql(
    "SELECT * FROM {company} WHERE LOWER(shortname) = LOWER(:shortname)",
    ['shortname' => 'pamplona']
);

if (!$pamplonacompany) {
    // Try alternative search.
    $pamplonacompany = $DB->get_record_sql(
        "SELECT * FROM {company} WHERE LOWER(name) LIKE LOWER(:name)",
        ['name' => '%pamplona%']
    );
}

if ($pamplonacompany) {
    echo "Found company: {$pamplonacompany->name} (ID: {$pamplonacompany->id})\n";
    $companyid = $pamplonacompany->id;

    // Get Proyectos Interinstitucionales department.
    $department = $DB->get_record('department', [
        'company' => $companyid,
        'shortname' => 'PROY-INTER',
    ]);

    if ($department) {
        echo "Found department: {$department->name} (ID: {$department->id})\n";
        $departmentid = $department->id;
    } else {
        echo "Department 'Proyectos Interinstitucionales' not found.\n";
        $departmentid = null;
    }
} else {
    echo "WARNING: Pamplona company not found.\n";
    $companyid = null;
    $departmentid = null;
}

echo "\n";

// ============================================================
// PHASE 4: ADD NEW VACANCIES
// ============================================================
cli_heading('Phase 4: Adding New Vacancies');

$profiles = $data['profiles'];
$stats = [
    'created' => 0,
    'skipped' => 0,
    'errors' => 0,
];

$totalvacancies = count($profiles);
$current = 0;

foreach ($profiles as $profile) {
    $current++;
    $prefix = "[$current/$totalvacancies]";

    $code = $profile['code'];
    $role = $profile['role'];

    foreach ($profile['locations'] as $location) {
        $locationName = $location['name'];
        $locationPositions = $location['positions'];

        // Check if vacancy already exists.
        $existing = $DB->get_record_sql(
            "SELECT * FROM {local_jobboard_vacancy}
             WHERE code = :code AND convocatoriaid = :convid",
            ['code' => $code, 'convid' => $convocatoriaid]
        );

        if ($existing) {
            if ($verbose) {
                echo "$prefix SKIP: $code @ $locationName (already exists, ID: {$existing->id})\n";
            }
            $stats['skipped']++;
            continue;
        }

        // Build vacancy record.
        $vacancy = new stdClass();
        $vacancy->code = $code;
        $vacancy->title = "$role - $locationName";
        $vacancy->description = build_vacancy_description_ova($profile, $location);
        $vacancy->contracttype = $profile['contracttype'];
        $vacancy->duration = 'Según proyecto interinstitucional';
        $vacancy->location = $locationName;
        $vacancy->modality = 'presencial';
        $vacancy->department = 'Proyectos Interinstitucionales';
        $vacancy->companyid = $companyid;
        $vacancy->departmentid = $departmentid;
        $vacancy->convocatoriaid = $convocatoriaid;
        $vacancy->positions = $locationPositions;
        $vacancy->requirements = build_vacancy_requirements_ova($profile, $data['required_documents']);
        $vacancy->desirable = build_vacancy_desirable_ova($profile);
        $vacancy->status = $publish ? 'published' : 'draft';
        $vacancy->publicationtype = 'public';
        $vacancy->createdby = $adminuser->id;
        $vacancy->timecreated = $now;

        if (!$dryrun) {
            try {
                $vacancyid = $DB->insert_record('local_jobboard_vacancy', $vacancy);
                $stats['created']++;

                if ($verbose) {
                    echo "$prefix CREATED: $code @ $locationName ($locationPositions pos) [ID: $vacancyid]\n";
                }

                // Create document requirements for this vacancy.
                $docreqcount = create_vacancy_doc_requirements_ova($vacancyid, $data['required_documents'], $now, $verbose);
                if ($verbose && $docreqcount > 0) {
                    echo "    -> Created $docreqcount document requirements\n";
                }

            } catch (Exception $e) {
                $stats['errors']++;
                echo "$prefix ERROR: $code @ $locationName - " . $e->getMessage() . "\n";
            }
        } else {
            $stats['created']++;
            if ($verbose) {
                echo "$prefix DRY RUN: Would create $code @ $locationName ($locationPositions pos)\n";
            }
        }
    }
}

// ============================================================
// SUMMARY
// ============================================================
cli_heading('Final Summary');

echo "Convocatoria: {$convocatoria->name}\n";
echo "Convocatoria ID: $convocatoriaid\n\n";

echo "Vacancies:\n";
echo str_repeat('-', 50) . "\n";
echo "  Created:  {$stats['created']}\n";
echo "  Skipped:  {$stats['skipped']}\n";
echo "  Errors:   {$stats['errors']}\n";
echo str_repeat('-', 50) . "\n";

$totalprocessed = $stats['created'] + $stats['skipped'];
echo "Total processed: $totalprocessed / $totalvacancies\n";

// Final count.
$newcount = $DB->count_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoriaid]);
echo "\nTotal vacancies in convocatoria: $newcount\n";

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
 * Build vacancy description HTML for OVA profiles.
 */
function build_vacancy_description_ova($profile, $location) {
    $locationName = $location['name'];
    $positions = $location['positions'];

    $html = '<div class="vacancy-description">';

    // Header.
    $html .= '<div class="alert alert-info">';
    $html .= '<strong>Código:</strong> ' . htmlspecialchars($profile['code']) . ' | ';
    $html .= '<strong>Posiciones:</strong> ' . $positions . ' | ';
    $html .= '<strong>Ubicación:</strong> ' . htmlspecialchars($locationName);
    $html .= '</div>';

    // Role.
    $html .= '<h4>Rol</h4>';
    $html .= '<p class="lead"><strong>' . htmlspecialchars($profile['role']) . '</strong></p>';

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
    $html .= '<tr><th>Ubicación</th><td>' . htmlspecialchars($locationName) . '</td></tr>';
    $html .= '<tr><th>Posiciones Disponibles</th><td>' . $positions . '</td></tr>';
    $html .= '</table>';

    $html .= '</div>';

    return $html;
}

/**
 * Build vacancy requirements HTML.
 */
function build_vacancy_requirements_ova($profile, $requireddocs) {
    $html = '<div class="vacancy-requirements">';

    $html .= '<h5>Requisitos Mínimos</h5>';
    $html .= '<ul>';
    $html .= '<li><strong>Formación:</strong> ' . htmlspecialchars($profile['education']['degree']) . '</li>';
    if (!empty($profile['education']['postgraduate'])) {
        $html .= '<li><strong>Posgrado:</strong> ' . htmlspecialchars($profile['education']['postgraduate']) . '</li>';
    }
    $html .= '<li><strong>Experiencia:</strong> Mínimo ' . $profile['experience']['years'] . ' año(s) de experiencia específica</li>';
    $html .= '<li>Experiencia en Diseño Instruccional en plataformas tecnológicas educativas</li>';
    $html .= '<li>No tener inhabilidades ni incompatibilidades para contratar con el Estado</li>';
    $html .= '</ul>';

    $html .= '<h5>Documentos Requeridos</h5>';
    $html .= '<ol>';
    foreach ($requireddocs as $doc) {
        $doctext = $doc['name'];
        if (!$doc['required'] && !empty($doc['condition'])) {
            $doctext .= ' (' . htmlspecialchars($doc['condition']) . ')';
        }
        $html .= '<li>' . htmlspecialchars($doctext) . '</li>';
    }
    $html .= '</ol>';

    $html .= '</div>';

    return $html;
}

/**
 * Build vacancy desirable requirements HTML.
 */
function build_vacancy_desirable_ova($profile) {
    $html = '<div class="vacancy-desirable">';

    $html .= '<h5>Requisitos Deseables</h5>';
    $html .= '<ul>';
    $html .= '<li>Experiencia en diseño de Objetos Virtuales de Aprendizaje (OVA)</li>';
    $html .= '<li>Conocimiento de metodologías de educación virtual</li>';
    $html .= '<li>Experiencia con plataformas LMS (Moodle, Canvas, etc.)</li>';
    $html .= '<li>Habilidades en producción de contenidos multimedia educativos</li>';
    $html .= '</ul>';

    $html .= '</div>';

    return $html;
}

/**
 * Create document requirements for a vacancy.
 */
function create_vacancy_doc_requirements_ova($vacancyid, $requireddocs, $now, $verbose = false) {
    global $DB;

    if (empty($requireddocs)) {
        return 0;
    }

    $count = 0;
    $sortorder = 0;

    foreach ($requireddocs as $doc) {
        $sortorder += 10;

        // Use doctype_code if available, otherwise use code.
        $doctypecode = $doc['doctype_code'] ?? $doc['code'];

        // Check if document type exists in the system.
        $doctype = $DB->get_record('local_jobboard_doctype', ['code' => $doctypecode]);
        if (!$doctype) {
            if ($verbose) {
                echo "    WARNING: Document type '$doctypecode' not found in system, skipping.\n";
            }
            continue;
        }

        // Build the document requirement record.
        $docreq = new stdClass();
        $docreq->vacancyid = $vacancyid;
        $docreq->documenttype = $doctypecode;
        $docreq->required = $doc['required'] ? 1 : 0;
        $docreq->acceptedformats = 'pdf';
        $docreq->maxsize = 5242880; // 5MB default.
        $docreq->requiresissuedate = 0;
        $docreq->maxagedays = null;
        $docreq->sortorder = $sortorder;
        $docreq->timecreated = $now;

        // Add instructions if provided.
        if (!empty($doc['instructions'])) {
            $docreq->instructions = $doc['instructions'];
        } else if (!empty($doc['condition'])) {
            $docreq->instructions = $doc['condition'];
        }

        // Insert the record.
        try {
            $DB->insert_record('local_jobboard_doc_requirement', $docreq);
            $count++;
        } catch (Exception $e) {
            if ($verbose) {
                echo "    ERROR creating doc requirement for '$doctypecode': " . $e->getMessage() . "\n";
            }
        }
    }

    return $count;
}
