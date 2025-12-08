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
        $shortmap = array_flip($shortoptions);
        $options = [];
        foreach ($longoptions as $key => $default) {
            $options[$key] = $default;
        }
        $args = getopt(implode('', array_map(fn($k) => $k . ':', array_keys($shortoptions))),
            array_map(fn($k) => is_bool($longoptions[$k]) ? $k : $k . ':', array_keys($longoptions)));
        foreach ($args as $key => $value) {
            $longkey = $shortmap[$key] ?? $key;
            if (isset($options[$longkey])) {
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
]);

if (!empty($unrecognized) && $moodleavailable) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$moodlemode = $moodleavailable ? 'MOODLE MODE (full import)' : 'STANDALONE MODE (parsing only)';

$help = <<<EOT
============================================================
ISER Job Board - Profile Import CLI v2.0
============================================================
Mode: $moodlemode

Automated import of professional profiles from extracted PDF text files
into the local_jobboard vacancy system.

This CLI can automatically create the complete IOMAD structure:
- Companies (Sedes): PAMPLONA, CÚCUTA, TIBÚ, SAN VICENTE
- Departments (Modalidades): PRESENCIAL, A DISTANCIA

USAGE:
  php cli.php [options]

BASIC OPTIONS:
  -h, --help              Show this help message
  -i, --input=DIR         Input directory with .txt files
                          (default: PERFILESPROFESORES_TEXT)
  -j, --export-json=FILE  Export parsed data to JSON file
  -v, --verbose           Show detailed output

MOODLE-ONLY OPTIONS:
  -C, --create-structure  AUTO-CREATE IOMAD companies (sedes) and departments
                          (modalidades) based on profile data
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
  # RECOMMENDED: Full import with structure creation
  php cli.php --create-structure --publish --public

  # With custom convocatoria
  php cli.php --create-structure --publish --public \\
      --convocatoria-name="Convocatoria Docentes 2026-1" \\
      --opendate=2026-01-15 --closedate=2026-02-15

  # FULL RESET and reimport
  php cli.php --reset --reset-convocatorias --create-structure --publish --public

  # Parse only (standalone mode)
  php cli.php --export-json=perfiles.json --verbose

STRUCTURE CREATED:
  Companies (Sedes):
    - ISER Sede Pamplona (shortname: PAMPLONA)
    - ISER Sede Cúcuta (shortname: CUCUTA)
    - ISER Sede Tibú (shortname: TIBU)
    - ISER Sede San Vicente (shortname: SANVICENTE)

  Departments per Company (Modalidades):
    - Presencial
    - A Distancia

EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

// ============================================================
// CONFIGURATION
// ============================================================

// Define ISER structure.
$ISER_SEDES = [
    'PAMPLONA' => [
        'name' => 'ISER Sede Pamplona',
        'shortname' => 'PAMPLONA',
        'city' => 'Pamplona',
        'code' => 'ISER-PAM',
    ],
    'CUCUTA' => [
        'name' => 'ISER Sede Cúcuta',
        'shortname' => 'CUCUTA',
        'city' => 'Cúcuta',
        'code' => 'ISER-CUC',
    ],
    'TIBU' => [
        'name' => 'ISER Sede Tibú',
        'shortname' => 'TIBU',
        'city' => 'Tibú',
        'code' => 'ISER-TIB',
    ],
    'SANVICENTE' => [
        'name' => 'ISER Sede San Vicente de Chucurí',
        'shortname' => 'SANVICENTE',
        'city' => 'San Vicente de Chucurí',
        'code' => 'ISER-SVC',
    ],
];

$ISER_MODALIDADES = [
    'PRESENCIAL' => ['name' => 'Presencial', 'shortname' => 'PRESENCIAL'],
    'DISTANCIA' => ['name' => 'A Distancia', 'shortname' => 'DISTANCIA'],
];

