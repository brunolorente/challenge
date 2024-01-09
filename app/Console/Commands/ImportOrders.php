<?php

namespace App\Console\Commands;

use App\Contracts\OrderImporterInterface;
use Illuminate\Console\Command;

class ImportOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sequra:import-orders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
        private OrderImporterInterface $orderImporter,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->orderImporter->import(getenv("ORDERS_CSV_URL"));
    }
}
