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
 * This script reads the text files extracted from PDF profiles and
 * generates a JSON file suitable for import_vacancies.php.
 *
 * Usage:
 *   php parse_profiles.php --input=PERFILESPROFESORES_TEXT --output=perfiles.json
 *
 * @package   local_jobboard
 * @copyright 2024 ISER
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// This script does not require Moodle bootstrap.
// It can run standalone.

$options = getopt('i:o:vh', ['input:', 'output:', 'verbose', 'help']);

$help = <<<EOT
Parse extracted profile text files and generate JSON for import.

Usage:
  php parse_profiles.php --input=DIR --output=FILE [options]

Options:
  -i, --input=DIR     Input directory with .txt files (required)
  -o, --output=FILE   Output JSON file path (required)
  -v, --verbose       Show detailed parsing information
  -h, --help          Show this help

Example:
  php parse_profiles.php -i PERFILESPROFESORES_TEXT -o perfiles_2026.json -v

The script expects text files extracted from ISER profile PDFs with format:
  CODIGO | TIPO VINCULACION | PROGRAMA | PERFIL | CURSOS

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
    // Try relative to script directory.
    $altpath = __DIR__ . '/../' . $inputdir;
    if (is_dir($altpath)) {
        $inputdir = $altpath;
    } else {
        echo "Error: Input directory not found: $inputdir\n";
        exit(1);
    }
}

echo "==============================================\n";
echo "PROFILE TEXT PARSER\n";
echo "==============================================\n";
echo "Input directory: $inputdir\n";
echo "Output file: $outputfile\n";
echo "\n";

// Get all text files.
$files = glob($inputdir . '/*.txt');
$files = array_filter($files, function($f) {
    // Exclude consolidated file.
    return strpos(basename($f), '_CONSOLIDADO') === false;
});
sort($files);

echo "Found " . count($files) . " text files to process\n\n";

$allprofiles = [];
$stats = [
    'files_processed' => 0,
    'profiles_found' => 0,
    'fcas_profiles' => 0,
    'fii_profiles' => 0,
    'errors' => 0,
];

/**
 * Parse a single text file to extract profiles.
 */
function parse_text_file($filepath, $verbose = false) {
    $content = file_get_contents($filepath);
    if ($content === false) {
        return [];
    }

    $profiles = [];
    $filename = basename($filepath);

    // Determine source info from filename.
    $source = [
        'faculty' => '',
        'modality' => 'PRESENCIAL',
        'location' => 'PAMPLONA',
    ];

    if (stripos($filename, 'FCAS') !== false) {
        $source['faculty'] = 'FCAS';
    } else if (stripos($filename, 'FII') !== false) {
        $source['faculty'] = 'FII';
    }

    if (stripos($filename, 'DISTANCIA') !== false) {
        $source['modality'] = 'A DISTANCIA';
    } else if (stripos($filename, 'PRESENCIAL') !== false) {
        $source['modality'] = 'PRESENCIAL';
    }

    // Extract location from content if available.
    if (preg_match('/MODALIDAD\s+(?:PRESENCIAL|A\s+DISTANCIA)\s+(?:EN\s+)?([A-ZÁÉÍÓÚ]+)/i', $content, $locmatch)) {
        $source['location'] = trim($locmatch[1]);
    }

    // Pattern to match profile codes (FCAS-XX or FII-XX).
    // Split content by profile codes.
    $pattern = '/\b(FCAS-\d+|FII-\d+)\b/';

    // Find all codes and their positions.
    if (!preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE)) {
        if ($verbose) {
            echo "  No profile codes found in $filename\n";
        }
        return [];
    }

    $codes = $matches[0];

    // Process each code block.
    for ($i = 0; $i < count($codes); $i++) {
        $code = $codes[$i][0];
        $start = $codes[$i][1];
        $end = isset($codes[$i + 1]) ? $codes[$i + 1][1] : strlen($content);

        // Extract the block for this profile.
        $block = substr($content, $start, $end - $start);

        // Parse the profile block.
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
    // Normalize whitespace.
    $block = preg_replace('/\s+/', ' ', $block);
    $block = trim($block);

    $profile = [
        'code' => $code,
        'faculty' => $source['faculty'],
        'modality' => $source['modality'],
        'location' => $source['location'],
        'contracttype' => '',
        'program' => '',
        'profile' => '',
        'courses' => [],
    ];

    // Try to determine faculty from code if not set.
    if (empty($profile['faculty'])) {
        if (strpos($code, 'FCAS') === 0) {
            $profile['faculty'] = 'FCAS';
        } else if (strpos($code, 'FII') === 0) {
            $profile['faculty'] = 'FII';
        }
    }

    // Contract type patterns.
    if (preg_match('/OCASIONAL\s*TIEMPO\s*COMPLETO/i', $block)) {
        $profile['contracttype'] = 'OCASIONAL TIEMPO COMPLETO';
    } else if (preg_match('/C[ÁA]TEDRA/i', $block)) {
        $profile['contracttype'] = 'CATEDRA';
    }

    // Program patterns.
    $programPatterns = [
        '/TECNOLOG[IÍ]A\s+EN\s+([A-ZÁÉÍÓÚ\s]+?)(?=\s+(?:PROFESIONAL|INGENIER|LICENCIAD|ADMINISTRAD|ORIENTAR|[A-Z]{2,}-\d+|\z))/i',
        '/TECNICA\s+PROFESIONAL\s+EN\s+([A-ZÁÉÍÓÚ\s]+?)(?=\s+(?:PROFESIONAL|INGENIER|LICENCIAD|ADMINISTRAD|ORIENTAR|[A-Z]{2,}-\d+|\z))/i',
    ];

    foreach ($programPatterns as $pattern) {
        if (preg_match($pattern, $block, $match)) {
            $program = 'TECNOLOGIA EN ' . trim($match[1]);
            $program = preg_replace('/\s+/', ' ', $program);
            $profile['program'] = $program;
            break;
        }
    }

    // Profile/requirements patterns.
    $profilePatterns = [
        '/(?:PROFESIONAL\s+EN|INGENIER[OA](?:\s+(?:DE|EN))?|LICENCIAD[OA](?:\s+EN)?|ADMINISTRADOR(?:\s+DE)?|M[EÉ]DICO)\s+([A-ZÁÉÍÓÚ\s\/\(\)]+?)(?=\s+(?:ORIENTAR|CURSOS?|[A-Z]{2,}-\d+|\z))/i',
    ];

    foreach ($profilePatterns as $pattern) {
        if (preg_match($pattern, $block, $match)) {
            $prof = trim($match[0]);
            // Clean up.
            $prof = preg_replace('/\s+/', ' ', $prof);
            $prof = preg_replace('/\s*\/\s*/', ' / ', $prof);
            $profile['profile'] = $prof;
            break;
        }
    }

    // Extract courses.
    if (preg_match('/ORIENTAR\s+(?:LOS\s+)?CURSOS?\s+(?:DE\s+)?:?\s*(.+?)(?=[A-Z]{2,}-\d+|\z)/is', $block, $match)) {
        $coursesText = $match[1];
        // Split by common separators.
        $courses = preg_split('/\s*[-–]\s*|\s*[•]\s*|\s+(?=[A-ZÁÉÍÓÚ]{3,})/u', $coursesText);
        $courses = array_map('trim', $courses);
        $courses = array_filter($courses, function($c) {
            return strlen($c) > 2 && !preg_match('/^(DE|EN|LOS|LAS|EL|LA|Y|O|PARA|DEL|AL)$/i', $c);
        });
        $profile['courses'] = array_values($courses);
    }

    // Only return if we have at least a code and some content.
    if (!empty($profile['code']) && (!empty($profile['program']) || !empty($profile['profile']))) {
        return $profile;
    }

    return null;
}

