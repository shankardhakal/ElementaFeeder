<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class ProductTypeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\ProductType::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/product-type');
        CRUD::setEntityNameStrings('product type', 'product types');
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('transformation_strategy');
    }

    protected function setupCreateOperation()
    {
        CRUD::field('name')->validationRules('required|min:3');
        CRUD::field('transformation_strategy')->validationRules('required|min:3')->hint('The fully-qualified class name of the strategy, e.g., App\Services\Transformation\Strategies\FashionProductStrategy');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}