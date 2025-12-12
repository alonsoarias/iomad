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
 * Unified CLI script for importing professional profiles as vacancies.
 *
 * This script automates the complete process:
 * 1. Creates IOMAD companies (sedes) if needed
 * 2. Creates IOMAD departments (modalidades) within each company
 * 3. Creates the convocatoria
 * 4. Creates vacancies associated to companies
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// Check if Moodle is available.
$configfile = __DIR__ . '/../../../config.php';
$moodleavailable = file_exists($configfile);

if ($moodleavailable) {
    define('CLI_SCRIPT', true);
    require($configfile);
    require_once($CFG->libdir . '/clilib.php');
} else {
    // Standalone mode - only parsing, no database import.
    define('MOODLE_INTERNAL', true);
    define('CLI_SCRIPT', true);
}

// Helper functions for standalone mode.
if (!$moodleavailable) {
    function cli_get_params($longoptions, $shortoptions) {
        // $shortoptions is already in format [short => long], no need to flip.
        $options = [];
        foreach ($longoptions as $key => $default) {
            $options[$key] = $default;
        }
        // Build short options spec: boolean options get no colon, others get colon.
        $shortspec = '';
        foreach ($shortoptions as $short => $long) {
            $shortspec .= is_bool($longoptions[$long]) ? $short : $short . ':';
        }
        // Build long options spec: boolean options get no colon, others get colon.
        $longspec = array_map(fn($k) => is_bool($longoptions[$k]) ? $k : $k . ':', array_keys($longoptions));
        $args = getopt($shortspec, $longspec);
        foreach ($args as $key => $value) {
            // Map short key to long key using $shortoptions directly.
            $longkey = $shortoptions[$key] ?? $key;
            // Use array_key_exists because isset returns false for null values.
            if (array_key_exists($longkey, $options)) {
                $options[$longkey] = is_bool($longoptions[$longkey]) ? true : $value;
            }
        }
        return [$options, []];
    }
    function cli_heading($text) {
        echo "\n" . str_repeat('=', 60) . "\n$text\n" . str_repeat('=', 60) . "\n";
    }
    function cli_error($text) { echo "ERROR: $text\n"; exit(1); }
    function cli_problem($text) { echo "WARNING: $text\n"; }
}

// CLI options.
list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'input' => null,
    'csv' => null,
    'json' => null,
    'export-csv-template' => false,
    'convocatoria' => null,
    'convocatoria-name' => null,
    'convocatoria-code' => null,
    'convocatoria-desc' => null,
    'create-structure' => false,
    'company' => null,
    'department' => null,
    'opendate' => null,
    'closedate' => null,
    'dryrun' => false,
    'update' => false,
    'status' => 'draft',
    'publish' => false,
    'public' => false,
    'reset' => false,
    'reset-convocatorias' => false,
    'verbose' => false,
    'export-json' => null,
    'create-sample' => false,
], [
    'h' => 'help',
    'i' => 'input',
    'c' => 'convocatoria',
    'C' => 'create-structure',
    'o' => 'opendate',
    'e' => 'closedate',
    'd' => 'dryrun',
    'u' => 'update',
    's' => 'status',
    'p' => 'publish',
    'P' => 'public',
    'r' => 'reset',
    'v' => 'verbose',
    'j' => 'export-json',
    'x' => 'csv',
    'J' => 'json',
    'T' => 'export-csv-template',
    'S' => 'create-sample',
]);

if (!empty($unrecognized) && $moodleavailable) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$moodlemode = $moodleavailable ? 'MOODLE MODE (full import)' : 'STANDALONE MODE (parsing only)';

$help = <<<EOT
============================================================
ISER Job Board - Profile Import CLI v2.2
============================================================
Mode: $moodlemode

Automated import of professional profiles into the local_jobboard vacancy system.
Supports importing from JSON, CSV, or extracted text files.

This CLI can automatically create the complete IOMAD structure:
- Companies (16 Centros Tutoriales): PAMPLONA, CUCUTA, TIBU, OCANA, TOLEDO, ELTARRA,
  SARDINATA, SANVICENTE, PUEBLOBELLO, SANPABLO, SANTAROSA, FUNDACION, CIMITARRA,
  SALAZAR, TAME, SARAVENA
- Departments (4 Modalidades per Company): PRESENCIAL, A DISTANCIA, VIRTUAL, HÍBRIDA

USAGE:
  php cli.php [options]

BASIC OPTIONS:
  -h, --help              Show this help message
  -i, --input=DIR         Input directory with .txt files
                          (default: PERFILESPROFESORES_TEXT)
  -J, --json=FILE         Import vacancies from JSON file (RECOMMENDED)
  -x, --csv=FILE          Import vacancies from CSV file
  -T, --export-csv-template  Generate a CSV template file for import
  -j, --export-json=FILE  Export parsed data to JSON file
  -v, --verbose           Show detailed output

JSON IMPORT (RECOMMENDED):
  The --json option imports from a pre-extracted JSON file like perfiles_2026.json.
  This is the most reliable method as it uses properly extracted DOCX data.

  Example:
    php cli.php --json=perfiles_2026.json --create-structure --publish --public

CSV IMPORT:
  The --csv option allows importing vacancies from a CSV file.
  Use --export-csv-template to generate a template with the correct format.

  CSV columns: code,contracttype,program,profile,courses,location,modality,faculty

  Example:
    php cli.php --export-csv-template > template.csv
    # Edit template.csv with your data
    php cli.php --csv=template.csv --create-structure --publish --public

MOODLE-ONLY OPTIONS:
  -C, --create-structure  AUTO-CREATE IOMAD companies (sedes) and departments
                          (modalidades) based on profile data
  -S, --create-sample     CREATE SAMPLE VACANCIES (4 per sede) without input file
                          Automatically includes --create-structure behavior
  -p, --publish           AUTO-CREATE convocatoria and PUBLISH vacancies
  -P, --public            Make vacancies PUBLIC (visible without login)
  -r, --reset             DELETE all existing vacancies before import
  --reset-convocatorias   Also delete convocatorias (use with --reset)
  -c, --convocatoria=ID   Use existing convocatoria ID
  --convocatoria-name=NAME  Name for new convocatoria (with --publish)
  --convocatoria-code=CODE  Code for new convocatoria (with --publish)
  --convocatoria-desc=DESC  Description for new convocatoria
  --company=ID            Default IOMAD company ID (if not using --create-structure)
  --department=ID         Default IOMAD department ID
  -o, --opendate=DATE     Opening date (YYYY-MM-DD), default: today
  -e, --closedate=DATE    Closing date (YYYY-MM-DD), default: +30 days
  -d, --dryrun            Simulate import without creating records
  -u, --update            Update existing vacancies (match by code)
  -s, --status=STATUS     Initial status: draft|published (default: draft)

EXAMPLES:
  # CREATE SAMPLE DATA: 4 vacancies per sede (no input file needed)
  php cli.php --create-sample --publish --public

  # Full import from text files with structure creation
  php cli.php --create-structure --publish --public

  # Import from CSV file
  php cli.php --csv=vacancies.csv --create-structure --publish --public

  # With custom convocatoria
  php cli.php --create-structure --publish --public \\
      --convocatoria-name="Convocatoria Docentes 2026-1" \\
      --opendate=2026-01-15 --closedate=2026-02-15

  # FULL RESET and reimport
  php cli.php --reset --reset-convocatorias --create-structure --publish --public

  # Parse only (standalone mode)
  php cli.php --export-json=perfiles.json --verbose

STRUCTURE CREATED (IOMAD Hierarchy):
  LEVEL 1 - Companies (16 Centros Tutoriales):
    - ISER Sede Pamplona (PAMPLONA) - Sede Principal
    - ISER Centro Tutorial Cúcuta (CUCUTA)
    - ISER Centro Tutorial Tibú (TIBU)
    - ISER Centro Tutorial Ocaña (OCANA)
    - ISER Centro Tutorial Toledo (TOLEDO)
    - ISER Centro Tutorial El Tarra (ELTARRA)
    - ISER Centro Tutorial Sardinata (SARDINATA)
    - ISER Centro Tutorial San Vicente (SANVICENTE)
    - ISER Centro Tutorial Pueblo Bello (PUEBLOBELLO)
    - ISER Centro Tutorial San Pablo (SANPABLO)
    - ISER Centro Tutorial Santa Rosa (SANTAROSA)
    - ISER Centro Tutorial Fundación (FUNDACION)
    - ISER Centro Tutorial Cimitarra (CIMITARRA)
    - ISER Centro Tutorial Salazar (SALAZAR)
    - ISER Centro Tutorial Tame (TAME)
    - ISER Centro Tutorial Saravena (SARAVENA)

  LEVEL 2 - Departments per Company (4 Modalidades Educativas):
    - Presencial (PRESENCIAL)
    - A Distancia (DISTANCIA)
    - Virtual (VIRTUAL)
    - Híbrida (HIBRIDA)

EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

// ============================================================
// CSV TEMPLATE EXPORT
// ============================================================
if ($options['export-csv-template']) {
    $template = <<<CSV
code,contracttype,program,profile,courses,location,modality,faculty
FCAS-01,OCASIONAL TIEMPO COMPLETO,TECNOLOGÍA EN GESTIÓN COMUNITARIA,PROFESIONAL EN TRABAJO SOCIAL,"SISTEMATIZACIÓN DE EXPERIENCIAS|SUJETO Y FAMILIA|DIRECCIÓN DE TRABAJO DE GRADO",PAMPLONA,PRESENCIAL,FCAS
FCAS-02,CATEDRA,TECNOLOGÍA EN GESTIÓN EMPRESARIAL,ADMINISTRADOR DE EMPRESAS CON POSGRADO EN ÁREAS AFINES,"EMPRENDIMIENTO|ADMINISTRACIÓN GENERAL",PAMPLONA,A DISTANCIA,FCAS
FII-01,OCASIONAL TIEMPO COMPLETO,TECNOLOGÍA EN GESTIÓN INDUSTRIAL,INGENIERO INDUSTRIAL,"ERGONOMÍA|GESTIÓN DE LA SEGURIDAD Y SALUD EN EL TRABAJO|GESTIÓN DEL TALENTO HUMANO",CUCUTA,PRESENCIAL,FII
CSV;
    echo $template . "\n";
    echo "\n# CSV IMPORT INSTRUCTIONS:\n";
    echo "# ========================\n";
    echo "# 1. Remove these comment lines and the example rows\n";
    echo "# 2. Add your vacancy data following the same format\n";
    echo "# 3. Columns:\n";
    echo "#    - code: Unique code (e.g., FCAS-01, FII-15)\n";
    echo "#    - contracttype: OCASIONAL TIEMPO COMPLETO or CATEDRA\n";
    echo "#    - program: Academic program name\n";
    echo "#    - profile: Professional profile required\n";
    echo "#    - courses: Courses separated by | (pipe character)\n";
    echo "#    - location: PAMPLONA, CUCUTA, TIBU, OCANA, TOLEDO, ELTARRA, SARDINATA,\n";
    echo "#                SANVICENTE, PUEBLOBELLO, SANPABLO, SANTAROSA, FUNDACION,\n";
    echo "#                CIMITARRA, SALAZAR, TAME, SARAVENA\n";
    echo "#    - modality: PRESENCIAL, A DISTANCIA, VIRTUAL, HIBRIDA\n";
    echo "#    - faculty: FCAS or FII\n";
    echo "# 4. Save and run: php cli.php --csv=yourfile.csv --create-structure --publish\n";
    exit(0);
}

// ============================================================
// CONFIGURATION
// ============================================================

