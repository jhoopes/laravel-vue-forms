<?php

namespace jhoopes\LaravelVueForms\Providers;

use Illuminate\Support\ServiceProvider;

class LaravelVueFormsServiceProvider extends ServiceProvider
{

    public function boot()
    {
        $this->defineResources();
        $this->definePublishes();

        // Bind a custom Model Factory to the container so we can set our own Model Factory Class on it.
        $this->app->singleton('jhoopes\LaravelVueForms\ModelFactory', function() {
            $pathToFactories = base_path('/vendor/jhoopes/laravel-vue-forms') . '/database/factories';
            return Factory::construct(\Faker\Factory::create(), $pathToFactories);
        });
    }


    protected function defineResources()
    {

        $this->loadMigrationsFrom(base_path('/vendor/jhoopes/laravel-vue-forms') . '/database/migrations');

    }

    protected function definePublishes()
    {
        $this->publishes([
            base_path('/vendor/jhoopes/laravel-vue-forms') . '/install-stubs/laravel-vue-forms.php'
                => config_path('laravel-vue-forms.php'),
        ]);
    }


    public function register()
    {

//        if(!defined('LVFORMS_PATH')) {
//            define('LVFORMS_PATH', realpath(__DIR__.'/../../'));
//        }

        $this->mergeConfigFrom(
            base_path('/vendor/jhoopes/laravel-vue-forms') . '/install-stubs/laravel-vue-forms.php', 'laravel-vue-forms'
        );

        $this->app->register(RouteServiceProvider::class);
    }
}
