<?php

namespace App\Console\Commands;

use App\Contracts\MerchantImporterInterface;
use Illuminate\Console\Command;

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
        $this->merchantImporter->import(getenv('MERCHANTS_CSV_URL'));
    }
}
