<?php

namespace jhoopes\LaravelVueForms\Contracts\Repositories;

use Illuminate\Support\Collection;

interface LaravelVueForms
{

    /**
     * Get the value fields of NonEAV Fields for un-flattening them to set up saving structure
     *
     * @param $formConfig
     * @return Collection
     */
    public function getNonEAVValueFields($formConfig) : Collection;

    /**
     * Get fields that are EAV fields, but not related to any specific sub-model
     *
     * @param $formConfig
     * @return Collection
     */
    public function getNonRelatedEAVFields($formConfig) : Collection;

    /**
     * Get the EAV fields for a related model/sub-model by the relationship name
     *
     * @param $relationship
     * @param $formConfig
     * @return Collection
     */
    public function getRelatedEAVFields($relationship, $formConfig) : Collection;

    /**
     * Set the implicit fields on the base model
     *
     * @param Model $model
     * @param Collection $fields
     * @param array $data
     */
    public function setImplicitFieldsOnModel($model, $fields, $data);

    /**
     * @param Model $model
     * @param array $data
     * @param Collection|null $fields Default is to use non related EAV fields
     * @throws \InvalidArgumentException
     */
    public function saveEAVFields($model, $data, $fields);

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
    public function setRelatedFieldsOnModel($model, $fields, $data, $formConfig);


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
    public function unFlattenFields(\Illuminate\Support\Collection $fields) : Collection;



    /**
     * Transform a data array into the valid data array based on form configuration
     *
     * @param $formConfig
     * @param $data
     * @param bool $defaultData
     * @return array
     */
    public function getValidData($formConfig, $data, $defaultData = false) : array;

    /**
     * Get the default for a field
     *
     * @param $field
     * @return mixed|null
     */
    public function getDefaultFieldValue($field);
}