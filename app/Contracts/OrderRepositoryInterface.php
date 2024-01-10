<?php

namespace App\Contracts;

use App\DTOs\OrderData;
use Ramsey\Uuid\UuidInterface;

interface OrderRepositoryInterface
{
    public function insert(OrderData $orderData): bool;

    public function findAll(): array;

    public function updateOrderAsDisbursed(UuidInterface $uuid): bool;
}
