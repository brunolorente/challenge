<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use ValueError;

final class CsvReader
{
    private array $keys = [];

    public function readCsvFromFile(string $file, bool $hasHeader = true): array
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

    public function readCsvFromFileParts(string $data, bool $hasHeader = true): array
    {
        $csv = array_map(fn($row) => str_getcsv($row, ";"), explode("\n", $data));
        if ($hasHeader) {
            $this->keys = array_shift($csv);
        }
        foreach ($csv as $i=>$row) {
            try {
                $csv[$i] = array_combine($this->keys, $row);
            } catch (ValueError $e) {
                Log::error(sprintf("Error reading missing values in order %s", json_encode($row)));
                continue;
            }
        }

        return $csv;
    }
}
