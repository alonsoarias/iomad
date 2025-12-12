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
 * CLI script to create sample data following ISER organizational architecture.
 *
 * Creates complete ISER structure:
 * - IOMAD Companies (16 Sedes/Centros Tutoriales)
 * - IOMAD Departments (4 Modalidades por Sede)
 * - Convocatorias (2)
 * - Vacancies (4 per convocatoria)
 *
 * Usage:
 *   php create_sample_convocatorias.php                    # Create all
 *   php create_sample_convocatorias.php --structure-only   # Only IOMAD structure
 *   php create_sample_convocatorias.php --publish          # Create and publish
 *   php create_sample_convocatorias.php --delete           # Delete all sample data
 *
 * @package   local_jobboard
 * @copyright 2024-2025 ISER - Instituto Superior de Educación Rural
 * @author    Alonso Arias <soporteplataformas@iser.edu.co>
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// CLI options.
list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'structure-only' => false,
    'publish' => false,
    'public' => false,
    'delete' => false,
    'verbose' => false,
    'sedes' => 'PAMPLONA,CUCUTA',  // Default: only 2 sedes for testing.
    'all-sedes' => false,
], [
    'h' => 'help',
    'S' => 'structure-only',
    'p' => 'publish',
    'P' => 'public',
    'd' => 'delete',
    'v' => 'verbose',
    'a' => 'all-sedes',
]);

// ============================================================
// ISER ORGANIZATIONAL STRUCTURE
// ============================================================

// 16 Sedes/Centros Tutoriales del ISER.
$ISER_SEDES = [
    'PAMPLONA' => [
        'name' => 'ISER Sede Pamplona',
        'shortname' => 'ISER-PAMPLONA',
        'city' => 'Pamplona',
        'code' => 'ISER-PAM',
        'is_main' => true,
    ],
    'CUCUTA' => [
        'name' => 'ISER Centro Tutorial Cúcuta',
        'shortname' => 'ISER-CUCUTA',
        'city' => 'Cúcuta',
        'code' => 'ISER-CUC',
    ],
    'TIBU' => [
        'name' => 'ISER Centro Tutorial Tibú',
        'shortname' => 'ISER-TIBU',
        'city' => 'Tibú',
        'code' => 'ISER-TIB',
    ],
    'OCANA' => [
        'name' => 'ISER Centro Tutorial Ocaña',
        'shortname' => 'ISER-OCANA',
        'city' => 'Ocaña',
        'code' => 'ISER-OCA',
    ],
    'TOLEDO' => [
        'name' => 'ISER Centro Tutorial Toledo',
        'shortname' => 'ISER-TOLEDO',
        'city' => 'Toledo',
        'code' => 'ISER-TOL',
    ],
    'ELTARRA' => [
        'name' => 'ISER Centro Tutorial El Tarra',
        'shortname' => 'ISER-ELTARRA',
        'city' => 'El Tarra',
        'code' => 'ISER-TAR',
    ],
    'SARDINATA' => [
        'name' => 'ISER Centro Tutorial Sardinata',
        'shortname' => 'ISER-SARDINATA',
        'city' => 'Sardinata',
        'code' => 'ISER-SAR',
    ],
    'SANVICENTE' => [
        'name' => 'ISER Centro Tutorial San Vicente',
        'shortname' => 'ISER-SANVICENTE',
        'city' => 'San Vicente del Chucurí',
        'code' => 'ISER-SVC',
    ],
    'PUEBLOBELLO' => [
        'name' => 'ISER Centro Tutorial Pueblo Bello',
        'shortname' => 'ISER-PUEBLOBELLO',
        'city' => 'Pueblo Bello',
        'code' => 'ISER-PBL',
    ],
    'SANPABLO' => [
        'name' => 'ISER Centro Tutorial San Pablo',
        'shortname' => 'ISER-SANPABLO',
        'city' => 'San Pablo',
        'code' => 'ISER-SPB',
    ],
    'SANTAROSA' => [
        'name' => 'ISER Centro Tutorial Santa Rosa',
        'shortname' => 'ISER-SANTAROSA',
        'city' => 'Santa Rosa del Sur',
        'code' => 'ISER-SRS',
    ],
    'FUNDACION' => [
        'name' => 'ISER Centro Tutorial Fundación',
        'shortname' => 'ISER-FUNDACION',
        'city' => 'Fundación',
        'code' => 'ISER-FUN',
    ],
    'CIMITARRA' => [
        'name' => 'ISER Centro Tutorial Cimitarra',
        'shortname' => 'ISER-CIMITARRA',
        'city' => 'Cimitarra',
        'code' => 'ISER-CIM',
    ],
    'SALAZAR' => [
        'name' => 'ISER Centro Tutorial Salazar',
        'shortname' => 'ISER-SALAZAR',
        'city' => 'Salazar',
        'code' => 'ISER-SAL',
    ],
    'TAME' => [
        'name' => 'ISER Centro Tutorial Tame',
        'shortname' => 'ISER-TAME',
        'city' => 'Tame',
        'code' => 'ISER-TAM',
    ],
    'SARAVENA' => [
        'name' => 'ISER Centro Tutorial Saravena',
        'shortname' => 'ISER-SARAVENA',
        'city' => 'Saravena',
        'code' => 'ISER-SRV',
    ],
];

