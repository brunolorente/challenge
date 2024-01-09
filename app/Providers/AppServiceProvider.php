<?php

namespace App\Providers;

use App\Contracts\MerchantImporterInterface;
use App\Contracts\MerchantRepositoryInterface;
use App\Repositories\EloquentMerchantRepository;
use App\Services\MerchantImporterFromCsv;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
