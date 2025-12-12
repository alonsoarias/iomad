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
 * CLI script to create sample convocatorias with vacancies for testing.
 *
 * Creates 2 convocatorias, each with 4 vacancies (cursos/programas).
 *
 * Usage:
 *   php create_sample_convocatorias.php
 *   php create_sample_convocatorias.php --publish     # Create and publish
 *   php create_sample_convocatorias.php --public      # Make public (no auth required)
 *   php create_sample_convocatorias.php --delete      # Delete sample convocatorias
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// CLI options.
list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'publish' => false,
    'public' => false,
    'delete' => false,
    'verbose' => false,
], [
    'h' => 'help',
    'p' => 'publish',
    'P' => 'public',
    'd' => 'delete',
    'v' => 'verbose',
]);

if ($options['help']) {
    $help = <<<EOT
Create sample convocatorias with vacancies for testing.

Creates 2 convocatorias, each with 4 vacancies (courses/programs).

Options:
  -h, --help      Show this help message
  -p, --publish   Create convocatorias with 'open' status (default: draft)
  -P, --public    Make convocatorias public (no auth required)
  -d, --delete    Delete all sample convocatorias (those starting with 'SAMPLE-')
  -v, --verbose   Show detailed output

Examples:
  php create_sample_convocatorias.php              # Create draft convocatorias
  php create_sample_convocatorias.php --publish    # Create and publish
  php create_sample_convocatorias.php --delete     # Delete samples

EOT;
    echo $help;
    exit(0);
}

// Sample data for convocatorias.
$convocatorias = [
    [
        'code' => 'SAMPLE-CONV-2025-01',
        'name' => 'Convocatoria Docente Primer Semestre 2025',
        'description' => 'Convocatoria para la selección de docentes de cátedra para el primer semestre académico del año 2025. Se requieren profesionales con experiencia en educación superior.',
        'brief_description' => 'Docentes de cátedra para primer semestre 2025',
        'vacancies' => [
            [
                'code' => 'SAMPLE-VAC-2025-01-A',
                'title' => 'Docente de Matemáticas Básicas',
                'description' => 'Se requiere docente para el curso de Matemáticas Básicas dirigido a estudiantes de primer semestre. Debe contar con experiencia en didáctica de las matemáticas.',
                'department' => 'Ciencias Básicas',
                'modality' => 'Presencial',
                'positions' => 2,
                'requirements' => 'Licenciatura o profesional en Matemáticas, Ingeniería o afines. Mínimo 2 años de experiencia docente.',
            ],
            [
                'code' => 'SAMPLE-VAC-2025-01-B',
                'title' => 'Docente de Física General',
                'description' => 'Docente para el curso de Física General. Debe tener habilidades en laboratorio y manejo de equipos experimentales.',
                'department' => 'Ciencias Básicas',
                'modality' => 'Presencial',
                'positions' => 1,
                'requirements' => 'Licenciatura en Física o Ingeniería Física. Experiencia en laboratorios de física.',
            ],
            [
                'code' => 'SAMPLE-VAC-2025-01-C',
                'title' => 'Docente de Comunicación Oral y Escrita',
                'description' => 'Docente para fortalecer competencias comunicativas de los estudiantes. Énfasis en producción textual y expresión oral.',
                'department' => 'Humanidades',
                'modality' => 'Virtual',
                'positions' => 3,
                'requirements' => 'Licenciatura en Español, Literatura o Comunicación Social. Experiencia en redacción académica.',
            ],
            [
                'code' => 'SAMPLE-VAC-2025-01-D',
                'title' => 'Docente de Introducción a la Programación',
                'description' => 'Docente para curso introductorio de programación. Se trabajará con Python como lenguaje principal.',
                'department' => 'Ingeniería de Sistemas',
                'modality' => 'Híbrida',
                'positions' => 2,
                'requirements' => 'Ingeniero de Sistemas o afines. Experiencia en Python y metodologías de enseñanza de programación.',
            ],
        ],
    ],
    [
        'code' => 'SAMPLE-CONV-2025-02',
        'name' => 'Convocatoria Docente Segundo Semestre 2025',
        'description' => 'Convocatoria para la selección de docentes de cátedra para el segundo semestre académico del año 2025. Énfasis en programas de ingeniería y ciencias de la salud.',
        'brief_description' => 'Docentes de cátedra para segundo semestre 2025',
        'vacancies' => [
            [
                'code' => 'SAMPLE-VAC-2025-02-A',
                'title' => 'Docente de Cálculo Diferencial',
                'description' => 'Se requiere docente para el curso de Cálculo Diferencial para programas de ingeniería. Debe manejar herramientas tecnológicas de apoyo.',
                'department' => 'Ciencias Básicas',
                'modality' => 'Presencial',
                'positions' => 2,
                'requirements' => 'Maestría en Matemáticas o Educación Matemática. Mínimo 3 años de experiencia docente universitaria.',
            ],
            [
                'code' => 'SAMPLE-VAC-2025-02-B',
                'title' => 'Docente de Anatomía Humana',
                'description' => 'Docente para el programa de Enfermería. Debe tener experiencia en anfiteatro y prácticas de laboratorio.',
                'department' => 'Ciencias de la Salud',
                'modality' => 'Presencial',
                'positions' => 1,
                'requirements' => 'Médico o Enfermero profesional con especialización. Experiencia en docencia anatómica.',
            ],
            [
                'code' => 'SAMPLE-VAC-2025-02-C',
                'title' => 'Docente de Base de Datos',
                'description' => 'Docente para el curso de Bases de Datos. Énfasis en SQL, diseño relacional y NoSQL.',
                'department' => 'Ingeniería de Sistemas',
                'modality' => 'Virtual',
                'positions' => 2,
                'requirements' => 'Ingeniero de Sistemas o afines. Certificación en bases de datos preferible.',
            ],
            [
                'code' => 'SAMPLE-VAC-2025-02-D',
                'title' => 'Docente de Inglés Técnico',
                'description' => 'Docente para el curso de Inglés Técnico para ingenierías. Nivel B2 o superior en inglés.',
                'department' => 'Idiomas',
                'modality' => 'Híbrida',
                'positions' => 3,
                'requirements' => 'Licenciatura en Idiomas o certificación TEFL/TESOL. Experiencia en inglés para propósitos específicos.',
            ],
        ],
    ],
];