// 4 Modalidades Educativas.
$ISER_MODALIDADES = [
    'PRESENCIAL' => ['name' => 'Presencial', 'shortname' => 'PRESENCIAL'],
    'DISTANCIA' => ['name' => 'A Distancia', 'shortname' => 'DISTANCIA'],
    'VIRTUAL' => ['name' => 'Virtual', 'shortname' => 'VIRTUAL'],
    'HIBRIDA' => ['name' => 'Híbrida', 'shortname' => 'HIBRIDA'],
];

// Programas Académicos del ISER.
$ISER_PROGRAMAS = [
    // FCAS - Facultad de Ciencias Administrativas y Sociales.
    'FCAS' => [
        'name' => 'Facultad de Ciencias Administrativas y Sociales',
        'programs' => [
            'TGC' => [
                'name' => 'Tecnología en Gestión Comunitaria',
                'courses' => [
                    'Sistematización de Experiencias',
                    'Sujeto y Familia',
                    'Dirección de Trabajo de Grado',
                    'Desarrollo Comunitario',
                ],
            ],
            'TGE' => [
                'name' => 'Tecnología en Gestión Empresarial',
                'courses' => [
                    'Emprendimiento',
                    'Administración General',
                    'Contabilidad Básica',
                    'Mercadeo',
                ],
            ],
            'TGD' => [
                'name' => 'Tecnología en Gestión Documental',
                'courses' => [
                    'Archivística',
                    'Gestión de Información',
                    'Legislación Documental',
                    'Preservación Digital',
                ],
            ],
        ],
    ],
    // FII - Facultad de Ingenierías e Innovación.
    'FII' => [
        'name' => 'Facultad de Ingenierías e Innovación',
        'programs' => [
            'TGI' => [
                'name' => 'Tecnología en Gestión Industrial',
                'courses' => [
                    'Ergonomía',
                    'Gestión de Seguridad y Salud en el Trabajo',
                    'Gestión del Talento Humano',
                    'Control de Calidad',
                ],
            ],
            'TGA' => [
                'name' => 'Tecnología en Gestión Ambiental',
                'courses' => [
                    'Gestión Ambiental',
                    'Legislación Ambiental',
                    'Evaluación de Impacto Ambiental',
                    'Desarrollo Sostenible',
                ],
            ],
            'TGINF' => [
                'name' => 'Tecnología en Gestión Informática',
                'courses' => [
                    'Programación I',
                    'Bases de Datos',
                    'Redes de Computadores',
                    'Desarrollo Web',
                ],
            ],
        ],
    ],
];