// Define ISER structure - All locations from profile files.
// Names simplified to just the city name for cleaner display.
$ISER_SEDES = [
    'PAMPLONA' => [
        'name' => 'Pamplona (Sede Principal)',
        'shortname' => 'PAMPLONA',
        'city' => 'Pamplona',
        'code' => 'ISER-PAM',
    ],
    'CUCUTA' => [
        'name' => 'Cúcuta',
        'shortname' => 'CUCUTA',
        'city' => 'Cúcuta',
        'code' => 'ISER-CUC',
    ],
    'TIBU' => [
        'name' => 'Tibú',
        'shortname' => 'TIBU',
        'city' => 'Tibú',
        'code' => 'ISER-TIB',
    ],
    'SANVICENTE' => [
        'name' => 'San Vicente del Chucurí',
        'shortname' => 'SANVICENTE',
        'city' => 'San Vicente del Chucurí',
        'code' => 'ISER-SVC',
    ],
    'ELTARRA' => [
        'name' => 'El Tarra',
        'shortname' => 'ELTARRA',
        'city' => 'El Tarra',
        'code' => 'ISER-TAR',
    ],
    'OCANA' => [
        'name' => 'Ocaña',
        'shortname' => 'OCANA',
        'city' => 'Ocaña',
        'code' => 'ISER-OCA',
    ],
    'PUEBLOBELLO' => [
        'name' => 'Pueblo Bello',
        'shortname' => 'PUEBLOBELLO',
        'city' => 'Pueblo Bello',
        'code' => 'ISER-PBL',
    ],
    'SANPABLO' => [
        'name' => 'San Pablo (Sur de Bolívar)',
        'shortname' => 'SANPABLO',
        'city' => 'San Pablo',
        'code' => 'ISER-SPB',
    ],
    'SANTAROSA' => [
        'name' => 'Santa Rosa del Sur',
        'shortname' => 'SANTAROSA',
        'city' => 'Santa Rosa del Sur',
        'code' => 'ISER-SRS',
    ],
    'TAME' => [
        'name' => 'Tame',
        'shortname' => 'TAME',
        'city' => 'Tame',
        'code' => 'ISER-TAM',
    ],
    'FUNDACION' => [
        'name' => 'Fundación',
        'shortname' => 'FUNDACION',
        'city' => 'Fundación',
        'code' => 'ISER-FUN',
    ],
    'CIMITARRA' => [
        'name' => 'Cimitarra',
        'shortname' => 'CIMITARRA',
        'city' => 'Cimitarra',
        'code' => 'ISER-CIM',
    ],
    'SALAZAR' => [
        'name' => 'Salazar',
        'shortname' => 'SALAZAR',
        'city' => 'Salazar',
        'code' => 'ISER-SAL',
    ],
    'TOLEDO' => [
        'name' => 'Toledo',
        'shortname' => 'TOLEDO',
        'city' => 'Toledo',
        'code' => 'ISER-TOL',
    ],
    'SARDINATA' => [
        'name' => 'Sardinata',
        'shortname' => 'SARDINATA',
        'city' => 'Sardinata',
        'code' => 'ISER-SAR',
    ],
    'SARAVENA' => [
        'name' => 'Saravena',
        'shortname' => 'SARAVENA',
        'city' => 'Saravena',
        'code' => 'ISER-SRV',
    ],
];

// Modalidades educativas según arquitectura IOMAD ISER.
$ISER_MODALIDADES = [
    'PRESENCIAL' => ['name' => 'Presencial', 'shortname' => 'PRESENCIAL'],
    'DISTANCIA' => ['name' => 'A Distancia', 'shortname' => 'DISTANCIA'],
    'VIRTUAL' => ['name' => 'Virtual', 'shortname' => 'VIRTUAL'],
    'HIBRIDA' => ['name' => 'Híbrida', 'shortname' => 'HIBRIDA'],
];

$plugindir = __DIR__ . '/..';
$inputdir = $options['input'] ?? $plugindir . '/PERFILESPROFESORES_TEXT';
$csvfile = $options['csv'] ?? null;
$jsonfile = $options['json'] ?? null;

// Validate input source (skip if --create-sample).
$createsample = $options['create-sample'];
if ($createsample) {
    // --create-sample implies --create-structure.
    $options['create-structure'] = true;
} else if ($jsonfile) {
    // Check relative to plugin dir if not absolute.
    if (!file_exists($jsonfile) && file_exists($plugindir . '/' . $jsonfile)) {
        $jsonfile = $plugindir . '/' . $jsonfile;
    }
    if (!file_exists($jsonfile)) {
        cli_error("JSON file not found: $jsonfile");
    }
} else if ($csvfile) {
    if (!file_exists($csvfile)) {
        cli_error("CSV file not found: $csvfile");
    }
} else if (!is_dir($inputdir)) {
    cli_error("Input directory not found: $inputdir");
}

// Dates.
$now = time();
$opendate = $now;
$closedate = $now + (30 * 24 * 60 * 60);

if (!empty($options['opendate'])) {
    $parsed = strtotime($options['opendate']);
    if ($parsed === false) cli_error("Invalid opendate format: {$options['opendate']}");
    $opendate = $parsed;
}
if (!empty($options['closedate'])) {
    $parsed = strtotime($options['closedate']);
    if ($parsed === false) cli_error("Invalid closedate format: {$options['closedate']}");
    $closedate = $parsed;
}

$validstatuses = ['draft', 'published'];
if (!in_array($options['status'], $validstatuses)) {
    cli_error("Invalid status: {$options['status']}. Must be: " . implode(', ', $validstatuses));
}

$verbose = $options['verbose'];
$dryrun = $options['dryrun'];
$shouldpublish = $options['publish'];
if ($shouldpublish) {
    $options['status'] = 'published';
}

// ============================================================
// HEADER
// ============================================================

cli_heading('ISER Job Board - Profile Import v2.2');
if ($createsample) {
    echo "Mode: CREATE SAMPLE DATA (4 vacancies per sede)\n";
} else if ($jsonfile) {
    echo "Input JSON: $jsonfile\n";
} else if ($csvfile) {
    echo "Input CSV: $csvfile\n";
} else {
    echo "Input directory: $inputdir\n";
}
echo "Open date: " . date('Y-m-d', $opendate) . "\n";
echo "Close date: " . date('Y-m-d', $closedate) . "\n";
echo "Create structure: " . ($options['create-structure'] ? 'YES (companies + departments)' : 'NO') . "\n";
echo "Create sample: " . ($createsample ? 'YES (4 vacancies per sede)' : 'NO') . "\n";
echo "Auto-publish: " . ($shouldpublish ? 'YES' : 'NO') . "\n";
echo "Publication type: " . ($options['public'] ? 'PUBLIC' : 'INTERNAL') . "\n";
echo "Status: {$options['status']}\n";
echo "Dry run: " . ($dryrun ? 'YES' : 'NO') . "\n";
if ($options['reset']) {
    echo "RESET MODE: YES\n";
}
echo "\n";

// ============================================================
// PHASE 1: PARSE INPUT (JSON, CSV, or Text Files)
// ============================================================

$allprofiles = [];
$parsestats = ['files' => 0, 'profiles' => 0, 'fcas' => 0, 'fii' => 0];
$locationstats = [];

// Sample programs for generating sample data.
$SAMPLE_PROGRAMS = [
    'FCAS' => [
        'name' => 'Facultad de Ciencias Administrativas y Sociales',
        'programs' => [
            'TGC' => [
                'name' => 'Tecnología en Gestión Comunitaria',
                'courses' => ['Sistematización de Experiencias', 'Sujeto y Familia', 'Dirección de Trabajo de Grado', 'Desarrollo Comunitario'],
            ],
            'TGE' => [
                'name' => 'Tecnología en Gestión Empresarial',
                'courses' => ['Emprendimiento', 'Administración General', 'Contabilidad Básica', 'Mercadeo'],
            ],
        ],
    ],
    'FII' => [
        'name' => 'Facultad de Ingenierías e Innovación',
        'programs' => [
            'TGI' => [
                'name' => 'Tecnología en Gestión Industrial',
                'courses' => ['Ergonomía', 'Gestión de Seguridad y Salud en el Trabajo', 'Gestión del Talento Humano', 'Control de Calidad'],
            ],
            'TGINF' => [
                'name' => 'Tecnología en Gestión Informática',
                'courses' => ['Programación I', 'Bases de Datos', 'Redes de Computadores', 'Desarrollo Web'],
            ],
        ],
    ],
];

if ($createsample) {
    // Generate sample vacancies: 4 per sede.
    cli_heading('Phase 1: Generating Sample Vacancy Data');

    $modalidades = ['PRESENCIAL', 'DISTANCIA', 'VIRTUAL', 'HIBRIDA'];
    $contracttypes = ['CATEDRA', 'OCASIONAL TIEMPO COMPLETO'];
    $sedecount = 0;
    $sedekeys = array_keys($ISER_SEDES);
    $totalsedes = count($sedekeys);

    foreach ($ISER_SEDES as $sedekey => $sedeinfo) {
        $sedecount++;
        $vacnum = 0;

        // Distribute sedes between 2 convocatorias: first half → closed, second half → open.
        $convocatoriaindex = ($sedecount <= ceil($totalsedes / 2)) ? 0 : 1;

        // Generate 4 vacancies per sede (1 per modalidad, rotating programs).
        $allprograms = [];
        foreach ($SAMPLE_PROGRAMS as $faculty => $facultydata) {
            foreach ($facultydata['programs'] as $progkey => $progdata) {
                $allprograms[] = [
                    'faculty' => $faculty,
                    'progkey' => $progkey,
                    'progdata' => $progdata,
                    'facultyname' => $facultydata['name'],
                ];
            }
        }

        foreach ($modalidades as $modidx => $modality) {
            $vacnum++;
            $prog = $allprograms[$modidx % count($allprograms)];
            $contracttype = $contracttypes[$vacnum % 2];

            $code = "SAMPLE-{$sedekey}-{$prog['faculty']}-{$prog['progkey']}-" . str_pad($vacnum, 2, '0', STR_PAD_LEFT);

            $allprofiles[$code] = [
                'code' => $code,
                'program' => $prog['progdata']['name'],
                'profile' => "Docente para el programa de {$prog['progdata']['name']}",
                'courses' => $prog['progdata']['courses'],
                'faculty' => $prog['faculty'],
                'location' => $sedekey,
                'modality' => $modality,
                'contracttype' => $contracttype,
                'convocatoria_index' => $convocatoriaindex,
            ];

            $parsestats['profiles']++;
            if ($prog['faculty'] === 'FCAS') $parsestats['fcas']++;
            else $parsestats['fii']++;

            $locationstats[$sedekey] = ($locationstats[$sedekey] ?? 0) + 1;
        }

        if ($verbose) {
            $convlabel = $convocatoriaindex === 0 ? 'Conv. Cerrada' : 'Conv. Abierta';
            echo "  {$sedeinfo['name']}: 4 vacancies ({$convlabel})\n";
        }
    }

    echo "\nSample data generation complete:\n";
    echo "  Sedes processed: $sedecount\n";
    echo "  Total vacancies: {$parsestats['profiles']}\n";
    echo "    - FCAS: {$parsestats['fcas']}\n";
    echo "    - FII: {$parsestats['fii']}\n";
    echo "  Distribution:\n";
    echo "    - Convocatoria Cerrada: " . (ceil($totalsedes / 2) * 4) . " vacancies\n";
    echo "    - Convocatoria Abierta: " . (floor($totalsedes / 2) * 4) . " vacancies\n";
    echo "\n  By location:\n";
    foreach ($locationstats as $loc => $cnt) {
        echo "    - $loc: $cnt\n";
    }

} else if ($jsonfile) {
    // Import from JSON file.
    cli_heading('Phase 1: Importing from JSON File');

    $jsoncontent = file_get_contents($jsonfile);
    $jsondata = json_decode($jsoncontent, true);

    if (!$jsondata || !isset($jsondata['vacancies'])) {
        cli_error("Invalid JSON format: missing 'vacancies' array");
    }

    echo "JSON source: " . ($jsondata['source'] ?? 'Unknown') . "\n";
    echo "Generated: " . ($jsondata['generated'] ?? 'Unknown') . "\n\n";

    foreach ($jsondata['vacancies'] as $profile) {
        $code = $profile['code'] ?? '';
        if (empty($code)) continue;

        $allprofiles[$code] = $profile;
        $parsestats['profiles']++;
        if (strpos($code, 'FCAS') === 0) $parsestats['fcas']++;
        else if (strpos($code, 'FII') === 0) $parsestats['fii']++;

        $loc = $profile['location'] ?? 'PAMPLONA';
        $locationstats[$loc] = ($locationstats[$loc] ?? 0) + 1;
    }
    $parsestats['files'] = 1;

    echo "JSON import complete:\n";
    echo "  Profiles imported: {$parsestats['profiles']}\n";
    echo "    - FCAS: {$parsestats['fcas']}\n";
    echo "    - FII: {$parsestats['fii']}\n";
    echo "\n  By location:\n";
    foreach ($locationstats as $loc => $cnt) {
        echo "    - $loc: $cnt\n";
    }

} else if ($csvfile) {
    // Import from CSV.
    cli_heading('Phase 1: Importing from CSV File');

    $profiles = parse_csv_file($csvfile, $verbose);
    $allprofiles = $profiles;

    foreach ($profiles as $code => $profile) {
        $parsestats['profiles']++;
        if (strpos($code, 'FCAS') === 0) $parsestats['fcas']++;
        else if (strpos($code, 'FII') === 0) $parsestats['fii']++;

        $loc = $profile['location'] ?? 'PAMPLONA';
        $locationstats[$loc] = ($locationstats[$loc] ?? 0) + 1;
    }
    $parsestats['files'] = 1;

    echo "\nCSV import complete:\n";
    echo "  Profiles imported: {$parsestats['profiles']}\n";
    echo "    - FCAS: {$parsestats['fcas']}\n";
    echo "    - FII: {$parsestats['fii']}\n";
    echo "\n  By location:\n";
    foreach ($locationstats as $loc => $cnt) {
        echo "    - $loc: $cnt\n";
    }

} else {
    // Parse text files.
    cli_heading('Phase 1: Parsing Profile Text Files');

    $files = glob($inputdir . '/*.txt');
    $files = array_filter($files, fn($f) => strpos(basename($f), '_CONSOLIDADO') === false);
    sort($files);

    echo "Found " . count($files) . " text files\n\n";

    foreach ($files as $file) {
        $filename = basename($file);
        $content = file_get_contents($file);

        // Determine location from content.
        $location = extract_location_from_content($content, $filename);

        $profiles = parse_profiles_from_text($content, $filename, $location);
        $count = count($profiles);

        if ($verbose) {
            echo "  $filename: $count profiles (Location: $location)\n";
        }

        foreach ($profiles as $code => $profile) {
            if (!isset($allprofiles[$code])) {
                $allprofiles[$code] = $profile;
                $parsestats['profiles']++;
                if (strpos($code, 'FCAS') === 0) $parsestats['fcas']++;
                else if (strpos($code, 'FII') === 0) $parsestats['fii']++;

                // Track location stats.
                $loc = $profile['location'] ?? 'PAMPLONA';
                $locationstats[$loc] = ($locationstats[$loc] ?? 0) + 1;
            }
        }
        $parsestats['files']++;
    }

    ksort($allprofiles);

    echo "\nParsing complete:\n";
    echo "  Files processed: {$parsestats['files']}\n";
    echo "  Profiles found: {$parsestats['profiles']}\n";
    echo "    - FCAS: {$parsestats['fcas']}\n";
    echo "    - FII: {$parsestats['fii']}\n";
    echo "\n  By location:\n";
    foreach ($locationstats as $loc => $cnt) {
        echo "    - $loc: $cnt\n";
    }
}

