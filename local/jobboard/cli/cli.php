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
 * 1. Reads extracted text files from PDF profiles
 * 2. Parses profiles to extract structured data
 * 3. Creates vacancies in the local_jobboard database
 *
 * Usage:
 *   php cli.php [options]
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
        echo "\n" . str_repeat('=', 60) . "\n";
        echo $text . "\n";
        echo str_repeat('=', 60) . "\n";
    }

    function cli_error($text) {
        echo "ERROR: $text\n";
        exit(1);
    }

    function cli_problem($text) {
        echo "WARNING: $text\n";
    }
}

// CLI options.
list($options, $unrecognized) = cli_get_params([
    'help' => false,
    'input' => null,
    'convocatoria' => null,
    'convocatoria-name' => null,
    'convocatoria-code' => null,
    'company' => null,
    'department' => null,
    'opendate' => null,
    'closedate' => null,
    'dryrun' => false,
    'update' => false,
    'status' => 'draft',
    'publish' => false,
    'reset' => false,
    'reset-convocatorias' => false,
    'verbose' => false,
    'export-json' => null,
], [
    'h' => 'help',
    'i' => 'input',
    'c' => 'convocatoria',
    'o' => 'opendate',
    'e' => 'closedate',
    'd' => 'dryrun',
    'u' => 'update',
    's' => 'status',
    'p' => 'publish',
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
ISER Job Board - Profile Import CLI
============================================================
Mode: $moodlemode

Automated import of professional profiles from extracted PDF text files
into the local_jobboard vacancy system.

IMPORTANT: To see vacancies in the system, you need:
  1. A convocatoria (call) to group vacancies
  2. Vacancies with status 'published'
  3. Use --publish to automatically create convocatoria and publish

USAGE:
  php cli.php [options]

OPTIONS:
  -h, --help              Show this help message
  -i, --input=DIR         Input directory with .txt files
                          (default: PERFILESPROFESORES_TEXT)
  -j, --export-json=FILE  Export parsed data to JSON file
  -v, --verbose           Show detailed output

MOODLE-ONLY OPTIONS (require config.php):
  -p, --publish           AUTO-CREATE convocatoria and PUBLISH vacancies
                          (Recommended for first import)
  -r, --reset             DELETE all existing vacancies before import
  --reset-convocatorias   Also delete convocatorias (use with --reset)
  -c, --convocatoria=ID   Use existing convocatoria ID
  --convocatoria-name=NAME  Name for new convocatoria (with --publish)
  --convocatoria-code=CODE  Code for new convocatoria (with --publish)
  --company=ID            Default IOMAD company ID
  --department=ID         Default IOMAD department ID
  -o, --opendate=DATE     Opening date (YYYY-MM-DD), default: today
  -e, --closedate=DATE    Closing date (YYYY-MM-DD), default: +30 days
  -d, --dryrun            Simulate import without creating records
  -u, --update            Update existing vacancies (match by code)
  -s, --status=STATUS     Initial status: draft|published (default: draft)

EXAMPLES:
  # RECOMMENDED: Full import with convocatoria creation and publish
  php cli.php --publish

  # RESET and reimport: Delete all vacancies and start fresh
  php cli.php --reset --publish

  # FULL RESET: Delete vacancies AND convocatorias, then reimport
  php cli.php --reset --reset-convocatorias --publish

  # With custom convocatoria name and dates
  php cli.php --publish --convocatoria-name="Convocatoria Docentes 2026-1" \\
              --opendate=2026-01-15 --closedate=2026-02-15

  # Parse only and export JSON (works without Moodle)
  php cli.php --export-json=perfiles.json --verbose

  # Dry run to preview import (requires Moodle)
  php cli.php --dryrun --verbose

  # Import to existing convocatoria
  php cli.php --convocatoria=1 --status=published

PROCESS:
  1. Reads text files from PERFILESPROFESORES_TEXT directory
  2. Parses each file to extract profile data
  3. If --publish: Creates convocatoria first
  4. Creates/updates vacancies linked to convocatoria
  5. If --publish: Sets status to 'published' and opens convocatoria

EOT;

if ($options['help']) {
    echo $help;
    exit(0);
}

// ============================================================
// CONFIGURATION
// ============================================================

$plugindir = __DIR__ . '/..';

// Input directory.
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
    if ($parsed === false) {
        cli_error("Invalid opendate format: {$options['opendate']}");
    }
    $opendate = $parsed;
}

if (!empty($options['closedate'])) {
    $parsed = strtotime($options['closedate']);
    if ($parsed === false) {
        cli_error("Invalid closedate format: {$options['closedate']}");
    }
    $closedate = $parsed;
}

// Status validation.
$validstatuses = ['draft', 'published'];
if (!in_array($options['status'], $validstatuses)) {
    cli_error("Invalid status: {$options['status']}. Must be: " . implode(', ', $validstatuses));
}

$verbose = $options['verbose'];
$dryrun = $options['dryrun'];

// ============================================================
// HEADER
// ============================================================

cli_heading('ISER Job Board - Profile Import');
echo "Input directory: $inputdir\n";
echo "Open date: " . date('Y-m-d', $opendate) . "\n";
echo "Close date: " . date('Y-m-d', $closedate) . "\n";
if ($options['reset']) {
    echo "RESET MODE: YES - Will delete existing vacancies" . ($options['reset-convocatorias'] ? ' and convocatorias' : '') . "\n";
}
echo "Auto-publish: " . ($options['publish'] ? 'YES (will create convocatoria)' : 'NO') . "\n";
echo "Status: {$options['status']}" . ($options['publish'] ? ' (auto-set by --publish)' : '') . "\n";
echo "Dry run: " . ($dryrun ? 'YES' : 'NO') . "\n";
echo "Update existing: " . ($options['update'] ? 'YES' : 'NO') . "\n";
if ($options['convocatoria']) {
    echo "Convocatoria ID: {$options['convocatoria']}\n";
}
if ($options['company']) {
    echo "Company ID: {$options['company']}\n";
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

foreach ($files as $file) {
    $filename = basename($file);
    $content = file_get_contents($file);

    $profiles = parse_profiles_from_text($content, $filename);
    $count = count($profiles);

    if ($verbose) {
        echo "  $filename: $count profiles\n";
    }

    foreach ($profiles as $code => $profile) {
        if (!isset($allprofiles[$code])) {
            $allprofiles[$code] = $profile;
            $parsestats['profiles']++;
            if (strpos($code, 'FCAS') === 0) {
                $parsestats['fcas']++;
            } else if (strpos($code, 'FII') === 0) {
                $parsestats['fii']++;
            }
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
        ],
        'vacancies' => array_values($allprofiles),
    ];
    file_put_contents($jsonfile, json_encode($jsondata, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    echo "\nJSON exported to: $jsonfile\n";
}

// ============================================================
// PHASE 2: CREATE VACANCIES
// ============================================================

if (!$moodleavailable) {
    echo "\n";
    cli_heading('Phase 2: Skipped (Moodle not available)');
    echo "Moodle config.php not found. Running in standalone mode.\n";
    echo "To import vacancies into database, run this script from Moodle installation.\n";
    echo "\nParsing completed successfully. Use --export-json to save parsed data.\n";
    exit(0);
}

echo "\n";

// Determine if we should publish (auto-create convocatoria).
$shouldpublish = $options['publish'];
if ($shouldpublish) {
    $options['status'] = 'published';
}

// ============================================================
// PHASE 2-RESET: DELETE EXISTING DATA (if --reset)
// ============================================================

if ($options['reset']) {
    cli_heading('Phase 2-RESET: Deleting Existing Data');

    $resetstats = ['vacancies' => 0, 'convocatorias' => 0, 'applications' => 0];

    // First, count what will be deleted.
    $vacancycount = $DB->count_records('local_jobboard_vacancy');
    $convcount = $DB->count_records('local_jobboard_convocatoria');

    echo "Found $vacancycount vacancies";
    if ($options['reset-convocatorias']) {
        echo " and $convcount convocatorias";
    }
    echo " to delete.\n";

    if ($dryrun) {
        echo "DRY RUN: Would delete $vacancycount vacancies";
        if ($options['reset-convocatorias']) {
            echo " and $convcount convocatorias";
        }
        echo "\n";
        $resetstats['vacancies'] = $vacancycount;
        $resetstats['convocatorias'] = $options['reset-convocatorias'] ? $convcount : 0;
    } else {
        // Delete related data first (applications, documents, etc.).
        // Get vacancy IDs first.
        $vacancyids = $DB->get_fieldset_select('local_jobboard_vacancy', 'id', '1=1');

        if (!empty($vacancyids)) {
            // Delete applications and related data.
            list($insql, $params) = $DB->get_in_or_equal($vacancyids, SQL_PARAMS_NAMED, 'vid');

            // Get application IDs for document deletion.
            $appids = $DB->get_fieldset_select('local_jobboard_application', 'id', "vacancyid $insql", $params);

            if (!empty($appids)) {
                list($appinsql, $appparams) = $DB->get_in_or_equal($appids, SQL_PARAMS_NAMED, 'aid');

                // Delete documents.
                $docids = $DB->get_fieldset_select('local_jobboard_document', 'id', "applicationid $appinsql", $appparams);
                if (!empty($docids)) {
                    list($docinsql, $docparams) = $DB->get_in_or_equal($docids, SQL_PARAMS_NAMED, 'did');
                    $DB->delete_records_select('local_jobboard_doc_validation', "documentid $docinsql", $docparams);
                    $DB->delete_records_select('local_jobboard_document', "id $docinsql", $docparams);
                }

                // Delete workflow logs.
                $DB->delete_records_select('local_jobboard_workflow_log', "applicationid $appinsql", $appparams);

                // Delete applications.
                $DB->delete_records_select('local_jobboard_application', "id $appinsql", $appparams);
                $resetstats['applications'] = count($appids);
            }

            // Delete document requirements.
            $DB->delete_records_select('local_jobboard_doc_requirement', "vacancyid $insql", $params);
        }

        // Delete all vacancies.
        $DB->delete_records('local_jobboard_vacancy');
        $resetstats['vacancies'] = $vacancycount;
        echo "Deleted $vacancycount vacancies";
        if ($resetstats['applications'] > 0) {
            echo " and {$resetstats['applications']} applications";
        }
        echo "\n";

        // Delete convocatorias if requested.
        if ($options['reset-convocatorias']) {
            $DB->delete_records('local_jobboard_convocatoria');
            $resetstats['convocatorias'] = $convcount;
            echo "Deleted $convcount convocatorias\n";
        }

        echo "Reset complete.\n";
    }
}

// ============================================================
// PHASE 2A: CREATE OR GET CONVOCATORIA
// ============================================================

$convocatoriaid = !empty($options['convocatoria']) ? (int) $options['convocatoria'] : null;

if ($shouldpublish && empty($convocatoriaid)) {
    cli_heading('Phase 2A: Creating Convocatoria');

    // Generate convocatoria details.
    $convcode = $options['convocatoria-code'] ?: 'CONV-' . date('Y') . '-' . date('md');
    $convname = $options['convocatoria-name'] ?: 'Convocatoria Docentes ISER ' . date('Y') . '-' . ceil(date('n') / 6);

    // Check if convocatoria with this code exists.
    $existingconv = $DB->get_record('local_jobboard_convocatoria', ['code' => $convcode]);

    if ($existingconv) {
        echo "Using existing convocatoria: $convcode (ID: {$existingconv->id})\n";
        $convocatoriaid = $existingconv->id;
    } else if (!$dryrun) {
        $adminuser = get_admin();
        $convrecord = new stdClass();
        $convrecord->code = $convcode;
        $convrecord->name = $convname;
        $convrecord->description = '<p>Convocatoria para perfiles profesionales docentes.</p>' .
            '<p>Total de perfiles: ' . count($allprofiles) . ' (FCAS: ' . $parsestats['fcas'] . ', FII: ' . $parsestats['fii'] . ')</p>';
        $convrecord->startdate = $opendate;
        $convrecord->enddate = $closedate;
        $convrecord->status = 'open';
        $convrecord->companyid = !empty($options['company']) ? (int) $options['company'] : null;
        $convrecord->departmentid = !empty($options['department']) ? (int) $options['department'] : null;
        $convrecord->publicationtype = 'internal';
        $convrecord->terms = '';
        $convrecord->createdby = $adminuser->id;
        $convrecord->timecreated = $now;

        $convocatoriaid = $DB->insert_record('local_jobboard_convocatoria', $convrecord);
        echo "Created convocatoria: $convname\n";
        echo "  Code: $convcode\n";
        echo "  ID: $convocatoriaid\n";
        echo "  Status: open\n";
        echo "  Period: " . date('Y-m-d', $opendate) . " to " . date('Y-m-d', $closedate) . "\n";
    } else {
        echo "DRY RUN: Would create convocatoria '$convname' ($convcode)\n";
        $convocatoriaid = 0; // Placeholder for dry run.
    }
} else if (!empty($convocatoriaid)) {
    $existingconv = $DB->get_record('local_jobboard_convocatoria', ['id' => $convocatoriaid]);
    if (!$existingconv) {
        cli_error("Convocatoria with ID $convocatoriaid not found");
    }
    echo "Using convocatoria: {$existingconv->name} (ID: $convocatoriaid)\n";
}

// ============================================================
// PHASE 2B: CREATE VACANCIES
// ============================================================

cli_heading('Phase 2B: Creating Vacancies');

$adminuser = get_admin();
$importstats = ['created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => 0];

$totalprofiles = count($allprofiles);
$current = 0;

foreach ($allprofiles as $code => $profile) {
    $current++;
    $prefix = "[$current/$totalprofiles]";

    // Check if vacancy exists.
    $existing = $DB->get_record('local_jobboard_vacancy', ['code' => $code]);

    if ($existing && !$options['update']) {
        if ($verbose) {
            echo "$prefix SKIP: $code (exists)\n";
        }
        $importstats['skipped']++;
        continue;
    }

    // Build vacancy record.
    $record = new stdClass();
    $record->code = $code;

    // Title: Program + Profile (shortened).
    $program = $profile['program'] ?: '';
    $proftext = $profile['profile'] ?: '';
    $record->title = $program ?: "Vacante $code";
    if ($proftext && strlen($proftext) < 100) {
        $record->title .= " - " . $proftext;
    }
    // Truncate title if too long.
    if (strlen($record->title) > 250) {
        $record->title = substr($record->title, 0, 247) . '...';
    }

    // Description: Courses list.
    $courses = $profile['courses'] ?? [];
    if (!empty($courses)) {
        $record->description = "<h4>Cursos a orientar:</h4>\n<ul>\n<li>" .
            implode("</li>\n<li>", $courses) . "</li>\n</ul>";
    } else {
        $record->description = '';
    }

    // Contract type.
    $record->contracttype = $profile['contracttype'] ?: 'CATEDRA';

    // Duration.
    $record->duration = stripos($record->contracttype, 'OCASIONAL') !== false ? 'Semestral' : 'Por horas';

    // Location.
    $record->location = $profile['location'] ?: 'PAMPLONA';

    // Department (text).
    $record->department = $program;

    // Requirements.
    if ($proftext) {
        $record->requirements = "<p><strong>Perfil profesional requerido:</strong></p>\n<p>$proftext</p>";
    } else {
        $record->requirements = '';
    }

    $record->desirable = '';

    // IOMAD IDs.
    $record->companyid = !empty($options['company']) ? (int) $options['company'] : null;
    $record->departmentid = !empty($options['department']) ? (int) $options['department'] : null;

    // Try to map modality to department if company is set.
    if ($record->companyid && empty($record->departmentid) && !empty($profile['modality'])) {
        $deptname = match(strtoupper($profile['modality'])) {
            'PRESENCIAL' => 'Presencial',
            'A DISTANCIA', 'DISTANCIA' => 'A Distancia',
            'VIRTUAL' => 'Virtual',
            'HIBRIDA', 'HÍBRIDA' => 'Híbrida',
            default => null,
        };
        if ($deptname) {
            $dept = $DB->get_record('department', ['company' => $record->companyid, 'name' => $deptname]);
            if ($dept) {
                $record->departmentid = $dept->id;
            }
        }
    }

    // Convocatoria (use the one we created/found earlier).
    $record->convocatoriaid = $convocatoriaid;

    // Dates and positions.
    $record->opendate = $opendate;
    $record->closedate = $closedate;
    $record->positions = 1;

    // Status.
    $record->status = $options['status'];
    $record->publicationtype = 'internal';

    // Audit fields.
    $record->createdby = $adminuser->id;
    $record->timecreated = $now;

    // Dry run?
    if ($dryrun) {
        echo "$prefix DRY: $code\n";
        if ($existing) {
            $importstats['updated']++;
        } else {
            $importstats['created']++;
        }
        continue;
    }

    // Insert or update.
    try {
        if ($existing) {
            $record->id = $existing->id;
            $record->modifiedby = $adminuser->id;
            $record->timemodified = $now;
            $DB->update_record('local_jobboard_vacancy', $record);
            if ($verbose) {
                echo "$prefix UPDATED: $code\n";
            }
            $importstats['updated']++;
        } else {
            $id = $DB->insert_record('local_jobboard_vacancy', $record);
            if ($verbose) {
                echo "$prefix CREATED: $code (ID: $id)\n";
            }
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
echo "Vacancies created: {$importstats['created']}\n";
echo "Vacancies updated: {$importstats['updated']}\n";
echo "Vacancies skipped: {$importstats['skipped']}\n";
echo "Errors: {$importstats['errors']}\n";
echo "Vacancy status: {$options['status']}\n";

if ($dryrun) {
    echo "\n*** DRY RUN - No changes were made to the database ***\n";
} else if ($importstats['created'] > 0 || $importstats['updated'] > 0) {
    echo "\n";
    echo "=== NEXT STEPS ===\n";
    if ($options['status'] === 'published' && $convocatoriaid) {
        echo "Vacancies are now PUBLISHED and visible in the system.\n";
        echo "Access: Site Administration > Local plugins > Job Board\n";
        echo "Or browse: /local/jobboard/?view=browse_convocatorias\n";
    } else if ($options['status'] === 'draft') {
        echo "Vacancies are in DRAFT status and NOT visible yet.\n";
        echo "To publish them:\n";
        echo "  1. Create/use a convocatoria\n";
        echo "  2. Run: php cli.php --publish --update\n";
        echo "  Or manually: Admin > Local plugins > Job Board > Manage\n";
    }
}

exit($importstats['errors'] > 0 ? 1 : 0);


// ============================================================
// PARSING FUNCTIONS
// ============================================================

/**
 * Parse profiles from text content.
 *
 * @param string $content The text content.
 * @param string $filename The source filename.
 * @return array Array of profiles keyed by code.
 */
function parse_profiles_from_text($content, $filename) {
    $profiles = [];

    // Determine source info from filename.
    $source = [
        'faculty' => strpos($filename, 'FCAS') !== false ? 'FCAS' :
                    (strpos($filename, 'FII') !== false ? 'FII' : ''),
        'modality' => stripos($filename, 'DISTANCIA') !== false ? 'A DISTANCIA' : 'PRESENCIAL',
        'location' => 'PAMPLONA',
    ];

    // Normalize line endings.
    $content = preg_replace('/\r\n|\r/', "\n", $content);

    // Pattern to find profile codes.
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

        // Normalize code.
        $code = preg_replace('/\s+/', '', strtoupper($coderaw));

        // Skip header rows.
        $blockstart = substr($content, $start, 100);
        if (preg_match('/CÓDIGO\s*TIPO/i', $blockstart)) {
            continue;
        }

        // Extract block.
        $block = substr($content, $start + strlen($coderaw), $end - $start - strlen($coderaw));
        $block = trim($block);

        // Parse block.
        $profile = parse_profile_block($code, $block, $source);
        if ($profile) {
            $profiles[$code] = $profile;
        }
    }

    return $profiles;
}

/**
 * Parse a single profile block.
 *
 * @param string $code The profile code.
 * @param string $block The text block.
 * @param array $source Source metadata.
 * @return array|null The parsed profile or null.
 */
function parse_profile_block($code, $block, $source) {
    // Normalize whitespace.
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
        $prog = 'TECNOLOGIA EN ' . trim(preg_replace('/\s+/', ' ', $m[1]));
        $profile['program'] = $prog;
    } else if (preg_match('/T[EÉ]CNICA\s+PROFESIONAL\s+EN\s+([A-ZÁÉÍÓÚÑ\s]+?)(?=\s+(?:PROFESIONAL|INGENIER|LICENCIAD|$))/iu', $normalized, $m)) {
        $prog = 'TECNICA PROFESIONAL EN ' . trim(preg_replace('/\s+/', ' ', $m[1]));
        $profile['program'] = $prog;
    }

    // Professional profile.
    $progend = 0;
    if (!empty($profile['program'])) {
        $pos = stripos($normalized, $profile['program']);
        if ($pos !== false) {
            $progend = $pos + strlen($profile['program']);
        }
    }

    $orientarpos = stripos($normalized, 'ORIENTAR');
    if ($orientarpos === false) {
        $orientarpos = strlen($normalized);
    }

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

    // Return if we have meaningful content.
    if (!empty($profile['contracttype']) || !empty($profile['program']) ||
        !empty($profile['profile']) || !empty($profile['courses'])) {
        return $profile;
    }

    return null;
}
