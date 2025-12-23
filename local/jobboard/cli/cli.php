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
    // Application deletion options.
    'delete-application' => false,
    'idnumber' => null,
    'application-id' => null,
    'vacancy-id' => null,
    'list-applications' => false,
    // Sync from sedes folder options.
    'sync-sedes' => false,
    'sedes-path' => null,
    // Sync metadata (faculties, programs).
    'sync-metadata' => false,
    'companyid' => null,
    // Normalize programs in database.
    'normalize-programs' => false,
    // Restore orphaned application.
    'restore-application' => false,
    'userid' => null,
    'new-vacancyid' => null,
    'source-applicationid' => null,
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
    'D' => 'delete-application',
    'I' => 'idnumber',
    'A' => 'application-id',
    'V' => 'vacancy-id',
    'L' => 'list-applications',
    'Y' => 'sync-sedes',
    'M' => 'sync-metadata',
    'N' => 'normalize-programs',
    'R' => 'restore-application',
    'U' => 'userid',
    'W' => 'new-vacancyid',
    'X' => 'source-applicationid',
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

SYNC FROM SEDES (Recommended for updates):
  -Y, --sync-sedes          SYNC vacancies from sedes/ folder JSONs
                            - Creates new vacancies not in DB
                            - Updates existing vacancies (by code+location+modality)
                            - Leaves unchanged vacancies not in JSONs
  --sedes-path=PATH         Path to sedes folder (default: ./sedes)

  -M, --sync-metadata       SYNC faculties and programs from sedes/ folder JSONs
                            - Creates/updates faculties (FII, FCAS, BIENESTAR)
                            - Creates/updates academic programs
  --companyid=ID            IOMAD company ID for faculties (default: 1)

  -N, --normalize-programs  NORMALIZE program names in database
                            - Fixes missing accents (TECNOLOGIA -> TECNOLOGÍA)
                            - Standardizes names (TODOS LOS PROGRAMAS -> TODOS LOS PROGRAMAS ACADÉMICOS)
                            - Updates vacancy.department field

  SYNC EXAMPLES:
    # Preview sync changes (dry run)
    php cli.php --sync-sedes --convocatoria=1 --dryrun --verbose

    # Sync vacancies for convocatoria 1
    php cli.php --sync-sedes --convocatoria=1 --verbose

    # Sync and publish
    php cli.php --sync-sedes --convocatoria=1 --status=published --public --verbose

    # Sync faculties and programs (run this first!)
    php cli.php --sync-metadata --verbose

    # Full sync: metadata first, then vacancies
    php cli.php --sync-metadata --verbose && php cli.php --sync-sedes --convocatoria=1 --status=published --public --verbose

APPLICATION MANAGEMENT:
  -D, --delete-application  DELETE applications (requires --idnumber or --application-id)
  -I, --idnumber=ID         User idnumber to identify the applicant
  -A, --application-id=ID   Specific application ID to delete
  -V, --vacancy-id=ID       Filter by vacancy ID (optional, use with --idnumber)
  -L, --list-applications   List applications instead of deleting

  IMPORTANT: Deleting an application removes ALL related data:
    - All uploaded documents and their files
    - Document validation records
    - Workflow history logs
    - Evaluation records (if any)
    - Notification records

APPLICATION DELETION EXAMPLES:
  # List all applications for a user by idnumber
  php cli.php --list-applications --idnumber=1234567890

  # Delete ALL applications for a user by idnumber
  php cli.php --delete-application --idnumber=1234567890

  # Delete applications for a user in a specific vacancy
  php cli.php --delete-application --idnumber=1234567890 --vacancy-id=42

  # Delete a specific application by ID
  php cli.php --delete-application --application-id=123

  # Dry run (preview what would be deleted)
  php cli.php --delete-application --idnumber=1234567890 --dryrun --verbose

APPLICATION RESTORATION:
  -R, --restore-application  RESTORE an orphaned application to a new vacancy
  -U, --userid=ID            User ID (from mdl_user.id)
  -W, --new-vacancyid=ID     New vacancy ID to assign the application to
  -X, --source-applicationid=ID  (Optional) Copy documents from this application ID

  Use this to restore applications that were orphaned during sync
  (e.g., when vacancy codes changed from FII-07 to FII-07a/FII-07b)

  RESTORE EXAMPLES:
    # Restore application for user 3064 to vacancy 793 (empty, no documents)
    php cli.php --restore-application --userid=3064 --new-vacancyid=793 --verbose

    # Restore WITH documents from a previous application (ID 25)
    php cli.php --restore-application --userid=3064 --new-vacancyid=793 --source-applicationid=25 --verbose

    # Preview what would be created (dry run)
    php cli.php --restore-application --userid=3064 --new-vacancyid=793 --dryrun --verbose

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
// SYNC METADATA (FACULTIES AND PROGRAMS)
// ============================================================
if ($options['sync-metadata']) {
    if (!$moodleavailable) {
        cli_error("Sync metadata requires Moodle. Run from Moodle installation.");
    }

    $verbose = $options['verbose'];
    $dryrun = $options['dryrun'];
    $companyid = $options['companyid'] ? (int) $options['companyid'] : 1;
    $sedespath = $options['sedes-path'] ?: __DIR__ . '/sedes';

    cli_heading("Sync Metadata from Sedes Folder");
    echo "Company ID: $companyid\n";
    echo "Sedes path: $sedespath\n";
    if ($dryrun) {
        echo "*** DRY RUN MODE - No changes will be made ***\n";
    }
    echo "\n";

    // Verify sedes folder exists.
    if (!is_dir($sedespath)) {
        cli_error("Sedes folder not found: $sedespath");
    }

    // Collect all unique faculties and programs from JSONs.
    $faculties = [];
    $programs = [];
    $sedefolders = glob($sedespath . '/*', GLOB_ONLYDIR);

    foreach ($sedefolders as $sedefolder) {
        $jsonfiles = glob($sedefolder . '/*.json');
        foreach ($jsonfiles as $jsonfile) {
            $basename = basename($jsonfile);
            if ($basename === '_RESUMEN.json') {
                continue;
            }

            $content = file_get_contents($jsonfile);
            $data = json_decode($content, true);

            if (!$data || !isset($data['vacancies'])) {
                continue;
            }

            foreach ($data['vacancies'] as $vac) {
                $faculty = trim($vac['faculty'] ?? '');
                $program = trim($vac['program'] ?? '');

                if (!empty($faculty) && !isset($faculties[$faculty])) {
                    $faculties[$faculty] = [
                        'code' => $faculty,
                        'name' => get_faculty_name($faculty),
                    ];
                }

                if (!empty($program) && !empty($faculty)) {
                    $programKey = $faculty . '::' . $program;
                    if (!isset($programs[$programKey])) {
                        $programs[$programKey] = [
                            'faculty' => $faculty,
                            'name' => $program,
                            'code' => generate_program_code($program),
                        ];
                    }
                }
            }
        }
    }

    echo "Found " . count($faculties) . " unique faculties\n";
    echo "Found " . count($programs) . " unique programs\n\n";

    // Stats.
    $stats = [
        'faculties_created' => 0,
        'faculties_updated' => 0,
        'programs_created' => 0,
        'programs_updated' => 0,
        'errors' => 0,
    ];

    $adminuser = get_admin();
    $now = time();

    // ---- SYNC FACULTIES ----
    cli_heading("Syncing Faculties");
    foreach ($faculties as $facultyCode => $facultyData) {
        $existing = $DB->get_record('local_jobboard_faculty', [
            'companyid' => $companyid,
            'code' => $facultyCode,
        ]);

        if ($existing) {
            if (!$dryrun) {
                $existing->name = $facultyData['name'];
                $existing->timemodified = $now;
                try {
                    $DB->update_record('local_jobboard_faculty', $existing);
                    $stats['faculties_updated']++;
                    if ($verbose) {
                        echo "UPDATED faculty: {$facultyCode} - {$facultyData['name']}\n";
                    }
                } catch (Exception $e) {
                    echo "ERROR updating faculty {$facultyCode}: " . $e->getMessage() . "\n";
                    $stats['errors']++;
                }
            } else {
                $stats['faculties_updated']++;
                if ($verbose) {
                    echo "DRY UPDATE faculty: {$facultyCode} - {$facultyData['name']}\n";
                }
            }
        } else {
            $record = new stdClass();
            $record->companyid = $companyid;
            $record->code = $facultyCode;
            $record->name = $facultyData['name'];
            $record->shortname = $facultyCode;
            $record->enabled = 1;
            $record->sortorder = 0;
            $record->timecreated = $now;

            if (!$dryrun) {
                try {
                    $facultyid = $DB->insert_record('local_jobboard_faculty', $record);
                    $stats['faculties_created']++;
                    if ($verbose) {
                        echo "CREATED faculty: {$facultyCode} - {$facultyData['name']} [ID: $facultyid]\n";
                    }
                } catch (Exception $e) {
                    echo "ERROR creating faculty {$facultyCode}: " . $e->getMessage() . "\n";
                    $stats['errors']++;
                }
            } else {
                $stats['faculties_created']++;
                if ($verbose) {
                    echo "DRY CREATE faculty: {$facultyCode} - {$facultyData['name']}\n";
                }
            }
        }
    }

    // ---- SYNC PROGRAMS ----
    cli_heading("Syncing Programs");

    // Get faculty IDs.
    $facultyIds = [];
    $existingFaculties = $DB->get_records('local_jobboard_faculty', ['companyid' => $companyid]);
    foreach ($existingFaculties as $f) {
        $facultyIds[$f->code] = $f->id;
    }

    foreach ($programs as $programKey => $programData) {
        $facultyCode = $programData['faculty'];
        $facultyId = $facultyIds[$facultyCode] ?? null;

        if (!$facultyId) {
            echo "WARNING: Faculty {$facultyCode} not found for program {$programData['name']}\n";
            continue;
        }

        $existing = $DB->get_record('local_jobboard_program', [
            'facultyid' => $facultyId,
            'code' => $programData['code'],
        ]);

        if ($existing) {
            if (!$dryrun) {
                $existing->name = $programData['name'];
                $existing->timemodified = $now;
                try {
                    $DB->update_record('local_jobboard_program', $existing);
                    $stats['programs_updated']++;
                    if ($verbose) {
                        echo "UPDATED program: [{$facultyCode}] {$programData['name']}\n";
                    }
                } catch (Exception $e) {
                    echo "ERROR updating program {$programData['code']}: " . $e->getMessage() . "\n";
                    $stats['errors']++;
                }
            } else {
                $stats['programs_updated']++;
                if ($verbose) {
                    echo "DRY UPDATE program: [{$facultyCode}] {$programData['name']}\n";
                }
            }
        } else {
            $record = new stdClass();
            $record->facultyid = $facultyId;
            $record->code = $programData['code'];
            $record->name = $programData['name'];
            $record->shortname = mb_substr($programData['name'], 0, 50, 'UTF-8');
            $record->enabled = 1;
            $record->sortorder = 0;
            $record->timecreated = $now;

            if (!$dryrun) {
                try {
                    $programid = $DB->insert_record('local_jobboard_program', $record);
                    $stats['programs_created']++;
                    if ($verbose) {
                        echo "CREATED program: [{$facultyCode}] {$programData['name']} [ID: $programid]\n";
                    }
                } catch (Exception $e) {
                    echo "ERROR creating program {$programData['code']}: " . $e->getMessage() . "\n";
                    $stats['errors']++;
                }
            } else {
                $stats['programs_created']++;
                if ($verbose) {
                    echo "DRY CREATE program: [{$facultyCode}] {$programData['name']}\n";
                }
            }
        }
    }

    // Summary.
    echo "\n";
    cli_heading("Sync Metadata Summary");
    echo "Faculties created: {$stats['faculties_created']}\n";
    echo "Faculties updated: {$stats['faculties_updated']}\n";
    echo "Programs created: {$stats['programs_created']}\n";
    echo "Programs updated: {$stats['programs_updated']}\n";
    echo "Errors: {$stats['errors']}\n";

    if ($dryrun) {
        echo "\n*** DRY RUN - No changes were made ***\n";
    }

    // Continue to next operation instead of exiting
}