// Export JSON if requested.
if (!empty($options['export-json'])) {
    $exportjsonfile = $options['export-json'];
    $exportjsondata = [
        'generated' => date('Y-m-d H:i:s'),
        'source' => 'PERFILES PROFESORES ISER',
        'stats' => [
            'total_profiles' => count($allprofiles),
            'fcas_profiles' => $parsestats['fcas'],
            'fii_profiles' => $parsestats['fii'],
            'by_location' => $locationstats,
        ],
        'vacancies' => array_values($allprofiles),
    ];
    file_put_contents($exportjsonfile, json_encode($exportjsondata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "\nJSON exported to: $exportjsonfile\n";
}

// ============================================================
// PHASE 2: DATABASE OPERATIONS (Moodle only)
// ============================================================

if (!$moodleavailable) {
    echo "\n";
    cli_heading('Phase 2-5: Skipped (Moodle not available)');
    echo "To import into database, run from Moodle installation.\n";
    exit(0);
}

// ============================================================
// PHASE 2-RESET: DELETE EXISTING DATA
// ============================================================

if ($options['reset']) {
    cli_heading('Phase 2-RESET: Deleting Existing Data');

    $vacancycount = $DB->count_records('local_jobboard_vacancy');
    $convcount = $DB->count_records('local_jobboard_convocatoria');

    echo "Found $vacancycount vacancies";
    if ($options['reset-convocatorias']) echo " and $convcount convocatorias";
    echo " to delete.\n";

    if (!$dryrun) {
        // Delete related data.
        $vacancyids = $DB->get_fieldset_select('local_jobboard_vacancy', 'id', '1=1');
        if (!empty($vacancyids)) {
            list($insql, $params) = $DB->get_in_or_equal($vacancyids, SQL_PARAMS_NAMED, 'vid');
            $appids = $DB->get_fieldset_select('local_jobboard_application', 'id', "vacancyid $insql", $params);
            if (!empty($appids)) {
                list($appinsql, $appparams) = $DB->get_in_or_equal($appids, SQL_PARAMS_NAMED, 'aid');
                $docids = $DB->get_fieldset_select('local_jobboard_document', 'id', "applicationid $appinsql", $appparams);
                if (!empty($docids)) {
                    list($docinsql, $docparams) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED, 'did');
                    $DB->delete_records_select('local_jobboard_doc_validation', "documentid $docinsql", $docparams);
                    $DB->delete_records_select('local_jobboard_document', "id $docinsql", $docparams);
                }
                $DB->delete_records_select('local_jobboard_workflow_log', "applicationid $appinsql", $appparams);
                $DB->delete_records_select('local_jobboard_application', "id $appinsql", $appparams);
            }
            $DB->delete_records_select('local_jobboard_doc_requirement', "vacancyid $insql", $params);
        }
        $DB->delete_records('local_jobboard_vacancy');
        echo "Deleted $vacancycount vacancies\n";

        if ($options['reset-convocatorias']) {
            $DB->delete_records('local_jobboard_convocatoria');
            echo "Deleted $convcount convocatorias\n";
        }
    } else {
        echo "DRY RUN: Would delete data\n";
    }
}

// ============================================================
// PHASE 3: CREATE IOMAD STRUCTURE
// ============================================================

$companymap = []; // location -> company_id
$departmentmap = []; // location_modality -> department_id

if ($options['create-structure']) {
    cli_heading('Phase 3: Creating IOMAD Structure');

    $adminuser = get_admin();

    // Determine which locations are needed.
    $neededlocations = array_unique(array_column($allprofiles, 'location'));
    echo "Locations needed: " . implode(', ', $neededlocations) . "\n\n";

    foreach ($ISER_SEDES as $key => $sedeinfo) {
        // Check if this location is needed.
        if (!in_array($key, $neededlocations) && $key !== 'PAMPLONA') {
            continue;
        }

        // Check if company exists.
        $company = $DB->get_record('company', ['shortname' => $sedeinfo['shortname']]);

        if ($company) {
            echo "Company exists: {$sedeinfo['name']} (ID: {$company->id})\n";
            $companymap[$key] = $company->id;
        } else if (!$dryrun) {
            // Create company.
            $companyrecord = new stdClass();
            $companyrecord->name = $sedeinfo['name'];
            $companyrecord->shortname = $sedeinfo['shortname'];
            $companyrecord->code = $sedeinfo['code'];
            $companyrecord->city = $sedeinfo['city'];
            $companyrecord->country = 'CO';
            $companyrecord->lang = 'es';
            $companyrecord->timezone = 'America/Bogota';
            $companyrecord->theme = '';
            $companyrecord->category = 0;
            $companyrecord->profileid = 0;
            $companyrecord->supervisorprofileid = 0;
            $companyrecord->departmentprofileid = 0;

            $companyid = $DB->insert_record('company', $companyrecord);
            $companymap[$key] = $companyid;
            echo "Created company: {$sedeinfo['name']} (ID: $companyid)\n";

            // Create root department for company.
            $rootdept = new stdClass();
            $rootdept->name = $sedeinfo['name'];
            $rootdept->shortname = $sedeinfo['shortname'];
            $rootdept->company = $companyid;
            $rootdept->parent = 0;
            $rootdeptid = $DB->insert_record('department', $rootdept);
        } else {
            echo "DRY RUN: Would create company {$sedeinfo['name']}\n";
            $companymap[$key] = 0;
        }

        // Create departments (modalidades) for this company.
        if (isset($companymap[$key]) && $companymap[$key] > 0) {
            $companyid = $companymap[$key];

            // Get root department.
            $rootdept = $DB->get_record('department', ['company' => $companyid, 'parent' => 0]);
            $parentid = $rootdept ? $rootdept->id : 0;

            foreach ($ISER_MODALIDADES as $modkey => $modinfo) {
                $deptkey = $key . '_' . $modkey;

                // Check if department exists.
                $dept = $DB->get_record('department', [
                    'company' => $companyid,
                    'shortname' => $modinfo['shortname']
                ]);

                if ($dept) {
                    $departmentmap[$deptkey] = $dept->id;
                    if ($verbose) {
                        echo "  Department exists: {$modinfo['name']} (ID: {$dept->id})\n";
                    }
                } else if (!$dryrun) {
                    $deptrecord = new stdClass();
                    $deptrecord->name = $modinfo['name'];
                    $deptrecord->shortname = $modinfo['shortname'];
                    $deptrecord->company = $companyid;
                    $deptrecord->parent = $parentid;

                    $deptid = $DB->insert_record('department', $deptrecord);
                    $departmentmap[$deptkey] = $deptid;
                    echo "  Created department: {$modinfo['name']} (ID: $deptid)\n";
                }
            }
        }
    }

    echo "\nStructure created:\n";
    echo "  Companies: " . count($companymap) . "\n";
    echo "  Departments: " . count($departmentmap) . "\n";
}

// ============================================================
// PHASE 4: CREATE CONVOCATORIA(S)
// ============================================================

$convocatoriaid = !empty($options['convocatoria']) ? (int) $options['convocatoria'] : null;
$convocatoriaids = []; // Array for multiple convocatorias when using --create-sample.

if ($shouldpublish && empty($convocatoriaid)) {
    cli_heading('Phase 4: Creating Convocatoria(s)');

    $year = date('Y');
    $semester = ceil(date('n') / 6);

    // When --create-sample, create 2 convocatorias: one closed, one open.
    if ($createsample) {
        $adminuser = get_admin();

        // Convocatoria 1: CERRADA (dates in the past).
        $closedcode = "CONV-ISER-{$year}-" . ($semester - 1 > 0 ? $semester - 1 : 1) . "-CLOSED";
        $closedname = "Convocatoria Docentes ISER {$year}-" . ($semester - 1 > 0 ? $semester - 1 : 1) . " (Cerrada)";

        $existingclosed = $DB->get_record('local_jobboard_convocatoria', ['code' => $closedcode]);
        if ($existingclosed) {
            echo "Using existing CLOSED convocatoria: $closedcode (ID: {$existingclosed->id})\n";
            $convocatoriaids[0] = $existingclosed->id;
        } else if (!$dryrun) {
            $closedstart = strtotime('-60 days');
            $closedend = strtotime('-30 days');

            $closedconv = new stdClass();
            $closedconv->code = $closedcode;
            $closedconv->name = $closedname;
            $closedconv->description = "<div class='alert alert-warning'><strong>Esta convocatoria ha finalizado.</strong></div><p>Convocatoria para docentes ocasionales y de cátedra del semestre anterior.</p>";
            $closedconv->startdate = $closedstart;
            $closedconv->enddate = $closedend;
            $closedconv->status = 'closed';
            $closedconv->publicationtype = $options['public'] ? 'public' : 'internal';
            $closedconv->createdby = $adminuser->id;
            $closedconv->timecreated = $now;

            $convocatoriaids[0] = $DB->insert_record('local_jobboard_convocatoria', $closedconv);
            echo "Created CLOSED convocatoria: $closedname\n";
            echo "  Code: $closedcode | ID: {$convocatoriaids[0]}\n";
            echo "  Period: " . date('Y-m-d', $closedstart) . " to " . date('Y-m-d', $closedend) . " (PAST)\n";
        }

        // Convocatoria 2: ABIERTA (current dates).
        $opencode = "CONV-ISER-{$year}-{$semester}-OPEN";
        $openname = "Convocatoria Docentes ISER {$year}-{$semester} (Abierta)";

        $existingopen = $DB->get_record('local_jobboard_convocatoria', ['code' => $opencode]);
        if ($existingopen) {
            echo "Using existing OPEN convocatoria: $opencode (ID: {$existingopen->id})\n";
            $convocatoriaids[1] = $existingopen->id;
        } else if (!$dryrun) {
            $openstart = $now;
            $openend = strtotime('+30 days');

            $openconv = new stdClass();
            $openconv->code = $opencode;
            $openconv->name = $openname;
            $openconv->description = "<div class='alert alert-success'><strong>¡Convocatoria abierta!</strong> Postúlate ahora.</div><p>Convocatoria para docentes ocasionales y de cátedra del semestre actual.</p>";
            $openconv->startdate = $openstart;
            $openconv->enddate = $openend;
            $openconv->status = 'open';
            $openconv->publicationtype = $options['public'] ? 'public' : 'internal';
            $openconv->createdby = $adminuser->id;
            $openconv->timecreated = $now;

            $convocatoriaids[1] = $DB->insert_record('local_jobboard_convocatoria', $openconv);
            echo "Created OPEN convocatoria: $openname\n";
            echo "  Code: $opencode | ID: {$convocatoriaids[1]}\n";
            echo "  Period: " . date('Y-m-d', $openstart) . " to " . date('Y-m-d', $openend) . " (CURRENT)\n";
        }

        echo "\nConvocatorias ready: " . count($convocatoriaids) . "\n";
    } else {
        // Normal mode: create single convocatoria.
        $convcode = $options['convocatoria-code'] ?: "CONV-ISER-{$year}-{$semester}";
        $convname = $options['convocatoria-name'] ?: "Convocatoria Docentes Ocasionales y Cátedra ISER {$year}-{$semester}";

    // Check if exists.
    $existingconv = $DB->get_record('local_jobboard_convocatoria', ['code' => $convcode]);

    if ($existingconv) {
        echo "Using existing convocatoria: $convcode (ID: {$existingconv->id})\n";
        $convocatoriaid = $existingconv->id;
    } else if (!$dryrun) {
        $adminuser = get_admin();

        // Count by contract type and program.
        $ocasionalCount = 0;
        $catedraCount = 0;
        $programStats = [];
        foreach ($allprofiles as $p) {
            if (stripos($p['contracttype'], 'OCASIONAL') !== false) {
                $ocasionalCount++;
            } else {
                $catedraCount++;
            }
            $prog = $p['program'] ?: 'Sin programa específico';
            $programStats[$prog] = ($programStats[$prog] ?? 0) + 1;
        }
        arsort($programStats);

        // Build distribution by location HTML.
        $locationHtml = '';
        foreach ($locationstats as $loc => $cnt) {
            $locname = $ISER_SEDES[$loc]['name'] ?? $loc;
            $locationHtml .= "<li><strong>{$locname}:</strong> {$cnt} vacantes</li>\n";
        }

        // Build program distribution HTML (top 10).
        $programHtml = '';
        $topPrograms = array_slice($programStats, 0, 10, true);
        foreach ($topPrograms as $prog => $cnt) {
            $programHtml .= "<li>{$prog}: {$cnt} perfiles</li>\n";
        }

        $totalVacancies = count($allprofiles);
        $openDateStr = date('d/m/Y', $opendate);
        $closeDateStr = date('d/m/Y', $closedate);

        // Build comprehensive description.
        $deschtml = <<<HTML
<div class="convocatoria-description">
    <h3>Convocatoria para Vinculación de Docentes ISER {$year}</h3>

    <div class="alert alert-info">
        <strong>Instituto Superior de Educación Rural - ISER</strong><br>
        Proceso de selección para docentes ocasionales y de cátedra - Vigencia {$year}
    </div>

    <h4>Información General</h4>
    <table class="table table-bordered">
        <tr><th>Código de Convocatoria</th><td><strong>{$convcode}</strong></td></tr>
        <tr><th>Período de Inscripción</th><td>{$openDateStr} al {$closeDateStr}</td></tr>
        <tr><th>Total de Vacantes</th><td><strong>{$totalVacancies}</strong></td></tr>
        <tr><th>Modalidades</th><td>Presencial y A Distancia</td></tr>
    </table>

    <h4>Distribución de Vacantes</h4>

    <h5>Por Tipo de Vinculación</h5>
    <ul>
        <li><strong>Docente Ocasional Tiempo Completo:</strong> {$ocasionalCount} vacantes
            <br><small class="text-muted">Contrato laboral a término fijo por período académico</small></li>
        <li><strong>Docente de Cátedra:</strong> {$catedraCount} vacantes
            <br><small class="text-muted">Contrato de prestación de servicios por horas</small></li>
    </ul>

    <h5>Por Facultad</h5>
    <ul>
        <li><strong>FCAS</strong> - Facultad de Ciencias Administrativas y Sociales: {$parsestats['fcas']} perfiles</li>
        <li><strong>FII</strong> - Facultad de Ingenierías e Informática: {$parsestats['fii']} perfiles</li>
    </ul>

    <h5>Por Sede / Centro Tutorial</h5>
    <ul>
        {$locationHtml}
    </ul>

    <h5>Programas Académicos con Mayor Demanda</h5>
    <ul>
        {$programHtml}
    </ul>

    <h4>Requisitos Generales</h4>
    <ol>
        <li>Título profesional universitario acorde al perfil requerido para la vacante</li>
        <li>Título de posgrado (especialización, maestría o doctorado) - según perfil</li>
        <li>Experiencia docente en educación superior (deseable mínimo 1 año)</li>
        <li>Disponibilidad horaria para la sede y modalidad seleccionada</li>
        <li>No tener inhabilidades ni incompatibilidades para contratar con el Estado</li>
    </ol>

    <h4>Documentos Requeridos</h4>
    <p>Los aspirantes deberán cargar en el sistema los siguientes documentos en formato PDF:</p>

    <h5>Documentos de Identificación</h5>
    <ul>
        <li>Hoja de vida actualizada (formato libre o SIGEP)</li>
        <li>Cédula de ciudadanía (ambas caras, legible)</li>
        <li>Libreta militar (hombres menores de 50 años)</li>
        <li>Foto reciente tipo documento (fondo blanco)</li>
    </ul>

    <h5>Documentos Académicos</h5>
    <ul>
        <li>Diploma y acta de grado de pregrado</li>
        <li>Diploma y acta de grado de posgrado (si aplica)</li>
        <li>Tarjeta profesional (para profesiones reguladas)</li>
        <li>Certificado de vigencia de tarjeta profesional (expedición no mayor a 3 meses)</li>
    </ul>

    <h5>Documentos Laborales</h5>
    <ul>
        <li>Certificaciones laborales de experiencia docente</li>
        <li>Certificaciones laborales de experiencia profesional relacionada</li>
    </ul>

    <h5>Certificados de Antecedentes (vigencia no mayor a 30 días)</h5>
    <ul>
        <li>Certificado de antecedentes disciplinarios - Procuraduría General de la Nación</li>
        <li>Certificado de antecedentes fiscales - Contraloría General de la República</li>
        <li>Certificado de antecedentes judiciales - Policía Nacional</li>
        <li>Certificado de medidas correctivas - Policía Nacional</li>
        <li>Certificado del Sistema de Registro de Inhabilidades por Delitos Sexuales</li>
    </ul>

    <h5>Documentos Financieros y de Seguridad Social</h5>
    <ul>
        <li>RUT actualizado (expedición no mayor a 3 meses)</li>
        <li>Certificación bancaria (cuenta de ahorros o corriente a nombre del aspirante)</li>
        <li>Certificado de afiliación a EPS</li>
        <li>Certificado de afiliación a Fondo de Pensiones</li>
    </ul>

    <h4>Proceso de Selección</h4>
    <ol>
        <li><strong>Inscripción:</strong> Registro en el sistema y carga de documentos</li>
        <li><strong>Verificación documental:</strong> Revisión de requisitos mínimos</li>
        <li><strong>Evaluación de méritos:</strong> Valoración de formación y experiencia</li>
        <li><strong>Entrevista:</strong> Evaluación de competencias (si aplica)</li>
        <li><strong>Publicación de resultados:</strong> Lista de elegibles</li>
        <li><strong>Vinculación:</strong> Sujeta a disponibilidad presupuestal</li>
    </ol>

    <h4>Contacto</h4>
    <p>Para mayor información sobre esta convocatoria:</p>
    <ul>
        <li><strong>Oficina de Talento Humano - ISER</strong></li>
        <li>Correo: talento.humano@iser.edu.co</li>
        <li>Teléfono: (607) 568XXXX</li>
        <li>Dirección: Pamplona, Norte de Santander</li>
    </ul>
</div>
HTML;

        // Build terms and conditions.
        $termshtml = <<<HTML
<div class="convocatoria-terms">
    <h4>Términos y Condiciones de la Convocatoria</h4>

    <p>Al registrarse y postularse a esta convocatoria, el aspirante declara bajo la gravedad de juramento y acepta expresamente lo siguiente:</p>

    <h5>1. Veracidad de la Información</h5>
    <p>Que toda la información consignada en el formulario de inscripción y los documentos adjuntos son verídicos, auténticos y pueden ser verificados por la institución. La presentación de documentos falsos o adulterados causará el rechazo inmediato de la postulación y las acciones legales correspondientes según la legislación colombiana.</p>

    <h5>2. Autorización de Tratamiento de Datos Personales</h5>
    <p>De conformidad con la Ley 1581 de 2012 (Ley de Protección de Datos Personales), el Decreto 1377 de 2013 y demás normas concordantes, autorizo expresamente al Instituto Superior de Educación Rural - ISER para:</p>
    <ul>
        <li>Recolectar, almacenar, usar, circular y procesar mis datos personales</li>
        <li>Verificar la autenticidad de los documentos presentados ante las entidades correspondientes</li>
        <li>Contactarme por cualquier medio (correo electrónico, teléfono, WhatsApp) para asuntos relacionados con esta convocatoria</li>
        <li>Compartir mi información con entidades de control cuando sea requerido</li>
    </ul>

    <h5>3. Proceso de Selección</h5>
    <p>Acepto que:</p>
    <ul>
        <li>El proceso de selección se realizará de acuerdo con los criterios establecidos por la institución</li>
        <li>La decisión final de vinculación es discrecional del ISER y no admite recurso alguno</li>
        <li>La inscripción y postulación NO genera ningún derecho ni expectativa de vinculación laboral</li>
        <li>El ISER se reserva el derecho de declarar desierta la convocatoria en cualquier momento</li>
    </ul>

    <h5>4. Tipo de Vinculación</h5>
    <ul>
        <li><strong>Docente Ocasional Tiempo Completo:</strong> Vinculación mediante contrato laboral a término fijo por el período académico correspondiente, con todas las prestaciones de ley.</li>
        <li><strong>Docente de Cátedra:</strong> Vinculación mediante contrato de prestación de servicios profesionales, remunerado por hora efectivamente dictada. No genera relación laboral.</li>
    </ul>

    <h5>5. Requisitos de Vinculación</h5>
    <p>En caso de ser seleccionado, la vinculación estará condicionada a:</p>
    <ul>
        <li>Disponibilidad presupuestal de la institución</li>
        <li>Cumplimiento de todos los requisitos legales y documentales</li>
        <li>Aprobación de exámenes médicos ocupacionales</li>
        <li>No estar incurso en inhabilidades o incompatibilidades legales</li>
    </ul>

    <h5>6. Compromiso del Aspirante</h5>
    <p>Me comprometo a:</p>
    <ul>
        <li>Mantener actualizados mis datos de contacto en el sistema</li>
        <li>Responder oportunamente a las comunicaciones de la institución</li>
        <li>Presentar los documentos originales cuando sean requeridos</li>
        <li>Informar cualquier cambio en mi situación que afecte mi participación</li>
    </ul>

    <h5>7. Declaración de Inhabilidades</h5>
    <p>Declaro que no me encuentro incurso en ninguna de las causales de inhabilidad o incompatibilidad previstas en la Constitución Política, la Ley 80 de 1993, la Ley 1474 de 2011 y demás normas concordantes para celebrar contratos con entidades públicas.</p>

    <p class="mt-4"><strong>NOTA IMPORTANTE:</strong> La aceptación de estos términos y condiciones es requisito indispensable para participar en esta convocatoria. Al hacer clic en "Acepto" y enviar mi postulación, confirmo haber leído, entendido y aceptado todas las condiciones aquí establecidas.</p>
</div>
HTML;

        $convrecord = new stdClass();
        $convrecord->code = $convcode;
        $convrecord->name = $convname;
        $convrecord->description = $deschtml;
        $convrecord->startdate = $opendate;
        $convrecord->enddate = $closedate;
        $convrecord->status = 'open';
        $convrecord->companyid = null; // Convocatoria is global, not tied to a company.
        $convrecord->departmentid = null;
        $convrecord->publicationtype = $options['public'] ? 'public' : 'internal';
        $convrecord->terms = $termshtml;
        $convrecord->createdby = $adminuser->id;
        $convrecord->timecreated = $now;

        $convocatoriaid = $DB->insert_record('local_jobboard_convocatoria', $convrecord);

        echo "Created convocatoria: $convname\n";
        echo "  Code: $convcode\n";
        echo "  ID: $convocatoriaid\n";
        echo "  Status: open\n";
        echo "  Period: " . date('Y-m-d', $opendate) . " to " . date('Y-m-d', $closedate) . "\n";
        echo "  Vacancies: Ocasional={$ocasionalCount}, Cátedra={$catedraCount}\n";
    } else {
        echo "DRY RUN: Would create convocatoria '$convname'\n";
        $convocatoriaid = 0;
    }
    } // End of normal mode (else branch of $createsample).
} else if (!empty($convocatoriaid)) {
    $existingconv = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
    if (!$existingconv) {
        cli_error("Convocatoria with ID $convocatoriaid not found");
    }
    echo "Using convocatoria: {$existingconv->name} (ID: $convocatoriaid)\n";
}

// ============================================================
// PHASE 5: CREATE VACANCIES
// ============================================================

cli_heading('Phase 5: Creating Vacancies');

$adminuser = get_admin();
$importstats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0];
$totalprofiles = count($allprofiles);
$current = 0;

