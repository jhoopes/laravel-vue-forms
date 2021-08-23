<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Support\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelVueForms extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'laravel_vue_forms';
    }
}
