<?php

namespace App\Services;

use App\DTOs\MerchantData;
use DateTime;

final class MerchantDataTransformer
{
    // TODO: Add some validations or ValueObjects

    public function transform(array $merchantData): MerchantData
    {
        return MerchantData::fromArray($this->sanitizeData($merchantData));
    }

    private function sanitizeData(array $merchantData) : array
    {
        $merchantData["ingest_date"] = DateTime::createFromFormat('Y-m-d h:i:s', date('Y-m-d h:i:s'));
        $merchantData["live_on"] = DateTime::createFromFormat("Y-m-d",$merchantData["live_on"]);
        $merchantData["origin"] = "csv";
        $merchantData["external_id"] = $merchantData["id"];
        unset($merchantData["id"]);

        return $merchantData;
    }
}
