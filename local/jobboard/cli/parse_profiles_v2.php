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
 * CLI script to parse extracted profile text files and generate JSON.
 *
 * Version 2: Improved parsing for ISER PDF table format.
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

$options = getopt('i:o:vh', ['input:', 'output:', 'verbose', 'help']);

$help = <<<EOT
Parse extracted profile text files and generate JSON for import.

Usage:
  php parse_profiles_v2.php --input=DIR --output=FILE [options]

Options:
  -i, --input=DIR     Input directory with .txt files (required)
  -o, --output=FILE   Output JSON file path (required)
  -v, --verbose       Show detailed parsing information
  -h, --help          Show this help

EOT;

if (isset($options['h']) || isset($options['help'])) {
    echo $help;
    exit(0);
}

$inputdir = $options['i'] ?? $options['input'] ?? null;
$outputfile = $options['o'] ?? $options['output'] ?? null;
$verbose = isset($options['v']) || isset($options['verbose']);

if (empty($inputdir) || empty($outputfile)) {
    echo "Error: --input and --output are required\n\n";
    echo $help;
    exit(1);
}

// Find input directory.
if (!is_dir($inputdir)) {
    $altpath = __DIR__ . '/../' . $inputdir;
    if (is_dir($altpath)) {
        $inputdir = $altpath;
    } else {
        echo "Error: Input directory not found: $inputdir\n";
        exit(1);
    }
}

echo "==============================================\n";
echo "PROFILE TEXT PARSER v2\n";
echo "==============================================\n";
echo "Input directory: $inputdir\n";
echo "Output file: $outputfile\n\n";

// Get all text files.
$files = glob($inputdir . '/*.txt');
$files = array_filter($files, fn($f) => strpos(basename($f), '_CONSOLIDADO') === false);
sort($files);

echo "Found " . count($files) . " text files to process\n\n";

$allprofiles = [];
$stats = ['files_processed' => 0, 'profiles_found' => 0, 'fcas' => 0, 'fii' => 0];

/**
 * Parse profiles from text content.
 */
function parse_profiles($content, $filename, $verbose = false) {
    $profiles = [];

    // Determine source info from filename.
    $source = [
        'faculty' => strpos($filename, 'FCAS') !== false ? 'FCAS' :
                    (strpos($filename, 'FII') !== false ? 'FII' : ''),
        'modality' => stripos($filename, 'DISTANCIA') !== false ? 'A DISTANCIA' : 'PRESENCIAL',
        'location' => 'PAMPLONA',
    ];

    // Normalize content - join broken lines but preserve structure.
    $content = preg_replace('/\r\n|\r/', "\n", $content);

    // Pattern to find profile blocks starting with code.
    // Match codes like FCAS-01, FCAS- 13, FII-01, etc.
    $pattern = '/\b(FCAS-?\s*\d+|FII-?\s*\d+)\b/i';

    // Find all codes and their positions.
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
        $code = preg_replace('/\s+/', '', strtoupper($coderaw));

        // Skip if this is a header row.
        $blockstart = substr($content, $start, 100);
        if (preg_match('/CÓDIGO\s*TIPO/i', $blockstart)) {
            continue;
        }

        // Extract block.
        $block = substr($content, $start + strlen($coderaw), $end - $start - strlen($coderaw));
        $block = trim($block);

        // Parse the block.
        $profile = parse_block($code, $block, $source, $verbose);
        if ($profile) {
            $profiles[$code] = $profile;
        }
    }

    return $profiles;
}

/**
 * Parse a single profile block.
 */
