<?php

namespace Feature\Services;

use App\Models\Disbursement;
use App\Services\DisbursementService;
use App\Models\Merchant;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DisbursementServiceTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->generateMerchants();
        $this->generateOrders();
    }


    public function testCalculateDisbursementsForDailyMerchant()
    {
        // given
        $disbursementService = $this->app->make(DisbursementService::class);

        // when
        $disbursementService->calculateDisbursements(Carbon::createFromFormat('Y-m-d', '2023-02-02')->subDay());

        // then
        $generatedDisbursement = Disbursement::where('merchant_id', 'f6a4bb66-e564-4b41-a4eb-9883ec602229')->first();
        $this->assertEquals(7.67, $generatedDisbursement->commission);
        $this->assertEquals(838.24, $generatedDisbursement->amount);
        $this->assertEquals(3, $generatedDisbursement->nb_of_orders);
    }

    public function testCalculateDisbursementsForWeeklyMerchant()
    {
        // given
        $disbursementService = $this->app->make(DisbursementService::class);

        // when
        $disbursementService->calculateDisbursements(Carbon::createFromFormat('Y-m-d', '2023-02-02'));

        // then
        $generatedDisbursement = Disbursement::where('merchant_id', 'a1a767a0-b71d-4443-ac88-26c7f980741b')->first();
        // aserts just for the weekly merchant
        $this->assertEquals(0.4, $generatedDisbursement->commission);
        $this->assertEquals(39.23, $generatedDisbursement->amount);
        $this->assertEquals(3, $generatedDisbursement->nb_of_orders);

        // asserts for both merchants
        $allDisbursements = Disbursement::all();
        $total = 0;
        foreach ($allDisbursements as $disbursement) {
            $total += $disbursement->nb_of_orders;
        }
        $this->assertEquals(3, $total); // this is just 3 because the orders for the daily merchant are for other date

    }


    private function generateMerchants()
    {
        Merchant::factory(1)->dailyMerchant()->create();
        Merchant::factory(1)->weeklyMerchant()->create();
    }

    private function generateOrders()
    {
        foreach (Order::factory(1)->dailyMerchantOrders() as $order)
        {
            $order->save();
        }
        foreach (Order::factory(1)->weeklyMerchantOrders() as $order)
        {
            $order->save();
        }
    }
}
