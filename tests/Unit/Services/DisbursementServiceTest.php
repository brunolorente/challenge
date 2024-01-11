<?php

namespace Unit\Services;

use App\Models\Merchant;
use App\Models\Order;
use App\Services\DisbursementService;
use App\Contracts\MerchantRepositoryInterface;
use App\Contracts\OrderRepositoryInterface;
use App\Contracts\DisbursementRepositoryInterface;
use App\Contracts\AdditionalFeeRepositoryInterface;
use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class DisbursementServiceTest extends TestCase
{
    private MerchantRepositoryInterface $merchantRepository;
    private OrderRepositoryInterface $orderRepository;
    private DisbursementRepositoryInterface $disbursementRepository;
    private AdditionalFeeRepositoryInterface $additionalFeeRepository;
    private DisbursementService $disbursementService;

    protected function setUp(): void
    {
        parent::setUp();

        // Crear mocks para los repositorios
        $this->merchantRepository = $this->createMock(MerchantRepositoryInterface::class);
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->disbursementRepository = $this->createMock(DisbursementRepositoryInterface::class);
        $this->additionalFeeRepository = $this->createMock(AdditionalFeeRepositoryInterface::class);

        // Crear instancia de DisbursementService con los mocks
        $this->disbursementService = new DisbursementService(
            $this->merchantRepository,
            $this->orderRepository,
            $this->disbursementRepository,
            $this->additionalFeeRepository
        );
    }

    public function testCalculateDisbursements(): void
    {
        // given
        $merchant = $this->getMerchant();
        $orders = $this->getOrders();

        // then
        $this->merchantRepository
            ->expects(self::exactly(1))
            ->method('findAll')
            ->willReturn([$merchant]);
        $this->merchantRepository
            ->expects(self::exactly(1))
            ->method('findMerchantNotDisbursedOrdersByMerchantIdBetweenTwoDates')
            ->willReturn($orders);
        $this->orderRepository
            ->expects(self::exactly(3))
            ->method('updateOrderAsDisbursed')
            ->willReturn(true);
        $this->disbursementRepository
            ->expects(self::exactly(1))
            ->method('insertDisbursement')
            ->willReturn(true);


        // when
        $this->disbursementService->calculateDisbursements(new Carbon('2023-02-02'));
    }

    private function getMerchant(): Merchant
    {
        $merchant = new Merchant(
            [
                'id' => 'a1a767a0-b71d-4443-ac88-26c7f980741b',
                'external_id' => 'bea7bc65-8b6a-4486-b6bd-34209794817f',
                'reference' => 'cormier_weissnat_and_hauck',
                'live_on' => '2023-01-26',
                'disbursement_frequency' => 'WEEKLY',
                'minimum_monthly_fee' => '30',
                'ingest_date' => '2024-01-09 09:15:14',
                'origin' => 'csv',
            ]
        );
        $merchant->setAttribute('id', 'a1a767a0-b71d-4443-ac88-26c7f980741b');// id of laravel models is not create if the model is not already persisted in db

        return $merchant;
    }

    private function getOrders(): array
    {
        $orderData = [
            [
                'external_id' => '0b73fb1d3332',
                'merchant_reference' => 'padberg_group',
                'amount' => '194.37',
                'created_at' => '2023-02-02',
                'ingest_date' => '2024-01-09 08:05:23',
                'origin' => 'csv',
                'disbursed' => false,
            ],
            [
                'external_id' => '9164f0688190',
                'merchant_reference' => 'padberg_group',
                'amount' => '371.33',
                'created_at' => '2023-02-02',
                'ingest_date' => '2024-01-09 08:05:23',
                'origin' => 'csv',
                'disbursed' => false,
            ],
            [
                'external_id' => '04e9b88fe8c7',
                'merchant_reference' => 'padberg_group',
                'amount' => '280.21',
                'created_at' => '2023-02-02',
                'ingest_date' => '2024-01-09 08:05:23',
                'origin' => 'csv',
                'disbursed' => false,
            ],
        ];

        $orders = [];
        foreach ($orderData as $data) {
            $order = new Order($data);
            $order->setAttribute('id', Uuid::uuid4()->toString());
            $orders[] = $order;
        }

        return $orders;
    }
}

