<?php

namespace App\Services;

use App\DTOs\OrderData;
use DateTime;

final class OrderDataTransformer
{
    // TODO: Add some validations or ValueObjects

    public function transform(array $merchantData): OrderData
    {
        return OrderData::fromArray($this->sanitizeData($merchantData));
    }

    private function sanitizeData(array $orderData) : array
    {
        $orderData['ingest_date'] = DateTime::createFromFormat('Y-m-d h:i:s', date('Y-m-d h:i:s'));
        $orderData['origin'] = 'csv';
        $orderData['external_id'] = $orderData['id'];
        $orderData['created_at'] = new DateTime($orderData['created_at']);

        unset($orderData['id']);

        return $orderData;
    }
}
