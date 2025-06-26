<?php

namespace App\Services\Chunking;

use App\Services\Chunking\Contracts\ChunkerInterface;
use Illuminate\Support\Facades\Storage;

class CsvChunker implements ChunkerInterface
{
    public const DEFAULT_CHUNK_SIZE = 1000;

    public function __construct(private int $chunkSize = self::DEFAULT_CHUNK_SIZE)
    {
    }

    public function createChunks(string $filePath): array
    {
        $chunks = [];
        // Note: For very large files (tens of GB), a more memory-efficient line-counting
        // method might be needed, but this is generally fine for files up to a few GB.
        $totalLines = $this->countLines($filePath);

        // We assume the first line is a header and skip it in our chunking ranges.
        $startLine = 2;

        while ($startLine <= $totalLines) {
            $endLine = min($startLine + $this->chunkSize - 1, $totalLines);
            $chunks[] = [
                'start' => $startLine,
                'end' => $endLine
            ];
            $startLine = $endLine + 1;
        }

        return $chunks;
    }

    private function countLines(string $filePath): int
    {
        $linecount = 0;
        $handle = fopen(Storage::disk('local_feeds')->path($filePath), "r");
        while(!feof($handle)){
          fgets($handle);
          $linecount++;
        }
        fclose($handle);
        return $linecount;
    }
}