<?php

namespace jhoopes\LaravelVueForms;

use Illuminate\Auth\Access\AuthorizationException;
use jhoopes\LaravelVueForms\Models\FormConfiguration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

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
     */
    public function save($with = [])
    {
        $fields = $this->unFlattenFields($this->getValueFields());

        $this->setImplicitFieldsOnModel($this->entityModel, $fields, $this->validData);
        $this->entityModel->save(); // save the entity model to ensure it exists for related fields
        $this->setRelatedFieldsOnModel($this->entityModel, $fields, $this->validData);

        return $this->entityModel->fresh($with);
    }


    protected function getValueFields()
    {
        return $this->formConfig->fields->pluck('value_field');
    }

    /**
     * Set the implicit fields on the base model
     *
     * @param $model
     * @param $fields
     * @param $data
     */
    protected function setImplicitFieldsOnModel($model, $fields, $data) {

        $attributes = [];
        foreach($fields as $fieldKey => $field) {

            if(!is_array($field)) {
                $attributes[$field] = array_get($data, $field);
            }
        }
        $model->fill($attributes);
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
     * @return array
     */
    protected function unFlattenFields(\Illuminate\Support\Collection $fields) {

        $unFlattened = [];
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
