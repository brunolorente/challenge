<?php

namespace App\DTOs;

use DateTimeInterface;

final class OrderData
{
    private function __construct(
        private string $external_id,
        private string $merchant_reference,
        private float $amount,
        private DateTimeInterface $created_at,
        private DateTimeInterface $ingest_date,
        private string $origin,
    ) {
    }

    public static function build(
        string $external_id,
        string $merchant_reference,
        float $amount,
        DateTimeInterface $created_at,
        DateTimeInterface $ingest_date,
        string $origin,
    ) : self
    {
        return new self(
            $external_id,
            $merchant_reference,
            $amount,
            $created_at,
            $ingest_date,
            $origin,
        );
    }

    public function toArray() : array
    {
        return  [
            'external_id' => $this->external_id,
            'merchant_reference' => $this->merchant_reference,
            'amount' => $this->amount,
            'created_at' => $this->created_at,
            'ingest_date' => $this->ingest_date,
            'origin' => $this->origin,
        ];
    }

    public static function fromArray(array $orderData): self
    {
        return self::build(
            $orderData["external_id"],
            $orderData["merchant_reference"],
            $orderData["amount"],
            $orderData["created_at"],
            $orderData["ingest_date"],
            $orderData["origin"]
        );
    }
}
