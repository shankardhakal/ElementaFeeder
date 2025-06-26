<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class DestinationPlatformCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\DestinationPlatform::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/destination-platform');
        CRUD::setEntityNameStrings('destination platform', 'destination platforms');
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('api_client_adapter');
    }

    protected function setupCreateOperation()
    {
        CRUD::field('name')->validationRules('required|min:3');
        CRUD::field('api_client_adapter')->validationRules('required|min:3')->hint('The fully-qualified class name of the adapter, e.g., App\Services\ApiClients\Adapters\WooCommerceClientAdapter');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}