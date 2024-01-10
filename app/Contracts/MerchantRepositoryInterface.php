<?php

namespace App\Contracts;

use App\DTOs\MerchantData;
use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

interface MerchantRepositoryInterface
{
    public function insert(MerchantData $merchantData): bool;

    public function findAll(): array;

    public function findMerchantOrdersByMerchantId(UuidInterface $uuid): array;

    public function findMerchantNotDisbursedOrdersByMerchantIdBetweenTwoDates(UuidInterface $uuid, DateTimeInterface $startDate, DateTimeInterface $endDate): array;
}
