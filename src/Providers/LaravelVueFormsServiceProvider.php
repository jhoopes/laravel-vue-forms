<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Providers;

use Illuminate\Support\ServiceProvider;
use jhoopes\LaravelVueForms\Models\Entity;
use jhoopes\LaravelVueForms\Models\EntityType;
use jhoopes\LaravelVueForms\Models\JSONAPISchemas\EntitySchema;
use jhoopes\LaravelVueForms\Models\JSONAPISchemas\EntityTypeSchema;
use Neomerx\JsonApi\Encoder\Encoder;
use jhoopes\LaravelVueForms\App\Commands\SeedAdmin;
use jhoopes\LaravelVueForms\App\Policies\EntityPolicy;
use jhoopes\LaravelVueForms\App\Policies\EntityTypePolicy;
use jhoopes\LaravelVueForms\App\Policies\FormFieldPolicy;
use jhoopes\LaravelVueForms\App\Policies\FormConfigurationPolicy;
use jhoopes\LaravelVueForms\Models\FormConfiguration;
use jhoopes\LaravelVueForms\Models\FormField;
use jhoopes\LaravelVueForms\Models\GenericOption;
use jhoopes\LaravelVueForms\Models\JSONAPISchemas\FormConfigurationSchema;
use jhoopes\LaravelVueForms\Models\JSONAPISchemas\FormFieldSchema;
use jhoopes\LaravelVueForms\Models\JSONAPISchemas\GenericOptionSchema;
use \jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class LaravelVueFormsServiceProvider extends ServiceProvider
{

    public function boot(): void
    {
        Encoder::instance([
            FormConfiguration::class => FormConfigurationSchema::class,
            FormField::class         => FormFieldSchema::class,
            GenericOption::class     => GenericOptionSchema::class,
            EntityType::class        => EntityTypeSchema::class,
            Entity::class            => EntitySchema::class,
        ]);

        $this->defineResources();
        $this->definePublishes();
        $this->defineCommands();

        if (config('laravel-vue-forms.openAccess')) {
            $this->registerOpenAccess();
        }

        $this->registerRoutes();
    }


    protected function defineResources(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
    }

    protected function definePublishes(): void
    {
        $this->publishes([
            __DIR__ . '/../../config/laravel-vue-forms.php'
            => config_path('laravel-vue-forms.php'),
        ]);
    }

    protected function defineCommands(): void
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                SeedAdmin::class,
            ]);
        }
    }

    protected function registerOpenAccess(): void
    {
        \Gate::policy(get_class(LaravelVueForms::model('form_configuration')), FormConfigurationPolicy::class);
        \Gate::policy(get_class(LaravelVueForms::model('form_field')), FormFieldPolicy::class);
        \Gate::policy(get_class(LaravelVueForms::model('entity_type')), EntityTypePolicy::class);
        \Gate::policy(get_class(LaravelVueForms::model('entity')), EntityPolicy::class);
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/laravel-vue-forms.php',
            'laravel-vue-forms'
        );

        $this->app->bind(LaravelVueForms::class, \jhoopes\LaravelVueForms\Support\Repositories\LaravelVueForms::class);
        $this->app->bind('laravel_vue_forms', \jhoopes\LaravelVueForms\LaravelVueForms::class);
    }


    public function registerRoutes()
    {
        if (config('laravel-vue-forms.use_base_api')) {
            \Route::prefix('/api')
                ->namespace('jhoopes\LaravelVueForms\App\Controllers\Api')
                ->middleware(config('laravel-vue-forms.api_middleware'))
                ->group(__DIR__ . './../App/Static/routes/api.php');
        }

        if (config('laravel-vue-forms.use_admin_api')) {
            \Route::prefix(LaravelVueForms::adminApiPrefix())
                ->namespace('jhoopes\LaravelVueForms\App\Controllers\Api\Admin')
                ->middleware(config('laravel-vue-forms.api_middleware'))
                ->group(__DIR__ . './../App/Static/routes/admin_api.php');
        }
    }

}
