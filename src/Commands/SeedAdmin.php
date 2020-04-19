<?php

namespace jhoopes\LaravelVueForms\Commands;

use Illuminate\Console\Command;
use jhoopes\LaravelVueForms\Facades\LaravelVueForms;
use jhoopes\LaravelVueForms\Models\FormConfiguration;

class SeedAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'forms:seedAdmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed the Admin System Forms';


    protected $formConfigurations;
    protected $formFields;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->formConfigurations = collect([]);
        $this->formFields = collect([]);
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->line('Seeding form admin...');
        $formSeeds = require base_path('/vendor/jhoopes/laravel-vue-forms/database/seeds/AdminForms.php');

        $this->line('Found ' . count($formSeeds['form_configurations']) . ' form configurations.');
        foreach($formSeeds['form_configurations'] as $formConfigName => $formConfigurationInfo) {
            $formConfigModel = LaravelVueForms::model('form_configuration')->updateOrCreate([
                'name' => $formConfigurationInfo['name']
            ],$formConfigurationInfo);
            $this->formConfigurations[$formConfigName] = $formConfigModel;
        }

        $this->line('Found ' . count($formSeeds['form_fields']) . ' form fields.');
        foreach($formSeeds['form_fields'] as $formFieldName => $formFieldInfo) {
            $formFieldModel = LaravelVueForms::model('form_field')->updateOrCreate([
                'name' => $formFieldInfo['name']
            ],$formFieldInfo);
            $this->formFields[$formFieldName] = $formFieldModel;
        }

        foreach($formSeeds['form_configuration_form_field'] as $formConfigName => $formConfigFields) {

            $formConfig = $this->formConfigurations->firstWhere('name', $formConfigName);
            if(!$formConfig) {
                throw new \Exception('Invalid Form Configuration to seed');
            }
            collect($formConfigFields)->each(function($fieldName, $index) use($formConfig) {
                $formField = $this->formFields->firstWhere('name', $fieldName);
                if(!$formField) {
                    throw new \Exception('Invalid Form Field to seed');
                }

                if(!$field = $formConfig->fields->firstWhere('name', $formField->name)) {
                    $formConfig->fields()->attach($formField, [
                        'order' => $index + 1
                    ]);
                }
            });
        }

    }
}
