<?php

\Route::get('form_configurations', [\jhoopes\LaravelVueForms\Http\Controllers\Api\Admin\FormConfigurationsController::class, 'index'])
    ->name('admin.form_configurations');

\Route::get('entity_types', 'GetEntityTypesController')
    ->name('admin.entity_types');

\Route::get('widget_types' , 'GetWidgetTypesController')
    ->name('admin.widget_types');

\Route::get('cast_types', 'GetCastTypesController')
    ->name('admin.cast_types');

\Route::get('validation_rules', 'GetStandardValidationRulesController')
    ->name('admin.validation_rules');

Route::post('form_configurations/{formConfigId}/form_fields', [
    \jhoopes\LaravelVueForms\Http\Controllers\Api\Admin\FormFieldController::class,
    'create'
])->name('admin.form_configurations.form_fields.create');
