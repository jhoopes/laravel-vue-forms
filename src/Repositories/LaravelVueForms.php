<?php

namespace jhoopes\LaravelVueForms\Repositories;

use mysql_xdevapi\Collection;

class LaravelVueForms implements \jhoopes\LaravelVueForms\Contracts\Repositories\LaravelVueForms
{

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
            return !str_contains($field->value_field, '.');
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
            return str_contains($field->value_field, $relationship . '.');
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
            if(!is_array($field)) {
                $attributes[$field] = array_get($data, $field);
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
    public function saveEAVFields($model, $data, $fields = null)
    {
        $traits = class_uses_deep($model);
        if(!in_array(HasValues::class, $traits)) {
            throw new \InvalidArgumentException('Invalid Model for EAV');
        }

        if($fields === null) {
            $fields = $this->getNonRelatedEAVFields();
        }

        foreach($fields as $field) {
            $model->setEAVValue(
                $field,
                array_get($data, $field->value_field)
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
    public function setRelatedFieldsOnModel($model, $fields, $data, $formConfig) {


        foreach($fields as $relationship => $field) {

            if(is_array($field)) {

                if($field['many'] === false) {
                    // meaning one to one or has one
                    $attributes = [];

                    foreach($field['fields'] as $relatedField) {

                        $attributeValue = array_get($data, $relationship . '.' . $relatedField);
                        $attributes[$relatedField] = $attributeValue;

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
    public function unFlattenFields(\Illuminate\Support\Collection $fields) {

        $unFlattened = collect([]);
        foreach($fields as $field) {

            if(str_contains($field, '.')) {


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


}