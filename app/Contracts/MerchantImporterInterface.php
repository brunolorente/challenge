<?php

namespace App\Contracts;

interface MerchantImporterInterface
{
    public function import(string $file): void;
}
