<?php

namespace Unit\Services;

use App\DTOs\MerchantData;
use App\Services\MerchantDataTransformer;
use PHPUnit\Framework\TestCase;

class MerchantDataTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        // given
        $transformer = new MerchantDataTransformer();
        $data = [
            'id' => '86312006-4d7e-45c4-9c28-788f4aa68a62',
            'reference' => 'padberg_group',
            'email' => 'info@padberg-group.com',
            'live_on' => '2023-02-01',
            'disbursement_frequency' => 'DAILY',
            'minimum_monthly_fee' => 0.0,
        ];
        //when
        $result = $transformer->transform($data);
        // then
        $this->assertInstanceOf(MerchantData::class, $result);
    }
}
