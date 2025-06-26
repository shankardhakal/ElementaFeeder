<?php

namespace App\Services\ApiClients;

use App\Models\Website;
use App\Services\ApiClients\Contracts\ApiClientAdapterInterface;

class ApiClientFactory
{
    public function make(Website $website): ApiClientAdapterInterface
    {
        $adapterClass = $website->destinationPlatform->api_client_adapter;

        if (!class_exists($adapterClass)) {
            throw new \Exception("API Client Adapter class not found: {$adapterClass}");
        }

        // We can pass the website object to the adapter's constructor
        // so it has access to credentials, URL, etc.
        return app($adapterClass, ['website' => $website]);
    }
}