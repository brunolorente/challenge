<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            //
        ];
    }

    public function dailyMerchantOrders()
    {
        return collect([
            new Order([
                'id' => '1f75ff01-78b7-4763-8d92-179eca10e14d',
                'external_id' => '20b674c93ea6',
                'merchant_reference' => 'padberg_group',
                'amount' => '433.21',
                'created_at' => '2023-01-30',
                'ingest_date' => '2024-01-09 08:05:23',
                'origin' => 'csv',
                'disbursed' => true,
            ]),
            new Order([
                'id' => 'daabc617-4a35-437c-bb83-eb6ba5ab8ce3',
                'external_id' => '0b73fb1d3332',
                'merchant_reference' => 'padberg_group',
                'amount' => '194.37',
                'created_at' => '2023-02-01',
                'ingest_date' => '2024-01-09 08:05:23',
                'origin' => 'csv',
                'disbursed' => false,
            ]),
            new Order([
                'id' => '703965fb-2c25-42bf-a781-e6caa10ecc7c',
                'external_id' => '9164f0688190',
                'merchant_reference' => 'padberg_group',
                'amount' => '371.33',
                'created_at' => '2023-02-01',
                'ingest_date' => '2024-01-09 08:05:23',
                'origin' => 'csv',
                'disbursed' => false,
            ]),
            new Order([
                'id' => 'c3841909-708d-4119-bdbf-1115176040e1',
                'external_id' => '04e9b88fe8c7',
                'merchant_reference' => 'padberg_group',
                'amount' => '280.21',
                'created_at' => '2023-02-01',
                'ingest_date' => '2024-01-09 08:05:23',
                'origin' => 'csv',
                'disbursed' => false,
            ]),
        ]);
    }

    public function weeklyMerchantOrders()
    {
        return collect([
            new Order([
                'id' => '4087f1e9-86cf-448c-abfd-3d461e2146b9',
                'external_id' => 'a5a9d97f7059',
                'merchant_reference' => 'cormier_weissnat_and_hauck',
                'amount' => '20.72',
                'created_at' => '2023-02-01',
                'ingest_date' => '2024-01-09 08:33:28',
                'origin' => 'csv',
                'disbursed' => false,
            ]),
            new Order([
                'id' => '27fb5fe3-ebfd-4e37-b618-dc3e76b9981f',
                'external_id' => 'a7ebd916b626',
                'merchant_reference' => 'cormier_weissnat_and_hauck',
                'amount' => '22.15',
                'created_at' => '2023-01-30',
                'ingest_date' => '2024-01-09 08:33:28',
                'origin' => 'csv',
                'disbursed' => true,
            ]),
            new Order([
                'id' => '5c04391a-64d7-46c3-8f5e-4db888a875b0',
                'external_id' => 'bbf13e3a6daa',
                'merchant_reference' => 'cormier_weissnat_and_hauck',
                'amount' => '6.1',
                'created_at' => '2023-01-31',
                'ingest_date' => '2024-01-09 08:33:28',
                'origin' => 'csv',
                'disbursed' => false,
            ]),
            new Order([
                'id' => '12a3a9ee-4d3d-4376-9f0e-b59b6d316e89',
                'external_id' => 'c3701ca2a953',
                'merchant_reference' => 'cormier_weissnat_and_hauck',
                'amount' => '12.81',
                'created_at' => '2023-02-01',
                'ingest_date' => '2024-01-09 08:33:28',
                'origin' => 'csv',
                'disbursed' => false,
            ]),
        ]);
    }

    public function poorMerchantOrders()
    {
        return collect([
            new Order([
                'id' => 'e24e5b39-a2e7-4cf9-8240-f6c87b6cba83',
                'external_id' => 'bbf13e3a6daa',
                'merchant_reference' => 'poor_merchant',
                'amount' => '6.1',
                'created_at' => '2023-01-31',
                'ingest_date' => '2024-01-09 08:33:28',
                'origin' => 'csv',
                'disbursed' => false,
            ]),
        ]);
    }
}
