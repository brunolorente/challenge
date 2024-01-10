<?php

namespace App\Services;

use App\Contracts\DisbursementRepositoryInterface;
use App\Contracts\MerchantRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\DTOs\DisbursementData;
use App\Models\Merchant;
use App\Models\Order;
use Carbon\Carbon;
use DateTimeInterface;
use Ramsey\Uuid\Nonstandard\Uuid;
use Ramsey\Uuid\UuidInterface;

class DisbursementService
{
    private DateTimeInterface $start;
    private DateTimeInterface $end;

    public function __construct(
        private MerchantRepositoryInterface $merchantRepository,
        private OrderRepositoryInterface $orderRepository,
        private DisbursementRepositoryInterface $disbursementRepository,
    ){
    }

    public function calculateDisbursements(Carbon $date): void
    {
        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {
            if ($this->isEligibleForDisbursement($merchant, $date)) {
                $orders = $this->getEligibleOrders($merchant, $merchant->disbursement_frequency, $date);
                if(count($orders) > 0) {
                    $this->processDisbursement($merchant, $orders);
                }
            }
        }
    }

    private function processDisbursement(Merchant $merchant, array $orders): void
    {
        $disbursementAmount = 0;
        $commissionAmount = 0;

        foreach ($orders as $order) {
            $commission = $this->calculateCommission($order);
            $disbursementAmount += ($order->amount - $commission);
            $commissionAmount += $commission;

            $this->orderRepository->updateOrderAsDisbursed(Uuid::fromString($order->id));
        }
        $this->saveAdditionalFeeForMonthlyMin($merchant);
        $this->saveDisbursement($disbursementAmount, Uuid::fromString($merchant->id), $commissionAmount, count($orders));
    }

    private function saveDisbursement(float $disbursementAmount, UuidInterface $uuid, float $commissionAmount, int $nbOfOrders): void
    {
        $this->disbursementRepository->insertDisbursement(
            DisbursementData::build(
                $disbursementAmount,
                $uuid,
                round($commissionAmount, 2),
                $this->start,
                $this->end,
                Uuid::fromString($this->generateUniqueReference()),
                $nbOfOrders,
            )
        );
    }

    private function saveAdditionalFeeForMonthlyMin(Merchant $merchant): void
    {
        if (Carbon::now()->startOfMonth()->isToday()) {
            $additionalFeeNeeded = $this->calculateAdditionalFee($merchant);

            if ($additionalFeeNeeded > 0) {
                // TODO: Verificar y calcular la cuota mínima mensual si es el primer desembolso del mes
                $additionalFeeRecord = [
                    'merchant_id' => $merchant->id,
                    'month' => Carbon::now()->subMonthNoOverflow()->format('Y-m'),
                    'fee' => $additionalFeeNeeded,
                ];
            }
        }
    }

    /**
     * @todo Calcula la cuota adicional necesaria para alcanzar la cuota mínima mensual.
     */
    private function calculateAdditionalFee(Merchant $merchant): float|int
    {
        $startOfLastMonth = Carbon::now()->subMonthNoOverflow()->startOfMonth();
        $endOfLastMonth = $startOfLastMonth->copy()->endOfMonth();
        /*
        $totalCommissionsLastMonth = $merchant->orders()
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->where('disbursed', false)->get();
            //->sum('commission');
        $totalComissionReal = 0;

        foreach ($totalCommissionsLastMonth as $order) {
            $totalComissionReal = $totalComissionReal + $this->calculateCommission($order);
        }
        */

        //if ($totalCommissionsLastMonth < $merchant->minimum_monthly_fee) {
        //    return $merchant->minimum_monthly_fee - $totalCommissionsLastMonth;
        //}

        return 0;
    }

    private function isEligibleForDisbursement(Merchant $merchant, Carbon $date): bool
    {
        if ($merchant->disbursement_frequency == 'DAILY') {
            return true;
        }

        if ($merchant->disbursement_frequency == 'WEEKLY') {
            return Carbon::createFromFormat('Y-m-d', $merchant->live_on)->dayOfWeek == $date->dayOfWeek;
        }

        return false;
    }

    private function getEligibleOrders(Merchant $merchant, string $frequency, Carbon $date): array
    {
        if ($frequency == 'DAILY') {
            $this->start = $date->copy()->startOfDay();
            $this->end = $date->copy()->endOfDay();
        } else if ($frequency == 'WEEKLY') {
            $this->start = $date->copy()->startOfWeek();
            $this->end = $date->copy()->endOfDay();
        }

        return $this->merchantRepository->findMerchantNotDisbursedOrdersByMerchantIdBetweenTwoDates(
            Uuid::fromString($merchant->id),
            $this->start,
            $this->end
        );
    }

    private function calculateCommission(Order $order): float
    {
        if ($order->amount < 50) {
            $commission = $order->amount * 0.01;
        } elseif ($order->amount >= 50 && $order->amount < 300) {
            $commission = $order->amount * 0.0095;
        } else {
            $commission = $order->amount * 0.0085;
        }

        return round($commission, 2);
    }

    private function generateUniqueReference(): string
    {
        return Uuid::uuid4()->toString();
    }
}
