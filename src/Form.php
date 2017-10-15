<?php

namespace jhoopes\LaravelVueForms;

use jhoopes\LaravelVueForms\Models\FormConfiguration;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Form
{

    protected $formConfig;
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
            $this->entityModel = app($this->formConfig->entity_model)->findOrFail($this->request->get('entityId'));
        }else {
            $this->entityModel = app($this->formConfig->entity_model)->make([]);
        }

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

    public function save()
    {
        $this->entityModel
            ->fill($this->validData)
            ->save();

        return $this->entityModel->fresh();
    }



}