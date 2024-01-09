<?php

namespace App\Services;

final class CsvReader
{
    public function readCsv(string $file, bool $hasHeader = true): array
    {
        $csv = array_map(fn($row) => str_getcsv($row, ";"), file($file,FILE_SKIP_EMPTY_LINES));

        if ($hasHeader) {
            $keys = array_shift($csv);
            foreach ($csv as $i=>$row) {
                $csv[$i] = array_combine($keys, $row);
            }
        }

        return $csv;
    }
}