// Handle delete option.
if ($options['delete']) {
    cli_heading('Eliminando convocatorias de muestra...');

    // Find and delete sample convocatorias.
    $samples = $DB->get_records_select('local_jobboard_convocatoria',
        "code LIKE 'SAMPLE-%'", null, '', 'id, code, name');

    if (empty($samples)) {
        cli_writeln('No se encontraron convocatorias de muestra para eliminar.');
        exit(0);
    }

    foreach ($samples as $conv) {
        cli_writeln("Eliminando: {$conv->code} - {$conv->name}");

        // Delete associated vacancies first.
        $vacancies = $DB->get_records('local_jobboard_vacancy', ['convocatoriaid' => $conv->id]);
        foreach ($vacancies as $vacancy) {
            // Delete applications for this vacancy.
            $apps = $DB->get_records('local_jobboard_application', ['vacancyid' => $vacancy->id]);
            foreach ($apps as $app) {
                // Delete documents for this application.
                $DB->delete_records('local_jobboard_document', ['applicationid' => $app->id]);
                $DB->delete_records('local_jobboard_doc_validation', ['documentid' => $app->id]);
            }
            $DB->delete_records('local_jobboard_application', ['vacancyid' => $vacancy->id]);
            $DB->delete_records('local_jobboard_vacancy_field', ['vacancyid' => $vacancy->id]);
        }
        $DB->delete_records('local_jobboard_vacancy', ['convocatoriaid' => $conv->id]);

        // Delete the convocatoria.
        $DB->delete_records('local_jobboard_convocatoria', ['id' => $conv->id]);

        if ($options['verbose']) {
            cli_writeln("  - Vacantes eliminadas: " . count($vacancies));
        }
    }

    cli_writeln("\n¡Se eliminaron " . count($samples) . " convocatorias de muestra!");
    exit(0);
}

// Create convocatorias.
cli_heading('Creando convocatorias de muestra...');

