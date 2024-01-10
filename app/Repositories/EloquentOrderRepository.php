<?php

namespace App\Repositories;

use App\Contracts\OrderRepositoryInterface;
use App\DTOs\OrderData;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\UuidInterface;

class EloquentOrderRepository implements OrderRepositoryInterface
{

    public function insert(OrderData $orderData): bool
    {
        $order = new Order($orderData->toArray());
        try {
            return $order->save();
        } catch (\Exception $e) {
            Log::error(sprintf("Error inserting order with data: %s \n Error message: %s", json_encode($orderData), $e->getMessage()));
            return false;
        }
    }

    public function findAll(): array
    {
        return Order::all()->toArray();
    }

    public function updateOrderAsDisbursed(UuidInterface $uuid): bool
    {

        //try {
            return Order::findOrFail($uuid->toString())->update(["disbursed" => true]);
        //} catch (\Exception $e) {
        //    Log::error(sprintf("Error updating order status id: %s \n Error message: %s", $uuid->toString(), $e->getMessage()));
         //   return false;
        //}
    }
}