// Get default company if specified and structure not created.
$defaultcompanyid = null;
if (!empty($options['company'])) {
    $defaultcompanyid = (int) $options['company'];
}

foreach ($allprofiles as $code => $profile) {
    $current++;
    $prefix = "[$current/$totalprofiles]";

    // Check if exists.
    $existing = $DB->get_record('local_jobboard_vacancy', ['code' => $code]);

    if ($existing && !$options['update']) {
        if ($verbose) echo "$prefix SKIP: $code (exists)\n";
        $importstats['skipped']++;
        continue;
    }

    // Build vacancy record.
    $record = new stdClass();
    $record->code = $code;

    // Extract profile data.
    $program = $profile['program'] ?: '';
    $proftext = $profile['profile'] ?: '';
    $courses = $profile['courses'] ?? [];
    $faculty = $profile['faculty'] ?? '';
    $location = $profile['location'] ?? 'PAMPLONA';
    $modality = $profile['modality'] ?? 'PRESENCIAL';
    // Map modality to key: A DISTANCIA -> DISTANCIA, PRESENCIAL -> PRESENCIAL, VIRTUAL -> VIRTUAL, HIBRIDA -> HIBRIDA
    if (stripos($modality, 'DISTANCIA') !== false) {
        $modalitykey = 'DISTANCIA';
    } else if (stripos($modality, 'VIRTUAL') !== false) {
        $modalitykey = 'VIRTUAL';
    } else if (stripos($modality, 'HIBRIDA') !== false || stripos($modality, 'HÍBRIDA') !== false) {
        $modalitykey = 'HIBRIDA';
    } else {
        $modalitykey = 'PRESENCIAL';
    }
    $contracttypeRaw = $profile['contracttype'] ?: 'CATEDRA';
    $isOcasional = stripos($contracttypeRaw, 'OCASIONAL') !== false;

    // Map contract type from JSON to form-expected keys.
    // Form expects: catedra, temporal, termino_fijo, prestacion_servicios, planta.
    if ($isOcasional) {
        $contracttype = 'temporal'; // Ocasional Tiempo Completo -> temporal.
    } else if (stripos($contracttypeRaw, 'CATEDRA') !== false || stripos($contracttypeRaw, 'CÁTEDRA') !== false) {
        $contracttype = 'catedra';
    } else if (stripos($contracttypeRaw, 'PLANTA') !== false) {
        $contracttype = 'planta';
    } else if (stripos($contracttypeRaw, 'PRESTACION') !== false || stripos($contracttypeRaw, 'SERVICIOS') !== false) {
        $contracttype = 'prestacion_servicios';
    } else if (stripos($contracttypeRaw, 'FIJO') !== false) {
        $contracttype = 'termino_fijo';
    } else {
        $contracttype = 'catedra'; // Default to catedra.
    }

    // Title: Program + Brief Profile.
    $record->title = $program ?: "Docente {$faculty}";
    if ($proftext && strlen($proftext) < 80) {
        $record->title .= " - " . $proftext;
    }
    if (strlen($record->title) > 250) {
        $record->title = substr($record->title, 0, 247) . '...';
    }

    // Get location name.
    $locationName = $ISER_SEDES[$location]['name'] ?? $location;
    $modalityNames = ['PRESENCIAL' => 'Presencial', 'DISTANCIA' => 'A Distancia', 'VIRTUAL' => 'Virtual', 'HIBRIDA' => 'Híbrida'];
    $modalityName = $modalityNames[$modalitykey] ?? $modalitykey;
    $facultyName = $faculty === 'FCAS' ? 'Ciencias Administrativas y Sociales' :
                   ($faculty === 'FII' ? 'Ingenierías e Informática' : $faculty);

    // Build comprehensive description.
    $deschtml = "<div class=\"vacancy-description\">\n";

    // Vacancy header info.
    $deschtml .= "<div class=\"alert alert-secondary\">\n";
    $deschtml .= "<strong>Código:</strong> {$code} | ";
    $deschtml .= "<strong>Facultad:</strong> {$facultyName} | ";
    $deschtml .= "<strong>Modalidad:</strong> {$modalityName}\n";
    $deschtml .= "</div>\n";

    // Program info.
    if ($program) {
        $deschtml .= "<h4>Programa Académico</h4>\n";
        $deschtml .= "<p><strong>{$program}</strong></p>\n";
    }

    // Courses to teach.
    if (!empty($courses)) {
        $deschtml .= "<h4>Cursos/Asignaturas a Orientar</h4>\n";
        $deschtml .= "<ul class=\"list-group list-group-flush mb-3\">\n";
        foreach ($courses as $course) {
            $deschtml .= "<li class=\"list-group-item\"><i class=\"fa fa-book mr-2\"></i>{$course}</li>\n";
        }
        $deschtml .= "</ul>\n";
    }

    // Contract type information.
    $deschtml .= "<h4>Información de la Vinculación</h4>\n";
    $deschtml .= "<table class=\"table table-sm\">\n";
    $deschtml .= "<tr><th>Tipo de Vinculación</th><td>";

    if ($isOcasional) {
        $deschtml .= "<span class=\"badge badge-primary\">Ocasional Tiempo Completo</span>";
        $deschtml .= "</td></tr>\n";
        $deschtml .= "<tr><th>Duración</th><td>4 meses (período académico semestral) - Contrato laboral a término fijo</td></tr>\n";
        $deschtml .= "<tr><th>Prestaciones</th><td>Seguridad social, prima de servicios, vacaciones (30 días/año)</td></tr>\n";
        $deschtml .= "<tr><th>Dedicación</th><td>Tiempo completo (40 horas semanales)</td></tr>\n";
    } else {
        $deschtml .= "<span class=\"badge badge-info\">Cátedra</span>";
        $deschtml .= "</td></tr>\n";
        $deschtml .= "<tr><th>Duración</th><td>Semestre académico (16 semanas) - Contrato de prestación de servicios</td></tr>\n";
        $deschtml .= "<tr><th>Dedicación</th><td>Por horas según programación académica</td></tr>\n";
    }

    $deschtml .= "<tr><th>Sede</th><td>{$locationName}</td></tr>\n";
    $deschtml .= "<tr><th>Modalidad</th><td>{$modalityName}</td></tr>\n";
    $deschtml .= "<tr><th>Facultad</th><td>{$facultyName}</td></tr>\n";
    $deschtml .= "</table>\n";

    $deschtml .= "</div>\n";
    $record->description = $deschtml;

    // Contract type and duration.
    $record->contracttype = $contracttype;

    if ($isOcasional) {
        // Docente Ocasional Tiempo Completo.
        $record->duration = '4 meses (período académico semestral) - Contrato laboral a término fijo';
    } else {
        // Docente de Cátedra.
        $record->duration = 'Semestre académico (16 semanas) - Contrato de prestación de servicios por horas';
    }

    // Location (text field).
    $record->location = $locationName;

    // Modality (educational modality) - use key for form compatibility.
    // Form expects: presencial, distancia, virtual, hibrida (lowercase).
    $modalityFormKey = strtolower($modalitykey);
    $record->modality = $modalityFormKey;

    // Department (text field = modality display name).
    $record->department = $modalityName;

    // Build requirements.
    $reqhtml = "<div class=\"vacancy-requirements\">\n";
    $reqhtml .= "<h5>Perfil Profesional Requerido</h5>\n";
    if ($proftext) {
        $reqhtml .= "<p class=\"lead\">{$proftext}</p>\n";
    }

    $reqhtml .= "<h5>Requisitos Mínimos</h5>\n";
    $reqhtml .= "<ul>\n";
    $reqhtml .= "<li>Título profesional universitario acorde al perfil solicitado</li>\n";
    if (preg_match('/POSGRADO|ESPECIALIZA|MAESTR|DOCTOR|MAGISTER/i', $proftext)) {
        $reqhtml .= "<li>Título de posgrado en el área o afines</li>\n";
    }
    $reqhtml .= "<li>No tener inhabilidades ni incompatibilidades para contratar con el Estado</li>\n";
    $reqhtml .= "<li>Disponibilidad para la sede {$locationName} en modalidad {$modalityName}</li>\n";
    $reqhtml .= "</ul>\n";

    $reqhtml .= "<h5>Documentos a Presentar</h5>\n";
    $reqhtml .= "<ul>\n";
    $reqhtml .= "<li>Hoja de vida actualizada</li>\n";
    $reqhtml .= "<li>Cédula de ciudadanía</li>\n";
    $reqhtml .= "<li>Títulos académicos (pregrado y posgrado)</li>\n";
    $reqhtml .= "<li>Tarjeta profesional (si aplica)</li>\n";
    $reqhtml .= "<li>Certificaciones de experiencia laboral</li>\n";
    $reqhtml .= "<li>Certificados de antecedentes vigentes</li>\n";
    $reqhtml .= "</ul>\n";
    $reqhtml .= "</div>\n";
    $record->requirements = $reqhtml;

    // Desirable requirements.
    $deshtml = "<div class=\"vacancy-desirable\">\n";
    $deshtml .= "<h5>Requisitos Deseables</h5>\n";
    $deshtml .= "<ul>\n";
    $deshtml .= "<li>Experiencia docente en educación superior mínimo 1 año</li>\n";
    $deshtml .= "<li>Publicaciones académicas o investigaciones en el área</li>\n";
    $deshtml .= "<li>Manejo de herramientas tecnológicas para educación virtual</li>\n";
    // Requisitos específicos por modalidad.
    if (in_array($modalitykey, ['DISTANCIA', 'VIRTUAL', 'HIBRIDA'])) {
        $deshtml .= "<li>Experiencia en educación a distancia o virtual</li>\n";
        $deshtml .= "<li>Certificación en diseño instruccional o tutoría virtual</li>\n";
        if ($modalitykey === 'VIRTUAL' || $modalitykey === 'HIBRIDA') {
            $deshtml .= "<li>Manejo de plataformas LMS (Moodle, Canvas, Blackboard)</li>\n";
            $deshtml .= "<li>Experiencia en creación de contenidos multimedia educativos</li>\n";
        }
    }
    $deshtml .= "<li>Dominio de un segundo idioma (preferiblemente inglés)</li>\n";
    $deshtml .= "</ul>\n";
    $deshtml .= "</div>\n";
    $record->desirable = $deshtml;

    // IOMAD Company ID.
    if ($options['create-structure'] && isset($companymap[$location])) {
        $record->companyid = $companymap[$location];
    } else {
        $record->companyid = $defaultcompanyid;
    }

    // IOMAD Department ID (based on modality).
    $deptkey = $location . '_' . $modalitykey;
    if ($options['create-structure'] && isset($departmentmap[$deptkey])) {
        $record->departmentid = $departmentmap[$deptkey];
    } else if ($record->companyid && !empty($modality)) {
        // Try to find the department by name using the modalityNames mapping.
        $deptname = $modalityNames[$modalitykey] ?? 'Presencial';
        $dept = $DB->get_record('department', ['company' => $record->companyid, 'name' => $deptname]);
        $record->departmentid = $dept ? $dept->id : null;
    } else {
        $record->departmentid = !empty($options['department']) ? (int) $options['department'] : null;
    }

    // Convocatoria (dates are inherited from convocatoria).
    // When --create-sample, use convocatoria_index to assign to correct convocatoria.
    if ($createsample && !empty($convocatoriaids) && isset($profile['convocatoria_index'])) {
        $cidx = $profile['convocatoria_index'];
        $record->convocatoriaid = $convocatoriaids[$cidx] ?? $convocatoriaids[1] ?? null;
    } else {
        $record->convocatoriaid = $convocatoriaid;
    }

    // Number of positions - always 1 per vacancy (each profile = 1 position).
    // Multiple profiles don't mean multiple positions per vacancy.
    $record->positions = 1;

    // Status and publication type.
    $record->status = $options['status'];
    $record->publicationtype = $options['public'] ? 'public' : 'internal';

    // Audit fields.
    $record->createdby = $adminuser->id;
    $record->timecreated = $now;

    // Dry run?
    if ($dryrun) {
        echo "$prefix DRY: $code -> {$locationName} ({$modalityName})\n";
        $importstats[$existing ? 'updated' : 'created']++;
        continue;
    }

    // Insert or update.
    try {
        if ($existing) {
            $record->id = $existing->id;
            $record->modifiedby = $adminuser->id;
            $record->timemodified = $now;
            $DB->update_record('local_jobboard_vacancy', $record);
            if ($verbose) echo "$prefix UPDATED: $code\n";
            $importstats['updated']++;
        } else {
            $id = $DB->insert_record('local_jobboard_vacancy', $record);
            if ($verbose) echo "$prefix CREATED: $code (ID: $id) -> {$locationName}\n";
            $importstats['created']++;
        }
    } catch (Exception $e) {
        cli_problem("$prefix ERROR: $code - " . $e->getMessage());
        $importstats['errors']++;
    }
}