/**
 * Get faculty full name from code.
 */
function get_faculty_name($code) {
    $names = [
        'FII' => 'Facultad de Ingenierías e Informática',
        'FCAS' => 'Facultad de Ciencias Administrativas y Sociales',
        'BIENESTAR' => 'Bienestar Institucional',
    ];
    return $names[$code] ?? $code;
}

/**
 * Generate a program code from name.
 */
function generate_program_code($name) {
    // Normalize and create a short code.
    $name = mb_strtoupper($name, 'UTF-8');
    $name = preg_replace('/[^A-Z0-9\s]/', '', $name);
    $words = preg_split('/\s+/', trim($name));

    // Take first letter of each significant word.
    $significant = array_filter($words, function($w) {
        return !in_array($w, ['EN', 'DE', 'LA', 'LAS', 'LOS', 'Y', 'E', 'O', 'A']);
    });

    if (count($significant) <= 3) {
        return implode('', array_map(fn($w) => mb_substr($w, 0, 3, 'UTF-8'), $significant));
    }

    return implode('', array_map(fn($w) => mb_substr($w, 0, 1, 'UTF-8'), array_slice($significant, 0, 8)));
}

// ============================================================
// NORMALIZE PROGRAMS IN DATABASE
// ============================================================
if ($options['normalize-programs']) {
    if (!$moodleavailable) {
        cli_error("Normalize programs requires Moodle. Run from Moodle installation.");
    }

    $verbose = $options['verbose'];
    $dryrun = $options['dryrun'];

    cli_heading("Normalize Program Names in Database");
    if ($dryrun) {
        echo "*** DRY RUN MODE - No changes will be made ***\n";
    }
    echo "\n";

    // Normalization function
    $normalizeProgram = function($program) {
        $trimmed = trim($program);

        // Step 1: Convert to uppercase first
        $result = mb_strtoupper($trimmed, 'UTF-8');

        // Step 2: Fix common typos
        $typoFixes = [
            'TECNOLOGÍA NE ' => 'TECNOLOGÍA EN ',  // Typo: NE -> EN
            ' / ' => ' - ',  // Normalize separator
        ];
        foreach ($typoFixes as $from => $to) {
            $result = str_replace($from, $to, $result);
        }

        // Step 3: Normalize accents - replace non-accented with accented
        $accentReplacements = [
            'TECNOLOGIA' => 'TECNOLOGÍA',
            'TECNICA' => 'TÉCNICA',
            'GESTION' => 'GESTIÓN',
            'PRODUCCION' => 'PRODUCCIÓN',
            'PROTECCION' => 'PROTECCIÓN',
            'RECUPERACION' => 'RECUPERACIÓN',
            'CONSTRUCCION' => 'CONSTRUCCIÓN',
            'ECOSISTEMA FORESTALES' => 'ECOSISTEMAS FORESTALES',
            'INGENIERIAS' => 'INGENIERÍAS',
            'INFORMATICA' => 'INFORMÁTICA',
            'ACADEMICOS' => 'ACADÉMICOS',
        ];
        foreach ($accentReplacements as $from => $to) {
            if (mb_strpos($result, $to, 0, 'UTF-8') === false) {
                $result = str_replace($from, $to, $result);
            }
        }

        // Step 4: Normalize specific program names to standard format
        $programMapping = [
            // Incomplete entries - map to most likely full name
            'TECNOLOGÍA EN' => 'TECNOLOGÍA EN GESTIÓN EMPRESARIAL',
            'TECNOLOGÍA EN GESTIÓN' => 'TECNOLOGÍA EN GESTIÓN EMPRESARIAL',
            // TODOS variations
            'TODOS LOS PROGRAMAS' => 'TODOS LOS PROGRAMAS ACADÉMICOS',
            'TODOS LOS PROGRAMAS DE LA FACULTAD DE INGENIERÍAS E INFORMÁTICA' => 'TODOS LOS PROGRAMAS ACADÉMICOS',
            // BIENESTAR
            'BIENESTAR INSTITUCIONAL' => 'BIENESTAR',
        ];

        if (isset($programMapping[$result])) {
            $result = $programMapping[$result];
        }

        return $result;
    };

    // Get all distinct programs from vacancies
    $programs = $DB->get_records_sql('SELECT DISTINCT department FROM {local_jobboard_vacancy} ORDER BY department');
    $stats = ['normalized' => 0, 'unchanged' => 0, 'vacancies_updated' => 0];

    cli_heading("Programs Found");
    foreach ($programs as $p) {
        $original = $p->department;
        $normalized = $normalizeProgram($original);

        if ($original !== $normalized) {
            echo "  ✗ $original\n    → $normalized\n\n";

            if (!$dryrun) {
                // Update all vacancies with this program name
                $count = $DB->count_records('local_jobboard_vacancy', ['department' => $original]);
                $DB->execute(
                    "UPDATE {local_jobboard_vacancy} SET department = ? WHERE department = ?",
                    [$normalized, $original]
                );
                $stats['vacancies_updated'] += $count;
            }
            $stats['normalized']++;
        } else {
            if ($verbose) {
                echo "  ✓ $original\n";
            }
            $stats['unchanged']++;
        }
    }

    echo "\n";
    cli_heading("Summary");
    echo "Programs normalized: " . $stats['normalized'] . "\n";
    echo "Programs unchanged: " . $stats['unchanged'] . "\n";
    if (!$dryrun) {
        echo "Vacancies updated: " . $stats['vacancies_updated'] . "\n";
    }

    // Continue to next operation instead of exiting
}

