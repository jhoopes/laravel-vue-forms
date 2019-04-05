<?php

namespace jhoopes\LaravelVueForms;

use jhoopes\LaravelVueForms\Models\FormConfiguration;
use jhoopes\LaravelVueForms\Contracts\Repositories\LaravelVueForms;

class Validation
{

    /** @var array $params A parameters array you can use in validation rules */
    protected $params;

    /** @var LaravelVueForms */
    protected $laravelVueFormsRepository;

    public function __construct(array $params = [])
    {
        $this->params = $params;
        $this->laravelVueFormsRepository = app(LaravelVueForms::class);
    }

    protected $action;
    protected $entityModel;

    public function init($action, $entityModel)
    {
        $this->action = $action;
        $this->entityModel = $entityModel;
    }


    public function validate(FormConfiguration $formConfig, $data, $defaultData = false)
    {
        $rules = $this->getValidationRules($formConfig);

        if (!is_array($data)) { // meaning null, or not a valid laravel vue form request
            $data = [];
        }

        $validator = \Validator::make($data, $rules);
        $validator->setAttributeNames($this->getAttributeNames($formConfig));
        $validator->validate();

        return $this->laravelVueFormsRepository->getValidData($formConfig, $data, $defaultData);
    }

    public function getValidationRules(FormConfiguration $formConfig)
    {
        $rules = [];
        $formConfig->fields->each(function($field) use (&$rules) {
            $rule = [];
            if (!empty($field->field_extra['required'])) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            if (!empty($field->field_extra['validation_rules'])) {
                collect($field->field_extra['validation_rules'])->each(function($validation_rule) use (&$rule) {
                    $matches = [];
                    $ruleParams = [];
                    if (is_array($validation_rule)) {
                        $ruleParams = $validation_rule['params'];
                        $validation_rule = $validation_rule['rule'];
                    }

                    if (class_exists($validation_rule)) {
                        $validation_rule = new $validation_rule($this->entityModel, array_merge($this->params, $ruleParams));
                    } elseif (preg_match_all('{(params\..*?)}', $validation_rule, $matches)) {
                        foreach ($matches as $match) {
                            $rule = str_replace('{' . $match . '}', array_get($this->params, $match));
                        }
                    }

                    $rule[] = $validation_rule;
                });
            }

            $rules[$field->value_field] = $rule;
        });
        return $rules;
    }

    protected function getAttributeNames($formConfig)
    {
        $attributeNames = [];
        $formConfig->fields->each(function($field) use (&$attributeNames) {
            $attributeNames[$field['value_field']] = $field['label'];
        });
        return $attributeNames;
    }
}
