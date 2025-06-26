<?php

namespace App\Services\ApiClients\Contracts;

use App\DataTransferObjects\ProductDto;

interface ApiClientAdapterInterface
{
    /**
     * Creates a new product on the destination platform.
     * Returns the ID of the newly created product.
     *
     * @param ProductDto $product
     * @return string|int
     */
    public function createProduct(ProductDto $product): string|int;

    /**
     * Updates an existing product on the destination platform.
     *
     * @param string|int $destinationId
     * @param ProductDto $product
     * @return void
     */
    public function updateProduct(string|int $destinationId, ProductDto $product): void;
}