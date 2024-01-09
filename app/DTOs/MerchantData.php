<?php

namespace App\DTOs;

use DateTimeInterface;

final class MerchantData
{
    private function __construct(
        private string $external_id,
        private string $reference,
        private string $email,
        private DateTimeInterface $live_on,
        private string $disbursement_frequency,
        private float $minimum_monthly_fee,
        private DateTimeInterface $ingest_date,
        private string $origin,
    ) {
    }

    public static function build(
        string $external_id,
        string $reference,
        string $email,
        DateTimeInterface $live_on,
        string $disbursement_frequency,
        float $minimum_monthly_fee,
        DateTimeInterface $ingest_date,
        string $origin,
    ) : self
    {
        return new self(
            $external_id,
            $reference,
            $email,
            $live_on,
            $disbursement_frequency,
            $minimum_monthly_fee,
            $ingest_date,
            $origin,
        );
    }

    public function toArray() : array
    {
        return  [
            'external_id' => $this->external_id,
            'reference' => $this->reference,
            'email' => $this->email,
            'live_on' => $this->live_on,
            'disbursement_frequency' => $this->disbursement_frequency,
            'minimum_monthly_fee' => $this->minimum_monthly_fee,
            'ingest_date' => $this->ingest_date,
            'origin' => $this->origin,
        ];
    }

    public static function fromArray(array $merchantData): self
    {
        return self::build(
            $merchantData["external_id"],
            $merchantData["reference"],
            $merchantData["email"],
            $merchantData["live_on"],
            $merchantData["disbursement_frequency"],
            $merchantData["minimum_monthly_fee"],
            $merchantData["ingest_date"],
            $merchantData["origin"]
        );
    }
}
