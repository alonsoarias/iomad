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
 * CLI script to import vacancies from JSON file.
 *
 * This script imports professional profiles as job vacancies into the
 * local_jobboard plugin, supporting IOMAD multi-tenant structure.
 *
 * Usage:
 *   php import_vacancies.php --file=perfiles.json [--convocatoria=ID] [--dryrun]
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

define('CLI_SCRIPT', true);

require(__DIR__ . '/../../../config.php');
require_once($CFG->libdir . '/clilib.php');

// CLI options.
list($options, $unrecognized) = cli_get_params([
    'file' => null,
    'convocatoria' => null,
    'company' => null,
    'department' => null,
    'opendate' => null,
    'closedate' => null,
    'dryrun' => false,
    'update' => false,
    'status' => 'draft',
    'verbose' => false,
    'help' => false,
], [
    'f' => 'file',
    'c' => 'convocatoria',
    'o' => 'opendate',
    'e' => 'closedate',
    'd' => 'dryrun',
    'u' => 'update',
    's' => 'status',
    'v' => 'verbose',
    'h' => 'help',
]);

if ($unrecognized) {
    $unrecognized = implode("\n  ", $unrecognized);
    cli_error(get_string('cliunknowoption', 'admin', $unrecognized));
}

$help = <<<EOT
CLI script to import vacancies from JSON file.

Options:
  -f, --file=FILE          Path to JSON file with vacancy data (required)
  -c, --convocatoria=ID    Convocatoria ID to associate vacancies with
  --company=ID             Default IOMAD company ID for vacancies
  --department=ID          Default IOMAD department ID for vacancies
  -o, --opendate=DATE      Opening date (YYYY-MM-DD or timestamp), default: now
  -e, --closedate=DATE     Closing date (YYYY-MM-DD or timestamp), default: +30 days
  -d, --dryrun             Simulate import without creating records
  -u, --update             Update existing vacancies (by code)
  -s, --status=STATUS      Initial status: draft, published (default: draft)
  -v, --verbose            Show detailed information about company/department mapping
  -h, --help               Print this help

Example:
  php import_vacancies.php --file=perfiles.json --convocatoria=1 --opendate=2026-01-15 --closedate=2026-02-15

JSON format expected:
{
  "vacancies": [
    {
      "code": "FCAS-01",
      "contracttype": "OCASIONAL TIEMPO COMPLETO",
      "program": "TECNOLOGIA EN GESTION COMUNITARIA",
      "profile": "PROFESIONAL EN TRABAJO SOCIAL",
      "courses": ["SISTEMATIZACION DE EXPERIENCIAS", "SUJETO Y FAMILIA"],
      "faculty": "FCAS",
      "modality": "PRESENCIAL",
      "location": "PAMPLONA",
      "company_shortname": "FCAS",
      "department_name": "Presencial"
    }
  ]
}

Notes:
  - faculty: Used as company_shortname if company_shortname is not provided
  - modality: Mapped to department name (PRESENCIAL->Presencial, A DISTANCIA->A Distancia, etc.)
  - department_name: Explicit department name (overrides modality mapping)
  - Only child departments (parent > 0) are used, root departments are excluded

EOT;

if ($options['help'] || empty($options['file'])) {
    echo $help;
    exit($options['help'] ? 0 : 1);
}

// Validate file.
$filepath = $options['file'];
if (!file_exists($filepath)) {
    // Try relative to plugin directory.
    $altpath = __DIR__ . '/../' . $filepath;
    if (file_exists($altpath)) {
        $filepath = $altpath;
    } else {
        cli_error("File not found: {$options['file']}");
    }
}

// Parse dates.
$now = time();
$opendate = $now;
$closedate = $now + (30 * 24 * 60 * 60); // 30 days from now.

if (!empty($options['opendate'])) {
    if (is_numeric($options['opendate'])) {
        $opendate = (int) $options['opendate'];
    } else {
        $parsed = strtotime($options['opendate']);
        if ($parsed === false) {
            cli_error("Invalid opendate format: {$options['opendate']}");
        }
        $opendate = $parsed;
    }
}

if (!empty($options['closedate'])) {
    if (is_numeric($options['closedate'])) {
        $closedate = (int) $options['closedate'];
    } else {
        $parsed = strtotime($options['closedate']);
        if ($parsed === false) {
            cli_error("Invalid closedate format: {$options['closedate']}");
        }
        $closedate = $parsed;
    }
}

// Validate status.
$validstatuses = ['draft', 'published'];
if (!in_array($options['status'], $validstatuses)) {
    cli_error("Invalid status: {$options['status']}. Must be: " . implode(', ', $validstatuses));
}

// Read JSON file.
$jsoncontent = file_get_contents($filepath);
if ($jsoncontent === false) {
    cli_error("Could not read file: $filepath");
}

$data = json_decode($jsoncontent, true);
if ($data === null) {
    cli_error("Invalid JSON in file: " . json_last_error_msg());
}

if (empty($data['vacancies']) || !is_array($data['vacancies'])) {
    cli_error("JSON must contain 'vacancies' array");
}

