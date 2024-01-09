<?php

namespace App\Services;

use App\Contracts\FileDownloaderInterface;

class CurlFileDownloader implements FileDownloaderInterface {
    public function download(string $url, int $start, int $end): string {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RANGE, "$start-$end");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }
}

