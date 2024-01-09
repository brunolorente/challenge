<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Contracts\MerchantImporterInterface;

class ImportMerchants extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sequra:import-merchants';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
        private MerchantImporterInterface $merchantImporter,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->merchantImporter->import(getenv("MERCHANTS_CSV_URL"));
    }
}
