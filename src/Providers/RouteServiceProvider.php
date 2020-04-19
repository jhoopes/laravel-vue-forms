<?php

namespace jhoopes\LaravelVueForms\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use jhoopes\LaravelVueForms\Facades\LaravelVueForms;

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
        $this->mapWebRoutes();
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
            \Route::prefix(LaravelVueForms::apiPrefix())
                ->namespace($this->namespace . '\Api')
                ->middleware(config('laravel-vue-forms.api_middleware'))
                ->group(base_path('/vendor/jhoopes/laravel-vue-forms') . '/routes/api.php');

            if(config('laravel-vue-forms.use_admin_api')) {
                \Route::prefix(LaravelVueForms::adminApiPrefix())
                    ->namespace($this->namespace . '\Api\Admin')
                    ->middleware(config('laravel-vue-forms.api_middleware'))
                    ->group(base_path('/vendor/jhoopes/laravel-vue-forms') . '/routes/admin_api.php');
            }

        }else {
            \Route::prefix(LaravelVueForms::apiPrefix())
                ->namespace($this->namespace . '\Api')
                ->group(base_path('/vendor/jhoopes/laravel-vue-forms') . '/routes/api.php');

            if(config('laravel-vue-forms.use_admin_api')) {
                \Route::prefix(LaravelVueForms::adminApiPrefix())
                    ->namespace($this->namespace . '\Api\Admin')
                    ->group(base_path('/vendor/jhoopes/laravel-vue-forms') . '/routes/admin_api.php');
            }
        }
    }

    protected function mapWebRoutes()
    {
        if(!config('laravel-vue-forms.use_web_routes')) {
            return;
        }

        if(config('laravel-vue-forms.admin_middleware')) {
            \Route::prefix(LaravelVueForms::webAdminPrefix())
                ->namespace($this->namespace . '')
                ->middleware(config('laravel-vue-forms.admin_middleware'))
                ->group(base_path('/vendor/jhoopes/laravel-vue-forms') . '/routes/web.php');
        }else {
            \Route::prefix(LaravelVueForms::webAdminPrefix())
                ->namespace($this->namespace . '')
                ->group(base_path('/vendor/jhoopes/laravel-vue-forms') . '/routes/web.php');
        }

    }
}
