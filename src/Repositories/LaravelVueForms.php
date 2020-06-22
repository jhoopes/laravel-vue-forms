<?php

namespace jhoopes\LaravelVueForms\Repositories;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Mews\Purifier\Purifier;
use Illuminate\Support\Collection;
use jhoopes\LaravelVueForms\Models\Helpers\HasValues;

class LaravelVueForms implements \jhoopes\LaravelVueForms\Contracts\Repositories\LaravelVueForms
{

    /** @var Purifier  */
    protected $purifier;

    public function __construct()
    {
        $this->purifier = app('purifier');
    }

    /**
     * Get the value fields of NonEAV Fields for un-flattening them to set up saving structure
     *
     * @param $formConfig
     * @return Collection
     */
    public function getNonEAVValueFields($formConfig) : Collection
    {
        return $formConfig->fields->where('is_eav', 0)->pluck('value_field');
    }

    /**
     * Get fields that are EAV fields, but not related to any specific sub-model
     *
     * @param $formConfig
     * @return Collection
     */
    public function getNonRelatedEAVFields($formConfig) : Collection
    {
        return $formConfig->fields->filter(function($field) {
            return !Str::contains($field->value_field, '.');
        })->where('is_eav', 1);
    }

    /**
     * Get the EAV fields for a related model/sub-model by the relationship name
     *
     * @param $relationship
     * @param $formConfig
     * @return Collection
     */
    public function getRelatedEAVFields($relationship, $formConfig) : Collection
    {
        return $formConfig->fields->filter(function($field) use($relationship) {
            return Str::contains($field->value_field, $relationship . '.');
        })->where('is_eav', 1);
    }

    /**
     * Set the implicit fields on the base model
     *
     * @param Model $model
     * @param Collection $fields
     * @param array $data
     */
    public function setImplicitFieldsOnModel($model, $fields, $data)
    {
        $attributes = [];

        foreach($fields as $fieldKey => $field) {
            if(!is_array($field) && Arr::has($data, $field)) {
                $attributes[$field] = Arr::get($data, $field);
            }
        }
        $model->fill($attributes);
    }

    /**
     * @param Model $model
     * @param array $data
     * @param Collection|null $fields Default is to use non related EAV fields
     * @throws \InvalidArgumentException
     */
    public function saveEAVFields($model, $data, $fields)
    {
        $traits = class_uses_deep($model);
        if(!in_array(HasValues::class, $traits)) {
            throw new \InvalidArgumentException('Invalid Model for EAV');
        }

//        if($fields === null) {
//            $fields = $this->getNonRelatedEAVFields();
//        }

        foreach($fields as $field) {
            $model->setEAVValue(
                $field,
                Arr::get($data, $field->value_field)
            );
        }
    }


    /**
     * Set related records fields on the base implicit model
     *
     * This function only works with 1 level deep of setting related models and fields
     *
     * E.G:
     * address.address
     * address.city
     * address.state
     * ---- or ---- (with updates in the "TODO" section in unflattening fields)
     * addresses.*.address
     * addresses.*.city
     * addresses.*.state
     *
     * @param $model
     * @param $fields
     * @param $data
     */
    public function setRelatedFieldsOnModel($model, $fields, $data, $formConfig)
    {


        foreach($fields as $relationship => $field) {

            if(is_array($field)) {

                if($field['many'] === false) {
                    // meaning one to one or has one
                    $attributes = [];

                    foreach($field['fields'] as $relatedField) {
                        if(Arr::has($data, $relationship . '.' . $relatedField)) {
                            $attributeValue = Arr::get($data, $relationship . '.' . $relatedField);
                            $attributes[$relatedField] = $attributeValue;
                        }
                    }

                    if($model->$relationship !== null) {
                        $relatedModel = $model->$relationship;
                        $relatedModel->fill($attributes)->save();
                    }else {
                        $relatedModel = $model->$relationship()->create($attributes);
                    }

                    $eavFields = $this->getRelatedEAVFields($relationship, $formConfig);
                    if($eavFields->count() > 0) {
                        $this->saveEAVFields($relatedModel, $data, $eavFields);
                    }

                }
            }
        }
    }


    /**
     * Un-flatten the field list for array/object accessors
     *
     * E.g.
     *
     * [
     *      'name', 'email', 'address.address', 'address.city', 'address.state'
     * ]
     *
     * becomes
     *
     * [
     *      'name',
     *      'email',
     *      'address' => [
     *          'many' => false,
     *          'fields' => [
     *              'address',
     *              'city',
     *              'state'
     *      ]
     * ]
     *
     * @param \Illuminate\Support\Collection $fields
     * @return Collection
     */
    public function unFlattenFields(\Illuminate\Support\Collection $fields) : Collection
    {

        $unFlattened = collect([]);
        foreach($fields as $field) {

            if(Str::contains($field, '.')) {


                $keys = explode('.', $field);
                $firstRelationship = array_shift($keys);

                if($firstRelationship === '*') {

                    // TODO: Need to find a good way to support wildcards ( one to many relationships)

                }else {

                    $newFields = $fields->filter(function($field) use($firstRelationship) {
                        return starts_with($field, $firstRelationship);
                    })->map(function($field) use($firstRelationship) {
                        return str_replace($firstRelationship . '.', '', $field);
                    });

                    $unFlattened[$firstRelationship] = [
                        'many' => false,
                        'fields' => $this->unFlattenFields($newFields)
                    ];
                }
            } else {
                $unFlattened[] = $field;
            }
        }

        return $unFlattened;
    }



    // Validation Helpers

    /**
     * Transform a data array into the valid data array based on form configuration
     *
     * @param $formConfig
     * @param $data
     * @param bool $defaultData
     * @return array
     */
    public function getValidData($formConfig, $data, $defaultData = false) : array
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
                $dataValue = json_decode($dataValue);
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
     * @param $field
     * @return mixed|null
     */
    public function getDefaultFieldValue($field)
    {

        if(isset($field->field_extra['default'])) {
            return $field->field_extra['default'];
        }

        return null;
    }


}
