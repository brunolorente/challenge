<?php

namespace Unit\Services;

use App\Services\CsvReader;
use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase
{
    public function testReadCsvWithHeader(): void
    {
        // given
        $csvReader = new CsvReader();
        // when
        $result = $csvReader->readCsvFromFile('/var/www/tests/Data/merchants.csv');
        // then
        $this->assertIsArray($result);
        $this->assertCount(50, $result);
    }

    public function testReadCsvWithoutHeader(): void
    {
        // given
        $csvReader = new CsvReader();
        // when
        $result = $csvReader->readCsvFromFile('/var/www/tests/Data/merchants-without-header.csv', false);
        // then
        $this->assertIsArray($result);
        $this->assertCount(50, $result);
    }
}

