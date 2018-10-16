<?php

namespace jhoopes\LaravelVueForms;


use jhoopes\LaravelVueForms\Models\FormConfiguration;

class Validation
{

    /** @var array $params A parameters array you can use in validation rules */
    protected $params;

    public function __construct(array $params = [])
    {
        $this->params = $params;
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

        $validator = \Validator::make($data, $rules);
        $validator->setAttributeNames($this->getAttributeNames($formConfig));
        $validator->validate();

        return $this->getValidData($formConfig, $data, $defaultData);
    }

    protected function getValidationRules(FormConfiguration $formConfig)
    {
        $rules = [];
        $formConfig->fields->each(function ($field) use (&$rules) {
            $rule = [];
            if (!empty($field->field_extra['required'])) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            if (!empty($field->field_extra['validation_rules'])) {
                collect($field->field_extra['validation_rules'])->each(function ($validation_rule) use (&$rule) {

                    $matches = [];
                    $ruleParams = [];
                    if(is_array($validation_rule)) {
                        $ruleParams = $validation_rule['params'];
                        $validation_rule = $validation_rule['rule'];
                    }

                    if(class_exists($validation_rule)) {
                        $validation_rule = new $validation_rule($this->entityModel, array_merge($this->params, $ruleParams));
                    }else if(preg_match_all('{(params\..*?)}', $validation_rule, $matches)) {

                        foreach($matches as $match) {
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
        $formConfig->fields->each(function($field) use(&$attributeNames) {
            $attributeNames[$field['value_field']] = $field['label'];
        });
        return $attributeNames;
    }

    protected function getValidData($formConfig, $data, $defaultData = false)
    {
        $validData = [];
        $formConfig->fields->whereNotIn('widget', ['column', 'section'])->each(function ($field) use (&$validData, $data, $defaultData) {

            $dataValue = array_get($data, $field->value_field);
            if ($field->disabled === 0 && $dataValue !== null ) {
                array_set($validData, $field->value_field, $dataValue);
            } else if ($defaultData) { // default field if available
                if($field->disabled === 1 || !isset($data[$field->value_field])) {

                    array_set($validData, $field->value_field, $this->getDefaultFieldValue($field));
                }
            }elseif($dataValue === null) {
                array_set($validData, $field->value_field, null);
            }
        });

        return $validData;
    }

    protected function getDefaultFieldValue($field)
    {

        if(isset($field->field_extra['default'])) {
            return $field->field_extra['default'];
        }

        return null;
    }
}
