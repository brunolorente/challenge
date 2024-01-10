<?php

namespace App\Console\Commands;

use App\Services\DisbursementService;
use Carbon\Carbon;
use Exception;
use Illuminate\Console\Command;

class GenerateDisbursements extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sequra:generate-disbursements {date}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
        private DisbursementService $disbursementService,
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $date = Carbon::createFromFormat('Y-m-d', $this->argument('date'));
        } catch (Exception $e) {
            $this->error('Invalid date format. Please use Y-m-d.');
            return;
        }

        $this->disbursementService->calculateDisbursements($date);
    }
}
