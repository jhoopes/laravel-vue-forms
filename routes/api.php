<?php


Route::get('/configuration/{formConfigId}', 'FormConfiguration@show');

Route::post('/submit', 'FormSubmit@store');
Route::patch('/submit', 'FormSubmit@update');