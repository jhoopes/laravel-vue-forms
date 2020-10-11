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


Route::get('form_fields', [
    \jhoopes\LaravelVueForms\Http\Controllers\Api\Admin\FormFieldController::class,
    'index'
]);

Route::delete('form_fields/{formFieldId}', [
    \jhoopes\LaravelVueForms\Http\Controllers\Api\Admin\FormFieldController::class,
    'delete'
]);

Route::post('form_configurations/{formConfigId}/form_fields', [
    \jhoopes\LaravelVueForms\Http\Controllers\Api\Admin\FormConfigurationFormFieldController::class,
    'create'
])->name('admin.form_configurations.form_fields.create');

Route::patch('form_configurations/{formConfigId}/form_fields', [
    \jhoopes\LaravelVueForms\Http\Controllers\Api\Admin\FormConfigurationFormFieldController::class,
    'update'
])->name('admin.form_configurations.form_fields.update');

Route::delete('form_configurations/{formConfigId}/form_fields/{formFieldId}', [
    \jhoopes\LaravelVueForms\Http\Controllers\Api\Admin\FormConfigurationFormFieldController::class,
    'delete'
]);

Route::patch('form_configurations/{formConfigId}/form_fields/order', [
    \jhoopes\LaravelVueForms\Http\Controllers\Api\Admin\FormConfigurationFormFieldOrderController::class,
    'update'
]);
