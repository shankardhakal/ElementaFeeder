<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class FeedCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Feed::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/feed');
        CRUD::setEntityNameStrings('feed', 'feeds');
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column([
            'label'     => 'Network',
            'type'      => 'select',
            'name'      => 'network_id',
            'entity'    => 'network',
            'attribute' => 'name',
            'model'     => "App\Models\Network",
        ]);
        CRUD::column([
            'label'     => 'Product Type',
            'type'      => 'select',
            'name'      => 'product_type_id',
            'entity'    => 'productType',
            'attribute' => 'name',
            'model'     => "App\Models\ProductType",
        ]);
        CRUD::column('is_active')->type('check');
        CRUD::column('last_import_status')->wrapper([
            'element' => 'span',
            'class' => function ($crud, $column, $entry, $related_key) {
                if ($entry->last_import_status == 'completed') {
                    return 'badge badge-success';
                }
                if ($entry->last_import_status == 'failed') {
                    return 'badge badge-danger';
                }
                if ($entry->last_import_status == 'processing') {
                    return 'badge badge-info';
                }
                return 'badge badge-secondary';
            },
        ]);
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();
        CRUD::column('feed_url')->type('url');
        CRUD::column('unique_identifier_field');
        CRUD::column('schedule_cron');
        CRUD::column('parser_options')->type('textarea');
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:3|max:255',
            'feed_url' => 'required|url',
            'network_id' => 'required|exists:networks,id',
            'product_type_id' => 'required|exists:product_types,id',
            'parser_type' => 'required|in:csv,xml,json',
            'unique_identifier_field' => 'required|string|max:255',
            'schedule_cron' => 'required|string|max:255',
        ]);

        CRUD::field('name')->tab('Primary Configuration');
        CRUD::field('feed_url')->type('url')->tab('Primary Configuration');

        if (request()->has('network_id')) {
            CRUD::field([
                'name'  => 'network_id',
                'type'  => 'hidden',
                'value' => request('network_id'),
            ]);
        } else {
            CRUD::field([
                'label'     => 'Network',
                'type'      => 'select',
                'name'      => 'network_id',
                'entity'    => 'network',
                'attribute' => 'name',
                'model'     => "App\Models\Network",
            ])->tab('Primary Configuration');
        } // <--- THIS WAS THE MISSING BRACE

        CRUD::field([
            'label'     => 'Product Type',
            'type'      => 'select',
            'name'      => 'product_type_id',
            'entity'    => 'productType',
            'attribute' => 'name',
            'model'     => "App\Models\ProductType",
        ])->tab('Primary Configuration');
        CRUD::field('unique_identifier_field')
            ->hint('The exact name of the field/column in the source feed that contains the unique SKU or EAN.')
            ->tab('Primary Configuration');
        CRUD::field('is_active')->type('switch')->tab('Primary Configuration');

        CRUD::field('parser_type')->type('select_from_array')->options(['csv' => 'CSV', 'xml' => 'XML', 'json' => 'JSON'])->tab('Parser Settings');
        CRUD::field('parser_options')->type('textarea')->hint('e.g., {"delimiter": ";", "has_header": true} for CSV or {"iterator_path": "products.product"} for XML/JSON. Must be valid JSON.')->tab('Parser Settings');

        CRUD::field('schedule_cron')->default('0 */6 * * *')->hint('The schedule for running the import automatically. Default is every 6 hours.')->tab('Scheduling');

        CRUD::field([
            'label'     => "Websites to Syndicate To",
            'type'      => 'checklist',
            'name'      => 'websites',
            'entity'    => 'websites',
            'attribute' => 'name',
            'model'     => "App\Models\Website",
            'pivot'     => true,
        ])->tab('Destinations');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
        
        CRUD::modifyField('network_id', [
             'label'     => 'Network',
             'type'      => 'select',
             'name'      => 'network_id',
             'entity'    => 'network',
             'attribute' => 'name',
             'model'     => "App\Models\Network",
             'attributes' => [
                'readonly' => 'readonly',
                'disabled' => 'disabled',
             ]
        ]);
    }
}