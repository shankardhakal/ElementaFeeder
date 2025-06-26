<?php

use Illuminate\Support\Facades\Route;

// --------------------------
// Custom Backpack Routes
// --------------------------
Route::group([
    'prefix'     => config('backpack.base.route_prefix', 'admin'),
    'middleware' => array_merge(
        (array) config('backpack.base.web_middleware', 'web'),
        (array) config('backpack.base.middleware_key', 'admin')
    ),
    'namespace'  => 'App\Http\Controllers\Admin',
], function () { // custom admin routes
    // Core Configuration
    Route::crud('product-type', 'ProductTypeCrudController');
    Route::crud('destination-platform', 'DestinationPlatformCrudController');
    Route::crud('website', 'WebsiteCrudController');
    Route::crud('transformation-rule', 'TransformationRuleCrudController');
    
    // Primary Entry Point
    Route::crud('network', 'NetworkCrudController');
    
    // Feed CRUD for global list and create/edit forms
    Route::crud('feed', 'FeedCrudController');
    
    // Custom Feed Management Page Route
    Route::get('feed/{id}/manage', 'FeedManagerController@manage')->name('feed.manage');
});