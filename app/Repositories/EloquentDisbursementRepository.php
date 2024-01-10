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
        // Calc total for disbursements per year
        $disbursementsTotals = DB::table('disbursements')
            ->select(
                DB::raw('EXTRACT(YEAR FROM orders_start) as year'),
                DB::raw('COUNT(id) as number_of_disbursements'),
                DB::raw('SUM(amount) as amount_disbursed_to_merchants'),
                DB::raw('SUM(commission) as amount_of_order_fees')
            )
            ->groupBy(DB::raw('EXTRACT(YEAR FROM orders_start)'))
            ->orderBy('year');

        // Calc total for additional_fees per year
        $additionalFeesTotals = DB::table('additional_fees')
            ->join('merchants', 'merchants.id', '=', 'additional_fees.merchant_id')
            ->select(
                DB::raw('EXTRACT(YEAR FROM additional_fees.date) as year'),
                DB::raw('COUNT(additional_fees.id) as number_of_monthly_fees_charged'),
                DB::raw('SUM(additional_fees.fee) as total_fee_amount')
            )
            ->groupBy(DB::raw('EXTRACT(YEAR FROM additional_fees.date)'));

        // Join both results based on year
        return DB::query()
            ->fromSub($disbursementsTotals, 'disbursements_totals')
            ->leftJoinSub($additionalFeesTotals, 'additional_fees_totals', function ($join) {
                $join->on('disbursements_totals.year', '=', 'additional_fees_totals.year');
            })
            ->select(
                'disbursements_totals.year',
                'disbursements_totals.number_of_disbursements',
                'disbursements_totals.amount_disbursed_to_merchants',
                'disbursements_totals.amount_of_order_fees',
                'additional_fees_totals.number_of_monthly_fees_charged',
                DB::raw('COALESCE(additional_fees_totals.total_fee_amount, 0) as amount_of_monthly_fee_charged')
            )
            ->orderBy('disbursements_totals.year')
            ->get()
            ->toArray();
    }
}
