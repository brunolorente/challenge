<?php

namespace Feature\Console\Commands;

use App\Contracts\MerchantImporterInterface;
use App\Contracts\MerchantRepositoryInterface;
use App\Services\CsvReader;
use App\Services\MerchantDataTransformer;
use App\Services\MerchantImporterFromCsv;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Tests\CreatesApplication;

class ImportMerchantsTest extends TestCase
{
    use RefreshDatabase, CreatesApplication;

    private Application $app;

    private MerchantRepositoryInterface|MockObject $merchantRepositoryMock;

    private MerchantImporterFromCsv $merchantImporter;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = $this->createApplication();
        $this->merchantRepositoryMock = $this->createMock(MerchantRepositoryInterface::class);
        $this->merchantImporter = new MerchantImporterFromCsv(
            new CsvReader(),
            $this->merchantRepositoryMock,
            new MerchantDataTransformer()
        );

        $this->app->instance(MerchantImporterInterface::class, $this->merchantImporter);
    }

    public function testImportMerchantsCommand(): void
    {
        // given

        // when
        $this->merchantRepositoryMock->expects(self::exactly(50))->method('insert');
        $status = Artisan::call('sequra:import-merchants');

        // then
        $this->assertEquals(0, $status);
    }
}
