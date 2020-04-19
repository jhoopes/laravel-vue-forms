<?php

namespace jhoopes\LaravelVueForms\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelVueForms extends Facade
{


    protected static function getFacadeAccessor()
    {
        return 'laravel_vue_forms';
    }
}
