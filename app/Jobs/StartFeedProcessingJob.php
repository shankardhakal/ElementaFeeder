<?php

namespace App\Jobs;

use App\Models\Feed;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class StartFeedProcessingJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public Feed $feed)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::withContext(['feed_id' => $this->feed->id]);
        Log::info('Starting new import process for feed.');

        // 1. Create the master ImportJob record to track this entire run.
        $importJob = $this->feed->importJobs()->create([
            'status' => 'pending',
        ]);

        // 2. Update the feed's last status
        $this->feed->update(['last_import_status' => 'processing']);

        // 3. Dispatch the next job in the chain to handle downloading and chunking.
        DownloadAndChunkFeedJob::dispatch($importJob);

        Log::info('ImportJob record created and DownloadAndChunkFeedJob dispatched.', ['import_job_id' => $importJob->id]);
    }
}