<?php

namespace App\Services\Syndication;

use App\DataTransferObjects\ProductDto;
use App\Models\Website;
use Illuminate\Support\Facades\Redis;

class ProductSyndicator
{
    /**
     * Determines if a product should be created or updated.
     *
     * @param ProductDto $dto
     * @param Website $website
     * @return array An array containing the action and destination ID if available.
     */
    public function getSyndicationAction(ProductDto $dto, Website $website): array
    {
        $redisKey = $this->getRedisKey($website);
        $uniqueIdentifier = $dto->uniqueIdentifier;

        // Use HGET to check if the specific SKU/EAN exists in our hash for this website.
        $destinationId = Redis::hget($redisKey, $uniqueIdentifier);

        if ($destinationId) {
            return ['action' => 'update', 'destination_id' => $destinationId];
        }

        return ['action' => 'create', 'destination_id' => null];
    }

    /**
     * Caches the mapping of a newly created product's identifier to its destination ID.
     */
    public function cacheNewProduct(ProductDto $dto, Website $website, string|int $destinationId): void
    {
        $redisKey = $this->getRedisKey($website);
        $uniqueIdentifier = $dto->uniqueIdentifier;

        // Use HSET to add the new product's SKU->ID mapping to our hash.
        Redis::hset($redisKey, $uniqueIdentifier, $destinationId);
    }

    /**
     * Generates the Redis key for a website's product hash map.
     */
    private function getRedisKey(Website $website): string
    {
        return "website:{$website->id}:products";
    }
}