// ============================================================
// SYNC FROM SEDES FOLDER
// ============================================================
if ($options['sync-sedes']) {
    if (!$moodleavailable) {
        cli_error("Sync requires Moodle. Run from Moodle installation.");
    }

    $verbose = $options['verbose'];
    $dryrun = $options['dryrun'];
    $convocatoriaid = $options['convocatoria'] ? (int) $options['convocatoria'] : null;
    $sedespath = $options['sedes-path'] ?: __DIR__ . '/sedes';

    if (!$convocatoriaid) {
        cli_error("You must specify --convocatoria=ID for sync operation");
    }

    // Verify convocatoria exists.
    $convocatoria = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
    if (!$convocatoria) {
        cli_error("Convocatoria with ID $convocatoriaid not found");
    }

    cli_heading("Sync Vacancies from Sedes Folder");
    echo "Convocatoria: {$convocatoria->name} (ID: $convocatoriaid)\n";
    echo "Sedes path: $sedespath\n";
    if ($dryrun) {
        echo "*** DRY RUN MODE - No changes will be made ***\n";
    }
    echo "\n";

    // Verify sedes folder exists.
    if (!is_dir($sedespath)) {
        cli_error("Sedes folder not found: $sedespath");
    }

    // Location name mapping (folder name -> DB location name).
    $locationMapping = [
        'PAMPLONA' => 'Pamplona (Sede Principal)',
        'CUCUTA' => 'Cúcuta',
        'TIBU' => 'Tibú',
        'OCANA' => 'Ocaña',
        'TOLEDO' => 'Toledo',
        'EL_TARRA' => 'El Tarra',
        'SARDINATA' => 'Sardinata',
        'SAN_VICENTE_DE_CHUCURI' => 'San Vicente del Chucurí',
        'PUEBLO_BELLO' => 'Pueblo Bello',
        'SAN_PABLO' => 'San Pablo (Sur de Bolívar)',
        'SANTA_ROSA_DEL_SUR' => 'Santa Rosa del Sur',
        'FUNDACION' => 'Fundación',
        'CIMITARRA' => 'Cimitarra',
        'SALAZAR_DE_LAS_PALMAS' => 'SALAZAR DE LAS PALMAS',
        'TAME' => 'Tame',
        'SARAVENA' => 'Saravena',
        'DISTANCIA_GENERAL' => 'A Distancia (Todos los CT)',
    ];

    // Modality mapping.
    $modalityMapping = [
        'PRESENCIAL' => 'presencial',
        'DISTANCIA' => 'distancia',
        'PRESENCIAL/DISTANCIA' => 'presencial', // Default to presencial for mixed.
    ];

    // Contract type mapping (normalized types from JSON files).
    $contractMapping = [
        'OCASIONAL TIEMPO COMPLETO' => 'ocasional_tc',
        'HORA CÁTEDRA' => 'catedra',
    ];

    // Faculty mapping for department field.
    $facultyMapping = [
        'FII' => 'Ingenierías e Informática',
        'FCAS' => 'Ciencias Administrativas y Sociales',
        'BIENESTAR' => 'BIENESTAR INSTITUCIONAL',
    ];

    // Read all vacancies from sedes folder.
    $jsonVacancies = [];
    $sedeFolders = scandir($sedespath);

    foreach ($sedeFolders as $sedeFolder) {
        if ($sedeFolder === '.' || $sedeFolder === '..') {
            continue;
        }

        $sedePath = $sedespath . '/' . $sedeFolder;
        if (!is_dir($sedePath)) {
            continue;
        }

        $locationName = $locationMapping[$sedeFolder] ?? $sedeFolder;

        // Read all JSON files in this sede folder.
        $jsonFiles = glob($sedePath . '/*.json');
        foreach ($jsonFiles as $jsonFile) {
            $filename = basename($jsonFile);
            if ($filename === '_RESUMEN.json') {
                continue;
            }

            $content = file_get_contents($jsonFile);
            $data = json_decode($content, true);
            if (!$data || !isset($data['vacancies'])) {
                if ($verbose) {
                    echo "WARNING: Invalid JSON in $jsonFile\n";
                }
                continue;
            }

            foreach ($data['vacancies'] as $vacancy) {
                $code = $vacancy['code'] ?? '';
                if (empty($code)) {
                    continue;
                }

                // Determine modality from vacancy or data.
                $modality = $vacancy['modality'] ?? $data['modality'] ?? 'PRESENCIAL';
                $modalityDb = $modalityMapping[strtoupper($modality)] ?? 'presencial';

                // Get program for unique key.
                $program = $vacancy['program'] ?? $data['program'] ?? '';

                // Create unique key (includes program to allow same code in different programs).
                $uniqueKey = $code . '|' . $locationName . '|' . $modalityDb . '|' . $program;

                $jsonVacancies[$uniqueKey] = [
                    'code' => $code,
                    'location' => $locationName,
                    'modality' => $modalityDb,
                    'program' => $vacancy['program'] ?? $data['program'] ?? '',
                    'profile' => $vacancy['profile'] ?? '',
                    'activities' => $vacancy['activities'] ?? $vacancy['courses'] ?? '',
                    'contract_type' => $vacancy['contract_type'] ?? 'CATEDRA',
                    'faculty' => $vacancy['faculty'] ?? $data['faculty'] ?? 'FCAS',
                    'positions' => (int) ($vacancy['positions'] ?? 1),
                    'sede_folder' => $sedeFolder,
                ];
            }
        }
    }

    echo "Vacancies read from JSONs: " . count($jsonVacancies) . "\n\n";

    // Get existing vacancies for this convocatoria.
    $existingVacancies = $DB->get_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoriaid]);
    $existingByKey = [];
    $existingByCode = []; // Additional index by code only
    foreach ($existingVacancies as $vac) {
        // Include department (program) in key to match JSON key structure
        $key = $vac->code . '|' . $vac->location . '|' . $vac->modality . '|' . ($vac->department ?? '');
        $existingByKey[$key] = $vac;
        // Also index by code for application preservation
        if (!isset($existingByCode[$vac->code])) {
            $existingByCode[$vac->code] = [];
        }
        $existingByCode[$vac->code][] = $vac;
    }

    echo "Existing vacancies in DB for convocatoria $convocatoriaid: " . count($existingVacancies) . "\n\n";

    $adminuser = get_admin();
    $now = time();

    // Statistics.
    $stats = [
        'created' => 0,
        'deleted' => 0,
        'restored' => 0,
        'docs_restored' => 0,
        'orphaned' => 0,
        'errors' => 0,
    ];

    // ========================================================================
    // PASO 1: Respaldar TODAS las postulaciones con info de vacante Y DOCUMENTOS
    // ========================================================================
    cli_heading("PASO 1: Respaldar Postulaciones y Documentos");

    $sql = "SELECT a.*, v.code, v.department as program, v.title as vacancy_title,
                   v.location, v.modality
            FROM {local_jobboard_application} a
            INNER JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
            WHERE v.convocatoriaid = ?";
    $applications = $DB->get_records_sql($sql, [$convocatoriaid]);

    // Backup documents for each application
    $applicationDocuments = [];
    $totalDocs = 0;
    if (!empty($applications)) {
        $appIds = array_keys($applications);
        list($inSql, $params) = $DB->get_in_or_equal($appIds, SQL_PARAMS_NAMED);
        $documents = $DB->get_records_select('local_jobboard_document', "applicationid $inSql", $params);

        // Group documents by application ID
        foreach ($documents as $doc) {
            if (!isset($applicationDocuments[$doc->applicationid])) {
                $applicationDocuments[$doc->applicationid] = [];
            }
            $applicationDocuments[$doc->applicationid][] = $doc;
            $totalDocs++;
        }
    }

    if (!empty($applications)) {
        echo "Postulaciones respaldadas: " . count($applications) . "\n";
        echo "Documentos respaldados: " . $totalDocs . "\n";
        if ($verbose) {
            foreach ($applications as $app) {
                // Extract profile from title (format: "PROGRAM - PROFILE")
                $profile = '';
                if (strpos($app->vacancy_title, ' - ') !== false) {
                    $parts = explode(' - ', $app->vacancy_title, 2);
                    $profile = $parts[1] ?? '';
                }
                $docCount = isset($applicationDocuments[$app->id]) ? count($applicationDocuments[$app->id]) : 0;
                echo "  - App #{$app->id}: {$app->code} @ {$app->location} | Docs: {$docCount} | Programa: {$app->program} | Perfil: " . mb_substr($profile, 0, 40, 'UTF-8') . "\n";
            }
        }
        echo "\n";
    } else {
        echo "No hay postulaciones para respaldar.\n\n";
    }

    // ========================================================================
    // PASO 2: Eliminar documentos, postulaciones y vacantes (datos ya respaldados en memoria)
    // ========================================================================
    cli_heading("PASO 2: Eliminar Documentos, Postulaciones y Vacantes");

    if (!empty($existingVacancies)) {
        if (!$dryrun) {
            // Get all vacancy IDs for this convocatoria
            $vacancyIds = array_keys($existingVacancies);

            // Delete documents for applications (data is already in $applicationDocuments)
            if (!empty($applications)) {
                $appIds = array_keys($applications);
                list($docInSql, $docParams) = $DB->get_in_or_equal($appIds, SQL_PARAMS_NAMED);
                $DB->execute("DELETE FROM {local_jobboard_document} WHERE applicationid $docInSql", $docParams);
                echo "Documentos eliminados: " . $totalDocs . " (respaldados en memoria)\n";
            }

            // Delete applications for these vacancies (data is already in $applications)
            if (!empty($applications)) {
                list($inSql, $params) = $DB->get_in_or_equal($vacancyIds, SQL_PARAMS_NAMED);
                $DB->execute("DELETE FROM {local_jobboard_application} WHERE vacancyid $inSql", $params);
                echo "Postulaciones eliminadas: " . count($applications) . " (respaldadas en memoria)\n";
            }

            // Delete all vacancies
            $DB->delete_records('local_jobboard_vacancy', ['convocatoriaid' => $convocatoriaid]);
            $stats['deleted'] = count($existingVacancies);
        } else {
            $stats['deleted'] = count($existingVacancies);
        }
        echo "Vacantes eliminadas: {$stats['deleted']}\n\n";
    } else {
        echo "No hay vacantes para eliminar.\n\n";
    }

    // ========================================================================
    // PASO 3: Crear TODAS las vacantes desde JSONs
    // ========================================================================
    cli_heading("PASO 3: Crear Vacantes desde JSONs");

    // Index new vacancies by program+profile for matching applications
    $newVacanciesByProgramProfile = [];
    $newVacanciesById = [];

    foreach ($jsonVacancies as $key => $vac) {
        $code = $vac['code'];
        $location = $vac['location'];
        $modality = $vac['modality'];
        $program = $vac['program'];
        $profile = $vac['profile'];

        // Build vacancy record.
        $record = new stdClass();
        $record->code = $code;
        $record->title = $program . ' - ' . mb_substr($profile, 0, 100, 'UTF-8');
        $record->description = build_vacancy_description_sync($vac);
        $record->contracttype = $contractMapping[$vac['contract_type']] ?? 'catedra';
        $record->duration = $record->contracttype === 'ocasional_tc'
            ? '4 meses (período académico semestral) - Contrato laboral a término fijo'
            : 'Semestre académico (16 semanas) - Contrato de prestación de servicios por horas';
        $record->location = $location;
        $record->modality = $modality;
        $record->department = $program;
        $record->convocatoriaid = $convocatoriaid;
        $record->positions = $vac['positions'];
        $record->requirements = build_vacancy_requirements_sync($vac);
        $record->desirable = build_vacancy_desirable_sync();
        $record->status = $options['status'] ?: 'available';
        $record->publicationtype = $options['public'] ? 'public' : 'internal';
        $record->createdby = $adminuser->id;
        $record->timecreated = $now;

        if (!$dryrun) {
            try {
                $newid = $DB->insert_record('local_jobboard_vacancy', $record);
                $stats['created']++;

                // Store for matching
                $newVacanciesById[$newid] = (object)[
                    'id' => $newid,
                    'code' => $code,
                    'program' => $program,
                    'profile' => $profile,
                    'location' => $location,
                    'modality' => $modality,
                ];

                // Index by program+profile (normalized)
                $matchKey = mb_strtoupper(trim($program), 'UTF-8') . '|' . mb_strtoupper(trim($profile), 'UTF-8');
                if (!isset($newVacanciesByProgramProfile[$matchKey])) {
                    $newVacanciesByProgramProfile[$matchKey] = [];
                }
                $newVacanciesByProgramProfile[$matchKey][] = $newid;

                // Also index by code for fallback matching
                $codeKey = $code . '|' . $location . '|' . $modality;
                $newVacanciesByProgramProfile[$codeKey] = [$newid];

                if ($verbose) {
                    echo "+ CREADA: $code @ $location ($modality) [ID: $newid]\n";
                }
            } catch (Exception $e) {
                echo "✗ ERROR creando $code: " . $e->getMessage() . "\n";
                $stats['errors']++;
            }
        } else {
            $stats['created']++;
            if ($verbose) {
                echo "[DRY] + CREADA: $code @ $location ($modality)\n";
            }
        }
    }

    echo "\nCreadas: {$stats['created']} vacantes\n\n";

    // ========================================================================
    // PASO 4: Re-crear postulaciones con nuevas vacantes
    // ========================================================================
    if (!empty($applications)) {
        cli_heading("PASO 4: Restaurar Postulaciones");

        $orphanedApps = [];

        foreach ($applications as $app) {
            // Extract profile from old vacancy title
            $oldProfile = '';
            if (strpos($app->vacancy_title, ' - ') !== false) {
                $parts = explode(' - ', $app->vacancy_title, 2);
                $oldProfile = $parts[1] ?? '';
            }

            // Try to find matching vacancy
            $matchingVacancyId = null;
            $matchMethod = '';

            // Method 1: Match by program + profile (exact)
            $matchKey = mb_strtoupper(trim($app->program), 'UTF-8') . '|' . mb_strtoupper(trim($oldProfile), 'UTF-8');
            if (isset($newVacanciesByProgramProfile[$matchKey])) {
                $matchingVacancyId = $newVacanciesByProgramProfile[$matchKey][0];
                $matchMethod = 'programa+perfil';
            }

            // Method 2: Match by code + location + modality
            if (!$matchingVacancyId) {
                $codeKey = $app->code . '|' . $app->location . '|' . $app->modality;
                if (isset($newVacanciesByProgramProfile[$codeKey])) {
                    $matchingVacancyId = $newVacanciesByProgramProfile[$codeKey][0];
                    $matchMethod = 'código+ubicación';
                }
            }

            // Method 3: Match by code only (any location)
            if (!$matchingVacancyId) {
                foreach ($newVacanciesById as $vid => $vinfo) {
                    if ($vinfo->code === $app->code) {
                        $matchingVacancyId = $vid;
                        $matchMethod = 'código';
                        break;
                    }
                }
            }

            // Method 4: Match by program only (first match)
            if (!$matchingVacancyId) {
                foreach ($newVacanciesById as $vid => $vinfo) {
                    if (mb_strtoupper(trim($vinfo->program), 'UTF-8') === mb_strtoupper(trim($app->program), 'UTF-8')) {
                        $matchingVacancyId = $vid;
                        $matchMethod = 'programa';
                        break;
                    }
                }
            }

            if ($matchingVacancyId) {
                if (!$dryrun) {
                    // Re-create application with new vacancy ID
                    $newApp = new stdClass();
                    $newApp->vacancyid = $matchingVacancyId;
                    $newApp->userid = $app->userid;
                    $newApp->status = $app->status;
                    $newApp->timecreated = $app->timecreated;
                    $newApp->timemodified = $app->timemodified;
                    // Copy other fields if they exist
                    if (isset($app->coverletter)) $newApp->coverletter = $app->coverletter;
                    if (isset($app->resume)) $newApp->resume = $app->resume;
                    if (isset($app->notes)) $newApp->notes = $app->notes;
                    // Copy all application fields
                    if (isset($app->statusnotes)) $newApp->statusnotes = $app->statusnotes;
                    if (isset($app->isexemption)) $newApp->isexemption = $app->isexemption;
                    if (isset($app->exemptionreason)) $newApp->exemptionreason = $app->exemptionreason;
                    if (isset($app->consentgiven)) $newApp->consentgiven = $app->consentgiven;
                    if (isset($app->consenttimestamp)) $newApp->consenttimestamp = $app->consenttimestamp;
                    if (isset($app->consentip)) $newApp->consentip = $app->consentip;
                    if (isset($app->consentuseragent)) $newApp->consentuseragent = $app->consentuseragent;
                    if (isset($app->digitalsignature)) $newApp->digitalsignature = $app->digitalsignature;
                    if (isset($app->applicationdata)) $newApp->applicationdata = $app->applicationdata;
                    if (isset($app->reviewerid)) $newApp->reviewerid = $app->reviewerid;

                    try {
                        $newAppId = $DB->insert_record('local_jobboard_application', $newApp);
                        $stats['restored']++;

                        // Restore documents and create mdl_files entries using contenthash
                        $fs = get_file_storage();
                        $context = \context_system::instance();
                        $docsRestored = 0;
                        $filesCopied = 0;

                        if (isset($applicationDocuments[$app->id])) {
                            foreach ($applicationDocuments[$app->id] as $doc) {
                                // 1. Restore document record with new applicationid
                                $newDoc = clone $doc;
                                unset($newDoc->id);
                                $newDoc->applicationid = $newAppId;
                                try {
                                    $DB->insert_record('local_jobboard_document', $newDoc);
                                    $docsRestored++;
                                    $stats['docs_restored']++;

                                    // 2. Create mdl_files entry using contenthash (Opción A)
                                    if (!empty($doc->contenthash)) {
                                        // Check if file already exists for new application
                                        $existingFile = $fs->get_file(
                                            $context->id,
                                            'local_jobboard',
                                            'application_documents',
                                            $newAppId,
                                            '/',
                                            $doc->filename
                                        );

                                        if (!$existingFile) {
                                            // Find any existing file with same contenthash
                                            $sourceFile = $DB->get_record_sql(
                                                "SELECT * FROM {files}
                                                 WHERE contenthash = ?
                                                   AND filename <> '.'
                                                   AND filesize > 0
                                                 LIMIT 1",
                                                [$doc->contenthash]
                                            );

                                            if ($sourceFile) {
                                                // Get the stored_file object
                                                $storedFile = $fs->get_file_by_id($sourceFile->id);
                                                if ($storedFile) {
                                                    $newFileRecord = [
                                                        'contextid' => $context->id,
                                                        'component' => 'local_jobboard',
                                                        'filearea' => 'application_documents',
                                                        'itemid' => $newAppId,
                                                        'filepath' => '/',
                                                        'filename' => $doc->filename,
                                                    ];
                                                    $fs->create_file_from_storedfile($newFileRecord, $storedFile);
                                                    $filesCopied++;
                                                }
                                            }
                                        }
                                    }
                                } catch (Exception $e) {
                                    if ($verbose) {
                                        echo "  ⚠ Error restaurando documento: " . $e->getMessage() . "\n";
                                    }
                                }
                            }
                        }

                        if ($verbose) {
                            $matchedVac = $newVacanciesById[$matchingVacancyId];
                            $docInfo = $docsRestored > 0 ? " [+{$docsRestored} docs, {$filesCopied} files]" : "";
                            echo "✓ RESTAURADA: {$app->code} → {$matchedVac->code} @ {$matchedVac->location} (match: $matchMethod){$docInfo}\n";
                        }
                    } catch (Exception $e) {
                        echo "✗ ERROR restaurando App #{$app->id}: " . $e->getMessage() . "\n";
                        $stats['errors']++;
                    }
                } else {
                    $stats['restored']++;
                    if ($verbose) {
                        $matchedVac = $newVacanciesById[$matchingVacancyId];
                        $docCount = isset($applicationDocuments[$app->id]) ? count($applicationDocuments[$app->id]) : 0;
                        $docInfo = $docCount > 0 ? " [+{$docCount} docs]" : "";
                        echo "[DRY] ✓ RESTAURADA: {$app->code} → {$matchedVac->code} @ {$matchedVac->location} (match: $matchMethod){$docInfo}\n";
                    }
                }
            } else {
                $orphanedApps[] = $app;
                $stats['orphaned']++;
                echo "⚠ SIN MATCH: {$app->code} @ {$app->location} | Programa: {$app->program} | Perfil: " . mb_substr($oldProfile, 0, 40, 'UTF-8') . "\n";
            }
        }

        echo "\n";

        if (!empty($orphanedApps)) {
            echo "*** ATENCIÓN: {$stats['orphaned']} postulación(es) no encontraron vacante correspondiente ***\n";
            echo "Estas postulaciones NO fueron restauradas. Revise los programas/perfiles.\n\n";
        }
    }

    // Summary.
    echo "\n";
    cli_heading("Resumen de Sincronización");
    echo "╔════════════════════════════════════════════════════════╗\n";
    echo "║  Vacantes Eliminadas      : " . str_pad($stats['deleted'], 5) . "                    ║\n";
    echo "║  Vacantes Creadas         : " . str_pad($stats['created'], 5) . "                    ║\n";
    echo "║  Postulaciones Restauradas: " . str_pad($stats['restored'], 5) . "                    ║\n";
    echo "║  Documentos Restaurados   : " . str_pad($stats['docs_restored'], 5) . "                    ║\n";
    echo "║  Postulaciones Huérfanas  : " . str_pad($stats['orphaned'], 5) . "                    ║\n";
    echo "║  Errores                  : " . str_pad($stats['errors'], 5) . "                    ║\n";
    echo "╚════════════════════════════════════════════════════════╝\n";

    if ($dryrun) {
        echo "\n*** DRY RUN - No changes were made ***\n";
    }

    // Continue to next operation instead of exiting
}

