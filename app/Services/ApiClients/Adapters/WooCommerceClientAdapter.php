<?php

namespace App\Services\ApiClients\Adapters;

use App\DataTransferObjects\ProductDto;
use App\Models\Website;
use App\Services\ApiClients\Contracts\ApiClientAdapterInterface;
use Automattic\WooCommerce\Client;
use Illuminate\Support\Arr;

class WooCommerceClientAdapter implements ApiClientAdapterInterface
{
    private Client $wooClient;

    public function __construct(Website $website)
    {
        $credentials = $website->credentials;

        $this->wooClient = new Client(
            $website->url,
            Arr::get($credentials, 'consumer_key'),
            Arr::get($credentials, 'consumer_secret'),
            [
                'version' => 'wc/v3',
                'timeout' => 45, // Increased timeout for API calls
            ]
        );
    }

    public function createProduct(ProductDto $product): string|int
    {
        $data = $this->mapDtoToWooCommerce($product);
        $response = $this->wooClient->post('products', $data);

        if (empty($response['id'])) {
            throw new \Exception('WooCommerce API did not return an ID when creating a product.');
        }

        return $response['id'];
    }

    public function updateProduct(string|int $destinationId, ProductDto $product): void
    {
        $data = $this->mapDtoToWooCommerce($product);
        $this->wooClient->put('products/' . $destinationId, $data);
    }

    /**
     * Maps our internal ProductDto to the array structure required by the WooCommerce API.
     */
    private function mapDtoToWooCommerce(ProductDto $dto): array
    {
        $categories = !empty($dto->categories) ? array_map(fn($cat) => ['name' => $cat], $dto->categories) : [];
        $images = !empty($dto->imageUrls) ? array_map(fn($url) => ['src' => $url], $dto->imageUrls) : [];

        // Map our attributes to WooCommerce's attribute structure
        $attributes = [];
        if (!empty($dto->attributes)) {
            foreach ($dto->attributes as $name => $value) {
                if (empty($value)) continue; // Skip empty attributes

                $attributes[] = [
                    'name' => $name,
                    'position' => 0,
                    'visible' => true,
                    'variation' => false,
                    'options' => is_array($value) ? $value : [$value],
                ];
            }
        }
        
        return [
            'name' => $dto->name,
            'type' => 'simple', // For standard products. Could be 'variable' if DTO supports it.
            'regular_price' => (string)$dto->price,
            'sale_price' => $dto->salePrice ? (string)$dto->salePrice : '',
            'description' => $dto->description,
            'sku' => $dto->uniqueIdentifier,
            'categories' => $categories,
            'images' => $images,
            'attributes' => $attributes,
            'manage_stock' => false,
        ];
    }
}