// ============================================================
// SUMMARY
// ============================================================

echo "\n";
cli_heading('Import Summary');
echo "Profiles parsed: {$parsestats['profiles']}\n";
if ($convocatoriaid) {
    echo "Convocatoria ID: $convocatoriaid\n";
}
if ($options['create-structure']) {
    echo "Companies created/used: " . count($companymap) . "\n";
    echo "Departments created/used: " . count($departmentmap) . "\n";
}
echo "Vacancies created: {$importstats['created']}\n";
echo "Vacancies updated: {$importstats['updated']}\n";
echo "Vacancies skipped: {$importstats['skipped']}\n";
echo "Errors: {$importstats['errors']}\n";

if ($dryrun) {
    echo "\n*** DRY RUN - No changes were made ***\n";
} else if ($importstats['created'] > 0 || $importstats['updated'] > 0) {
    echo "\n=== SUCCESS ===\n";
    if ($options['status'] === 'published') {
        echo "Vacancies are now PUBLISHED.\n";
        echo "Browse: /local/jobboard/?view=public\n";
    }
}

exit($importstats['errors'] > 0 ? 1 : 0);


// ============================================================
// PARSING FUNCTIONS (Rewritten v2.1 for accurate extraction)
// ============================================================

/**
 * Location patterns mapping to normalized keys.
 */
