<?php

namespace jhoopes\LaravelVueForms;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use jhoopes\LaravelVueForms\Models\FormConfiguration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Models\Helpers\HasValues;

class Form
{

    protected $formConfig;
    protected $action;
    protected $request;

    /**
     * @var Model
     */
    protected $entityModel;

    protected $validator;

    protected $validData;

    public function __construct($formConfigId, Request $request, Validation $validation)
    {

        $this->formConfig = FormConfiguration::findOrFail($formConfigId);
        $this->request = $request;
        $this->validator = $validation;

        if($this->request->has('entityId')) {
            $this->action = 'update';
            $this->entityModel = app($this->formConfig->entity_model)->findOrFail($this->request->get('entityId'));
        }else {
            $this->entityModel = app($this->formConfig->entity_model)->make([]);
            $this->action = 'create';
        }

        $validation->init($this->action, $this->entityModel);

        if(config('laravel-vue-forms.check_permissions')) {
            $this->hasPermission();
        }
    }

    /**
     * @return $this
     * @throws AuthorizationException
     */
    public function hasPermission()
    {
        if(\Gate::denies($this->action, $this->entityModel)) {
            throw new AuthorizationException('This action (' . $this->action . ', ' .
                $this->formConfig->entity_model .') is unauthorized');
        }

        return $this;
    }

    /**
     * Use the validator to validate the request data
     *
     * @return $this
     */
    public function validate()
    {
        $this->validData = $this->validator->validate($this->formConfig, $this->request->get('data'));

        return $this;
    }

    public function getValidData()
    {
        return $this->validData;
    }

    public function getFormConfiguration()
    {
        return $this->formConfig;
    }

    public function getEntityModel()
    {
        return $this->entityModel;
    }

    /**
     * Save the entity model with related fields as well.
     *
     * @return null|static
     * @throws \InvalidArgumentException
     */
    public function save($with = [])
    {
        $fields = $this->unFlattenFields($this->getNonEAVValueFields());

        $this->setImplicitFieldsOnModel($this->entityModel, $fields, $this->validData);
        $this->entityModel->save(); // save the entity model to ensure it exists for related fields, and EAV fields

        if($this->getNonRelatedEAVFields()->count() > 0) {
            $this->saveEAVFields($this->entityModel, $this->validData);
        }

        $this->setRelatedFieldsOnModel($this->entityModel, $fields, $this->validData);

        return $this->entityModel->fresh($with);
    }


    protected function getNonEAVValueFields()
    {
        return $this->formConfig->fields->where('is_eav', 0)->pluck('value_field');
    }

    protected function getNonRelatedEAVFields() : Collection
    {
        return $this->formConfig->fields->filter(function($field) {
            return !str_contains($field->value_field, '.');
        })->where('is_eav', 1);
    }

    protected function getRelatedEAVFields($relationship) : Collection
    {
        return $this->formConfig->fields->filter(function($field) use($relationship) {
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
    protected function setImplicitFieldsOnModel($model, $fields, $data) {

        $attributes = [];

        foreach($fields->where('is_eav', 0)->all() as $fieldKey => $field) {

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
    protected function saveEAVFields($model, $data, $fields = null)
    {
        //$traits = (new \ReflectionClass(\get_class($model)))->getTraits();
        $traits = class_uses($model);
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
    function setRelatedFieldsOnModel($model, $fields, $data) {


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

                    $eavFields = $this->getRelatedEAVFields($relationship);
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
    protected function unFlattenFields(\Illuminate\Support\Collection $fields) {

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
