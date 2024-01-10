<?php

namespace App\Services;

use App\Contracts\AdditionalFeeInterface;
use App\Contracts\DisbursementRepositoryInterface;
use App\Contracts\MerchantRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\DTOs\AdditionalFeeData;
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
        private AdditionalFeeInterface $additionalFeeRepository,
    ){
    }

    public function calculateDisbursements(Carbon $date): void
    {
        $merchants = Merchant::all();

        foreach ($merchants as $merchant) {
            if ($this->isEligibleForDisbursement($merchant, $date)) {
                $orders = $this->getEligibleOrders($merchant, $merchant->disbursement_frequency, $date);
                if(count($orders) > 0) {
                    $this->processDisbursement($merchant, $orders, $date);
                }
            }
        }
    }

    private function processDisbursement(Merchant $merchant, array $orders, DateTimeInterface $date): void
    {
        $disbursementAmount = 0;
        $commissionAmount = 0;
        foreach ($orders as $order) {
            $commission = $this->calculateCommission($order);
            $disbursementAmount += ($order->amount - $commission);
            $commissionAmount += $commission;

            $this->orderRepository->updateOrderAsDisbursed(Uuid::fromString($order->id));
        }
        $this->saveAdditionalFeeForMonthlyMin($merchant, $date);
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

    private function saveAdditionalFeeForMonthlyMin(Merchant $merchant, DateTimeInterface $date): void
    {
        $carbonDate = Carbon::parse($date)->startOf('MONTH');
        if ($carbonDate->format('Y-m-d') === $date->format('Y-m-d')) {
            $additionalFeeNeeded = $this->calculateAdditionalFee($merchant, $date);
            if ($additionalFeeNeeded > 0) {
                $this->additionalFeeRepository->insert(AdditionalFeeData::fromArray([
                    'merchant_id' => Uuid::fromString($merchant->id),
                    'date' => $carbonDate,
                    'fee' => $additionalFeeNeeded,
                ]));
            }
        }
    }

    private function calculateAdditionalFee(Merchant $merchant, DateTimeInterface $date): float|int
    {
        $startOfLastMonth = Carbon::parse($date)->startOf('MONTH')->subMonthNoOverflow()->startOfMonth();
        $endOfLastMonth = $startOfLastMonth->copy()->endOfMonth();

        $totalOrdersLastMonth = $merchant->orders()
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->get();

        $totalCommissionsLastMonth = 0;
        foreach ($totalOrdersLastMonth as $order) {
            $totalCommissionsLastMonth += $this->calculateCommission($order);
        }

        if ($totalCommissionsLastMonth < $merchant->minimum_monthly_fee) {
            return $merchant->minimum_monthly_fee - $totalCommissionsLastMonth;
        }

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
