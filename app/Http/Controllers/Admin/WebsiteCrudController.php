<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class WebsiteCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Website::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/website');
        CRUD::setEntityNameStrings('website', 'websites');
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('url')->type('url');
        CRUD::column([
            'label'     => 'Platform',
            'type'      => 'select',
            'name'      => 'destination_platform_id',
            'entity'    => 'destinationPlatform',
            'attribute' => 'name',
            'model'     => "App\Models\DestinationPlatform",
        ]);
        CRUD::column('is_active')->type('check');
    }
    
    protected function setupShowOperation(): void
    {
        $this->setupListOperation();
        CRUD::column('rate_limit_per_minute');
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:3|max:255',
            'url' => 'required|url',
            'destination_platform_id' => 'required|exists:destination_platforms,id',
            'rate_limit_per_minute' => 'required|integer|min:1',
        ]);

        CRUD::field('name');
        CRUD::field('url')->type('url');
        CRUD::field([
            'label'     => 'Destination Platform',
            'type'      => 'select',
            'name'      => 'destination_platform_id',
            'entity'    => 'destinationPlatform',
            'attribute' => 'name',
            'model'     => "App\Models\DestinationPlatform",
        ]);

        CRUD::field('credentials')->type('textarea')->hint('API keys, secrets, or other authentication tokens go here in valid JSON format.');
        CRUD::field('rate_limit_per_minute')->type('number')->default(100);
        CRUD::field('is_active')->type('switch');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}