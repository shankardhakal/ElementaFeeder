<?php

namespace App\DataTransferObjects;

use Spatie\LaravelData\Data;

class ProductDto extends Data
{
    public function __construct(
        // The guaranteed unique identifier (SKU/EAN)
        public string $uniqueIdentifier,
        public ?string $name,
        public ?string $description,
        public ?float $price,
        public ?float $salePrice,
        public ?string $productUrl,
        public ?string $brand,
        /** @var string[] */
        public array $imageUrls,
        /** @var string[] */
        public array $categories,
        /** @var array<string, mixed> */
        public array $attributes,
        // The original, unmodified row for reference in advanced rules
        public array $rawSourceData,
    ) {}
}