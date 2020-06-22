<?php

namespace jhoopes\LaravelVueForms\Providers;

use Illuminate\Support\ServiceProvider;
use jhoopes\LaravelVueForms\Commands\SeedAdmin;
use jhoopes\LaravelVueForms\Policies\FormFieldPolicy;
use jhoopes\LaravelVueForms\Policies\FormConfigurationPolicy;
use jhoopes\LaravelVueForms\Contracts\Repositories\LaravelVueForms;

class LaravelVueFormsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->defineResources();
        $this->definePublishes();
        $this->defineCommands();

        if (config('laravel-vue-forms.openAccess')) {
            $this->registerOpenAccess();
        }

        $this->loadFactoriesFrom(base_path('/vendor/jhoopes/laravel-vue-forms/database/factories'));
    }


    protected function defineResources(): void
    {
        $this->loadViewsFrom(base_path('vendor/jhoopes/laravel-vue-forms/resources/views'), 'forms');
        $this->loadMigrationsFrom(base_path('/vendor/jhoopes/laravel-vue-forms') . '/database/migrations');
    }

    protected function definePublishes():void
    {
        $this->publishes([
            base_path('/vendor/jhoopes/laravel-vue-forms') . '/config/laravel-vue-forms.php'
                => config_path('laravel-vue-forms.php'),
        ]);
    }

    protected function defineCommands():void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SeedAdmin::class,
            ]);
        }
    }

    protected function registerOpenAccess(): void
    {
        \Gate::policy(get_class(\jhoopes\LaravelVueForms\Facades\LaravelVueForms::model('form_configuration')), FormConfigurationPolicy::class);
        \Gate::policy(get_class(\jhoopes\LaravelVueForms\Facades\LaravelVueForms::model('form_field')), FormFieldPolicy::class);
    }


    public function register(): void
    {
        $this->mergeConfigFrom(
            base_path('/vendor/jhoopes/laravel-vue-forms/config/laravel-vue-forms.php'),
            'laravel-vue-forms'
        );

        $this->app->bind(LaravelVueForms::class, \jhoopes\LaravelVueForms\Repositories\LaravelVueForms::class);
        $this->app->bind('laravel_vue_forms', \jhoopes\LaravelVueForms\LaravelVueForms::class);

        $this->app->register(RouteServiceProvider::class);
    }
}
