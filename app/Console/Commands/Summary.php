<?php

namespace App\Console\Commands;

use App\Contracts\DisbursementRepositoryInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class Summary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sequra:summary';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function __construct(
        private DisbursementRepositoryInterface $disbursementRepository
    ) {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $results = $this->disbursementRepository->getSummary();

        $headers = [
            'Year',
            'Number of Disbursements',
            'Amount Disbursed to Merchants',
            'Amount of Order Fees',
            'Number of Monthly Fees Charged',
            'Amount of Monthly Fee Charged'
        ];

        $data = collect($results)->map(function ($item) {
            return [
                $item->year,
                $item->number_of_disbursements,
                number_format($item->amount_disbursed_to_merchants, 2, ',', '.') . ' €',
                number_format($item->amount_of_order_fees, 2, ',', '.') . ' €',
                "TODO",//$item->number_of_monthly_fees_charged,
                "TODO",//number_format($item->amount_of_monthly_fee_charged, 2, ',', '.') . ' €'
            ];
        })->toArray();

        $this->table($headers, $data);
    }
}
