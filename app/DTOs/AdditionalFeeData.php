<?php

namespace App\DTOs;

use DateTimeInterface;
use Ramsey\Uuid\UuidInterface;

class AdditionalFeeData
{
    private function __construct(
        private float $fee,
        private UuidInterface $merchant_id,
        private DateTimeInterface $date,
    ) {
    }

    public static function build(
        float $fee,
        UuidInterface $merchant_id,
        DateTimeInterface $date,
    ) : self {
        return new self(
            $fee,
            $merchant_id,
            $date,
        );
    }

    public function toArray() : array
    {
        return  [
            'fee' => $this->fee,
            'merchant_id' => $this->merchant_id,
            'date' => $this->date,
        ];
    }

    public static function fromArray(array $feeData): self
    {
        return self::build(
            $feeData['fee'],
            $feeData['merchant_id'],
            $feeData['date'],
        );
    }
}
