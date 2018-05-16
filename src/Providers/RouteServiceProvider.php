<?php

namespace jhoopes\LaravelVueForms\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'jhoopes\LaravelVueForms\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        //
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {

        if(config('laravel-vue-forms.api_middleware')) {
            \Route::prefix('api/forms')
                ->namespace($this->namespace . '\Api')
                ->middleware(config('laravel-vue-forms.api_middleware'))
                ->group(base_path('/vendor/jhoopes/laravel-vue-forms') . '/routes/api.php');
        }else {
            \Route::prefix('api/forms')
                ->namespace($this->namespace . '\Api')
                ->group(base_path('/vendor/jhoopes/laravel-vue-forms') . '/routes/api.php');
        }
    }
}
