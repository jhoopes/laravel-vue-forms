<?php declare(strict_types=1);

\Route::get('form_configurations', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration\FormConfigurationsController::class,
    'index'
])->name('admin.form_configurations');

\Route::get('entity_types', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\Entities\GetEntityTypesController::class,
    'index'
])->name('admin.entity_types');

\Route::get('entity_types/{entityTypeId}', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\Entities\EntityTypesController::class,
    'show'
])->name('admin.entity_types.show');

\Route::get('entity_type_options', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\Entities\GetEntityTypeOptionsController::class,
    'index'
]);

\Route::get('built_in_entity_types', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\Entities\GetBuiltInEntityTypesController::class,
    'index'
]);

\Route::get('widget_types' , [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration\GetWidgetTypesController::class,
    'index'
])
    ->name('admin.widget_types');

\Route::get('cast_types', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration\GetCastTypesController::class,
    'index'
])->name('admin.cast_types');

\Route::get('validation_rules', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration\GetStandardValidationRulesController::class,
    'index'
])->name('admin.validation_rules');


Route::get('form_fields', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration\FormFieldController::class,
    'index'
]);

Route::delete('form_fields/{formFieldId}', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration\FormFieldController::class,
    'delete'
]);

Route::post('form_configurations/{formConfigId}/form_fields', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration\FormConfigurationFormFieldController::class,
    'create'
])->name('admin.form_configurations.form_fields.create');

Route::patch('form_configurations/{formConfigId}/form_fields', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration\FormConfigurationFormFieldController::class,
    'update'
])->name('admin.form_configurations.form_fields.update');

Route::delete('form_configurations/{formConfigId}/form_fields/{formFieldId}', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration\FormConfigurationFormFieldController::class,
    'delete'
]);

Route::patch('form_configurations/{formConfigId}/form_fields/order', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration\FormConfigurationFormFieldOrderController::class,
    'update'
]);
