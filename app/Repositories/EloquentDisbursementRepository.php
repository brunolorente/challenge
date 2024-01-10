<?php

namespace App\Repositories;

use App\Contracts\DisbursementRepositoryInterface;
use App\DTOs\DisbursementData;
use App\Models\Disbursement;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EloquentDisbursementRepository implements DisbursementRepositoryInterface
{
    public function insertDisbursement(DisbursementData $disbursementData): bool
    {
        $disbursement = new Disbursement($disbursementData->toArray());
        try {
            return $disbursement->save();
        } catch (\Exception $e) {
            Log::error(sprintf("Error inserting disbursement with data: %s \n Error message: %s", json_encode($disbursementData), $e->getMessage()));
            return false;
        }
    }

    public function getSummary(): array
    {
        return DB::table('disbursements')
            ->select(
                DB::raw('EXTRACT(YEAR FROM orders_start) as year'),
                DB::raw('COUNT(*) as number_of_disbursements'),
                DB::raw('SUM(amount) as amount_disbursed_to_merchants'),
                DB::raw('SUM(commission) as amount_of_order_fees')
            )
            ->groupBy(DB::raw('EXTRACT(YEAR FROM orders_start)'))
            ->orderBy('year')
            ->get()->toArray();
    }
}