$vacancies = $data['vacancies'];
$totalcount = count($vacancies);

cli_heading("Importing $totalcount vacancies from: $filepath");
echo "Open date: " . date('Y-m-d H:i', $opendate) . "\n";
echo "Close date: " . date('Y-m-d H:i', $closedate) . "\n";
echo "Status: {$options['status']}\n";
echo "Dry run: " . ($options['dryrun'] ? 'YES' : 'NO') . "\n";
echo "Update existing: " . ($options['update'] ? 'YES' : 'NO') . "\n";
echo "Verbose: " . ($options['verbose'] ? 'YES' : 'NO') . "\n";
echo "\n";

// Show available companies and departments in verbose mode.
if ($options['verbose']) {
    cli_heading("Available IOMAD Companies and Departments");

    // Get unique faculties from JSON.
    $faculties = [];
    foreach ($vacancies as $v) {
        if (!empty($v['faculty'])) {
            $faculties[strtoupper(trim($v['faculty']))] = true;
        }
    }

    foreach (array_keys($faculties) as $faculty) {
        $company = $DB->get_record('company', ['shortname' => $faculty]);
        if ($company) {
            echo "\nCompany: {$company->name} (ID: {$company->id}, shortname: {$company->shortname})\n";
            $depts = $DB->get_records('department', ['company' => $company->id], 'parent ASC, name ASC');
            echo "  Departments:\n";
            foreach ($depts as $dept) {
                $type = ($dept->parent == 0) ? 'ROOT' : 'CHILD';
                echo "    - ID: {$dept->id}, Name: '{$dept->name}', Parent: {$dept->parent} ($type)\n";
            }
        } else {
            echo "\nWARNING: Company not found for faculty: $faculty\n";
        }
    }
    echo "\n";
}

// Get admin user for createdby.
$adminuser = get_admin();

// Counters.
$created = 0;
$updated = 0;
$skipped = 0;
$errors = 0;

// IOMAD mappings cache.
$companymap = [];
$departmentmap = [];

/**
 * Get or create IOMAD company by shortname.
 */
function get_company_id($shortname) {
    global $DB, $companymap;

    if (isset($companymap[$shortname])) {
        return $companymap[$shortname];
    }

    $company = $DB->get_record('company', ['shortname' => $shortname]);
    if ($company) {
        $companymap[$shortname] = $company->id;
        return $company->id;
    }

    return null;
}

/**
 * Get all child departments for a company (for debugging).
 */
function get_company_departments($companyid) {
    global $DB;

    $sql = "SELECT id, name, parent FROM {department}
            WHERE company = :company
            ORDER BY parent ASC, name ASC";
    return $DB->get_records_sql($sql, ['company' => $companyid]);
}

/**
 * Get IOMAD department by name within a company.
 * Only returns child departments (parent > 0), excluding the root department.
 * Uses case-insensitive matching.
 */
function get_department_id($companyid, $name) {
    global $DB, $departmentmap;

    $key = "{$companyid}_{$name}";
    if (isset($departmentmap[$key])) {
        return $departmentmap[$key];
    }

    // Only get child departments (parent > 0) - exclude root department.
    // Use LOWER for case-insensitive matching.
    $sql = "SELECT id, name FROM {department}
            WHERE company = :company AND LOWER(name) = LOWER(:name) AND parent > 0";
    $dept = $DB->get_record_sql($sql, ['company' => $companyid, 'name' => $name]);
    if ($dept) {
        $departmentmap[$key] = $dept->id;
        return $dept->id;
    }

    // Try partial match if exact match fails.
    $sql = "SELECT id, name FROM {department}
            WHERE company = :company AND LOWER(name) LIKE LOWER(:pattern) AND parent > 0";
    $dept = $DB->get_record_sql($sql, ['company' => $companyid, 'pattern' => '%' . $name . '%']);
    if ($dept) {
        $departmentmap[$key] = $dept->id;
        return $dept->id;
    }

    return null;
}

/**
 * Get first child department for a company if no specific department is found.
 * This is useful as a fallback when modality doesn't match any department name.
 */
function get_first_child_department($companyid) {
    global $DB, $departmentmap;

    $key = "{$companyid}_first";
    if (isset($departmentmap[$key])) {
        return $departmentmap[$key];
    }

    // Get first child department (parent > 0).
    $sql = "SELECT id, name FROM {department}
            WHERE company = :company AND parent > 0
            ORDER BY name ASC";
    $dept = $DB->get_record_sql($sql, ['company' => $companyid], IGNORE_MULTIPLE);
    if ($dept) {
        $departmentmap[$key] = $dept->id;
        return $dept->id;
    }

    return null;
}

