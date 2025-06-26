<?php

namespace App\Services\Parsers;

use App\Services\Parsers\Contracts\StreamingParserInterface;
use Illuminate\Support\Facades\Storage;
use League\Csv\Reader;
use League\Csv\Statement;

class StreamingCsvParser implements StreamingParserInterface
{
    private ?Reader $csvReader = null;
    private array $options = [];

    public function open(string $filePath, array $options = []): void
    {
        $this->options = $options;
        $path = Storage::disk('local_feeds')->path($filePath);

        $this->csvReader = Reader::createFromPath($path, 'r');

        if (isset($this->options['delimiter'])) {
            $this->csvReader->setDelimiter($this->options['delimiter']);
        }

        // We assume the first row is always the header.
        $this->csvReader->setHeaderOffset(0);
    }

    public function getRows(int $start, int $end): \Generator
    {
        if (!$this->csvReader) {
            throw new \Exception("Parser has not been opened. Call open() first.");
        }

        // The offset for the statement needs to account for the header row.
        // If we want to start reading at line 2, the offset is 1.
        $offset = $start - 2;
        $limit = $end - $start + 1;

        $statement = Statement::create()
            ->offset($offset)
            ->limit($limit);

        $records = $statement->process($this->csvReader);

        foreach ($records as $record) {
            yield $record;
        }
    }

    public function close(): void
    {
        $this->csvReader = null;
    }
}