/**
 * Compare old vacancy with new data and return list of changed fields.
 */
function get_vacancy_changes($existing, $newRecord) {
    $changes = [];
    $compareFields = [
        'title' => 'Título',
        'contracttype' => 'Tipo Contrato',
        'location' => 'Ubicación',
        'modality' => 'Modalidad',
        'department' => 'Programa',
        'positions' => 'Posiciones',
    ];

    foreach ($compareFields as $field => $label) {
        $oldVal = isset($existing->$field) ? trim($existing->$field) : '';
        $newVal = isset($newRecord->$field) ? trim($newRecord->$field) : '';

        if ($oldVal !== $newVal) {
            $changes[] = [
                'field' => $label,
                'old' => mb_substr($oldVal, 0, 50, 'UTF-8'),
                'new' => mb_substr($newVal, 0, 50, 'UTF-8'),
            ];
        }
    }

    return $changes;
}

/**
 * Print detailed change report for a vacancy.
 */
function print_vacancy_changes($code, $changes, $appCount = 0) {
    if (empty($changes)) {
        echo "  (sin cambios en campos principales)\n";
        return;
    }

    foreach ($changes as $change) {
        $fieldPad = str_pad($change['field'], 15);
        echo "    ├─ $fieldPad: \"{$change['old']}\" → \"{$change['new']}\"\n";
    }
}

