<?php

namespace App\Console\Commands;

use App\Jobs\StartFeedProcessingJob;
use App\Models\Feed;
use Cron\CronExpression;
use Illuminate\Console\Command;

class ProcessScheduledFeedsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-scheduled-feeds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks for feeds that are due to run based on their cron schedule and dispatches processing jobs.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled feeds to process...');

        $feeds = Feed::where('is_active', true)->get();

        $dueFeeds = $feeds->filter(function ($feed) {
            // Using a library to check if the cron expression is due now.
            $cron = new CronExpression($feed->schedule_cron);
            return $cron->isDue();
        });

        if ($dueFeeds->isEmpty()) {
            $this->info('No feeds are due to run at this time.');
            return self::SUCCESS;
        }

        $this->info("Found {$dueFeeds->count()} feeds due for processing.");

        foreach ($dueFeeds as $feed) {
            $this->line("Dispatching processing job for: {$feed->name}");
            StartFeedProcessingJob::dispatch($feed);
        }

        $this->info('All due feeds have been dispatched.');
        return self::SUCCESS;
    }
}