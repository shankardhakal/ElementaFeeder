<?php

namespace App\Http\Controllers\Admin;

use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class NetworkCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(\App\Models\Network::class);
        CRUD::setRoute(config('backpack.base.route_prefix') . '/network');
        CRUD::setEntityNameStrings('network', 'networks');
    }

    protected function setupListOperation()
    {
        CRUD::column('name');
        CRUD::column('created_at');
    }

    protected function setupShowOperation()
    {
        CRUD::set('show.setFromDb', false); // We are defining the show columns manually.

        CRUD::column('name');
        CRUD::column('created_at');
        CRUD::column('updated_at');

        // --- CORRECTED: Manually format the JSON for display ---
        CRUD::column('parser_options_template')
            ->label('Parser Options Template')
            ->type('custom_html')
            ->value(function($entry) {
                if (empty($entry->parser_options_template)) {
                    return '-';
                }
                // json_encode the array and wrap it in <pre> tags for nice formatting
                return '<pre>'.json_encode($entry->parser_options_template, JSON_PRETTY_PRINT).'</pre>';
            });

        CRUD::column('separator')->type('custom_html')->value('<hr>');

        CRUD::button('add_feed')->stack('line')->view('vendor.backpack.ui.buttons.add_feed_to_network');

        // --- CORRECTED: Explicitly define the relationship display ---
        CRUD::column([
            'label'     => 'Feeds in this Network',
            'type'      => 'relationship',
            'name'      => 'feeds',      // The relationship method in your model
            'attribute' => 'name',       // The attribute on the related model to display
            'model'     => "App\Models\Feed",
        ]);
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation([
            'name' => 'required|min:2|max:255|unique:networks,name',
            'parser_options_template' => 'nullable|json'
        ]);

        CRUD::field('name');
        CRUD::field('parser_options_template')->type('textarea')->hint('Optional: Add a JSON template for parser options that all feeds in this network can use. e.g., {"delimiter": ";"}');
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }
}