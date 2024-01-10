<?php

namespace App\Contracts;

use App\DTOs\AdditionalFeeData;

interface AdditionalFeeInterface
{
    public function insert(AdditionalFeeData $feeData): bool;
}
