<?php

namespace App\Jobs;

use App\DataTransferObjects\ProductDto;
use App\Models\ImportJob;
use App\Models\Website;
use App\Services\ApiClients\ApiClientFactory;
use App\Services\Syndication\ProductSyndicator;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Throwable;

class SyndicateProductJob implements ShouldQueue, ShouldBeUnique
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     * @var int
     */
    public int $tries = 5;

    /**
     * The maximum number of unhandled exceptions to allow before failing.
     * @var int
     */
    public int $maxExceptions = 3;

    /**
     * Calculate the number of seconds to wait before retrying the job.
     */
    public function backoff(): array
    {
        return [60, 120, 300, 600, 1800]; // 1m, 2m, 5m, 10m, 30m
    }

    /**
     * The unique ID for the job.
     * This prevents duplicate syndication jobs for the same product to the same website in a short time.
     */
    public function uniqueId(): string
    {
        return $this->website->id . ':' . $this->productDto->uniqueIdentifier;
    }
    
    /**
     * Create a new job instance.
     */
    public function __construct(
        public ProductDto $productDto,
        public Website $website,
        public ImportJob $importJob
    ) {
    }

    /**
     * Execute the job.
     */
    public function handle(ProductSyndicator $syndicator, ApiClientFactory $apiClientFactory): void
    {
        if ($this->batch() && $this->batch()->cancelled()) {
            return;
        }

        Log::withContext([
            'import_job_id' => $this->importJob->id,
            'website_id' => $this->website->id,
            'product_sku' => $this->productDto->uniqueIdentifier,
        ]);

        // Apply rate limiting based on the website's configuration.
        $limiter = RateLimiter::attempt(
            'syndicate-to-'.$this->website->id,
            $this->website->rate_limit_per_minute ?: 100,
            function () use ($syndicator, $apiClientFactory) {
                // 1. Get the syndication action (create or update) from Redis via the syndicator service.
                $syndicationAction = $syndicator->getSyndicationAction($this->productDto, $this->website);
                $action = $syndicationAction['action'];
                $destinationId = $syndicationAction['destination_id'];

                // 2. Get the correct API client using the factory.
                $apiClient = $apiClientFactory->make($this->website);

                // 3. Perform the action.
                if ($action === 'create') {
                    $newDestinationId = $apiClient->createProduct($this->productDto);
                    $syndicator->cacheNewProduct($this->productDto, $this->website, $newDestinationId);
                    $this->importJob->increment('products_created');
                    Log::info('Product created successfully.', ['destination_id' => $newDestinationId]);
                } else { // 'update'
                    $apiClient->updateProduct($destinationId, $this->productDto);
                    $this->importJob->increment('products_updated');
                    Log::info('Product updated successfully.', ['destination_id' => $destinationId]);
                }
            }
        );

        if (! $limiter) {
             // Could not obtain lock...
            $this->release(10); // Release back to the queue to try again in 10 seconds.
        }
    }
}