<?php

namespace App\DTOs;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class DisbursementData{
    private function __construct(
        private float $amount,
        private UuidInterface $merchant_id,
        private float $commission,
        private DateTimeInterface $orders_start,
        private DateTimeInterface $orders_end,
        private UuidInterface $reference,
        private int $nbOfOrders,
    ) {
    }

    public static function build(
        float $amount,
        UuidInterface $merchant_id,
        float $commission,
        DateTimeInterface $orders_start,
        DateTimeInterface $orders_end,
        UuidInterface $reference,
        int $nbOfOrders,
    ) : self
    {
        return new self(
            $amount,
            $merchant_id,
            $commission,
            $orders_start,
            $orders_end,
            $reference,
            $nbOfOrders,
        );
    }

    public function toArray() : array
    {
        return  [
            'amount' => $this->amount,
            'merchant_id' => $this->merchant_id,
            'commission' => $this->commission,
            'orders_start' => $this->orders_start,
            'orders_end' => $this->orders_end,
            'reference' => $this->reference,
            'nb_of_orders' => $this->nbOfOrders,
        ];
    }

    public static function fromArray(array $disbursementData): self
    {
        return self::build(
            $disbursementData["amount"],
            $disbursementData["merchant_id"],
            $disbursementData["commission"],
            $disbursementData["orders_start"],
            $disbursementData["orders_end"],
            $disbursementData["reference"],
            $disbursementData["nb_of_orders"],
        );
    }
}
