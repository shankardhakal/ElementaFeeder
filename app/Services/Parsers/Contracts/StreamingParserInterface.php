<?php

namespace App\Services\Parsers\Contracts;

interface StreamingParserInterface
{
    /**
     * Opens the file for reading.
     *
     * @param string $filePath The path to the file on storage.
     * @param array $options Parser-specific options.
     */
    public function open(string $filePath, array $options = []): void;

    /**
     * Reads the file from a specific start point to an end point, yielding one row at a time.
     *
     * @param int $start The starting line/byte.
     * @param int $end The ending line/byte.
     * @return \Generator
     */
    public function getRows(int $start, int $end): \Generator;

    /**
     * Closes any open file handles.
     */
    public function close(): void;
}