function get_location_patterns() {
    return [
        // Centros Tutoriales (order matters - more specific first).
        'CENTRO TUTORIAL EL TARRA' => 'ELTARRA',
        'CENTRO TUTORIAL OCA[ÑN]A' => 'OCANA',
        'CENTRO TUTORIAL PUEBLO BELLO' => 'PUEBLOBELLO',
        'CENTRO TUTORIAL SAN JOS[EÉ] DE C[UÚ]CUTA' => 'CUCUTA',
        'CENTRO TUTORIAL SAN PABLO' => 'SANPABLO',
        'CENTRO TUTORIAL SAN VICENTE' => 'SANVICENTE',
        'CENTRO TUTORIAL SANTA ROSA' => 'SANTAROSA',
        'CENTRO TUTORIAL TAME' => 'TAME',
        'CENTRO TUTORIAL TIB[UÚ]' => 'TIBU',
        'CENTRO TUTORIAL FUNDACION' => 'FUNDACION',
        'CENTRO TUTORIAL TOLEDO' => 'TOLEDO',
        // Individual location names.
        'EL TARRA' => 'ELTARRA',
        'OCA[ÑN]A' => 'OCANA',
        'PUEBLO BELLO' => 'PUEBLOBELLO',
        'SAN VICENTE' => 'SANVICENTE',
        'SANTA ROSA' => 'SANTAROSA',
        'SAN PABLO' => 'SANPABLO',
        'CIMITARRA' => 'CIMITARRA',
        'SALAZAR' => 'SALAZAR',
        'TOLEDO' => 'TOLEDO',
        'TAME' => 'TAME',
        'TIB[UÚ]' => 'TIBU',
        'C[UÚ]CUTA' => 'CUCUTA',
        'FUNDACI[OÓ]N' => 'FUNDACION',
        'SARDINATA' => 'SARDINATA',
        'SARAVENA' => 'SARAVENA',
        'PAMPLONA' => 'PAMPLONA',
    ];
}

/**
 * Extract location from a text segment.
 * @param string $text Text to search in.
 * @return string|null Normalized location key or null.
 */
function detect_location($text) {
    $patterns = get_location_patterns();
    $text_upper = strtoupper($text);

    foreach ($patterns as $pattern => $location) {
        if (preg_match('/' . $pattern . '/iu', $text_upper)) {
            return $location;
        }
    }
    return null;
}

/**
 * Detect modality from text segment.
 * @param string $text Text to search in.
 * @return string 'PRESENCIAL', 'A DISTANCIA', 'VIRTUAL', or 'HIBRIDA'.
 */
function detect_modality($text) {
    // Check for Virtual first (most specific).
    if (preg_match('/MODALIDAD\s+VIRTUAL|VIRTUAL/iu', $text)) {
        return 'VIRTUAL';
    }
    // Check for Híbrida.
    if (preg_match('/MODALIDAD\s+H[IÍ]BRIDA|H[IÍ]BRIDA/iu', $text)) {
        return 'HIBRIDA';
    }
    // Check for A Distancia.
    if (preg_match('/MODALIDAD\s+A?\s*DISTANCIA|A\s+DISTANCIA/iu', $text)) {
        return 'A DISTANCIA';
    }
    return 'PRESENCIAL';
}

/**
 * Build a regex pattern to match location section headers.
 * @return string Regex pattern.
 */
function get_section_header_pattern() {
    return '/(?:^|\n)\s*(?:'
        . 'CENTRO\s+TUTORIAL\s+[^\n]+|'
        . 'PROGRAMA\s+MODALIDAD\s+(?:A\s+)?DISTANCIA[^\n]*|'
        . 'MODALIDAD\s+(?:A\s+)?DISTANCIA[^\n]*|'
        . 'MODALIDAD\s+PRESENCIAL[^\n]*|'
        . '(?:PAMPLONA|C[UÚ]CUTA|TIB[UÚ]|SALAZAR|CIMITARRA|OCA[ÑN]A|TOLEDO|TAME)\s*(?:\n|$)'
        . ')/iu';
}

/**
 * Split content into sections by location/modality headers.
 * @param string $content File content.
 * @param string $filename File name for context.
 * @return array Array of sections with metadata.
 */
function split_into_sections($content, $filename) {
    $content = preg_replace('/\r\n|\r/', "\n", $content);

    // Determine faculty from filename.
    $faculty = '';
    if (stripos($filename, 'FCAS') !== false) {
        $faculty = 'FCAS';
    } else if (stripos($filename, 'FII') !== false) {
        $faculty = 'FII';
    }

    // Default modality from filename.
    $default_modality = 'PRESENCIAL';
    if (stripos($filename, 'DISTANCIA') !== false) {
        $default_modality = 'A DISTANCIA';
    }

    // Find all section headers with their positions.
    $pattern = get_section_header_pattern();
    $sections = [];

    if (preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        $headers = $matches[0];
        $numheaders = count($headers);

        for ($i = 0; $i < $numheaders; $i++) {
            $header_text = $headers[$i][0];
            $start = $headers[$i][1];
            $end = isset($headers[$i + 1]) ? $headers[$i + 1][1] : strlen($content);

            $section_content = substr($content, $start, $end - $start);

            // Detect location and modality for this section.
            $location = detect_location($header_text) ?: 'PAMPLONA';
            $modality = detect_modality($header_text);

            // If modality not in header, check if it's in the section start.
            if ($modality === 'PRESENCIAL') {
                $modality = detect_modality(substr($section_content, 0, 200));
            }

            $sections[] = [
                'content' => $section_content,
                'location' => $location,
                'modality' => $modality !== 'PRESENCIAL' ? $modality : $default_modality,
                'faculty' => $faculty,
            ];
        }
    }

    // If no sections found, treat entire content as one section.
    if (empty($sections)) {
        $location = detect_location($content) ?: 'PAMPLONA';
        $modality = detect_modality($content);
        if ($modality === 'PRESENCIAL') {
            $modality = $default_modality;
        }

        $sections[] = [
            'content' => $content,
            'location' => $location,
            'modality' => $modality,
            'faculty' => $faculty,
        ];
    }

    return $sections;
}

/**
 * Parse profiles from text content - REWRITTEN for accuracy.
 * @param string $content Full file content.
 * @param string $filename File name.
 * @param string $defaultlocation Default location (unused, kept for compatibility).
 * @return array Array of profiles keyed by code.
 */
function parse_profiles_from_text($content, $filename, $defaultlocation = 'PAMPLONA') {
    $profiles = [];

    // Split into sections.
    $sections = split_into_sections($content, $filename);

    foreach ($sections as $section) {
        $section_profiles = parse_section_profiles($section);
        foreach ($section_profiles as $code => $profile) {
            // Only add if not already present (avoid duplicates).
            if (!isset($profiles[$code])) {
                $profiles[$code] = $profile;
            }
        }
    }

    return $profiles;
}