/**
 * Build vacancy description HTML for sync.
 */
function build_vacancy_description_sync($vac) {
    $faculty = $vac['faculty'];
    $facultyName = [
        'FII' => 'Ingenierías e Informática',
        'FCAS' => 'Ciencias Administrativas y Sociales',
        'BIENESTAR' => 'BIENESTAR INSTITUCIONAL',
    ][$faculty] ?? $faculty;

    $contractLabel = $vac['contract_type'] === 'OCASIONAL TIEMPO COMPLETO'
        ? 'Ocasional Tiempo Completo'
        : 'Hora Cátedra';

    $duration = $vac['contract_type'] === 'OCASIONAL TIEMPO COMPLETO'
        ? '4 meses (período académico semestral) - Contrato laboral a término fijo'
        : 'Semestre académico (16 semanas) - Contrato de prestación de servicios por horas';

    $activities = $vac['activities'];
    if (is_array($activities)) {
        $activities = implode(' | ', $activities);
    }

    return '<div class="vacancy-description">
<div class="alert alert-secondary">
<strong>Código:</strong> ' . htmlspecialchars($vac['code']) . ' | <strong>Facultad:</strong> ' . htmlspecialchars($facultyName) . ' | <strong>Modalidad:</strong> ' . ucfirst($vac['modality']) . '
</div>
<h4>Programa Académico</h4>
<p><strong>' . htmlspecialchars($vac['program']) . '</strong></p>
<h4>Actividades/Cursos a Orientar</h4>
<p>' . htmlspecialchars($activities) . '</p>
<h4>Información de la Vinculación</h4>
<table class="table table-sm">
<tr><th>Tipo de Vinculación</th><td><span class="badge badge-primary">' . $contractLabel . '</span></td></tr>
<tr><th>Duración</th><td>' . $duration . '</td></tr>
<tr><th>Sede</th><td>' . htmlspecialchars($vac['location']) . '</td></tr>
<tr><th>Modalidad</th><td>' . ucfirst($vac['modality']) . '</td></tr>
<tr><th>Facultad</th><td>' . htmlspecialchars($facultyName) . '</td></tr>
</table>
</div>';
}

/**
 * Build vacancy requirements HTML for sync.
 */
function build_vacancy_requirements_sync($vac) {
    return '<div class="vacancy-requirements">
<h5>Perfil Profesional Requerido</h5>
<p class="lead">' . htmlspecialchars($vac['profile']) . '</p>
<h5>Requisitos Mínimos</h5>
<ul>
<li>Título profesional universitario acorde al perfil solicitado</li>
<li>No tener inhabilidades ni incompatibilidades para contratar con el Estado</li>
<li>Disponibilidad para la sede ' . htmlspecialchars($vac['location']) . ' en modalidad ' . ucfirst($vac['modality']) . '</li>
</ul>
<h5>Documentos a Presentar</h5>
<ul>
<li>Hoja de vida actualizada</li>
<li>Cédula de ciudadanía</li>
<li>Títulos académicos (pregrado y posgrado)</li>
<li>Tarjeta profesional (si aplica)</li>
<li>Certificaciones de experiencia laboral</li>
<li>Certificados de antecedentes vigentes</li>
</ul>
</div>';
}

/**
 * Build vacancy desirable HTML for sync.
 */
function build_vacancy_desirable_sync() {
    return '<div class="vacancy-desirable">
<h5>Requisitos Deseables</h5>
<ul>
<li>Experiencia docente en educación superior mínimo 1 año</li>
<li>Publicaciones académicas o investigaciones en el área</li>
<li>Manejo de herramientas tecnológicas para educación virtual</li>
<li>Dominio de un segundo idioma (preferiblemente inglés)</li>
</ul>
</div>';
}

