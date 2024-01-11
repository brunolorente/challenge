<?php

namespace Unit\Services;

use App\Contracts\MerchantRepositoryInterface;
use App\Services\CsvReader;
use App\Services\MerchantDataTransformer;
use App\Services\MerchantImporterFromCsv;
use PHPUnit\Framework\TestCase;

class MerchantImporterFromCsvTest extends TestCase
{
    private CsvReader $csvReader;

    private MerchantImporterFromCsv $merchantImporterFromCsv;

    protected function setUp(): void
    {
        parent::setUp();

        $this->csvReader = new CsvReader();
        $this->dataTransformer = new MerchantDataTransformer();
        $this->mockRepository = $this->createMock(MerchantRepositoryInterface::class);
        $this->merchantImporterFromCsv = new MerchantImporterFromCsv($this->csvReader, $this->mockRepository, $this->dataTransformer);
    }

    public function testImport(): void
    {
        // given

        // then
        $this->mockRepository->expects(self::exactly(50))->method('insert');

        // when
        $this->merchantImporterFromCsv->import('/var/www/tests/fixtures/merchants.csv');
    }
}
