<?php


Route::get('/{sub_route?}', [\jhoopes\LaravelVueForms\Http\Controllers\Admin\DashboardController::class, 'index'])
    ->where(['sub_route'=> '.*'])
    ->name('formAdmin.home');