// Sample convocatorias with vacancies.
$SAMPLE_CONVOCATORIAS = [
    [
        'code' => 'SAMPLE-CONV-2025-01',
        'name' => 'Convocatoria Docente ISER - Primer Semestre 2025',
        'description' => 'Convocatoria para la selección de docentes de cátedra y ocasionales para el primer semestre académico 2025 del Instituto Superior de Educación Rural - ISER.',
        'brief_description' => 'Docentes de cátedra y ocasionales - Primer semestre 2025',
        'vacancies' => [
            [
                'faculty' => 'FCAS',
                'program' => 'TGC',
                'sede' => 'PAMPLONA',
                'modality' => 'PRESENCIAL',
                'contracttype' => 'OCASIONAL TIEMPO COMPLETO',
                'positions' => 2,
            ],
            [
                'faculty' => 'FCAS',
                'program' => 'TGE',
                'sede' => 'CUCUTA',
                'modality' => 'DISTANCIA',
                'contracttype' => 'CATEDRA',
                'positions' => 3,
            ],
            [
                'faculty' => 'FII',
                'program' => 'TGI',
                'sede' => 'PAMPLONA',
                'modality' => 'PRESENCIAL',
                'contracttype' => 'OCASIONAL TIEMPO COMPLETO',
                'positions' => 1,
            ],
            [
                'faculty' => 'FII',
                'program' => 'TGINF',
                'sede' => 'CUCUTA',
                'modality' => 'VIRTUAL',
                'contracttype' => 'CATEDRA',
                'positions' => 2,
            ],
        ],
    ],
    [
        'code' => 'SAMPLE-CONV-2025-02',
        'name' => 'Convocatoria Docente ISER - Segundo Semestre 2025',
        'description' => 'Convocatoria para la selección de docentes de cátedra para el segundo semestre académico 2025. Énfasis en programas de la Facultad de Ingenierías.',
        'brief_description' => 'Docentes de cátedra - Segundo semestre 2025',
        'vacancies' => [
            [
                'faculty' => 'FII',
                'program' => 'TGA',
                'sede' => 'PAMPLONA',
                'modality' => 'HIBRIDA',
                'contracttype' => 'CATEDRA',
                'positions' => 2,
            ],
            [
                'faculty' => 'FII',
                'program' => 'TGI',
                'sede' => 'CUCUTA',
                'modality' => 'PRESENCIAL',
                'contracttype' => 'CATEDRA',
                'positions' => 1,
            ],
            [
                'faculty' => 'FCAS',
                'program' => 'TGD',
                'sede' => 'PAMPLONA',
                'modality' => 'VIRTUAL',
                'contracttype' => 'CATEDRA',
                'positions' => 2,
            ],
            [
                'faculty' => 'FCAS',
                'program' => 'TGC',
                'sede' => 'CUCUTA',
                'modality' => 'DISTANCIA',
                'contracttype' => 'OCASIONAL TIEMPO COMPLETO',
                'positions' => 1,
            ],
        ],
    ],
];

// ============================================================
// HELP
// ============================================================

if ($options['help']) {
    $help = <<<EOT
============================================================
ISER Job Board - Sample Data Generator
============================================================

Creates complete ISER organizational structure and sample data.

ISER STRUCTURE (IOMAD Hierarchy):
  LEVEL 1 - Companies (16 Sedes/Centros Tutoriales):
    - ISER Sede Pamplona (Sede Principal)
    - ISER Centro Tutorial Cúcuta, Tibú, Ocaña, Toledo, El Tarra
    - ISER Centro Tutorial Sardinata, San Vicente, Pueblo Bello
    - ISER Centro Tutorial San Pablo, Santa Rosa, Fundación
    - ISER Centro Tutorial Cimitarra, Salazar, Tame, Saravena

  LEVEL 2 - Departments per Company (4 Modalidades Educativas):
    - Presencial, A Distancia, Virtual, Híbrida

  LEVEL 3 - Convocatorias (2)
    - Primer Semestre 2025
    - Segundo Semestre 2025

  LEVEL 4 - Vacancies (4 per convocatoria = 8 total)
    - Associated to specific sedes and modalidades
    - Linked to ISER academic programs (FCAS and FII)

OPTIONS:
  -h, --help            Show this help message
  -S, --structure-only  Only create IOMAD structure (companies + departments)
  -p, --publish         Create convocatorias with 'open' status
  -P, --public          Make convocatorias public (no auth required)
  -d, --delete          Delete all sample data
  -v, --verbose         Show detailed output
  -a, --all-sedes       Create all 16 sedes (default: only 2 for testing)
  --sedes=LIST          Comma-separated list of sedes to create
                        (default: PAMPLONA,CUCUTA)

EXAMPLES:
  # Create structure + convocatorias (only 2 sedes for testing)
  php create_sample_convocatorias.php

  # Create all 16 sedes + convocatorias
  php create_sample_convocatorias.php --all-sedes

  # Create specific sedes
  php create_sample_convocatorias.php --sedes=PAMPLONA,CUCUTA,TIBU

  # Create only IOMAD structure (no convocatorias)
  php create_sample_convocatorias.php --structure-only

  # Create and publish convocatorias
  php create_sample_convocatorias.php --publish --public

  # Delete all sample data
  php create_sample_convocatorias.php --delete

EOT;
    echo $help;
    exit(0);
}

// ============================================================
// DELETE MODE
// ============================================================

