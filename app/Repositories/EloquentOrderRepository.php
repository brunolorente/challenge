<?php

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;
use App\DTOs\OrderData;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class EloquentOrderRepository implements OrderRepositoryInterface
{

    public function insert(OrderData $orderData): bool
    {
        $order = new Order($orderData->toArray());
        try {
            $order->save();
        } catch (\Exception $e) {
            Log::error(sprintf("Error inserting order with data: %s \n Error message: %s", json_encode($orderData), $e->getMessage()));
            return false;
        }

        return true;
    }

    public function findAll(): array
    {
        return Order::all()->toArray();
    }
}