function parse_block($code, $block, $source, $verbose = false) {
    // Normalize whitespace for pattern matching.
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

    // 1. Contract type - at the beginning of block.
    if (preg_match('/^\s*(OCASIONAL\s+TIEMPO\s+COMPLETO|C[ÁA]TEDRA)/i', $normalized, $m)) {
        $ct = strtoupper(trim($m[1]));
        $profile['contracttype'] = str_replace('ÁTEDRA', 'ATEDRA', $ct);
    }

    // 2. Program - TECNOLOGÍA EN ... or TÉCNICA PROFESIONAL EN ...
    if (preg_match('/TECNOLOG[IÍ]A\s+EN\s+([A-ZÁÉÍÓÚÑ\s]+?)(?=\s+(?:PROFESIONAL|INGENIER|LICENCIAD|ADMINISTRAD|MÉDICO|ABOGAD|CONTADOR|ECONOMISTA|PSICOLOG|TRABAJADOR|TECNÓLOG|ORIENTAR|$))/iu', $normalized, $m)) {
        $prog = 'TECNOLOGIA EN ' . trim(preg_replace('/\s+/', ' ', $m[1]));
        $profile['program'] = $prog;
    } else if (preg_match('/T[EÉ]CNICA\s+PROFESIONAL\s+EN\s+([A-ZÁÉÍÓÚÑ\s]+?)(?=\s+(?:PROFESIONAL|INGENIER|LICENCIAD|$))/iu', $normalized, $m)) {
        $prog = 'TECNICA PROFESIONAL EN ' . trim(preg_replace('/\s+/', ' ', $m[1]));
        $profile['program'] = $prog;
    }

    // 3. Professional profile - after program, before ORIENTAR.
    // Extract the text between program end and ORIENTAR.
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
        // Clean up common patterns.
        $proftext = preg_replace('/^\s*(?:Y\s+|,\s*)+/', '', $proftext);
        $proftext = preg_replace('/\s*(?:ORIENTAR.*|CURSOS?.*)$/i', '', $proftext);
        if (strlen($proftext) > 5) {
            $profile['profile'] = trim($proftext);
        }
    }

    // 4. Courses - after "ORIENTAR LOS CURSOS DE:" until end or next code.
    if (preg_match('/ORIENTAR\s+(?:LOS\s+)?CURSOS?\s*(?:DE)?\s*:?\s*(.+?)$/isu', $normalized, $m)) {
        $coursestext = trim($m[1]);
        // Split by common patterns.
        $courses = preg_split('/\s*(?:[-–•]|\n|\s{2,})\s*/u', $coursestext);
        $courses = array_map('trim', $courses);
        $courses = array_filter($courses, function($c) {
            $c = trim($c);
            // Filter out very short, common words, and garbage.
            if (strlen($c) < 3) return false;
            if (preg_match('/^(?:DE|EN|LOS|LAS|EL|LA|Y|O|PARA|DEL|AL|CON|CURSOS?|ORIENTAR|CÓDIGO|TIPO|VINCULACIÓN|PROGRAMA|ACADÉMICO|PERFIL|PROFESIONAL|ESPECÍFICO|POSIBLES)$/i', $c)) {
                return false;
            }
            return true;
        });
        $profile['courses'] = array_values($courses);
    }

    // Only return if we have meaningful content.
    if (!empty($profile['contracttype']) || !empty($profile['program']) ||
        !empty($profile['profile']) || !empty($profile['courses'])) {
        return $profile;
    }

    return null;
}

// Process files.
foreach ($files as $file) {
    $filename = basename($file);
    $content = file_get_contents($file);

    echo "Processing: $filename";

    $profiles = parse_profiles($content, $filename, $verbose);
    $count = count($profiles);

    echo " ... found $count profiles\n";

    foreach ($profiles as $code => $profile) {
        if (!isset($allprofiles[$code])) {
            $allprofiles[$code] = $profile;
            $stats['profiles_found']++;
            if (strpos($code, 'FCAS') === 0) $stats['fcas']++;
            else if (strpos($code, 'FII') === 0) $stats['fii']++;
        }
    }

    $stats['files_processed']++;
}

// Sort and prepare output.
ksort($allprofiles);
$vacancies = array_values($allprofiles);

$output = [
    'generated' => date('Y-m-d H:i:s'),
    'source' => 'PERFILES PROFESORES ISER 2026',
    'stats' => [
        'total_profiles' => count($vacancies),
        'fcas_profiles' => $stats['fcas'],
        'fii_profiles' => $stats['fii'],
    ],
    'vacancies' => $vacancies,
];

// Write JSON.
$json = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
file_put_contents($outputfile, $json);

// Summary.
echo "\n";
echo "==============================================\n";
echo "PARSING COMPLETE\n";
echo "==============================================\n";
echo "Files processed: {$stats['files_processed']}\n";
echo "Total profiles: {$stats['profiles_found']}\n";
echo "  - FCAS: {$stats['fcas']}\n";
echo "  - FII: {$stats['fii']}\n";
echo "Output: $outputfile (" . number_format(filesize($outputfile)) . " bytes)\n";

// Sample output.
if ($verbose && count($vacancies) > 0) {
    echo "\n--- Sample profiles ---\n";
    for ($i = 0; $i < min(5, count($vacancies)); $i++) {
        echo json_encode($vacancies[$i], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
}

exit(0);
