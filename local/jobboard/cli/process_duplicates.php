<?php
/**
 * Procesa duplicados en JSONs:
 * - Duplicados idénticos: elimina las copias extras
 * - Duplicados con contenido diferente: renombra con sufijos a, b, c...
 *
 * Uso: php process_duplicates.php [--dry-run]
 */

$dryRun = in_array('--dry-run', $argv);

if ($dryRun) {
    echo "=== MODO DRY-RUN (no se modificarán archivos) ===\n\n";
}

$sedesDir = __DIR__ . "/sedes";
$sedeFolders = glob($sedesDir . "/*", GLOB_ONLYDIR);

$totalIdenticalRemoved = 0;
$totalRenamed = 0;
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
        $modified = false;

        // Agrupar vacantes por código
        $byCode = [];
        foreach ($data['vacancies'] as $i => $vacancy) {
            $code = $vacancy['code'] ?? '';
            if (empty($code)) continue;

            if (!isset($byCode[$code])) {
                $byCode[$code] = [];
            }
            $byCode[$code][] = [
                'index' => $i,
                'vacancy' => $vacancy,
            ];
        }

        // Procesar duplicados
        $newVacancies = [];
        $processedCodes = [];

        foreach ($data['vacancies'] as $i => $vacancy) {
            $code = $vacancy['code'] ?? '';
            if (empty($code)) {
                $newVacancies[] = $vacancy;
                continue;
            }

            // Si ya procesamos este código, saltar
            if (isset($processedCodes[$code])) {
                continue;
            }

            $entries = $byCode[$code];

            // Si no hay duplicados, agregar directamente
            if (count($entries) === 1) {
                $newVacancies[] = $vacancy;
                $processedCodes[$code] = true;
                continue;
            }

            // Hay duplicados - analizar contenido
            $uniqueContents = [];
            foreach ($entries as $entry) {
                $v = $entry['vacancy'];
                $contentKey = md5(
                    ($v['profile'] ?? '') . '|' .
                    ($v['activities'] ?? $v['courses'] ?? '') . '|' .
                    ($v['contract_type'] ?? '')
                );

                if (!isset($uniqueContents[$contentKey])) {
                    $uniqueContents[$contentKey] = [];
                }
                $uniqueContents[$contentKey][] = $entry;
            }

            if (count($uniqueContents) === 1) {
                // Todos idénticos - mantener solo uno
                $newVacancies[] = $vacancy;
                $removed = count($entries) - 1;
                $totalIdenticalRemoved += $removed;
                echo "✓ [$locationName] $code: eliminadas $removed copias idénticas\n";
                $modified = true;
            } else {
                // Variantes diferentes - renombrar con sufijos
                $suffixIndex = 0;
                $suffixes = range('a', 'z');

                foreach ($uniqueContents as $hash => $variantEntries) {
                    // Tomar la primera entrada de cada variante
                    $entry = $variantEntries[0];
                    $newVacancy = $entry['vacancy'];

                    // Asignar sufijo
                    $newVacancy['code'] = $code . $suffixes[$suffixIndex];
                    $newVacancies[] = $newVacancy;

                    echo "⚠️  [$locationName] $code → {$newVacancy['code']}: ";
                    echo mb_substr($newVacancy['profile'] ?? '', 0, 50, 'UTF-8') . "...\n";

                    $totalRenamed++;
                    $suffixIndex++;

                    // Si hay copias idénticas de esta variante, eliminarlas
                    if (count($variantEntries) > 1) {
                        $removed = count($variantEntries) - 1;
                        $totalIdenticalRemoved += $removed;
                        echo "   (eliminadas $removed copias idénticas de esta variante)\n";
                    }
                }
                $modified = true;
            }

            $processedCodes[$code] = true;
        }

        if ($modified) {
            $filesModified++;
            $data['vacancies'] = $newVacancies;

            if (!$dryRun) {
                $jsonOutput = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                file_put_contents($jsonFile, $jsonOutput);
            }

            echo "   Archivo: $fileName ($originalCount → " . count($newVacancies) . " vacantes)\n\n";
        }
    }
}

echo "\n=== RESUMEN ===\n";
echo "Archivos modificados: $filesModified\n";
echo "Copias idénticas eliminadas: $totalIdenticalRemoved\n";
echo "Códigos renombrados (variantes): $totalRenamed\n";

if ($dryRun) {
    echo "\n(Ejecutar sin --dry-run para aplicar cambios)\n";
} else {
    echo "\n✓ Cambios aplicados\n";
}
