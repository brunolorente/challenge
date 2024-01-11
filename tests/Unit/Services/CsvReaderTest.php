<?php

namespace Unit\Services;

use App\Services\CsvReader;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\TestCase;

class CsvReaderTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Log::shouldReceive('error');
    }

    public function testReadCsvWithHeader(): void
    {
        // given
        $csvReader = new CsvReader();
        // when
        $result = $csvReader->readCsvFromFile('/var/www/tests/fixtures/merchants.csv');
        // then
        $this->assertIsArray($result);
        $this->assertCount(50, $result);
    }

    public function testReadCsvWithoutHeader(): void
    {
        // given
        $csvReader = new CsvReader();
        // when
        $result = $csvReader->readCsvFromFile('/var/www/tests/fixtures/merchants-without-header.csv', false);
        // then
        $this->assertIsArray($result);
        $this->assertCount(50, $result);
    }

    public function testReadCsvFromFilePartsWithHeader(): void
    {
        // given
        $csvReader = new CsvReader();
        $data = "header1;header2;header3\nvalue1;value2;value3\nvalue4;value5;value6";

        // when
        $result = $csvReader->readCsvFromFileParts($data);

        // then
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertArrayHasKey('header1', $result[0]);
        $this->assertEquals('value1', $result[0]['header1']);
        $this->assertEquals('value4', $result[1]['header1']);
    }

    public function testReadCsvFromFilePartsWithoutHeader(): void
    {
        // given
        $csvReader = new CsvReader();
        $data = "value1;value2;value3\nvalue4;value5;value6";

        // when
        $result = $csvReader->readCsvFromFileParts($data, false);

        // then
        $this->assertIsArray($result);
        $this->assertCount(2, $result);

        $this->assertEquals('value1', $result[0][0]);
        $this->assertEquals('value4', $result[1][0]);
    }

    public function testShouldThrowsException(): void
    {
        // given
        $csvReader = new CsvReader();
        $data = "header1;header2;header3\nvalue1;value3\nvalue4;value5;value6";
        // when
        $result = $csvReader->readCsvFromFileParts($data);
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Error reading missing values in order');
            });

        // then
        $this->assertIsArray($result);
        $this->assertCount(2, $result);
    }
}
