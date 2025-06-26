<?php

namespace App\Services\Chunking\Contracts;

interface ChunkerInterface
{
    /**
     * Scans a file and returns an array of chunk ranges.
     *
     * @param string $filePath The path to the file on storage.
     * @return array An array of arrays, e.g., [['start' => 1, 'end' => 1000], ['start' => 1001, 'end' => 2000]]
     */
    public function createChunks(string $filePath): array;
}