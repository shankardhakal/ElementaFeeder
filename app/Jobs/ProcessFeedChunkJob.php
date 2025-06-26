<?php

namespace App\Jobs;

use App\DataTransferObjects\ProductDto;
use App\Models\ImportJobChunk;
use App\Services\Parsers\StreamingCsvParser; // Assuming CSV for now
use App\Services\Transformation\DataTransformerService;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessFeedChunkJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     * @var int
     */
    public int $tries = 3;

    /**
     * Create a new job instance.
     */
    public function __construct(public ImportJobChunk $importJobChunk)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(DataTransformerService $transformer): void
    {
        // If this job is part of a batch that has been cancelled, stop execution.
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        Log::withContext(['import_job_chunk_id' => $this->importJobChunk->id]);
        $this->importJobChunk->update(['status' => 'processing']);
        
        $importJob = $this->importJobChunk->importJob;
        $feed = $importJob->feed;

        // Instantiate the correct parser
        $parser = new StreamingCsvParser(); // We will make this dynamic later
        $parser->open($importJob->file_path, $feed->parser_options ?? []);

        $productsProcessed = 0;

        foreach ($parser->getRows($this->importJobChunk->range_start, $this->importJobChunk->range_end) as $rawRow) {
            $productDto = $transformer->transform($rawRow, $feed);

            if ($productDto instanceof ProductDto) {
                // For each website this feed is linked to, dispatch a syndication job.
                foreach ($feed->websites as $website) {
                    SyndicateProductJob::dispatch($productDto, $website, $importJob)->onQueue('syndication');
                }
            } else {
                Log::warning('Skipping product due to transformation failure or missing unique ID.', ['row' => $rawRow]);
            }
            $productsProcessed++;
        }
        
        $parser->close();

        // Update counters on the main import job atomically.
        $importJob->increment('products_processed', $productsProcessed);
        $importJob->increment('processed_chunks');
        
        $this->importJobChunk->update(['status' => 'completed']);
        Log::info("Chunk processing completed. Processed {$productsProcessed} products.");
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        $this->importJobChunk->update(['status' => 'failed']);
        Log::error('Chunk processing failed.', ['exception' => $exception->getMessage()]);
    }
}