<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class TransformationRuleCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\TransformationRule::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/transformation-rule');
        CRUD::setEntityNameStrings('transformation rule', 'transformation rules');
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column([
            'label'     => 'Feed',
            'type'      => 'select',
            'name'      => 'feed_id',
            'entity'    => 'feed',
            'attribute' => 'name',
            'model'     => "App\Models\Feed",
        ]);
        CRUD::column('priority');
        CRUD::column('is_active')->type('check');
    }

    protected function setupShowOperation()
    {
        $this->setupListOperation();
        CRUD::column('conditions')->type('textarea');
        CRUD::column('actions')->type('textarea');
        CRUD::column('created_at');
        CRUD::column('updated_at');
    }


    protected function setupCreateOperation()
    {
        CRUD::setValidation([
             'name' => 'required|min:3|max:255',
             'feed_id' => 'required|exists:feeds,id',
             'priority' => 'required|integer|min:0',
             'conditions' => 'nullable|json',
             'actions' => 'nullable|json',
        ]);

        CRUD::field('name');
        CRUD::field([
            'label'     => 'Feed',
            'type'      => 'select',
            'name'      => 'feed_id',
            'entity'    => 'feed',
            'attribute' => 'name',
            'model'     => "App\Models\Feed",
        ]);
        CRUD::field('priority')->type('number')->default(0)->hint('Rules with a lower priority number will run first.');
        CRUD::field('is_active')->type('switch');
        
        CRUD::field('conditions')->type('textarea')->hint('<b>Example:</b><br><code>[{"field": "category", "operator": "contains", "value": "Shoes"}, {"field": "price", "operator": ">", "value": 100}]</code>');
        CRUD::field('actions')->type('textarea')->hint('<b>Example:</b><br><code>[{"type": "set_static_value", "field": "shipping_cost", "value": 0}]</code>');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}