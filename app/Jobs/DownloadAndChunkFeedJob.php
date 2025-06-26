<?php

namespace App\Jobs;

use App\Models\ImportJob;
use App\Services\Chunking\CsvChunker; // Assuming CSV for now
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class DownloadAndChunkFeedJob implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public ImportJob $importJob)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::withContext(['import_job_id' => $this->importJob->id]);

        try {
            // 1. Download the file to our dedicated local disk
            $this->importJob->update(['status' => 'downloading']);
            $filePath = $this->downloadFile();

            // 2. Instantiate the correct chunker (hard-coded to CSV for now)
            $this->importJob->update(['status' => 'chunking']);
            $chunker = new CsvChunker(); // We will make this dynamic later
            $chunks = $chunker->createChunks($filePath);

            if (empty($chunks)) {
                Log::warning('No chunks were created from the feed file. Aborting.');
                $this->importJob->update(['status' => 'failed']);
                return;
            }

            // 3. Create all the chunk records in the database
            $chunkJobs = [];
            foreach ($chunks as $range) {
                $importJobChunk = $this->importJob->chunks()->create([
                    'status' => 'pending',
                    'range_start' => $range['start'],
                    'range_end' => $range['end'],
                ]);
                $chunkJobs[] = new ProcessFeedChunkJob($importJobChunk);
            }
            $this->importJob->update(['total_chunks' => count($chunkJobs)]);

            // 4. Dispatch all ProcessFeedChunkJob's in a batch for better monitoring
            $batch = Bus::batch($chunkJobs)
                ->then(function () {
                    $this->importJob->refresh();
                    $this->importJob->update(['status' => 'completed']);
                    $this->importJob->feed->update(['last_import_status' => 'completed']);
                    Log::info('Batch completed successfully.');
                })
                ->catch(function (Throwable $e) {
                    $this->importJob->update(['status' => 'failed']);
                    $this->importJob->feed->update(['last_import_status' => 'failed']);
                    Log::error('A job in the batch failed.', ['exception' => $e->getMessage()]);
                })
                ->finally(function () {
                    // Optional: Clean up the downloaded file after processing
                    // Storage::disk('local_feeds')->delete($this->importJob->file_path);
                })
                ->name("Import Job ID: {$this->importJob->id}")
                ->onQueue('processing') // Use a dedicated queue for heavy lifting
                ->dispatch();

            $this->importJob->update(['status' => 'processing', 'processing_batch_id' => $batch->id]);
            Log::info("Batch of {$this->importJob->total_chunks} chunks dispatched.", ['batch_id' => $batch->id]);

        } catch (Throwable $e) {
            $this->fail($e);
        }
    }

    /**
     * Downloads the feed file and returns the local storage path.
     */
    private function downloadFile(): string
    {
        $feedUrl = $this->importJob->feed->feed_url;
        $fileName = $this->importJob->id . '_' . basename($feedUrl);
        $filePath = 'imports/' . $fileName;

        $response = Http::timeout(300)->get($feedUrl);

        if (!$response->successful()) {
            throw new \Exception("Failed to download feed. Status: " . $response->status());
        }

        Storage::disk('local_feeds')->put($filePath, $response->body());
        $this->importJob->update(['file_path' => $filePath]);

        return $filePath;
    }
}