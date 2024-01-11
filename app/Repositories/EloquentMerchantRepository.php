<?php

namespace App\Repositories;

use App\Contracts\MerchantRepositoryInterface;
use App\DTOs\MerchantData;
use App\Models\Merchant;
use DateTimeInterface;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\UuidInterface;

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
        return Merchant::all()->all();
    }

    public function findMerchantOrdersByMerchantId(UuidInterface $uuid): array
    {
        return Merchant::findOrFail($uuid->toString())->orders->all();
    }

    public function findMerchantNotDisbursedOrdersByMerchantIdBetweenTwoDates(UuidInterface $uuid, DateTimeInterface $startDate, DateTimeInterface $endDate): array
    {
        //return Merchant::findOrFail($uuid->toString())->orders->where('disbursed', false)->all();
        return Merchant::findOrFail($uuid->toString())->orders()
            ->where('created_at', '>=', $startDate)
            ->where('created_at', '<=', $endDate)
            ->where('disbursed', false)
            ->get()
            ->all();
    }
}

/*
 *
 ->orders()
            ->where('created_at', '>=', '2023-02-01')
            ->where('created_at', '<=', '2023-02-28')
            ->where('disbursed', false)
            ->get();
 *
 *
 */
