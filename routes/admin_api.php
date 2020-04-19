<?php

\Route::get('form_configurations',
    [\jhoopes\LaravelVueForms\Http\Controllers\Api\Admin\FormConfigurationsController::class, 'index']
)->name('admin.form_configurations');
