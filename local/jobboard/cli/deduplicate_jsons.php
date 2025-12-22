<?php
/**
 * Script para eliminar duplicados internos de los archivos JSON de sedes.
 * Los duplicados ocurrieron durante la extracción del DOCX.
 *
 * Uso: php deduplicate_jsons.php [--dry-run]
 */

$dryRun = in_array('--dry-run', $argv);

if ($dryRun) {
    echo "=== MODO DRY-RUN (no se modificarán archivos) ===\n\n";
}

$sedesDir = __DIR__ . "/sedes";
$sedeFolders = glob($sedesDir . "/*", GLOB_ONLYDIR);

$totalDuplicatesRemoved = 0;
$filesModified = 0;

foreach ($sedeFolders as $sedeFolder) {
    $locationName = basename($sedeFolder);
    $jsonFiles = glob($sedeFolder . "/*.json");

    foreach ($jsonFiles as $jsonFile) {
        $fileName = basename($jsonFile);
        $content = file_get_contents($jsonFile);
        $data = json_decode($content, true);

        if (!$data || !isset($data['vacancies'])) {
            continue;
        }

        $originalCount = count($data['vacancies']);
        $uniqueVacancies = [];
        $seenCodes = [];
        $duplicatesInFile = 0;

        foreach ($data['vacancies'] as $vacancy) {
            $code = $vacancy['code'] ?? '';
            if (empty($code)) {
                continue;
            }

            if (!isset($seenCodes[$code])) {
                $seenCodes[$code] = true;
                $uniqueVacancies[] = $vacancy;
            } else {
                $duplicatesInFile++;
            }
        }

        if ($duplicatesInFile > 0) {
            echo "[$locationName] $fileName: $originalCount → " . count($uniqueVacancies) . " vacantes (-$duplicatesInFile duplicados)\n";
            $totalDuplicatesRemoved += $duplicatesInFile;
            $filesModified++;

            if (!$dryRun) {
                $data['vacancies'] = $uniqueVacancies;
                $jsonOutput = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                file_put_contents($jsonFile, $jsonOutput);
            }
        }
    }
}

echo "\n=== RESUMEN ===\n";
echo "Archivos modificados: $filesModified\n";
echo "Duplicados eliminados: $totalDuplicatesRemoved\n";

if ($dryRun) {
    echo "\n(Ejecutar sin --dry-run para aplicar cambios)\n";
} else {
    echo "\n✓ Cambios aplicados\n";
}
