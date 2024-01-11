<?php

namespace Unit\Services;

use _PHPStan_a81df6648\Nette\Neon\Exception;
use App\Contracts\FileDownloaderInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Services\CsvReader;
use App\Services\OrderDataTransformer;
use App\Services\OrderImporterFromCsv;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\TestCase;
use TypeError;

class OrderImporterFromCsvTest extends TestCase
{
    private CsvReader $csvReader;
    private FileDownloaderInterface $fileDownloader;
    private OrderImporterFromCsv $orderImporterFromCsv;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockOrderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->fileDownloader = $this->createMock(FileDownloaderInterface::class);

        $this->csvReader = new CsvReader();
        $this->dataTransformer = new OrderDataTransformer();

        $this->orderImporterFromCsv = new OrderImporterFromCsv(
            $this->mockOrderRepository,
            $this->csvReader,
            $this->fileDownloader,
            $this->dataTransformer);
    }

    public function testImport(): void
    {
        // given
        $csvContent = file_get_contents('/var/www/tests/fixtures/orders.csv');

        // then
        $this->mockOrderRepository
            ->expects(self::exactly(42))
            ->method('insert');
        $this->fileDownloader
            ->expects(self::exactly(2))
            ->method('download')
            ->willReturnOnConsecutiveCalls($csvContent, false);

        // when
        // the download doesn't occur because the file downloader is mocked
        $this->orderImporterFromCsv->import('https://sequra.github.io/backend-challenge/orders.csv');
    }

    public function testPersistOrderHandlesTypeException(): void
    {
        // given
        $csvContent = file_get_contents('/var/www/tests/fixtures/orders.csv');

        // Then
        $this->fileDownloader
            ->expects(self::exactly(2))
            ->method('download')
            ->willReturnOnConsecutiveCalls($csvContent, false);
        $this->mockOrderRepository->method('insert')
            ->will($this->throwException(new TypeError("Test 1 exception")));

        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Test 1 exception');
            });

        // when
        // the download doesn't occur because the file downloader is mocked
        $this->orderImporterFromCsv->import('https://sequra.github.io/backend-challenge/orders.csv');
    }

    public function testPersistOrderHandlesException(): void
    {
        // given
        $csvContent = file_get_contents('/var/www/tests/fixtures/orders.csv');

        // Then
        $this->fileDownloader
            ->expects(self::exactly(2))
            ->method('download')
            ->willReturnOnConsecutiveCalls($csvContent, false);

        $this->mockOrderRepository->method('insert')
            ->will($this->throwException(new Exception("Test 2 exception")));
        Log::shouldReceive('error')
            ->once()
            ->withArgs(function ($message) {
                return str_contains($message, 'Test 2 exception');
            });

        // when
        // the download doesn't occur because the file downloader is mocked
        $this->orderImporterFromCsv->import('https://sequra.github.io/backend-challenge/orders.csv');
    }
}