// ============================================================
// APPLICATION MANAGEMENT (List/Delete)
// ============================================================
if ($options['delete-application'] || $options['list-applications']) {
    if (!$moodleavailable) {
        cli_error("Application management requires Moodle. Run from Moodle installation.");
    }

    $idnumber = $options['idnumber'];
    $applicationid = $options['application-id'] ? (int) $options['application-id'] : null;
    $vacancyid = $options['vacancy-id'] ? (int) $options['vacancy-id'] : null;
    $verbose = $options['verbose'];
    $dryrun = $options['dryrun'];

    // Validate parameters.
    if (empty($idnumber) && empty($applicationid)) {
        cli_error("You must specify --idnumber or --application-id");
    }

    // Find user by idnumber if provided.
    $userid = null;
    if (!empty($idnumber)) {
        $user = $DB->get_record('user', ['idnumber' => $idnumber]);
        if (!$user) {
            cli_error("User with idnumber '$idnumber' not found");
        }
        $userid = $user->id;
        echo "User found: {$user->firstname} {$user->lastname} (ID: {$user->id}, email: {$user->email})\n\n";
    }

    // Build query for applications.
    $params = [];
    $where = ['1=1'];

    if ($applicationid) {
        $where[] = 'a.id = :appid';
        $params['appid'] = $applicationid;
    }

    if ($userid) {
        $where[] = 'a.userid = :userid';
        $params['userid'] = $userid;
    }

    if ($vacancyid) {
        $where[] = 'a.vacancyid = :vacancyid';
        $params['vacancyid'] = $vacancyid;
    }

    $wheresql = implode(' AND ', $where);

    // Get applications.
    $sql = "SELECT a.*, v.code as vacancy_code, v.title as vacancy_title, v.location, v.modality,
                   u.firstname, u.lastname, u.email, u.idnumber as user_idnumber
            FROM {local_jobboard_application} a
            JOIN {local_jobboard_vacancy} v ON v.id = a.vacancyid
            JOIN {user} u ON u.id = a.userid
            WHERE $wheresql
            ORDER BY a.timecreated DESC";

    $applications = $DB->get_records_sql($sql, $params);

    if (empty($applications)) {
        echo "No applications found matching the criteria.\n";
        exit(0);
    }

    // ============================================================
    // LIST APPLICATIONS MODE
    // ============================================================
    if ($options['list-applications']) {
        cli_heading('Applications Found: ' . count($applications));
        echo str_repeat('-', 120) . "\n";
        printf("%-6s | %-12s | %-15s | %-30s | %-25s | %-20s\n",
            'ID', 'Status', 'Vacancy Code', 'Vacancy Title', 'Location', 'Date');
        echo str_repeat('-', 120) . "\n";

        foreach ($applications as $app) {
            $title = strlen($app->vacancy_title) > 28 ? substr($app->vacancy_title, 0, 25) . '...' : $app->vacancy_title;
            $location = strlen($app->location) > 23 ? substr($app->location, 0, 20) . '...' : $app->location;

            printf("%-6d | %-12s | %-15s | %-30s | %-25s | %-20s\n",
                $app->id,
                $app->status,
                $app->vacancy_code,
                $title,
                $location,
                date('Y-m-d H:i', $app->timecreated)
            );

            if ($verbose) {
                // Show documents count.
                $doccount = $DB->count_records('local_jobboard_document', ['applicationid' => $app->id]);
                $workflowcount = $DB->count_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);
                echo "       | Documents: $doccount | Workflow entries: $workflowcount\n";
            }
        }

        echo str_repeat('-', 120) . "\n";
        echo "Total: " . count($applications) . " applications\n";

        if (!$options['delete-application']) {
            echo "\nTo delete these applications, add --delete-application flag.\n";
        }

        if (!$options['delete-application']) {
            exit(0);
        }
    }

    // ============================================================
    // DELETE APPLICATIONS MODE
    // ============================================================
    cli_heading('Deleting Applications');

    $totalapps = count($applications);
    echo "Found $totalapps application(s) to delete.\n\n";

    if ($dryrun) {
        echo "*** DRY RUN MODE - No changes will be made ***\n\n";
    }

    $stats = [
        'applications' => 0,
        'documents' => 0,
        'doc_validations' => 0,
        'workflow_logs' => 0,
        'evaluations' => 0,
        'notifications' => 0,
        'files' => 0,
    ];

    // Get file storage.
    $fs = get_file_storage();
    $context = \context_system::instance();

    foreach ($applications as $app) {
        echo "Processing application ID: {$app->id}\n";
        echo "  Vacancy: {$app->vacancy_code} - {$app->vacancy_title}\n";
        echo "  User: {$app->firstname} {$app->lastname} ({$app->user_idnumber})\n";
        echo "  Status: {$app->status}\n";
        echo "  Created: " . date('Y-m-d H:i:s', $app->timecreated) . "\n";

        // Get documents for this application.
        $documents = $DB->get_records('local_jobboard_document', ['applicationid' => $app->id]);
        $doccount = count($documents);
        echo "  Documents: $doccount\n";

        if (!$dryrun) {
            // 1. Delete document validations.
            if (!empty($documents)) {
                $docids = array_keys($documents);
                list($docinsql, $docparams) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED, 'did');
                $valcount = $DB->count_records_select('local_jobboard_doc_validation', "documentid $docinsql", $docparams);
                $DB->delete_records_select('local_jobboard_doc_validation', "documentid $docinsql", $docparams);
                $stats['doc_validations'] += $valcount;
                if ($verbose) echo "    Deleted $valcount document validation(s)\n";
            }

            // 2. Delete document files from Moodle file storage.
            foreach ($documents as $doc) {
                // Files are stored in component 'local_jobboard', filearea 'application_documents'.
                $files = $fs->get_area_files(
                    $context->id,
                    'local_jobboard',
                    'application_documents',
                    $doc->id,
                    'id',
                    false
                );
                foreach ($files as $file) {
                    $file->delete();
                    $stats['files']++;
                }
            }
            if ($verbose && $doccount > 0) echo "    Deleted document files from storage\n";

            // 3. Delete documents records.
            $DB->delete_records('local_jobboard_document', ['applicationid' => $app->id]);
            $stats['documents'] += $doccount;
            if ($verbose) echo "    Deleted $doccount document record(s)\n";

            // 4. Delete workflow logs.
            $wfcount = $DB->count_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);
            $DB->delete_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);
            $stats['workflow_logs'] += $wfcount;
            if ($verbose) echo "    Deleted $wfcount workflow log(s)\n";

            // 5. Delete evaluations (if table exists).
            if ($DB->get_manager()->table_exists('local_jobboard_evaluation')) {
                $evalcount = $DB->count_records('local_jobboard_evaluation', ['applicationid' => $app->id]);
                if ($evalcount > 0) {
                    $DB->delete_records('local_jobboard_evaluation', ['applicationid' => $app->id]);
                    $stats['evaluations'] += $evalcount;
                    if ($verbose) echo "    Deleted $evalcount evaluation(s)\n";
                }
            }

            // 6. Delete notifications (notifications are linked via JSON data field).
            // The notification table uses 'data' JSON field to store applicationid.
            // We try to find and delete notifications that reference this application.
            try {
                $likeparam = '%"applicationid":' . $app->id . '%';
                $notifcount = $DB->count_records_sql(
                    "SELECT COUNT(*) FROM {local_jobboard_notification}
                     WHERE userid = :userid AND " . $DB->sql_like('data', ':pattern'),
                    ['userid' => $app->userid, 'pattern' => $likeparam]
                );
                if ($notifcount > 0) {
                    // Use execute() for DELETE with LIKE since delete_records_sql doesn't exist.
                    $sql = "DELETE FROM {local_jobboard_notification}
                            WHERE userid = ? AND " . $DB->sql_like('data', '?');
                    $DB->execute($sql, [$app->userid, $likeparam]);
                    $stats['notifications'] += $notifcount;
                    if ($verbose) echo "    Deleted $notifcount notification(s)\n";
                }
            } catch (Exception $e) {
                // Notifications table might have different structure, skip silently.
                if ($verbose) echo "    Note: Could not process notifications (table structure may differ)\n";
            }

            // 7. Delete application record.
            $DB->delete_records('local_jobboard_application', ['id' => $app->id]);
            $stats['applications']++;
            echo "  DELETED application ID: {$app->id}\n";

        } else {
            // Dry run - just count.
            $stats['applications']++;
            $stats['documents'] += $doccount;

            if (!empty($documents)) {
                $docids = array_keys($documents);
                list($docinsql, $docparams) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED, 'did');
                $stats['doc_validations'] += $DB->count_records_select('local_jobboard_doc_validation', "documentid $docinsql", $docparams);
            }

            $stats['workflow_logs'] += $DB->count_records('local_jobboard_workflow_log', ['applicationid' => $app->id]);

            if ($DB->get_manager()->table_exists('local_jobboard_evaluation')) {
                $stats['evaluations'] += $DB->count_records('local_jobboard_evaluation', ['applicationid' => $app->id]);
            }

            // Try to count notifications (may fail if table structure differs).
            try {
                $likeparam = '%"applicationid":' . $app->id . '%';
                $stats['notifications'] += $DB->count_records_sql(
                    "SELECT COUNT(*) FROM {local_jobboard_notification}
                     WHERE userid = :userid AND " . $DB->sql_like('data', ':pattern'),
                    ['userid' => $app->userid, 'pattern' => $likeparam]
                );
            } catch (Exception $e) {
                // Skip if table structure differs.
            }

            echo "  Would DELETE application ID: {$app->id}\n";
        }

        echo "\n";
    }

    // Summary.
    echo str_repeat('=', 60) . "\n";
    echo $dryrun ? "DRY RUN SUMMARY (no changes made):\n" : "DELETION SUMMARY:\n";
    echo str_repeat('=', 60) . "\n";
    echo "Applications deleted:      {$stats['applications']}\n";
    echo "Documents deleted:         {$stats['documents']}\n";
    echo "Document validations:      {$stats['doc_validations']}\n";
    echo "Workflow logs:             {$stats['workflow_logs']}\n";
    echo "Evaluations:               {$stats['evaluations']}\n";
    echo "Notifications:             {$stats['notifications']}\n";
    echo "Files removed:             {$stats['files']}\n";
    echo str_repeat('=', 60) . "\n";

    if ($dryrun) {
        echo "\n*** DRY RUN - Run without --dryrun to actually delete ***\n";
    } else {
        echo "\n=== DELETION COMPLETE ===\n";
    }

    exit(0);
}