if ($options['delete']) {
    cli_heading('Eliminando datos de muestra ISER...');

    // 1. Delete sample convocatorias and vacancies.
    $convs = $DB->get_records_select('local_jobboard_convocatoria',
        "code LIKE 'SAMPLE-%'", null, '', 'id, code, name');

    foreach ($convs as $conv) {
        cli_writeln("Eliminando convocatoria: {$conv->code}");

        // Delete vacancies.
        $vacancies = $DB->get_records('local_jobboard_vacancy', ['convocatoriaid' => $conv->id]);
        foreach ($vacancies as $vac) {
            // Delete applications.
            $apps = $DB->get_records('local_jobboard_application', ['vacancyid' => $vac->id]);
            foreach ($apps as $app) {
                $DB->delete_records('local_jobboard_document', ['applicationid' => $app->id]);
            }
            $DB->delete_records('local_jobboard_application', ['vacancyid' => $vac->id]);
            $DB->delete_records('local_jobboard_vacancy_field', ['vacancyid' => $vac->id]);
        }
        $DB->delete_records('local_jobboard_vacancy', ['convocatoriaid' => $conv->id]);
        $DB->delete_records('local_jobboard_convocatoria', ['id' => $conv->id]);
    }
    cli_writeln("  - Convocatorias eliminadas: " . count($convs));

    // 2. Delete sample vacancies (orphan).
    $orphanvacs = $DB->delete_records_select('local_jobboard_vacancy', "code LIKE 'SAMPLE-%'");

    // 3. Delete IOMAD departments and companies.
    $companies = $DB->get_records_select('company', "shortname LIKE 'ISER-%'", null, '', 'id, shortname, name');

    foreach ($companies as $company) {
        cli_writeln("Eliminando company: {$company->shortname}");

        // Delete departments.
        $DB->delete_records('department', ['company' => $company->id]);

        // Delete company.
        $DB->delete_records('company', ['id' => $company->id]);
    }
    cli_writeln("  - Companies eliminadas: " . count($companies));

    cli_writeln("\n¡Datos de muestra eliminados!");
    exit(0);
}

// ============================================================
// DETERMINE WHICH SEDES TO CREATE
// ============================================================

$sedesToCreate = [];
if ($options['all-sedes']) {
    $sedesToCreate = array_keys($ISER_SEDES);
} else {
    $sedeList = explode(',', $options['sedes']);
    foreach ($sedeList as $sede) {
        $sede = strtoupper(trim($sede));
        if (isset($ISER_SEDES[$sede])) {
            $sedesToCreate[] = $sede;
        }
    }
}

if (empty($sedesToCreate)) {
    $sedesToCreate = ['PAMPLONA', 'CUCUTA'];
}

cli_heading('ISER Job Board - Generador de Datos de Muestra');
cli_writeln("Sedes a crear: " . implode(', ', $sedesToCreate));
cli_writeln("Modalidades por sede: " . count($ISER_MODALIDADES));
cli_writeln("");

// ============================================================
// PHASE 1: CREATE IOMAD STRUCTURE
// ============================================================

cli_writeln("== FASE 1: Estructura IOMAD (Companies + Departments) ==\n");

$companyMap = [];
$departmentMap = [];
$createdCompanies = 0;
$createdDepartments = 0;

