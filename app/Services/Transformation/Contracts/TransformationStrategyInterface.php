<?php

namespace App\Services\Transformation\Contracts;

use App\DataTransferObjects\ProductDto;

interface TransformationStrategyInterface
{
    /**
     * Applies domain-specific transformation logic to a ProductDto.
     *
     * @param ProductDto $dto
     * @return ProductDto
     */
    public function execute(ProductDto $dto): ProductDto;
}