// Process vacancies.
foreach ($vacancies as $index => $vdata) {
    $num = $index + 1;

    // Validate required fields.
    if (empty($vdata['code'])) {
        cli_problem("[$num/$totalcount] Missing code - skipping");
        $errors++;
        continue;
    }

    $code = trim($vdata['code']);

    // Check if vacancy exists.
    $existing = $DB->get_record('local_jobboard_vacancy', ['code' => $code]);

    if ($existing && !$options['update']) {
        echo "[$num/$totalcount] SKIP: $code (already exists)\n";
        $skipped++;
        continue;
    }

    // Build vacancy record.
    $record = new stdClass();
    $record->code = $code;

    // Title from program + profile.
    $program = trim($vdata['program'] ?? '');
    $profile = trim($vdata['profile'] ?? '');
    $record->title = $program ?: "Vacante $code";
    if ($profile) {
        $record->title .= " - $profile";
    }

    // Description from courses.
    $courses = $vdata['courses'] ?? [];
    if (!empty($courses)) {
        $courselist = is_array($courses) ? implode("\n- ", $courses) : $courses;
        $record->description = "<h4>Cursos a orientar:</h4>\n<ul>\n<li>" .
            (is_array($courses) ? implode("</li>\n<li>", $courses) : $courses) .
            "</li>\n</ul>";
    } else {
        $record->description = '';
    }

    // Contract type.
    $record->contracttype = trim($vdata['contracttype'] ?? 'CATEDRA');

    // Duration (default based on contract type).
    if (stripos($record->contracttype, 'OCASIONAL') !== false) {
        $record->duration = 'Semestral';
    } else {
        $record->duration = 'Por horas';
    }

    // Location.
    $record->location = trim($vdata['location'] ?? 'PAMPLONA');

    // Modality.
    $record->modality = trim($vdata['modality'] ?? 'PRESENCIAL');

    // Department (text field) = program.
    $record->department = $program;

    // Requirements = profile.
    $record->requirements = $profile ? "<p><strong>Perfil profesional requerido:</strong></p>\n<p>$profile</p>" : '';

    // Desirable (empty for now).
    $record->desirable = '';

    // IOMAD company ID.
    // Priority: CLI option > company_shortname field > faculty field.
    $companyid = null;
    if (!empty($options['company'])) {
        $companyid = (int) $options['company'];
    } else if (!empty($vdata['company_shortname'])) {
        $companyid = get_company_id($vdata['company_shortname']);
    } else if (!empty($vdata['faculty'])) {
        // Use faculty as company shortname (e.g., FCAS, FII).
        $companyid = get_company_id(strtoupper(trim($vdata['faculty'])));
    }
    $record->companyid = $companyid;

    // IOMAD department ID (child department only, based on modality).
    // Priority: CLI option > department_name field > modality mapping > first child.
    $departmentid = null;
    if (!empty($options['department'])) {
        $departmentid = (int) $options['department'];
    } else if ($companyid) {
        // Try department_name field first.
        if (!empty($vdata['department_name'])) {
            $departmentid = get_department_id($companyid, trim($vdata['department_name']));
        }

        // Try modality mapping if department not found.
        if (!$departmentid && !empty($vdata['modality'])) {
            $modality = strtoupper(trim($vdata['modality']));
            $deptname = match($modality) {
                'PRESENCIAL' => 'Presencial',
                'A DISTANCIA', 'DISTANCIA' => 'A Distancia',
                'VIRTUAL' => 'Virtual',
                'HIBRIDA', 'HÍBRIDA' => 'Híbrida',
                default => null,
            };
            if ($deptname) {
                $departmentid = get_department_id($companyid, $deptname);
            }
        }

        // Fallback: use first child department if no specific match.
        if (!$departmentid) {
            $departmentid = get_first_child_department($companyid);
        }
    }
    $record->departmentid = $departmentid;

    // Convocatoria (dates are inherited from convocatoria).
    $record->convocatoriaid = !empty($options['convocatoria']) ? (int) $options['convocatoria'] : null;

    // Positions (default 1).
    $record->positions = (int) ($vdata['positions'] ?? 1);

    // Status.
    $record->status = $options['status'];
    $record->publicationtype = 'internal';

    // Audit fields.
    $record->createdby = $adminuser->id;
    $record->timecreated = $now;

    // Build info string for verbose output.
    $companyinfo = $companyid ? "company=$companyid" : "no company";
    $deptinfo = $departmentid ? "dept=$departmentid" : "no dept";
    $info = "[$companyinfo, $deptinfo, {$record->modality}]";

    // Dry run?
    if ($options['dryrun']) {
        echo "[$num/$totalcount] DRY RUN: $code $info\n";
        if ($existing) {
            $updated++;
        } else {
            $created++;
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
            echo "[$num/$totalcount] UPDATED: $code $info\n";
            $updated++;
        } else {
            $id = $DB->insert_record('local_jobboard_vacancy', $record);
            echo "[$num/$totalcount] CREATED: $code (ID: $id) $info\n";
            $created++;
        }
    } catch (Exception $e) {
        cli_problem("[$num/$totalcount] ERROR: $code - " . $e->getMessage());
        $errors++;
    }
}

// Summary.
echo "\n";
cli_heading("Import Summary");
echo "Total processed: $totalcount\n";
echo "Created: $created\n";
echo "Updated: $updated\n";
echo "Skipped: $skipped\n";
echo "Errors: $errors\n";

if ($options['dryrun']) {
    echo "\n*** DRY RUN - No changes were made ***\n";
}

exit($errors > 0 ? 1 : 0);