// Process each file.
foreach ($files as $file) {
    $filename = basename($file);
    echo "Processing: $filename\n";

    $profiles = parse_text_file($file, $verbose);
    $count = count($profiles);

    if ($verbose) {
        echo "  Found $count profiles\n";
    }

    foreach ($profiles as $code => $profile) {
        // Avoid duplicates - keep first occurrence.
        if (!isset($allprofiles[$code])) {
            $allprofiles[$code] = $profile;
            $stats['profiles_found']++;

            if (strpos($code, 'FCAS') === 0) {
                $stats['fcas_profiles']++;
            } else if (strpos($code, 'FII') === 0) {
                $stats['fii_profiles']++;
            }
        }
    }

    $stats['files_processed']++;
}

// Convert to indexed array and sort by code.
$vacancies = array_values($allprofiles);
usort($vacancies, function($a, $b) {
    return strcmp($a['code'], $b['code']);
});

// Build output structure.
$output = [
    'generated' => date('Y-m-d H:i:s'),
    'source' => 'PERFILES PROFESORES ISER 2026',
    'stats' => [
        'total_profiles' => count($vacancies),
        'fcas_profiles' => $stats['fcas_profiles'],
        'fii_profiles' => $stats['fii_profiles'],
    ],
    'vacancies' => $vacancies,
];

// Write JSON file.
$json = json_encode($output, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
if (file_put_contents($outputfile, $json) === false) {
    echo "Error: Could not write output file: $outputfile\n";
    exit(1);
}

// Summary.
echo "\n";
echo "==============================================\n";
echo "PARSING COMPLETE\n";
echo "==============================================\n";
echo "Files processed: {$stats['files_processed']}\n";
echo "Total profiles found: {$stats['profiles_found']}\n";
echo "  - FCAS profiles: {$stats['fcas_profiles']}\n";
echo "  - FII profiles: {$stats['fii_profiles']}\n";
echo "\n";
echo "Output written to: $outputfile\n";
echo "File size: " . number_format(filesize($outputfile)) . " bytes\n";

// Show sample.
if ($verbose && count($vacancies) > 0) {
    echo "\n--- Sample profiles ---\n";
    for ($i = 0; $i < min(3, count($vacancies)); $i++) {
        echo json_encode($vacancies[$i], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
    }
}

exit(0);
