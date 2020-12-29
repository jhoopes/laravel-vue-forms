<?php

Route::group(['namespace' => 'Forms'], function() {

    Route::get('/configuration', 'FormConfiguration@index');
    Route::get('/configuration/{formConfigId}', 'FormConfiguration@show');

    Route::post('/submit', 'FormSubmit@store');
    Route::patch('/submit', 'FormSubmit@update');

});

