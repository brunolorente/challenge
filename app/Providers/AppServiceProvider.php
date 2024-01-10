<?php

namespace App\Providers;

use App\Contracts\AdditionalFeeInterface;
use App\Contracts\DisbursementRepositoryInterface;
use App\Contracts\FileDownloaderInterface;
use App\Contracts\MerchantImporterInterface;
use App\Contracts\MerchantRepositoryInterface;
use App\Contracts\OrderImporterInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Repositories\EloquentAdditionalFeeRepository;
use App\Repositories\EloquentDisbursementRepository;
use App\Repositories\EloquentMerchantRepository;
use App\Repositories\EloquentOrderRepository;
use App\Services\CurlFileDownloader;
use App\Services\MerchantImporterFromCsv;
use App\Services\OrderImporterFromCsv;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(MerchantImporterInterface::class, MerchantImporterFromCsv::class);
        $this->app->bind(MerchantRepositoryInterface::class, EloquentMerchantRepository::class);
        $this->app->bind(OrderImporterInterface::class, OrderImporterFromCsv::class);
        $this->app->bind(OrderRepositoryInterface::class, EloquentOrderRepository::class);
        $this->app->bind(FileDownloaderInterface::class, CurlFileDownloader::class);
        $this->app->bind(DisbursementRepositoryInterface::class, EloquentDisbursementRepository::class);
        $this->app->bind(AdditionalFeeInterface::class, EloquentAdditionalFeeRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
