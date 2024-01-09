<?php

namespace App\Repositories;

use App\Contracts\MerchantRepositoryInterface;
use App\DTOs\MerchantData;
use App\Models\Merchant;
use Illuminate\Support\Facades\Log;

class EloquentMerchantRepository implements MerchantRepositoryInterface
{

    public function insert(MerchantData $merchantData): bool
    {
        $merchant = new Merchant($merchantData->toArray());
        try {
            $merchant->save();
        } catch (\Exception $e) {
            Log::error(sprintf("Error inserting merchant with data: %s \n Error message: %s", json_encode($merchantData), $e->getMessage()));
            return false;
        }

        return true;
    }

    public function findAll(): array
    {
        return Merchant::all()->toArray();
    }
}