/**
 * Parse profiles from a single section.
 * @param array $section Section data with content, location, modality, faculty.
 * @return array Array of profiles.
 */
function parse_section_profiles($section) {
    $profiles = [];
    $content = $section['content'];

    // Find all profile codes.
    $pattern = '/\b(FCAS-?\s*\d+|FII-?\s*\d+)\b/i';

    if (!preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        return [];
    }

    $codes = $matches[0];
    $numcodes = count($codes);

    for ($i = 0; $i < $numcodes; $i++) {
        $coderaw = $codes[$i][0];
        $start = $codes[$i][1];
        $end = isset($codes[$i + 1]) ? $codes[$i + 1][1] : strlen($content);

        // Normalize code (remove spaces).
        $code = preg_replace('/[\s-]+/', '-', strtoupper(trim($coderaw)));
        $code = preg_replace('/-+/', '-', $code);

        // Skip table headers.
        $blockstart = substr($content, $start, 100);
        if (preg_match('/C[ÓO]DIGO\s*TIPO/iu', $blockstart)) {
            continue;
        }

        // Extract block for this profile.
        $block = substr($content, $start + strlen($coderaw), $end - $start - strlen($coderaw));
        $block = trim($block);

        // Parse the profile block.
        $source = [
            'faculty' => $section['faculty'] ?: (strpos($code, 'FCAS') === 0 ? 'FCAS' : 'FII'),
            'modality' => $section['modality'],
            'location' => $section['location'],
        ];

        $profile = parse_profile_block($code, $block, $source);
        if ($profile) {
            $profiles[$code] = $profile;
        }
    }

    return $profiles;
}

/**
 * Parse a single profile block - REWRITTEN for better extraction.
 * Handles both pipe-delimited tabular format and free text.
 * @param string $code Profile code (e.g., FCAS-01).
 * @param string $block Raw text block for this profile.
 * @param array $source Source info (faculty, modality, location).
 * @return array|null Profile data or null if invalid.
 */
function parse_profile_block($code, $block, $source) {
    $profile = [
        'code' => $code,
        'faculty' => $source['faculty'] ?: (strpos($code, 'FCAS') === 0 ? 'FCAS' : 'FII'),
        'modality' => $source['modality'],
        'location' => $source['location'],
        'contracttype' => '',
        'program' => '',
        'profile' => '',
        'courses' => [],
    ];

    // =====================
    // DETECT TABULAR FORMAT (pipe-delimited)
    // =====================
    // Format: CODE | CONTRACT TYPE | PROGRAM | PROFILE | COURSE1 | COURSE2 | ...
    if (strpos($block, '|') !== false) {
        // This is tabular format - parse by pipe delimiter.
        $parts = array_map('trim', explode('|', $block));

        // First part might be empty or contain the code - skip it.
        if (empty($parts[0]) || preg_match('/^' . preg_quote($code, '/') . '$/i', $parts[0])) {
            array_shift($parts);
        }

        // Extract contract type (first non-empty part).
        foreach ($parts as $idx => $part) {
            if (preg_match('/OCASIONAL\s+TIEMPO\s+COMPLETO/iu', $part)) {
                $profile['contracttype'] = 'OCASIONAL TIEMPO COMPLETO';
                unset($parts[$idx]);
                break;
            } else if (preg_match('/^C[ÁA]TEDRA$/iu', trim($part))) {
                $profile['contracttype'] = 'CATEDRA';
                unset($parts[$idx]);
                break;
            }
        }
        $parts = array_values($parts);

        // Extract program (look for TECNOLOGÍA/TÉCNICA pattern).
        foreach ($parts as $idx => $part) {
            if (preg_match('/^(TECNOLOG[IÍ]A\s+EN|T[EÉ]CNICA\s+PROFESIONAL\s+EN|TODOS\s+LOS\s+PROGRAMAS)/iu', trim($part))) {
                $profile['program'] = clean_program_name(trim($part));
                unset($parts[$idx]);
                break;
            }
        }
        $parts = array_values($parts);

        // Extract professional profile (first substantial text that looks like a profession).
        foreach ($parts as $idx => $part) {
            $part = trim($part);
            if (strlen($part) > 15 && preg_match('/(?:PROFESIONAL|INGENIER|LICENCIAD|ADMINISTRAD|CONTADOR|ECONOMISTA|PSIC[OÓ]LOG|TRABAJADOR|TECN[OÓ]LOG|QU[IÍ]MICO|ARQUITECTO|MICROBIO|COMUNICADOR|ABOGAD|M[EÉ]DICO|ZOOTECNISTA|AGR[OÓ]NOMO)/iu', $part)) {
                // This looks like a professional profile.
                $profile['profile'] = clean_profile_text($part);
                unset($parts[$idx]);
                break;
            }
        }
        $parts = array_values($parts);

        // Remaining parts are courses.
        $courses = [];
        foreach ($parts as $part) {
            $part = trim($part);
            // Skip empty or header-like content.
            if (empty($part)) continue;
            if (preg_match('/^(?:POSIBLES|CURSOS?\s*PARA|ORIENTAR)/iu', $part)) continue;
            // Clean "ORIENTAR LOS CURSOS DE:" prefix.
            $part = preg_replace('/^ORIENTAR\s+(?:LOS?\s+)?CURSOS?\s*(?:DE)?\s*:?\s*/iu', '', $part);
            $part = preg_replace('/^ORIENTAR\s+(?:EL\s+)?CURSO\s*(?:DE)?\s*:?\s*/iu', '', $part);
            $part = trim($part);

            if (is_valid_course($part)) {
                // May contain multiple courses separated by - or ,
                $subcourses = preg_split('/\s*[-–]\s*(?=[A-ZÁÉÍÓÚÑ])/u', $part);
                foreach ($subcourses as $sc) {
                    $sc = trim($sc);
                    if (is_valid_course($sc)) {
                        $courses[] = clean_course_name($sc);
                    }
                }
            }
        }
        $profile['courses'] = array_values(array_unique($courses));

        // Validate profile has meaningful data.
        if (empty($profile['contracttype']) && empty($profile['program']) &&
            empty($profile['profile']) && empty($profile['courses'])) {
            return null;
        }

        return $profile;
    }

    // =====================
    // FALLBACK: Free text format parsing
    // =====================
    // Normalize whitespace but preserve some structure.
    $normalized = preg_replace('/[ \t]+/', ' ', $block);
    $normalized = preg_replace('/\n\s*\n+/', "\n", $normalized);
    $normalized = trim($normalized);
    $oneline = preg_replace('/\s+/', ' ', $normalized);

    // =====================
    // 1. CONTRACT TYPE
    // =====================
    if (preg_match('/OCASIONAL\s+TIEMPO\s+COMPLETO/iu', $oneline)) {
        $profile['contracttype'] = 'OCASIONAL TIEMPO COMPLETO';
    } else if (preg_match('/C[ÁA]TEDRA/iu', $oneline)) {
        $profile['contracttype'] = 'CATEDRA';
    }

    // =====================
    // 2. PROGRAM
    // =====================
    // Look for TECNOLOGÍA EN...
    if (preg_match('/TECNOLOG[IÍ]A\s+EN\s+([A-ZÁÉÍÓÚÑ\s]+?)(?=\s+(?:PROFESIONAL|INGENIER|LICENCIAD|ADMINISTRAD|MÉDICO|ABOGAD|CONTADOR|ECONOMISTA|PSIC[OÓ]LOG|TRABAJADOR|TECN[ÓO]LOG|ORIENTAR|DOCENTE|QU[IÍ]MICO|ARQUITECTO|MICROBIO|COMUNICADOR|$))/iu', $oneline, $m)) {
        $prog = 'TECNOLOGÍA EN ' . trim(preg_replace('/\s+/', ' ', $m[1]));
        // Clean up common issues.
        $prog = preg_replace('/\s+(PROFESIONAL|INGENIER|LICENCIAD).*$/iu', '', $prog);
        $profile['program'] = trim($prog);
    }
    // TÉCNICA PROFESIONAL EN...
    else if (preg_match('/T[EÉ]CNICA\s+PROFESIONAL\s+EN\s+([A-ZÁÉÍÓÚÑ\s]+?)(?=\s+(?:PROFESIONAL|INGENIER|LICENCIAD|$))/iu', $oneline, $m)) {
        $profile['program'] = 'TÉCNICA PROFESIONAL EN ' . trim(preg_replace('/\s+/', ' ', $m[1]));
    }
    // Special: TODOS LOS PROGRAMAS.
    else if (preg_match('/TODOS\s+LOS\s+PROGRAMAS/iu', $oneline)) {
        $profile['program'] = 'TODOS LOS PROGRAMAS';
    }

    // =====================
    // 3. PROFESSIONAL PROFILE
    // =====================
    // Extract professional profile - look for professional titles.
    $prof_patterns = [
        '/(?:PROFESIONAL\s+(?:EN\s+)?|INGENIER[OA]?\s*(?:\s+EN|\s+DE|\(A\))?|LICENCIAD[OA]?\s*(?:\s+EN|\(A\))?|ADMINISTRADOR[A]?\s*(?:\s+DE)?|CONTADOR[A]?\s*(?:\s+P[UÚ]BLICO)?|ECONOMISTA|PSIC[OÓ]LOG[OA]?|TRABAJADOR[A]?\s+SOCIAL|TECN[OÓ]LOG[OA]?\s*(?:\s+EN|\(A\))?|QU[IÍ]MICO|ARQUITECTO|MICROBIO|COMUNICADOR|ABOGAD[OA]?)([^O][A-ZÁÉÍÓÚÑ\s,\/\.\-\(\)]+)/iu',
    ];

    foreach ($prof_patterns as $pattern) {
        if (preg_match_all($pattern, $oneline, $pm, PREG_SET_ORDER)) {
            $parts = [];
            foreach ($pm as $match) {
                $full = trim($match[0]);
                // Clean up.
                $full = preg_replace('/\s*(?:ORIENTAR|POSIBLES|CURSOS|CÓDIGO|TIPO|VINCULACIÓN).*$/iu', '', $full);
                $full = preg_replace('/\s+/', ' ', $full);
                if (strlen($full) > 10 && !preg_match('/^(?:TECNOLOGÍA|TÉCNICA|PROGRAMA)/iu', $full)) {
                    $parts[] = $full;
                }
            }
            if (!empty($parts)) {
                $profile['profile'] = implode(' / ', array_unique($parts));
                break;
            }
        }
    }

    // Fallback: Look between program and ORIENTAR.
    if (empty($profile['profile']) && !empty($profile['program'])) {
        $progpos = stripos($oneline, $profile['program']);
        if ($progpos !== false) {
            $afterprog = substr($oneline, $progpos + strlen($profile['program']));
            $orientarpos = stripos($afterprog, 'ORIENTAR');
            if ($orientarpos === false) {
                $orientarpos = strlen($afterprog);
            }
            $proftext = trim(substr($afterprog, 0, $orientarpos));
            $proftext = preg_replace('/^[\s,\.]+/', '', $proftext);
            if (strlen($proftext) > 15) {
                $profile['profile'] = $proftext;
            }
        }
    }

    // =====================
    // 4. COURSES
    // =====================
    $courses = [];

    // Method 1: Look for "ORIENTAR LOS CURSOS DE:" pattern.
    if (preg_match('/ORIENTAR\s+(?:LOS\s+)?CURSOS?\s*(?:DE)?\s*:?\s*(.+?)$/isu', $oneline, $cm)) {
        $coursestext = trim($cm[1]);
        // Split by common delimiters.
        $course_items = preg_split('/\s*(?:[-–•]|\s{2,})\s*/u', $coursestext);
        foreach ($course_items as $c) {
            $c = trim($c);
            if (is_valid_course($c)) {
                $courses[] = clean_course_name($c);
            }
        }
    }

    // Method 2: Look for course patterns at the end of blocks.
    if (empty($courses)) {
        // Get text after profile description.
        $lines = preg_split('/\n/', $normalized);
        $in_courses = false;
        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) continue;

            // Detect start of courses.
            if (preg_match('/ORIENTAR|CURSOS?\s*(?:PARA|DE)|POSIBLES\s+CURSOS/iu', $line)) {
                $in_courses = true;
                $line = preg_replace('/.*(?:ORIENTAR|CURSOS?\s*(?:PARA|DE)|POSIBLES\s+CURSOS)\s*:?\s*/iu', '', $line);
            }

            if ($in_courses && !empty($line)) {
                // Check if this looks like a course name.
                if (preg_match('/^[A-ZÁÉÍÓÚÑ][A-ZÁÉÍÓÚÑ\s,\.\-\(\)IVX0-9]+$/iu', $line)) {
                    $course_items = preg_split('/\s*[-–•]\s*/', $line);
                    foreach ($course_items as $c) {
                        $c = trim($c);
                        if (is_valid_course($c)) {
                            $courses[] = clean_course_name($c);
                        }
                    }
                }
            }
        }
    }

    $profile['courses'] = array_values(array_unique($courses));

    // Validate profile has meaningful data.
    if (empty($profile['contracttype']) && empty($profile['program']) &&
        empty($profile['profile']) && empty($profile['courses'])) {
        return null;
    }

    return $profile;
}

