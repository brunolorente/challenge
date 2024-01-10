<?php

namespace App\Repositories;

use App\Contracts\AdditionalFeeInterface;
use App\DTOs\AdditionalFeeData;
use App\Models\AdditionalFee;
use Illuminate\Support\Facades\Log;

class EloquentAdditionalFeeRepository implements AdditionalFeeInterface
{
    public function insert(AdditionalFeeData $feeData): bool
    {
        $fee = new AdditionalFee($feeData->toArray());
        try {
            return $fee->save();
        } catch (\Exception $e) {
            Log::error(sprintf("Error inserting additional fee with data: %s \n Error message: %s", json_encode($feeData), $e->getMessage()));
            return false;
        }
    }
}
