<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class MerchantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'email' => $this->faker->unique()->safeEmail,
        ];
    }

    public function dailyMerchant() {
        return $this->state(function (array $attributes) {
           return [
               "id" => "f6a4bb66-e564-4b41-a4eb-9883ec602229",
               "external_id" => "86312006-4d7e-45c4-9c28-788f4aa68a62",
               "reference" => "padberg_group",
               "live_on" => "2023-01-26",
               "disbursement_frequency" => "DAILY",
               "minimum_monthly_fee" => "0",
               "ingest_date" => "2024-01-09 09:15:14",
               "origin" => "csv",
           ];
        });
    }

    public function weeklyMerchant() {
        return $this->state(function (array $attributes) {
            return [
                "id" => "a1a767a0-b71d-4443-ac88-26c7f980741b",
                "external_id" => "bea7bc65-8b6a-4486-b6bd-34209794817f",
                "reference" => "cormier_weissnat_and_hauck",
                "live_on" => "2023-01-26",
                "disbursement_frequency" => "WEEKLY",
                "minimum_monthly_fee" => "30",
                "ingest_date" => "2024-01-09 09:15:14",
                "origin" => "csv",
            ];
        });
    }
}
