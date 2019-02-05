<?php

namespace jhoopes\LaravelVueForms;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Collection;
use jhoopes\LaravelVueForms\Contracts\Repositories\LaravelVueForms;
use jhoopes\LaravelVueForms\Models\FormConfiguration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Models\Helpers\HasValues;

class Form
{
    protected $laravelVueFormsRepository;
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
        $this->laravelVueFormsRepository = app(LaravelVueForms::class);
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
        $fields = $this->laravelVueFormsRepository
            ->unFlattenFields(
                $this->laravelVueFormsRepository->getNonEAVValueFields()
            );

        $this->laravelVueFormsRepository
            ->setImplicitFieldsOnModel($this->entityModel, $fields, $this->validData);

        // save the entity model to ensure it exists for related fields, and EAV fields
        $this->entityModel->save();

        if($this->getNonRelatedEAVFields()->count() > 0) {
            $this->saveEAVFields($this->entityModel, $this->validData);
        }

        $this->setRelatedFieldsOnModel($this->entityModel, $fields, $this->validData);

        return $this->entityModel->fresh($with);
    }
}
