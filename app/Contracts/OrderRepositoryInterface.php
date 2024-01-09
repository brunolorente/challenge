<?php

namespace App\Contracts;

use App\DTOs\OrderData;

interface OrderRepositoryInterface
{
    public function insert(OrderData $orderData): bool;

    public function findAll(): array;
}
