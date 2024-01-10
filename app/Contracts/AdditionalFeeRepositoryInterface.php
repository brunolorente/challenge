<?php

namespace App\Contracts;

use App\DTOs\AdditionalFeeData;

interface AdditionalFeeRepositoryInterface
{
    public function insert(AdditionalFeeData $feeData): bool;
}