/**
 * Check if a string looks like a valid course name.
 * @param string $c Course name candidate.
 * @return bool True if valid.
 */
function is_valid_course($c) {
    $c = trim($c);
    if (strlen($c) < 4) return false;
    if (strlen($c) > 100) return false;

    // Reject common non-course words.
    $reject = '/^(?:DE|EN|LOS|LAS|EL|LA|Y|O|PARA|DEL|AL|CON|A|'
        . 'CURSOS?|ORIENTAR|C[ÓO]DIGO|TIPO|VINCULACI[ÓO]N|PROGRAMA|ACAD[ÉE]MICO|'
        . 'PERFIL|PROFESIONAL|ESPEC[IÍ]FICO|POSIBLES|DOCENTE|C[ÁA]TEDRA|'
        . 'OCASIONAL|TIEMPO|COMPLETO|TECNOLOG[IÍ]A|T[ÉE]CNICA|GESTI[ÓO]N|'
        . 'POSGRADO|ESPECIALI|MAESTR[IÍ]A|DOCTOR)$/iu';

    if (preg_match($reject, $c)) {
        return false;
    }

    return true;
}

/**
 * Clean up a program name.
 * @param string $p Program name.
 * @return string Cleaned program name.
 */
function clean_program_name($p) {
    $p = trim($p);
    // Clean extra spaces.
    $p = preg_replace('/\s+/', ' ', $p);
    // Remove trailing punctuation.
    $p = preg_replace('/[\.\,;:]+$/', '', $p);
    // Standardize case for TECNOLOGÍA/TÉCNICA.
    $p = preg_replace('/TECNOLOG[IÍ]A/iu', 'TECNOLOGÍA', $p);
    $p = preg_replace('/T[EÉ]CNICA/iu', 'TÉCNICA', $p);
    return trim($p);
}

/**
 * Clean up professional profile text.
 * @param string $p Profile text.
 * @return string Cleaned profile text.
 */
function clean_profile_text($p) {
    $p = trim($p);
    // Clean extra spaces.
    $p = preg_replace('/\s+/', ' ', $p);
    // Remove trailing punctuation.
    $p = preg_replace('/[\.\,;:]+$/', '', $p);
    // Clean up common patterns.
    $p = preg_replace('/\s*\/\s*/', ' / ', $p);
    // Remove redundant "O AREAS AFINES" at the end if it appears multiple times.
    $p = preg_replace('/(\s+O\s+[AÁ]REAS?\s+AFINES?)+$/iu', ' O ÁREAS AFINES', $p);
    return trim($p);
}

/**
 * Clean up a course name.
 * @param string $c Course name.
 * @return string Cleaned course name.
 */
function clean_course_name($c) {
    $c = trim($c);
    // Remove leading numbers/bullets.
    $c = preg_replace('/^[\d\.\)\-\•]+\s*/', '', $c);
    // Clean extra spaces.
    $c = preg_replace('/\s+/', ' ', $c);
    // Remove trailing punctuation.
    $c = preg_replace('/[\.\,;:]+$/', '', $c);
    return trim($c);
}

/**
 * Legacy function - kept for compatibility.
 */
function extract_location_from_content($content, $filename) {
    return detect_location($content . ' ' . $filename) ?: 'PAMPLONA';
}

// ============================================================
// CSV PARSING FUNCTIONS
// ============================================================

/**
 * Parse profiles from a CSV file.
 *
 * Expected CSV format:
 * code,contracttype,program,profile,courses,location,modality,faculty
 *
 * Courses should be separated by | (pipe) character.
 *
 * @param string $filepath Path to the CSV file.
 * @param bool $verbose Show detailed output.
 * @return array Array of profiles keyed by code.
 */
function parse_csv_file($filepath, $verbose = false) {
    $profiles = [];

    $handle = fopen($filepath, 'r');
    if ($handle === false) {
        if (function_exists('cli_error')) {
            cli_error("Cannot open CSV file: $filepath");
        }
        return [];
    }

    // Read header row.
    $header = fgetcsv($handle);
    if ($header === false) {
        fclose($handle);
        if (function_exists('cli_error')) {
            cli_error("CSV file is empty or invalid: $filepath");
        }
        return [];
    }

    // Normalize header names.
    $header = array_map(function($h) {
        return strtolower(trim($h));
    }, $header);

    // Map expected column names.
    $colmap = [
        'code' => array_search('code', $header),
        'contracttype' => array_search('contracttype', $header),
        'program' => array_search('program', $header),
        'profile' => array_search('profile', $header),
        'courses' => array_search('courses', $header),
        'location' => array_search('location', $header),
        'modality' => array_search('modality', $header),
        'faculty' => array_search('faculty', $header),
    ];

    // Check required columns.
    if ($colmap['code'] === false) {
        fclose($handle);
        if (function_exists('cli_error')) {
            cli_error("CSV missing required column: code");
        }
        return [];
    }

    $rownum = 1;
    $errors = 0;

    while (($row = fgetcsv($handle)) !== false) {
        $rownum++;

        // Skip empty rows.
        if (empty($row) || (count($row) === 1 && empty($row[0]))) {
            continue;
        }

        // Skip comment rows (starting with #).
        if (isset($row[0]) && strpos(trim($row[0]), '#') === 0) {
            continue;
        }

        // Extract values.
        $code = $colmap['code'] !== false ? trim($row[$colmap['code']] ?? '') : '';

        if (empty($code)) {
            if ($verbose) {
                echo "  Row $rownum: skipping (empty code)\n";
            }
            continue;
        }

        // Normalize code.
        $code = preg_replace('/[\s-]+/', '-', strtoupper($code));
        $code = preg_replace('/-+/', '-', $code);

        // Extract other fields.
        $contracttype = $colmap['contracttype'] !== false ? trim($row[$colmap['contracttype']] ?? '') : '';
        $program = $colmap['program'] !== false ? trim($row[$colmap['program']] ?? '') : '';
        $profile_text = $colmap['profile'] !== false ? trim($row[$colmap['profile']] ?? '') : '';
        $courses_str = $colmap['courses'] !== false ? trim($row[$colmap['courses']] ?? '') : '';
        $location = $colmap['location'] !== false ? strtoupper(trim($row[$colmap['location']] ?? '')) : 'PAMPLONA';
        $modality = $colmap['modality'] !== false ? trim($row[$colmap['modality']] ?? '') : 'PRESENCIAL';
        $faculty = $colmap['faculty'] !== false ? strtoupper(trim($row[$colmap['faculty']] ?? '')) : '';

        // Auto-detect faculty from code if not specified.
        if (empty($faculty)) {
            $faculty = strpos($code, 'FCAS') === 0 ? 'FCAS' : (strpos($code, 'FII') === 0 ? 'FII' : '');
        }

        // Normalize location.
        $location = normalize_location_key($location);

        // Normalize modality to one of 4 valid values.
        if (stripos($modality, 'VIRTUAL') !== false) {
            $modality = 'VIRTUAL';
        } else if (stripos($modality, 'HIBRIDA') !== false || stripos($modality, 'HÍBRIDA') !== false) {
            $modality = 'HIBRIDA';
        } else if (stripos($modality, 'DISTANCIA') !== false) {
            $modality = 'A DISTANCIA';
        } else {
            $modality = 'PRESENCIAL';
        }

        // Parse courses (separated by |).
        $courses = [];
        if (!empty($courses_str)) {
            $course_items = explode('|', $courses_str);
            foreach ($course_items as $c) {
                $c = trim($c);
                if (!empty($c)) {
                    $courses[] = $c;
                }
            }
        }

        // Build profile.
        $profiles[$code] = [
            'code' => $code,
            'faculty' => $faculty,
            'modality' => $modality,
            'location' => $location,
            'contracttype' => strtoupper($contracttype),
            'program' => $program,
            'profile' => $profile_text,
            'courses' => $courses,
        ];

        if ($verbose) {
            echo "  Row $rownum: imported $code ({$location}, {$modality})\n";
        }
    }

    fclose($handle);

    if ($verbose) {
        echo "\n  Total rows processed: $rownum\n";
        echo "  Profiles imported: " . count($profiles) . "\n";
        if ($errors > 0) {
            echo "  Errors: $errors\n";
        }
    }

    return $profiles;
}

/**
 * Normalize location key from various input formats.
 *
 * @param string $location Location name from CSV.
 * @return string Normalized location key.
 */
function normalize_location_key($location) {
    $location = strtoupper(trim($location));

    // Remove common prefixes.
    $location = preg_replace('/^(?:ISER\s+)?(?:SEDE\s+|CENTRO\s+TUTORIAL\s+)?/i', '', $location);
    $location = trim($location);

    // Map common variations.
    $map = [
        'PAMPLONA' => 'PAMPLONA',
        'CÚCUTA' => 'CUCUTA',
        'CUCUTA' => 'CUCUTA',
        'SAN JOSÉ DE CÚCUTA' => 'CUCUTA',
        'SAN JOSE DE CUCUTA' => 'CUCUTA',
        'TIBÚ' => 'TIBU',
        'TIBU' => 'TIBU',
        'SAN VICENTE' => 'SANVICENTE',
        'SAN VICENTE DEL CHUCURÍ' => 'SANVICENTE',
        'SAN VICENTE DE CHUCURÍ' => 'SANVICENTE',
        'SANVICENTE' => 'SANVICENTE',
        'EL TARRA' => 'ELTARRA',
        'ELTARRA' => 'ELTARRA',
        'TARRA' => 'ELTARRA',
        'OCAÑA' => 'OCANA',
        'OCANA' => 'OCANA',
        'PUEBLO BELLO' => 'PUEBLOBELLO',
        'PUEBLOBELLO' => 'PUEBLOBELLO',
        'SAN PABLO' => 'SANPABLO',
        'SANPABLO' => 'SANPABLO',
        'SANTA ROSA' => 'SANTAROSA',
        'SANTAROSA' => 'SANTAROSA',
        'SANTA ROSA DEL SUR' => 'SANTAROSA',
        'TAME' => 'TAME',
        'FUNDACIÓN' => 'FUNDACION',
        'FUNDACION' => 'FUNDACION',
        'CIMITARRA' => 'CIMITARRA',
        'SALAZAR' => 'SALAZAR',
        'TOLEDO' => 'TOLEDO',
        'SARDINATA' => 'SARDINATA',
        'SARAVENA' => 'SARAVENA',
    ];

    if (isset($map[$location])) {
        return $map[$location];
    }

    // Try to detect from detect_location function.
    $detected = detect_location($location);
    if ($detected) {
        return $detected;
    }

    // Default to PAMPLONA if unrecognized.
    return 'PAMPLONA';
}
