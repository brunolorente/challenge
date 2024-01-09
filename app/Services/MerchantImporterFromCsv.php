<?php
namespace App\Services;

use App\Contracts\MerchantImporterInterface;
use App\Contracts\MerchantRepositoryInterface;

class MerchantImporterFromCsv implements MerchantImporterInterface
{
    public function __construct(
        private CsvReader $csvReader,
        private MerchantRepositoryInterface $merchantRepository,
        private MerchantDataTransformer $merchantDataTransformer,
    ){
    }

    public function import(string $file): void
    {
        array_map(
            fn($merchant) => $this->merchantRepository->insert($merchant),
            array_map(
                fn($merchant) => $this->merchantDataTransformer->transform($merchant),
                $this->csvReader->readCsvFromFile($file)
            )
        );
    }
}

