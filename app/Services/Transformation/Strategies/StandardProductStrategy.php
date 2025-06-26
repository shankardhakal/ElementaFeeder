<?php

namespace App\Services\Transformation\Strategies;

use App\DataTransferObjects\ProductDto;
use App\Services\Transformation\Contracts\TransformationStrategyInterface;

class StandardProductStrategy implements TransformationStrategyInterface
{
    public function execute(ProductDto $dto): ProductDto
    {
        // For a standard product, we don't need much complex logic.
        // The main job is to ensure the data is clean and valid.

        // Example: Ensure the price is a valid float.
        $dto->price = (float) $dto->price;
        if ($dto->salePrice) {
            $dto->salePrice = (float) $dto->salePrice;
        }

        // Example: Ensure main image exists
        if (empty($dto->imageUrls)) {
            // You could set a default placeholder image here if desired.
        }

        return $dto;
    }
}