$plugindir = __DIR__ . '/..';
$inputdir = $options['input'] ?? $plugindir . '/PERFILESPROFESORES_TEXT';
if (!is_dir($inputdir)) {
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

cli_heading('ISER Job Board - Profile Import v2.0');
echo "Input directory: $inputdir\n";
echo "Open date: " . date('Y-m-d', $opendate) . "\n";
echo "Close date: " . date('Y-m-d', $closedate) . "\n";
echo "Create structure: " . ($options['create-structure'] ? 'YES (companies + departments)' : 'NO') . "\n";
echo "Auto-publish: " . ($shouldpublish ? 'YES' : 'NO') . "\n";
echo "Publication type: " . ($options['public'] ? 'PUBLIC' : 'INTERNAL') . "\n";
echo "Status: {$options['status']}\n";
echo "Dry run: " . ($dryrun ? 'YES' : 'NO') . "\n";
if ($options['reset']) {
    echo "RESET MODE: YES\n";
}
echo "\n";

// ============================================================
// PHASE 1: PARSE TEXT FILES
// ============================================================

cli_heading('Phase 1: Parsing Profile Text Files');

$files = glob($inputdir . '/*.txt');
$files = array_filter($files, fn($f) => strpos(basename($f), '_CONSOLIDADO') === false);
sort($files);

echo "Found " . count($files) . " text files\n\n";

$allprofiles = [];
$parsestats = ['files' => 0, 'profiles' => 0, 'fcas' => 0, 'fii' => 0];
$locationstats = [];

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

// Export JSON if requested.
if (!empty($options['export-json'])) {
    $jsonfile = $options['export-json'];
    $jsondata = [
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
    file_put_contents($jsonfile, json_encode($jsondata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "\nJSON exported to: $jsonfile\n";
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
// PHASE 4: CREATE CONVOCATORIA
// ============================================================

$convocatoriaid = !empty($options['convocatoria']) ? (int) $options['convocatoria'] : null;

if ($shouldpublish && empty($convocatoriaid)) {
    cli_heading('Phase 4: Creating Convocatoria');

    $convcode = $options['convocatoria-code'] ?: 'CONV-' . date('Y') . '-' . date('md');
    $convname = $options['convocatoria-name'] ?: 'Convocatoria Docentes ISER ' . date('Y') . '-' . ceil(date('n') / 6);
    $convdesc = $options['convocatoria-desc'] ?: '';

    // Check if exists.
    $existingconv = $DB->get_record('local_jobboard_convocatoria', ['code' => $convcode]);

    if ($existingconv) {
        echo "Using existing convocatoria: $convcode (ID: {$existingconv->id})\n";
        $convocatoriaid = $existingconv->id;
    } else if (!$dryrun) {
        $adminuser = get_admin();

        // Build description.
        $deschtml = '<h3>Convocatoria para Docentes ISER</h3>';
        $deschtml .= '<p><strong>Período:</strong> ' . date('d/m/Y', $opendate) . ' al ' . date('d/m/Y', $closedate) . '</p>';
        $deschtml .= '<p><strong>Total de perfiles:</strong> ' . count($allprofiles) . '</p>';
        $deschtml .= '<ul>';
        $deschtml .= '<li>Facultad FCAS: ' . $parsestats['fcas'] . ' perfiles</li>';
        $deschtml .= '<li>Facultad FII: ' . $parsestats['fii'] . ' perfiles</li>';
        $deschtml .= '</ul>';
        $deschtml .= '<h4>Distribución por Sede:</h4><ul>';
        foreach ($locationstats as $loc => $cnt) {
            $deschtml .= "<li>$loc: $cnt vacantes</li>";
        }
        $deschtml .= '</ul>';
        if ($convdesc) {
            $deschtml .= '<p>' . $convdesc . '</p>';
        }

        $convrecord = new stdClass();
        $convrecord->code = $convcode;
        $convrecord->name = $convname;
        $convrecord->description = $deschtml;
        $convrecord->startdate = $opendate;
        $convrecord->enddate = $closedate;
        $convrecord->opendate = $opendate;
        $convrecord->closedate = $closedate;
        $convrecord->status = 'open';
        $convrecord->companyid = null; // Convocatoria is global, not tied to a company.
        $convrecord->departmentid = null;
        $convrecord->publicationtype = $options['public'] ? 'public' : 'internal';
        $convrecord->terms = '<p>Al postularse a esta convocatoria, el candidato acepta los términos y condiciones del proceso de selección docente del ISER.</p>';
        $convrecord->createdby = $adminuser->id;
        $convrecord->timecreated = $now;

        $convocatoriaid = $DB->insert_record('local_jobboard_convocatoria', $convrecord);

        echo "Created convocatoria: $convname\n";
        echo "  Code: $convcode\n";
        echo "  ID: $convocatoriaid\n";
        echo "  Status: open\n";
        echo "  Period: " . date('Y-m-d', $opendate) . " to " . date('Y-m-d', $closedate) . "\n";
    } else {
        echo "DRY RUN: Would create convocatoria '$convname'\n";
        $convocatoriaid = 0;
    }
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

    // Title.
    $program = $profile['program'] ?: '';
    $proftext = $profile['profile'] ?: '';
    $record->title = $program ?: "Vacante $code";
    if ($proftext && strlen($proftext) < 100) {
        $record->title .= " - " . $proftext;
    }
    if (strlen($record->title) > 250) {
        $record->title = substr($record->title, 0, 247) . '...';
    }

    // Description.
    $courses = $profile['courses'] ?? [];
    if (!empty($courses)) {
        $record->description = "<h4>Cursos a orientar:</h4>\n<ul>\n<li>" .
            implode("</li>\n<li>", $courses) . "</li>\n</ul>";
    } else {
        $record->description = '';
    }

    // Contract type and duration.
    $record->contracttype = $profile['contracttype'] ?: 'CATEDRA';
    $record->duration = stripos($record->contracttype, 'OCASIONAL') !== false ? 'Semestral' : 'Por horas';

    // Location.
    $location = $profile['location'] ?? 'PAMPLONA';
    $record->location = $location;

    // Department (text field).
    $record->department = $program;

    // Requirements.
    if ($proftext) {
        $record->requirements = "<p><strong>Perfil profesional requerido:</strong></p>\n<p>$proftext</p>";
    } else {
        $record->requirements = '';
    }
    $record->desirable = '';

    // IOMAD Company ID.
    if ($options['create-structure'] && isset($companymap[$location])) {
        $record->companyid = $companymap[$location];
    } else {
        $record->companyid = $defaultcompanyid;
    }

    // IOMAD Department ID (based on modality).
    $modality = $profile['modality'] ?? 'PRESENCIAL';
    $modalitykey = stripos($modality, 'DISTANCIA') !== false ? 'DISTANCIA' : 'PRESENCIAL';
    $deptkey = $location . '_' . $modalitykey;

    if ($options['create-structure'] && isset($departmentmap[$deptkey])) {
        $record->departmentid = $departmentmap[$deptkey];
    } else if ($record->companyid && !empty($profile['modality'])) {
        $deptname = $modalitykey === 'DISTANCIA' ? 'A Distancia' : 'Presencial';
        $dept = $DB->get_record('department', ['company' => $record->companyid, 'name' => $deptname]);
        $record->departmentid = $dept ? $dept->id : null;
    } else {
        $record->departmentid = !empty($options['department']) ? (int) $options['department'] : null;
    }

    // Convocatoria and dates.
    $record->convocatoriaid = $convocatoriaid;
    $record->opendate = $opendate;
    $record->closedate = $closedate;
    $record->positions = 1;

    // Status.
    $record->status = $options['status'];
    $record->publicationtype = $options['public'] ? 'public' : 'internal';

    // Audit fields.
    $record->createdby = $adminuser->id;
    $record->timecreated = $now;

    // Dry run?
    if ($dryrun) {
        echo "$prefix DRY: $code -> $location ($modalitykey)\n";
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
            if ($verbose) echo "$prefix CREATED: $code (ID: $id) -> $location\n";
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
// PARSING FUNCTIONS
// ============================================================

/**
 * Extract location from file content.
 */
function extract_location_from_content($content, $filename) {
    // Check filename first.
    $content_upper = strtoupper($content . ' ' . $filename);

    // Check for specific locations in content.
    if (preg_match('/SAN\s*VICENTE/i', $content_upper)) {
        return 'SANVICENTE';
    }
    if (preg_match('/TIB[UÚ]/i', $content_upper)) {
        return 'TIBU';
    }
    if (preg_match('/C[UÚ]CUTA/i', $content_upper)) {
        return 'CUCUTA';
    }

    // Default to PAMPLONA.
    return 'PAMPLONA';
}

/**
 * Parse profiles from text content.
 */
function parse_profiles_from_text($content, $filename, $defaultlocation = 'PAMPLONA') {
    $profiles = [];

    // Determine source info.
    $source = [
        'faculty' => strpos($filename, 'FCAS') !== false ? 'FCAS' :
                    (strpos($filename, 'FII') !== false ? 'FII' : ''),
        'modality' => stripos($filename, 'DISTANCIA') !== false ? 'A DISTANCIA' : 'PRESENCIAL',
        'location' => $defaultlocation,
    ];

    $content = preg_replace('/\r\n|\r/', "\n", $content);
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

        $code = preg_replace('/\s+/', '', strtoupper($coderaw));

        $blockstart = substr($content, $start, 100);
        if (preg_match('/CÓDIGO\s*TIPO/i', $blockstart)) continue;

        $block = substr($content, $start + strlen($coderaw), $end - $start - strlen($coderaw));
        $block = trim($block);

        $profile = parse_profile_block($code, $block, $source);
        if ($profile) {
            $profiles[$code] = $profile;
        }
    }

    return $profiles;
}

/**
 * Parse a single profile block.
 */
function parse_profile_block($code, $block, $source) {
    $normalized = preg_replace('/\s+/', ' ', $block);
    $normalized = trim($normalized);

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

    // Contract type.
    if (preg_match('/^\s*(OCASIONAL\s+TIEMPO\s+COMPLETO|C[ÁA]TEDRA)/i', $normalized, $m)) {
        $ct = strtoupper(trim($m[1]));
        $profile['contracttype'] = str_replace('ÁTEDRA', 'ATEDRA', $ct);
    }

    // Program.
    if (preg_match('/TECNOLOG[IÍ]A\s+EN\s+([A-ZÁÉÍÓÚÑ\s]+?)(?=\s+(?:PROFESIONAL|INGENIER|LICENCIAD|ADMINISTRAD|MÉDICO|ABOGAD|CONTADOR|ECONOMISTA|PSICOLOG|TRABAJADOR|TECNÓLOG|ORIENTAR|$))/iu', $normalized, $m)) {
        $profile['program'] = 'TECNOLOGIA EN ' . trim(preg_replace('/\s+/', ' ', $m[1]));
    } else if (preg_match('/T[EÉ]CNICA\s+PROFESIONAL\s+EN\s+([A-ZÁÉÍÓÚÑ\s]+?)(?=\s+(?:PROFESIONAL|INGENIER|LICENCIAD|$))/iu', $normalized, $m)) {
        $profile['program'] = 'TECNICA PROFESIONAL EN ' . trim(preg_replace('/\s+/', ' ', $m[1]));
    }

    // Professional profile.
    $progend = 0;
    if (!empty($profile['program'])) {
        $pos = stripos($normalized, $profile['program']);
        if ($pos !== false) $progend = $pos + strlen($profile['program']);
    }

    $orientarpos = stripos($normalized, 'ORIENTAR');
    if ($orientarpos === false) $orientarpos = strlen($normalized);

    if ($progend > 0 && $orientarpos > $progend) {
        $proftext = substr($normalized, $progend, $orientarpos - $progend);
        $proftext = trim($proftext);
        $proftext = preg_replace('/^\s*(?:Y\s+|,\s*)+/', '', $proftext);
        $proftext = preg_replace('/\s*(?:ORIENTAR.*|CURSOS?.*)$/i', '', $proftext);
        if (strlen($proftext) > 5) {
            $profile['profile'] = trim($proftext);
        }
    }

    // Courses.
    if (preg_match('/ORIENTAR\s+(?:LOS\s+)?CURSOS?\s*(?:DE)?\s*:?\s*(.+?)$/isu', $normalized, $m)) {
        $coursestext = trim($m[1]);
        $courses = preg_split('/\s*(?:[-–•]|\n|\s{2,})\s*/u', $coursestext);
        $courses = array_map('trim', $courses);
        $courses = array_filter($courses, function($c) {
            $c = trim($c);
            if (strlen($c) < 3) return false;
            if (preg_match('/^(?:DE|EN|LOS|LAS|EL|LA|Y|O|PARA|DEL|AL|CON|CURSOS?|ORIENTAR|CÓDIGO|TIPO|VINCULACIÓN|PROGRAMA|ACADÉMICO|PERFIL|PROFESIONAL|ESPECÍFICO|POSIBLES)$/i', $c)) {
                return false;
            }
            return true;
        });
        $profile['courses'] = array_values($courses);
    }

    if (!empty($profile['contracttype']) || !empty($profile['program']) ||
        !empty($profile['profile']) || !empty($profile['courses'])) {
        return $profile;
    }

    return null;
}
