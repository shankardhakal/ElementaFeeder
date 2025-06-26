<?php

namespace App\Services\Transformation;

use App\DataTransferObjects\ProductDto;
use App\Models\Feed;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class DataTransformerService
{
    // We will inject the RuleEngineService here later
    public function __construct()
    {
    }

    public function transform(array $rawRow, Feed $feed): ?ProductDto
    {
        // For now, we only use the feed's direct mapping rules.
        // Later, we will merge this with website-specific rules.
        $mappingRules = $feed->mappingRules;

        $mappedData = $this->applySimpleMappings($rawRow, $mappingRules);

        // Add the required fields for the DTO
        $mappedData['uniqueIdentifier'] = Arr::get($rawRow, $feed->unique_identifier_field);
        $mappedData['rawSourceData'] = $rawRow;

        // Basic validation: ensure we have a unique identifier.
        if (empty($mappedData['uniqueIdentifier'])) {
            // Log this error, as it's a critical data issue.
            return null;
        }
        
        // Fill in missing DTO fields with defaults to prevent errors.
        $defaults = [
            'name' => null,
            'description' => null,
            'price' => null,
            'salePrice' => null,
            'productUrl' => null,
            'brand' => null,
            'imageUrls' => [],
            'categories' => [],
            'attributes' => [],
        ];
        
        $dtoData = array_merge($defaults, $mappedData);

        return ProductDto::from($dtoData);
    }

    private function applySimpleMappings(array $rawRow, Collection $rules): array
    {
        $mapped = [];
        foreach ($rules as $rule) {
            // Arr::get can use dot notation for nested data if needed later
            if (Arr::has($rawRow, $rule->source_field)) {
                Arr::set($mapped, $rule->destination_field, Arr::get($rawRow, $rule->source_field));
            }
        }
        return $mapped;
    }
}