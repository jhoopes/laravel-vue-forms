<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Mews\Purifier\Purifier;
use jhoopes\LaravelVueForms\DTOs\ValidateDataForFormDTO;
use jhoopes\LaravelVueForms\Models\FormConfiguration;
use jhoopes\LaravelVueForms\Models\FormField;

class ValidateDataForForm
{

    public function __construct(
        public Purifier $purifier
    ) {}

    /**
     * return the valid data array
     *
     * @return array
     * @throws \Illuminate\Validation\ValidationException
     */
    public function execute(ValidateDataForFormDTO $dataForFormDTO): array
    {
        $validationRules = $this->getValidationRules($dataForFormDTO);
        $validator = \Validator::make($dataForFormDTO->unValidatedData, $validationRules);
        $validator->setAttributeNames($this->getAttributeNames($dataForFormDTO->formConfiguration));
        return $this->getValidData($dataForFormDTO->formConfiguration, $validator->validate());
    }

    public function getValidationRules(ValidateDataForFormDTO $dataForFormDTO): array
    {
        $rules = [];
        $dataForFormDTO->formConfiguration->fields->each(function($field) use (&$rules, $dataForFormDTO) {
            $rule = [];
            if (!empty($field->field_extra['required'])) {
                $rule[] = 'required';
            } else {
                $rule[] = 'nullable';
            }

            if (!empty($field->field_extra['validation_rules'])) {
                collect($field->field_extra['validation_rules'])->each(function($validation_rule) use (&$rule, $dataForFormDTO) {
                    $matches = [];
                    $ruleParams = [];
                    if (is_array($validation_rule)) {
                        $ruleParams = $validation_rule['params'];
                        $validation_rule = $validation_rule['rule'];
                    }

                    if (class_exists($validation_rule)) {
                        $validation_rule = new $validation_rule(
                            $dataForFormDTO->entityModel,
                            array_merge($dataForFormDTO->params, $ruleParams)
                        );
                    } elseif (preg_match_all('{(params\..*?)}', $validation_rule, $matches)) {
                        foreach ($matches as $match) {
                            str_replace(
                                '{' . $match . '}',
                                Arr::get($dataForFormDTO->params, $match),
                                $validation_rule
                            );
                        }
                    }

                    if(
                        is_string($validation_rule) &&
                        strstr($validation_rule, 'unique') &&
                        $this->entityModel !== null &&
                        $this->entityModel->exists
                    ) {
                        $validation_rule .= ',' . $this->entityModel->id;
                    }

                    $rule[] = $validation_rule;
                });
            }

            $rules[$field->value_field] = $rule;
        });
        return $rules;
    }

    protected function getAttributeNames($formConfig): array
    {
        $attributeNames = [];
        $formConfig->fields->each(function($field) use (&$attributeNames) {
            $attributeNames[$field['value_field']] = $field['label'];
        });
        return $attributeNames;
    }



    /**
     * Transform a data array into the valid data array based on form configuration
     *
     */
    public function getValidData(FormConfiguration $formConfig, array $data, bool $defaultData = false) : array
    {
        $validData = [];
        $fields = $formConfig->fields->whereNotIn('widget', ['column', 'section', 'static']);
        foreach($fields as $field) {

            // only attempt to set the field in valid data if the key is set in data,
            // and if we're not on a field that's disabled and not defaulting the data for it
            if(empty($field->value_field) || (!Arr::has($data, $field->value_field) && !($field->disabled === 1 && $defaultData) ) ) {
                continue;
            }

            $dataValue = Arr::get($data, $field->value_field);
            if($field->widget === 'wysiwyg' && $dataValue !== null && $field->disabled === 0) {

                if(isset($field->field_extra['purifier_config'])) {
                    $dataValue = $this->purifier->clean($dataValue, $field->field_extra['purifier_config']);
                } else {
                    $dataValue = $this->purifier->clean($dataValue);
                }

                Arr::set($validData, $field->value_field, $dataValue);
            }else if ($field->widget === 'code' && Arr::get($field->field_extra, 'editorOptions.mode') === 'json') {
                if($dataValue !== null) {
                    $dataValue = json_decode($dataValue);
                }

                Arr::set($validData, $field->value_field, $dataValue);
            } else if (!$field->disabled && $dataValue !== null ) {
                Arr::set($validData, $field->value_field, $dataValue);
            } else if ($defaultData && (!isset($data[$field->value_field]) || $dataValue === null)) { // default field if available
                Arr::set($validData, $field->value_field, $this->getDefaultFieldValue($field));
            }elseif($dataValue === null) {
                Arr::set($validData, $field->value_field, null);
            }
        }

        return $validData;
    }

    /**
     * Get the default for a field
     *
     */
    public function getDefaultFieldValue(FormField $field): mixed
    {

        if(isset($field->field_extra['default'])) {
            return $field->field_extra['default'];
        }

        return null;
    }

}
