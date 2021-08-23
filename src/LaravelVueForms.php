<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use jhoopes\LaravelVueForms\Actions\GetDefaultActiveFormForType;
use jhoopes\LaravelVueForms\DTOs\DefaultFormForTypeDTO;
use jhoopes\LaravelVueForms\Models\FormConfiguration;

class LaravelVueForms
{

    public static $models = [
        'entity'             => \jhoopes\LaravelVueForms\Models\Entity::class,
        'entity_file'        => \jhoopes\LaravelVueForms\Models\EntityFile::class,
        'entity_type'        => \jhoopes\LaravelVueForms\Models\EntityType::class,
        'form_configuration' => \jhoopes\LaravelVueForms\Models\FormConfiguration::class,
        'form_field'         => \jhoopes\LaravelVueForms\Models\FormField::class,
    ];


    public static function model($model, $concrete = null): Model
    {
        if ($concrete !== null) {
            self::$models[$model] = $concrete;
        }

        return app(self::$models[$model]);
    }

    public static function getModels(): array
    {
        return self::$models;
    }


    protected static $apiPrefix = '/forms';
    protected static $adminApiPrefix = '/api/forms/admin';
    protected static $webAdminPrefix = '/admin';
    protected static $adminAuthorization = '';
    protected static $useJSONAPI = false;

    public static function apiPrefix($apiPrefix = null)
    {
        if ($apiPrefix !== null) {
            self::$apiPrefix = $apiPrefix;
        }

        return self::$apiPrefix;
    }

    public static function adminApiPrefix($apiPrefix = null)
    {
        if ($apiPrefix !== null) {
            self::$adminApiPrefix = $apiPrefix;
        }

        return self::$adminApiPrefix;
    }

    public static function webAdminPrefix($webPrefix = null)
    {
        if ($webPrefix !== null) {
            self::$webAdminPrefix = $webPrefix;
        }

        return self::$webAdminPrefix;
    }


    public static function adminAuthorization($authorization = null)
    {
        if ($authorization) {
            self::$adminAuthorization = $authorization;
        }

        return self::$adminAuthorization;
    }

    public static function useJSONApi($use = null)
    {
        if($use) {
            self::$useJSONAPI = $use;
        }

        return self::$useJSONAPI;
    }


    public static function getDefaultFormSubmissionValidationRules($withEntityId = false): array
    {
        $rules = [
            'formConfigurationId' => [
                'required',
                'integer',
                Rule::exists('form_configurations', 'id')
            ],
            'data' => [
                'required',
                'array'
            ]
        ];

        if($withEntityId) {
            $rules['entityId'] = [
                'required',
                'integer'
            ];
        }

        return $rules;
    }

    public static function getDefaultFormConfigurationValidationRule(
        FormConfiguration|null &$formConfiguration,
        string $formType = null,
        string $entityType = null
    ): array {

        if(!$formConfiguration) {
            $formConfiguration = app(GetDefaultActiveFormForType::class)
                ->execute(DefaultFormForTypeDTO::fromController($formType, $entityType));
        }

        return [
            'default_form_configuration' => [
                function($attribute, $value, $fail) use(&$formConfiguration) {
                    if(!$formConfiguration) {
                        return $fail('You must either specify a form configuration, or have an active entity type form');
                    }
                }
            ]
        ];
    }

    /** http://php.net/manual/en/function.class-uses.php */
    public static function class_uses_deep($class, $autoload = true) {
        $traits = [];

        // Get all the traits of $class and its parent classes
        do {
            $class_name = is_object($class)? get_class($class): $class;
            if (class_exists($class_name, $autoload)) {
                $traits = array_merge(class_uses($class, $autoload), $traits);
            }
        } while ($class = get_parent_class($class));

        // Get traits of all parent traits
        $traits_to_search = $traits;
        while (!empty($traits_to_search)) {
            $new_traits = class_uses(array_pop($traits_to_search), $autoload);
            $traits = array_merge($new_traits, $traits);
            $traits_to_search = array_merge($new_traits, $traits_to_search);
        }

        return array_unique($traits);
    }

}
