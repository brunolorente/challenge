<?php

namespace App\Contracts;

interface FileDownloaderInterface
{
    public function download(string $url, int $start, int $end): bool|string;
}
