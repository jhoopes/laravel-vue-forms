<?php

namespace jhoopes\LaravelVueForms\Providers;

use Faker\Factory;
use Illuminate\Support\ServiceProvider;
use jhoopes\LaravelVueForms\Commands\SeedAdmin;
use jhoopes\LaravelVueForms\Contracts\Repositories\LaravelVueForms;

class LaravelVueFormsServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->defineResources();
        $this->definePublishes();
        $this->defineCommands();

        $this->loadFactoriesFrom(base_path('/vendor/jhoopes/laravel-vue-forms/database/factories'));
    }


    protected function defineResources()
    {
        $this->loadViewsFrom(base_path('vendor/jhoopes/laravel-vue-forms/resources/views'), 'forms');
        $this->loadMigrationsFrom(base_path('/vendor/jhoopes/laravel-vue-forms') . '/database/migrations');

    }

    protected function definePublishes()
    {
        $this->publishes([
            base_path('/vendor/jhoopes/laravel-vue-forms') . '/config/laravel-vue-forms.php'
                => config_path('laravel-vue-forms.php'),
        ]);
    }

    protected function defineCommands()
    {
        if($this->app->runningInConsole()) {
            $this->commands([
                SeedAdmin::class,
            ]);
        }
    }


    public function register()
    {

        $this->mergeConfigFrom(
            base_path('/vendor/jhoopes/laravel-vue-forms/config/laravel-vue-forms.php'), 'laravel-vue-forms'
        );

        $this->app->bind(LaravelVueForms::class, \jhoopes\LaravelVueForms\Repositories\LaravelVueForms::class);
        $this->app->bind('laravel_vue_forms', \jhoopes\LaravelVueForms\LaravelVueForms::class);

        $this->app->register(RouteServiceProvider::class);
    }
}