$now = time();
$status = $options['publish'] ? 'open' : 'draft';
$publicationtype = $options['public'] ? 'public' : 'internal';

$createdConvs = 0;
$createdVacs = 0;

foreach ($convocatorias as $convdata) {
    // Check if already exists.
    if ($DB->record_exists('local_jobboard_convocatoria', ['code' => $convdata['code']])) {
        cli_writeln("Convocatoria ya existe: {$convdata['code']} - omitiendo");
        continue;
    }

    // Create convocatoria.
    $convocatoria = new stdClass();
    $convocatoria->code = $convdata['code'];
    $convocatoria->name = $convdata['name'];
    $convocatoria->description = $convdata['description'];
    $convocatoria->brief_description = $convdata['brief_description'];
    $convocatoria->startdate = $now;
    $convocatoria->enddate = $now + (90 * 24 * 60 * 60); // 90 days from now.
    $convocatoria->status = $status;
    $convocatoria->publicationtype = $publicationtype;
    $convocatoria->allow_multiple_applications = 1;
    $convocatoria->max_applications_per_user = 2;
    $convocatoria->createdby = 2; // Admin user.
    $convocatoria->timecreated = $now;

    $convocatoriaid = $DB->insert_record('local_jobboard_convocatoria', $convocatoria);
    $createdConvs++;

    cli_writeln("\n✓ Convocatoria creada: {$convdata['code']}");
    cli_writeln("  - Nombre: {$convdata['name']}");
    cli_writeln("  - Estado: $status");
    cli_writeln("  - Tipo: $publicationtype");

    // Create vacancies.
    foreach ($convdata['vacancies'] as $vacdata) {
        // Check if vacancy already exists.
        if ($DB->record_exists('local_jobboard_vacancy', ['code' => $vacdata['code']])) {
            cli_writeln("  - Vacante ya existe: {$vacdata['code']} - omitiendo");
            continue;
        }

        $vacancy = new stdClass();
        $vacancy->code = $vacdata['code'];
        $vacancy->title = $vacdata['title'];
        $vacancy->description = $vacdata['description'];
        $vacancy->department = $vacdata['department'];
        $vacancy->modality = $vacdata['modality'];
        $vacancy->positions = $vacdata['positions'];
        $vacancy->requirements = $vacdata['requirements'];
        $vacancy->convocatoriaid = $convocatoriaid;
        $vacancy->status = $status === 'open' ? 'published' : 'draft';
        $vacancy->publicationtype = $publicationtype;
        $vacancy->opendate = $now;
        $vacancy->closedate = $now + (90 * 24 * 60 * 60);
        $vacancy->createdby = 2;
        $vacancy->timecreated = $now;
        $vacancy->contracttype = 'Cátedra';
        $vacancy->duration = '1 Semestre';
        $vacancy->location = 'Campus Principal';

        $vacancyid = $DB->insert_record('local_jobboard_vacancy', $vacancy);
        $createdVacs++;

        if ($options['verbose']) {
            cli_writeln("    ✓ Vacante: {$vacdata['code']} - {$vacdata['title']}");
            cli_writeln("      Posiciones: {$vacdata['positions']} | Modalidad: {$vacdata['modality']}");
        } else {
            cli_writeln("    ✓ Vacante: {$vacdata['title']} ({$vacdata['positions']} posiciones)");
        }
    }
}

cli_writeln("\n" . str_repeat('=', 60));
cli_writeln("¡Proceso completado!");
cli_writeln("  - Convocatorias creadas: $createdConvs");
cli_writeln("  - Vacantes creadas: $createdVacs");
cli_writeln(str_repeat('=', 60) . "\n");

if ($status === 'draft') {
    cli_writeln("NOTA: Las convocatorias fueron creadas en estado 'borrador'.");
    cli_writeln("Use --publish para crearlas en estado 'abierto'.");
}

if ($publicationtype === 'internal') {
    cli_writeln("NOTA: Las convocatorias son 'internas' (requieren autenticación).");
    cli_writeln("Use --public para hacerlas públicas.");
}

cli_writeln("\nPara ver las convocatorias, visite:");
cli_writeln("  /local/jobboard/index.php?view=convocatorias");
