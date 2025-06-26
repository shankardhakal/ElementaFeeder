<?php

namespace Database\Seeders;

use App\Models\DestinationPlatform;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

class InitialSetupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create our first Destination Platform
        DestinationPlatform::updateOrCreate(
            ['name' => 'WooCommerce'],
            ['api_client_adapter' => \App\Services\ApiClients\Adapters\WooCommerceClientAdapter::class]
        );

        // Create our first Product Type
        ProductType::updateOrCreate(
            ['name' => 'WooCommerce Standard Product'],
            ['transformation_strategy' => \App\Services\Transformation\Strategies\StandardProductStrategy::class]
        );
    }
}