// ============================================================
// APPLICATION RESTORATION (from sync orphans)
// ============================================================
if ($options['restore-application']) {
    if (!$moodleavailable) {
        cli_error("Application restoration requires Moodle. Run from Moodle installation.");
    }

    $userid = $options['userid'] ? (int) $options['userid'] : null;
    $newvacancyid = $options['new-vacancyid'] ? (int) $options['new-vacancyid'] : null;
    $sourceappid = $options['source-applicationid'] ? (int) $options['source-applicationid'] : null;
    $verbose = $options['verbose'];
    $dryrun = $options['dryrun'];

    // Validate parameters.
    if (empty($userid) || empty($newvacancyid)) {
        cli_error("You must specify --userid and --new-vacancyid");
    }

    cli_heading('Restoring Orphaned Application');

    // Get user info.
    $user = $DB->get_record('user', ['id' => $userid]);
    if (!$user) {
        cli_error("User with ID '$userid' not found");
    }
    echo "User: {$user->firstname} {$user->lastname} (ID: {$user->id}, email: {$user->email})\n";

    // Get vacancy info.
    $vacancy = $DB->get_record('local_jobboard_vacancy', ['id' => $newvacancyid]);
    if (!$vacancy) {
        cli_error("Vacancy with ID '$newvacancyid' not found");
    }
    echo "Vacancy: {$vacancy->code} - {$vacancy->title}\n";
    echo "  Location: {$vacancy->location}\n";
    echo "  Modality: {$vacancy->modality}\n";
    echo "  Program: {$vacancy->department}\n\n";

    // Get source application info if provided.
    $sourceApp = null;
    $sourceDocs = [];
    if ($sourceappid) {
        $sourceApp = $DB->get_record('local_jobboard_application', ['id' => $sourceappid]);
        if (!$sourceApp) {
            echo "WARNING: Source application ID '$sourceappid' not found. Will create empty application.\n\n";
        } else {
            $sourceDocs = $DB->get_records('local_jobboard_document', ['applicationid' => $sourceappid]);
            echo "Source Application: ID {$sourceApp->id}\n";
            echo "  Documents to copy: " . count($sourceDocs) . "\n\n";
        }
    }

    // Check if user already has an application for this vacancy.
    $existing = $DB->get_record('local_jobboard_application', [
        'userid' => $userid,
        'vacancyid' => $newvacancyid
    ]);
    if ($existing) {
        cli_error("User already has an application (ID: {$existing->id}) for this vacancy");
    }

    if ($dryrun) {
        echo "*** DRY RUN MODE - No changes will be made ***\n\n";
    }

    // Create application - copy data from source if available.
    $now = time();
    $application = new stdClass();
    $application->vacancyid = $newvacancyid;
    $application->userid = $userid;
    $application->status = $sourceApp ? $sourceApp->status : 'submitted';
    $application->statusnotes = $sourceApp ? $sourceApp->statusnotes : '';
    $application->isexemption = $sourceApp ? $sourceApp->isexemption : 0;
    $application->exemptionreason = $sourceApp ? $sourceApp->exemptionreason : '';
    $application->consentgiven = $sourceApp ? $sourceApp->consentgiven : 1;
    $application->consenttimestamp = $sourceApp ? $sourceApp->consenttimestamp : $now;
    $application->consentip = $sourceApp ? $sourceApp->consentip : '127.0.0.1';
    $application->consentuseragent = $sourceApp ? $sourceApp->consentuseragent : 'CLI Restore Tool';
    $application->digitalsignature = $sourceApp ? $sourceApp->digitalsignature : trim($user->firstname . ' ' . $user->lastname);
    $application->coverletter = $sourceApp ? $sourceApp->coverletter : '';
    $application->applicationdata = $sourceApp ? $sourceApp->applicationdata : '{}';
    $application->reviewerid = $sourceApp ? $sourceApp->reviewerid : null;
    $application->timecreated = $sourceApp ? $sourceApp->timecreated : $now;
    $application->timemodified = $now;

    if (!$dryrun) {
        $newid = $DB->insert_record('local_jobboard_application', $application);
        echo "SUCCESS: Created application ID: $newid\n";
        echo "  Vacancy: {$vacancy->code} ({$vacancy->location}, {$vacancy->modality})\n";
        echo "  User: {$user->firstname} {$user->lastname}\n";

        // Copy documents if source application was provided.
        if (!empty($sourceDocs)) {
            echo "\nRestoring documents...\n";
            $fs = get_file_storage();
            $context = \context_system::instance();
            $docsRestored = 0;
            $filesCopied = 0;

            foreach ($sourceDocs as $doc) {
                // 1. Create document record with new applicationid.
                $newDoc = clone $doc;
                unset($newDoc->id);
                $newDoc->applicationid = $newid;
                try {
                    $DB->insert_record('local_jobboard_document', $newDoc);
                    $docsRestored++;

                    // 2. Create mdl_files entry using contenthash (Opción A).
                    if (!empty($doc->contenthash)) {
                        $existingFile = $fs->get_file(
                            $context->id,
                            'local_jobboard',
                            'application_documents',
                            $newid,
                            '/',
                            $doc->filename
                        );

                        if (!$existingFile) {
                            // Find any existing file with same contenthash.
                            $sourceFile = $DB->get_record_sql(
                                "SELECT * FROM {files}
                                 WHERE contenthash = ?
                                   AND filename <> '.'
                                   AND filesize > 0
                                 LIMIT 1",
                                [$doc->contenthash]
                            );

                            if ($sourceFile) {
                                $storedFile = $fs->get_file_by_id($sourceFile->id);
                                if ($storedFile) {
                                    $newFileRecord = [
                                        'contextid' => $context->id,
                                        'component' => 'local_jobboard',
                                        'filearea' => 'application_documents',
                                        'itemid' => $newid,
                                        'filepath' => '/',
                                        'filename' => $doc->filename,
                                    ];
                                    $fs->create_file_from_storedfile($newFileRecord, $storedFile);
                                    $filesCopied++;
                                    if ($verbose) {
                                        echo "  ✓ {$doc->documenttype}: {$doc->filename}\n";
                                    }
                                }
                            } else {
                                if ($verbose) {
                                    echo "  ⚠ {$doc->documenttype}: archivo físico no encontrado (hash: {$doc->contenthash})\n";
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    echo "  ✗ Error: " . $e->getMessage() . "\n";
                }
            }
            echo "\nDocuments restored: $docsRestored, Files copied: $filesCopied\n";
        }
    } else {
        echo "Would CREATE application:\n";
        echo "  Vacancy ID: $newvacancyid ({$vacancy->code})\n";
        echo "  User ID: $userid ({$user->firstname} {$user->lastname})\n";
        echo "  Status: " . ($sourceApp ? $sourceApp->status : 'submitted') . "\n";
        if (!empty($sourceDocs)) {
            echo "  Documents to copy: " . count($sourceDocs) . "\n";
        }
    }

    echo "\n=== RESTORATION COMPLETE ===\n";
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

        // Distribute sedes between 3 convocatorias: first third → closed, second third → open, last third → future.
        $thirdsize = ceil($totalsedes / 3);
        if ($sedecount <= $thirdsize) {
            $convocatoriaindex = 0; // Closed.
        } else if ($sedecount <= $thirdsize * 2) {
            $convocatoriaindex = 1; // Open.
        } else {
            $convocatoriaindex = 2; // Future.
        }

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
            $convlabels = ['Conv. Cerrada', 'Conv. Abierta', 'Conv. Futura'];
            $convlabel = $convlabels[$convocatoriaindex] ?? 'Conv. Abierta';
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

    $consolidatedCount = 0;
    $totalPositions = 0;

    foreach ($jsondata['vacancies'] as $profile) {
        $code = $profile['code'] ?? '';
        if (empty($code)) continue;

        // Normalize location (e.g., "PAMPLONA - CENTROS TUTORIALES" -> "PAMPLONA").
        $loc = normalize_location($profile['location'] ?? 'PAMPLONA');
        $mod = $profile['modality'] ?? 'PRESENCIAL';
        // Normalize modality key.
        if (stripos($mod, 'DISTANCIA') !== false) {
            $modkey = 'DISTANCIA';
        } else if (stripos($mod, 'VIRTUAL') !== false) {
            $modkey = 'VIRTUAL';
        } else if (stripos($mod, 'HIBRIDA') !== false || stripos($mod, 'HÍBRIDA') !== false) {
            $modkey = 'HIBRIDA';
        } else {
            $modkey = 'PRESENCIAL';
        }
        $uniquekey = $code . '_' . $loc . '_' . $modkey;

        // Get positions from this profile (default 1).
        $positions = (int) ($profile['positions'] ?? 1);
        if ($positions < 1) $positions = 1;

        // Update location in profile to normalized value.
        $profile['location'] = $loc;

        // Consolidate duplicates: sum positions instead of overwriting.
        if (isset($allprofiles[$uniquekey])) {
            // Add positions to existing vacancy.
            $existingPositions = (int) ($allprofiles[$uniquekey]['positions'] ?? 1);
            $allprofiles[$uniquekey]['positions'] = $existingPositions + $positions;
            $consolidatedCount++;
            if ($verbose) {
                echo "  Consolidated: $code @ $loc ($modkey) - now has {$allprofiles[$uniquekey]['positions']} positions\n";
            }
        } else {
            // New vacancy.
            $profile['positions'] = $positions;
            $allprofiles[$uniquekey] = $profile;
            if (strpos($code, 'FCAS') === 0) $parsestats['fcas']++;
            else if (strpos($code, 'FII') === 0) $parsestats['fii']++;
        }

        $parsestats['profiles']++;
        $totalPositions += $positions;
        $locationstats[$loc] = ($locationstats[$loc] ?? 0) + 1;
    }
    $parsestats['files'] = 1;

    echo "JSON import complete:\n";
    echo "  Total entries in JSON: {$parsestats['profiles']}\n";
    echo "  Unique vacancies: " . count($allprofiles) . "\n";
    echo "  Consolidated (duplicates merged): $consolidatedCount\n";
    echo "  Total positions: $totalPositions\n";
    echo "    - FCAS vacancies: {$parsestats['fcas']}\n";
    echo "    - FII vacancies: {$parsestats['fii']}\n";
    echo "\n  By location:\n";
    foreach ($locationstats as $loc => $cnt) {
        echo "    - $loc: $cnt entries\n";
    }

} else if ($csvfile) {
    // Import from CSV.
    cli_heading('Phase 1: Importing from CSV File');

    $profiles = parse_csv_file($csvfile, $verbose);
    $consolidatedCount = 0;
    $totalPositions = 0;

    foreach ($profiles as $code => $profile) {
        // Normalize location.
        $loc = normalize_location($profile['location'] ?? 'PAMPLONA');
        $mod = $profile['modality'] ?? 'PRESENCIAL';
        // Normalize modality key.
        if (stripos($mod, 'DISTANCIA') !== false) {
            $modkey = 'DISTANCIA';
        } else if (stripos($mod, 'VIRTUAL') !== false) {
            $modkey = 'VIRTUAL';
        } else if (stripos($mod, 'HIBRIDA') !== false || stripos($mod, 'HÍBRIDA') !== false) {
            $modkey = 'HIBRIDA';
        } else {
            $modkey = 'PRESENCIAL';
        }
        $uniquekey = $code . '_' . $loc . '_' . $modkey;

        // Get positions from this profile (default 1).
        $positions = (int) ($profile['positions'] ?? 1);
        if ($positions < 1) $positions = 1;

        // Update location in profile to normalized value.
        $profile['location'] = $loc;

        // Consolidate duplicates: sum positions instead of overwriting.
        if (isset($allprofiles[$uniquekey])) {
            $existingPositions = (int) ($allprofiles[$uniquekey]['positions'] ?? 1);
            $allprofiles[$uniquekey]['positions'] = $existingPositions + $positions;
            $consolidatedCount++;
            if ($verbose) {
                echo "  Consolidated: $code @ $loc ($modkey) - now has {$allprofiles[$uniquekey]['positions']} positions\n";
            }
        } else {
            $profile['positions'] = $positions;
            $allprofiles[$uniquekey] = $profile;
            if (strpos($code, 'FCAS') === 0) $parsestats['fcas']++;
            else if (strpos($code, 'FII') === 0) $parsestats['fii']++;
        }

        $parsestats['profiles']++;
        $totalPositions += $positions;
        $locationstats[$loc] = ($locationstats[$loc] ?? 0) + 1;
    }
    $parsestats['files'] = 1;

    echo "\nCSV import complete:\n";
    echo "  Total entries: {$parsestats['profiles']}\n";
    echo "  Unique vacancies: " . count($allprofiles) . "\n";
    echo "  Consolidated: $consolidatedCount\n";
    echo "  Total positions: $totalPositions\n";
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

    $consolidatedCount = 0;
    $totalPositions = 0;

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
            // Normalize location.
            $loc = normalize_location($profile['location'] ?? 'PAMPLONA');
            $mod = $profile['modality'] ?? 'PRESENCIAL';
            // Normalize modality key.
            if (stripos($mod, 'DISTANCIA') !== false) {
                $modkey = 'DISTANCIA';
            } else if (stripos($mod, 'VIRTUAL') !== false) {
                $modkey = 'VIRTUAL';
            } else if (stripos($mod, 'HIBRIDA') !== false || stripos($mod, 'HÍBRIDA') !== false) {
                $modkey = 'HIBRIDA';
            } else {
                $modkey = 'PRESENCIAL';
            }
            $uniquekey = $code . '_' . $loc . '_' . $modkey;

            // Get positions from this profile (default 1).
            $positions = (int) ($profile['positions'] ?? 1);
            if ($positions < 1) $positions = 1;

            // Update location in profile to normalized value.
            $profile['location'] = $loc;

            // Consolidate duplicates: sum positions instead of overwriting.
            if (isset($allprofiles[$uniquekey])) {
                $existingPositions = (int) ($allprofiles[$uniquekey]['positions'] ?? 1);
                $allprofiles[$uniquekey]['positions'] = $existingPositions + $positions;
                $consolidatedCount++;
                if ($verbose) {
                    echo "    Consolidated: $code @ $loc ($modkey) - now has {$allprofiles[$uniquekey]['positions']} positions\n";
                }
            } else {
                $profile['positions'] = $positions;
                $allprofiles[$uniquekey] = $profile;
                if (strpos($code, 'FCAS') === 0) $parsestats['fcas']++;
                else if (strpos($code, 'FII') === 0) $parsestats['fii']++;
            }

            $parsestats['profiles']++;
            $totalPositions += $positions;
            $locationstats[$loc] = ($locationstats[$loc] ?? 0) + 1;
        }
        $parsestats['files']++;
    }

    ksort($allprofiles);

    echo "\nParsing complete:\n";
    echo "  Files processed: {$parsestats['files']}\n";
    echo "  Total entries: {$parsestats['profiles']}\n";
    echo "  Unique vacancies: " . count($allprofiles) . "\n";
    echo "  Consolidated: $consolidatedCount\n";
    echo "  Total positions: $totalPositions\n";
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

    // When --create-sample is used, create ALL sedes and departments.
    // Otherwise, only create the ones needed based on profiles.
    $neededlocations = array_unique(array_column($allprofiles, 'location'));

    if ($createsample) {
        echo "Creating ALL sedes (companies) and their departments...\n\n";
    } else {
        echo "Locations needed: " . implode(', ', $neededlocations) . "\n\n";
    }

    foreach ($ISER_SEDES as $key => $sedeinfo) {
        // Check if this location is needed (skip check when --create-sample).
        if (!$createsample && !in_array($key, $neededlocations) && $key !== 'PAMPLONA') {
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

    // When --create-sample, create 3 convocatorias: closed, open, and future.
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

        // Convocatoria 3: FUTURA (dates in the future).
        $nextsemester = $semester + 1 > 2 ? 1 : $semester + 1;
        $nextyear = $semester + 1 > 2 ? $year + 1 : $year;
        $futurecode = "CONV-ISER-{$nextyear}-{$nextsemester}-FUTURE";
        $futurename = "Convocatoria Docentes ISER {$nextyear}-{$nextsemester} (Próximamente)";

        $existingfuture = $DB->get_record('local_jobboard_convocatoria', ['code' => $futurecode]);
        if ($existingfuture) {
            echo "Using existing FUTURE convocatoria: $futurecode (ID: {$existingfuture->id})\n";
            $convocatoriaids[2] = $existingfuture->id;
        } else if (!$dryrun) {
            $futurestart = strtotime('+60 days');
            $futureend = strtotime('+90 days');

            $futureconv = new stdClass();
            $futureconv->code = $futurecode;
            $futureconv->name = $futurename;
            $futureconv->description = "<div class='alert alert-info'><strong>Próximamente.</strong> Esta convocatoria aún no ha iniciado.</div><p>Convocatoria para docentes ocasionales y de cátedra del próximo semestre. Consulte las fechas de apertura.</p>";
            $futureconv->startdate = $futurestart;
            $futureconv->enddate = $futureend;
            $futureconv->status = 'draft';
            $futureconv->publicationtype = $options['public'] ? 'public' : 'internal';
            $futureconv->createdby = $adminuser->id;
            $futureconv->timecreated = $now;

            $convocatoriaids[2] = $DB->insert_record('local_jobboard_convocatoria', $futureconv);
            echo "Created FUTURE convocatoria: $futurename\n";
            echo "  Code: $futurecode | ID: {$convocatoriaids[2]}\n";
            echo "  Period: " . date('Y-m-d', $futurestart) . " to " . date('Y-m-d', $futureend) . " (FUTURE)\n";
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

foreach ($allprofiles as $uniquekey => $profile) {
    $current++;
    $prefix = "[$current/$totalprofiles]";

    // Get the actual vacancy code from the profile (not the composite key).
    $code = $profile['code'] ?? '';
    if (empty($code)) {
        if ($verbose) echo "$prefix SKIP: empty code\n";
        $importstats['skipped']++;
        continue;
    }

    // Extract location and modality for duplicate check.
    $location = normalize_location($profile['location'] ?? 'PAMPLONA');
    $modality = $profile['modality'] ?? 'PRESENCIAL';
    // Normalize modality key.
    if (stripos($modality, 'DISTANCIA') !== false) {
        $modalitykey = 'DISTANCIA';
    } else if (stripos($modality, 'VIRTUAL') !== false) {
        $modalitykey = 'VIRTUAL';
    } else if (stripos($modality, 'HIBRIDA') !== false || stripos($modality, 'HÍBRIDA') !== false) {
        $modalitykey = 'HIBRIDA';
    } else {
        $modalitykey = 'PRESENCIAL';
    }
    $modalityFormKey = strtolower($modalitykey);

    // Get location name for comparison.
    $locationName = $ISER_SEDES[$location]['name'] ?? $location;

    // Check if exists by code + location + modality (allows same code in different locations/modalities).
    $existing = $DB->get_record_sql(
        "SELECT * FROM {local_jobboard_vacancy}
         WHERE code = :code AND location = :location AND modality = :modality",
        ['code' => $code, 'location' => $locationName, 'modality' => $modalityFormKey]
    );

    if ($existing && !$options['update']) {
        if ($verbose) echo "$prefix SKIP: $code @ $locationName ($modalityFormKey) (exists)\n";
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
    $location = normalize_location($profile['location'] ?? 'PAMPLONA');
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
    $isOcasionalTC = stripos($contracttypeRaw, 'OCASIONAL') !== false && stripos($contracttypeRaw, 'TIEMPO COMPLETO') !== false;
    $isOcasionalMT = stripos($contracttypeRaw, 'OCASIONAL') !== false && stripos($contracttypeRaw, 'MEDIO TIEMPO') !== false;
    $isOcasional = stripos($contracttypeRaw, 'OCASIONAL') !== false;

    // Map contract type from JSON to form-expected keys.
    // Form expects: catedra, ocasional_tc, ocasional_mt, temporal, termino_fijo, prestacion_servicios, planta.
    if ($isOcasionalTC) {
        $contracttype = 'ocasional_tc'; // Ocasional Tiempo Completo.
    } else if ($isOcasionalMT) {
        $contracttype = 'ocasional_mt'; // Ocasional Medio Tiempo.
    } else if ($isOcasional) {
        $contracttype = 'ocasional_tc'; // Default ocasional to tiempo completo.
    } else if (stripos($contracttypeRaw, 'CATEDRA') !== false || stripos($contracttypeRaw, 'CÁTEDRA') !== false) {
        $contracttype = 'catedra';
    } else if (stripos($contracttypeRaw, 'PLANTA') !== false) {
        $contracttype = 'planta';
    } else if (stripos($contracttypeRaw, 'PRESTACION') !== false || stripos($contracttypeRaw, 'SERVICIOS') !== false) {
        $contracttype = 'prestacion_servicios';
    } else if (stripos($contracttypeRaw, 'FIJO') !== false || stripos($contracttypeRaw, 'TEMPORAL') !== false) {
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

    // Department (text field = academic program name).
    // Use program name for proper filtering by "Programa Académico".
    $record->department = $program ?: $facultyName;

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

    // Number of positions - use consolidated value from profile (default 1).
    // Duplicate entries for same code+location+modality are merged with summed positions.
    $record->positions = (int) ($profile['positions'] ?? 1);
    if ($record->positions < 1) $record->positions = 1;

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
            return normalize_location($location);
        }
    }
    return null;
}

/**
 * Normalize location names to standard keys.
 * Handles variations like "PAMPLONA - CENTROS TUTORIALES" -> "PAMPLONA".
 *
 * @param string $location Raw location string.
 * @return string Normalized location key.
 */
function normalize_location($location) {
    $location = strtoupper(trim($location));

    // Normalize Pamplona variations (sede principal and centros tutoriales are the same).
    if (strpos($location, 'PAMPLONA') !== false) {
        return 'PAMPLONA';
    }

    // Normalize other common variations.
    $normalizations = [
        'CUCUTA' => 'CUCUTA',
        'CÚCUTA' => 'CUCUTA',
        'TIBU' => 'TIBU',
        'TIBÚ' => 'TIBU',
        'OCANA' => 'OCANA',
        'OCAÑA' => 'OCANA',
        'EL TARRA' => 'ELTARRA',
        'ELTARRA' => 'ELTARRA',
        'SAN VICENTE' => 'SANVICENTE',
        'SANVICENTE' => 'SANVICENTE',
        'PUEBLO BELLO' => 'PUEBLOBELLO',
        'PUEBLOBELLO' => 'PUEBLOBELLO',
        'SAN PABLO' => 'SANPABLO',
        'SANPABLO' => 'SANPABLO',
        'SANTA ROSA' => 'SANTAROSA',
        'SANTAROSA' => 'SANTAROSA',
        'FUNDACION' => 'FUNDACION',
        'FUNDACIÓN' => 'FUNDACION',
    ];

    foreach ($normalizations as $pattern => $normalized) {
        if (strpos($location, $pattern) !== false) {
            return $normalized;
        }
    }

    // Return as-is if no normalization needed.
    return $location;
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

    // Handle Pamplona variations first (sede principal and centros tutoriales are the same).
    if (strpos($location, 'PAMPLONA') !== false) {
        return 'PAMPLONA';
    }

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
