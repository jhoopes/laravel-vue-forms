<?php

\Route::get('form_configurations', [\jhoopes\LaravelVueForms\Http\Controllers\Api\Admin\FormConfigurationsController::class, 'index'])
    ->name('admin.form_configurations');

\Route::get('entity_types', 'GetEntityTypesController@index')
    ->name('admin.entity_types');

