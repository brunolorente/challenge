<?php

namespace App\Services;

use App\Contracts\FileDownloaderInterface;
use App\Contracts\OrderImporterInterface;
use App\Contracts\OrderRepositoryInterface;
use Illuminate\Support\Facades\Log;

class OrderImporterFromCsv implements OrderImporterInterface
{
    private const CHUNK_SIZE = 1024 * 1024; // 1MB

    public function __construct(
        private OrderRepositoryInterface $orderRepository,
        private CsvReader $csvReader,
        private FileDownloaderInterface $fileDownloader,
        private OrderDataTransformer $dataTransformer,
    ) {
    }

    public function import(string $file): void
    {
        $this->handlePartialDataImport($file);
    }

    private function handlePartialDataImport(string $file): void
    {
        $start = 0;
        $previousExtraData = '';

        while (true) {
            $data = $this->downloadDataPartials($start, $file, $previousExtraData);
            $previousExtraData = '';
            list($data, $previousExtraData) = $this->findLastCompleteRowInDownloadedPartial($data, $previousExtraData);

            if (empty($data)) {
                break;
            }

            array_map(fn ($order) => $this->persistOrder($order), $this->csvReader->readCsvFromFileParts($data, $start === 0));
            $start += self::CHUNK_SIZE;
        }
    }

    private function downloadDataPartials(int $start, string $file, string $previousExtraData): string
    {
        $end = $start + self::CHUNK_SIZE;
        $data = $this->fileDownloader->download($file, $start, $end);

        return $previousExtraData.$data;
    }

    private function findLastCompleteRowInDownloadedPartial(string $dataUntilLastCompleteRow, string $incompleteDataForNextIteration): array
    {
        $lastNewLinePos = strrpos($dataUntilLastCompleteRow, "\n");
        if ($lastNewLinePos !== false) {
            $incompleteDataForNextIteration = substr($dataUntilLastCompleteRow, $lastNewLinePos + 1);
            $dataUntilLastCompleteRow = substr($dataUntilLastCompleteRow, 0, $lastNewLinePos);
        }

        return [
            $dataUntilLastCompleteRow,
            $incompleteDataForNextIteration,
        ];
    }

    private function persistOrder(array $order): void
    {
        try {
            $this->orderRepository->insert($this->dataTransformer->transform($order));
        } catch (\TypeError $e) {
            Log::error(sprintf('Error: %s inserting order %s', $e->getMessage(), json_encode($order)));
        } catch (\Exception $e) {
            Log::error(sprintf('Error: %s inserting order %s', $e->getMessage(), json_encode($order)));
        }
    }
}