foreach ($sedesToCreate as $sedeKey) {
    $sedeInfo = $ISER_SEDES[$sedeKey];

    // Check if company exists.
    $company = $DB->get_record('company', ['shortname' => $sedeInfo['shortname']]);

    if ($company) {
        cli_writeln("✓ Company existe: {$sedeInfo['name']} (ID: {$company->id})");
        $companyMap[$sedeKey] = $company->id;
    } else {
        // Create company.
        $companyRecord = new stdClass();
        $companyRecord->name = $sedeInfo['name'];
        $companyRecord->shortname = $sedeInfo['shortname'];
        $companyRecord->code = $sedeInfo['code'];
        $companyRecord->city = $sedeInfo['city'];
        $companyRecord->country = 'CO';
        $companyRecord->lang = 'es';
        $companyRecord->timezone = 'America/Bogota';
        $companyRecord->theme = '';
        $companyRecord->category = 0;
        $companyRecord->profileid = 0;
        $companyRecord->supervisorprofileid = 0;
        $companyRecord->departmentprofileid = 0;

        $companyId = $DB->insert_record('company', $companyRecord);
        $companyMap[$sedeKey] = $companyId;
        $createdCompanies++;

        cli_writeln("✓ Company creada: {$sedeInfo['name']} (ID: {$companyId})");

        // Create root department.
        $rootDept = new stdClass();
        $rootDept->name = $sedeInfo['name'];
        $rootDept->shortname = $sedeInfo['shortname'];
        $rootDept->company = $companyId;
        $rootDept->parent = 0;
        $DB->insert_record('department', $rootDept);
    }

    // Create departments (modalidades).
    $companyId = $companyMap[$sedeKey];
    $rootDept = $DB->get_record('department', ['company' => $companyId, 'parent' => 0]);
    $parentId = $rootDept ? $rootDept->id : 0;

    foreach ($ISER_MODALIDADES as $modKey => $modInfo) {
        $deptKey = $sedeKey . '_' . $modKey;

        // Check if department exists.
        $dept = $DB->get_record('department', [
            'company' => $companyId,
            'shortname' => $modInfo['shortname'],
        ]);

        if ($dept) {
            $departmentMap[$deptKey] = $dept->id;
            if ($options['verbose']) {
                cli_writeln("  ✓ Dept existe: {$modInfo['name']} (ID: {$dept->id})");
            }
        } else {
            $deptRecord = new stdClass();
            $deptRecord->name = $modInfo['name'];
            $deptRecord->shortname = $modInfo['shortname'];
            $deptRecord->company = $companyId;
            $deptRecord->parent = $parentId;

            $deptId = $DB->insert_record('department', $deptRecord);
            $departmentMap[$deptKey] = $deptId;
            $createdDepartments++;

            if ($options['verbose']) {
                cli_writeln("  ✓ Dept creado: {$modInfo['name']} (ID: {$deptId})");
            }
        }
    }
}

cli_writeln("\nResumen estructura IOMAD:");
cli_writeln("  - Companies creadas: {$createdCompanies}");
cli_writeln("  - Departments creados: {$createdDepartments}");

if ($options['structure-only']) {
    cli_writeln("\n¡Estructura IOMAD creada! (--structure-only)");
    exit(0);
}

// ============================================================
// PHASE 2: CREATE CONVOCATORIAS
// ============================================================

cli_writeln("\n== FASE 2: Convocatorias y Vacantes ==\n");

$now = time();
$status = $options['publish'] ? 'open' : 'draft';
$vacancyStatus = $options['publish'] ? 'published' : 'draft';
$publicationType = $options['public'] ? 'public' : 'internal';

$createdConvs = 0;
$createdVacs = 0;

