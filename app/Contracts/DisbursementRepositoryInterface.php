<?php

namespace App\Contracts;

use App\DTOs\DisbursementData;

interface DisbursementRepositoryInterface
{
    public function insertDisbursement(DisbursementData $disbursementData): bool;

    public function getSummary(): array;
}
