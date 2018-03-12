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

        $this->hasPermission();
    }

    /**
     * @return $this
     * @throws AuthorizationException
     */
    public function hasPermission() {

        if(!\Gate::allows($this->action, $this->entityModel)) {
            throw new AuthorizationException('This action (' . $this->action . ') is unauthorized');
        }

        return $this;
    }

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

    public function save()
    {
        $this->entityModel
            ->fill($this->validData)
            ->save();

        return $this->entityModel->fresh();
    }


}