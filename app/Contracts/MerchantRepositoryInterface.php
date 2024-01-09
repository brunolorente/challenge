<?php

namespace App\Contracts;

use App\DTOs\MerchantData;

interface MerchantRepositoryInterface
{
    public function insert(MerchantData $merchantData): bool;

    public function findAll(): array;
}