foreach ($SAMPLE_CONVOCATORIAS as $convData) {
    // Check if already exists.
    if ($DB->record_exists('local_jobboard_convocatoria', ['code' => $convData['code']])) {
        cli_writeln("Convocatoria ya existe: {$convData['code']} - omitiendo");
        continue;
    }

    // Create convocatoria.
    $convocatoria = new stdClass();
    $convocatoria->code = $convData['code'];
    $convocatoria->name = $convData['name'];
    $convocatoria->description = $convData['description'];
    $convocatoria->brief_description = $convData['brief_description'];
    $convocatoria->startdate = $now;
    $convocatoria->enddate = $now + (90 * 24 * 60 * 60); // 90 days.
    $convocatoria->status = $status;
    $convocatoria->publicationtype = $publicationType;
    $convocatoria->allow_multiple_applications = 1;
    $convocatoria->max_applications_per_user = 2;
    $convocatoria->createdby = 2;
    $convocatoria->timecreated = $now;

    $convocatoriaId = $DB->insert_record('local_jobboard_convocatoria', $convocatoria);
    $createdConvs++;

    cli_writeln("✓ Convocatoria creada: {$convData['code']}");
    cli_writeln("  - Nombre: {$convData['name']}");
    cli_writeln("  - Estado: {$status} | Tipo: {$publicationType}");

    // Create vacancies.
    $vacNum = 1;
    foreach ($convData['vacancies'] as $vacData) {
        $faculty = $vacData['faculty'];
        $programKey = $vacData['program'];
        $sedeKey = $vacData['sede'];
        $modalityKey = $vacData['modality'];

        // Skip if sede not in our list.
        if (!isset($companyMap[$sedeKey])) {
            if ($options['verbose']) {
                cli_writeln("  - Omitiendo vacante (sede {$sedeKey} no creada)");
            }
            continue;
        }

        $programInfo = $ISER_PROGRAMAS[$faculty]['programs'][$programKey] ?? null;
        if (!$programInfo) {
            continue;
        }

        $sedeInfo = $ISER_SEDES[$sedeKey];
        $modalityInfo = $ISER_MODALIDADES[$modalityKey];

        // Build vacancy code.
        $vacCode = "SAMPLE-{$faculty}-{$programKey}-" . str_pad($vacNum, 2, '0', STR_PAD_LEFT);

        // Check if exists.
        if ($DB->record_exists('local_jobboard_vacancy', ['code' => $vacCode])) {
            continue;
        }

        // Build description.
        $coursesHtml = "<ul>\n";
        foreach ($programInfo['courses'] as $course) {
            $coursesHtml .= "  <li>{$course}</li>\n";
        }
        $coursesHtml .= "</ul>";

        $descHtml = <<<HTML
<div class="vacancy-description">
    <h4>{$programInfo['name']}</h4>
    <p><strong>Facultad:</strong> {$ISER_PROGRAMAS[$faculty]['name']}</p>
    <p><strong>Tipo de contrato:</strong> {$vacData['contracttype']}</p>
    <p><strong>Sede:</strong> {$sedeInfo['name']}</p>
    <p><strong>Modalidad:</strong> {$modalityInfo['name']}</p>

    <h5>Cursos a dictar:</h5>
    {$coursesHtml}
</div>
HTML;

        // Requirements.
        $reqHtml = <<<HTML
<ul>
    <li>Título profesional universitario acorde al perfil del programa</li>
    <li>Título de posgrado (preferible)</li>
    <li>Experiencia docente mínima de 1 año en educación superior</li>
    <li>No tener inhabilidades ni incompatibilidades para contratar con el Estado</li>
    <li>Disponibilidad para la sede {$sedeInfo['city']} en modalidad {$modalityInfo['name']}</li>
</ul>
HTML;

        // Create vacancy.
        $vacancy = new stdClass();
        $vacancy->code = $vacCode;
        $vacancy->title = "Docente {$programInfo['name']}";
        $vacancy->description = $descHtml;
        $vacancy->department = $ISER_PROGRAMAS[$faculty]['name'];
        $vacancy->modality = $modalityInfo['name'];
        $vacancy->location = $sedeInfo['city'];
        $vacancy->positions = $vacData['positions'];
        $vacancy->requirements = $reqHtml;
        $vacancy->contracttype = $vacData['contracttype'];
        $vacancy->duration = 'Semestre académico';
        $vacancy->convocatoriaid = $convocatoriaId;
        $vacancy->companyid = $companyMap[$sedeKey];
        $vacancy->departmentid = $departmentMap[$sedeKey . '_' . $modalityKey] ?? null;
        $vacancy->status = $vacancyStatus;
        $vacancy->publicationtype = $publicationType;
        $vacancy->opendate = $now;
        $vacancy->closedate = $now + (90 * 24 * 60 * 60);
        $vacancy->createdby = 2;
        $vacancy->timecreated = $now;

        $DB->insert_record('local_jobboard_vacancy', $vacancy);
        $createdVacs++;
        $vacNum++;

        if ($options['verbose']) {
            cli_writeln("    ✓ Vacante: {$vacCode}");
            cli_writeln("      Programa: {$programInfo['name']}");
            cli_writeln("      Sede: {$sedeInfo['city']} | Modalidad: {$modalityInfo['name']}");
            cli_writeln("      Posiciones: {$vacData['positions']}");
        } else {
            cli_writeln("    ✓ Vacante: Docente {$programInfo['name']} ({$vacData['positions']} pos.)");
        }
    }
}

// ============================================================
// SUMMARY
// ============================================================

cli_writeln("\n" . str_repeat('=', 60));
cli_writeln("¡Proceso completado!");
cli_writeln(str_repeat('=', 60));
cli_writeln("Estructura IOMAD:");
cli_writeln("  - Companies (Sedes): " . count($companyMap));
cli_writeln("  - Departments (Modalidades): " . count($departmentMap));
cli_writeln("\nDatos Jobboard:");
cli_writeln("  - Convocatorias creadas: {$createdConvs}");
cli_writeln("  - Vacantes creadas: {$createdVacs}");
cli_writeln(str_repeat('=', 60));

if ($status === 'draft') {
    cli_writeln("\nNOTA: Convocatorias creadas en estado 'borrador'.");
    cli_writeln("Use --publish para crearlas en estado 'abierto'.");
}

if ($publicationType === 'internal') {
    cli_writeln("\nNOTA: Convocatorias son 'internas' (requieren autenticación).");
    cli_writeln("Use --public para hacerlas públicas.");
}

cli_writeln("\nPara ver las convocatorias:");
cli_writeln("  /local/jobboard/index.php?view=convocatorias");
cli_writeln("");
