<?php declare(strict_types=1);

use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

Route::group([
    'prefix' => LaravelVueForms::apiPrefix()
], function() {

    Route::get('/configuration', [
        \jhoopes\LaravelVueForms\App\Controllers\Api\FormConfiguration::class,
        'index'
    ]);
    Route::get('/configuration/{formConfigId}', [
        \jhoopes\LaravelVueForms\App\Controllers\Api\FormConfiguration::class,
        'show'
    ]);

    Route::post('/', [
        \jhoopes\LaravelVueForms\App\Controllers\Api\FormSubmit::class,
        'updateOrCreate'
    ]);
    Route::patch('/', [
        \jhoopes\LaravelVueForms\App\Controllers\Api\FormSubmit::class,
        'updateOrCreate'
    ]);

    Route::post('/files', [
        \jhoopes\LaravelVueForms\App\Controllers\Api\FilesController::class,
        'store'
    ]);

});

Route::get('/entities/{entityType}', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\EntitiesController::class,
    'index'
]);


Route::post('/entities/{entityType}', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\EntitiesController::class,
    'create'
]);

Route::get('/entities/{entityType}/{entityId}', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\EntitiesController::class,
    'show'
]);

Route::patch('/entities/{entityType}/{entityId}', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\EntitiesController::class,
    'update'
]);

Route::delete('/entities/{entityType}/{entityId}', [
    \jhoopes\LaravelVueForms\App\Controllers\Api\EntitiesController::class,
    'delete'
]);
