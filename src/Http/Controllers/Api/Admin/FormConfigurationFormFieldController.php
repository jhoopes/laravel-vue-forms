<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use jhoopes\LaravelVueForms\Form;
use jhoopes\LaravelVueForms\Validation;
use jhoopes\LaravelVueForms\Facades\LaravelVueForms;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class FormConfigurationFormFieldController extends Controller
{
    public function create(Request $request, $formConfigId)
    {
        $this->authorizeAdminRequest();
        $formConfig = LaravelVueForms::model('form_configuration')->findOrFail($formConfigId);

        if($request->has('existingFieldId')) {
            $request->validate([
                'existingFieldId' => [
                    'required',
                    'integer',
                    Rule::exists('form_fields', 'id')
                ]
            ]);

            $savedFormField = LaravelVueForms::model('form_field')->findOrFail($request->get('existingFieldId'));
        }else {
            $this->validate($request, [
                'formConfigurationId' => 'required|integer',
                'data'                => 'required|array'
            ]);

            $savedFormField = (new Form($request->get('formConfigurationId'), $request, new Validation()))
                ->validate()
                ->save();
        }

        $order = $request->get('data.order', ($formConfig->fields()->count() + 1));

        $formConfig->fields()->attach($savedFormField, [
            'order' => $order
        ]);

        return $this->resourceResponse($savedFormField);
    }

    public function update(Request $request, $formConfigId)
    {
        $this->authorizeAdminRequest();
        $formConfig = LaravelVueForms::model('form_configuration')->findOrFail($formConfigId);
        $this->validate($request, [
            'formConfigurationId' => 'required|integer',
            'data'                => 'required|array',
            'entityId'            => [
                'required',
                'integer',
                Rule::exists('form_fields', 'id')
            ]
        ]);

        $savedFormField = (new Form($request->get('formConfigurationId'), $request, new Validation()))
            ->validate()
            ->save();

        return $this->resourceResponse($savedFormField);
    }

    public function delete($formConfigurationId, $formFieldId)
    {
        $this->authorizeAdminRequest();
        $formConfig = LaravelVueForms::model('form_configuration')->findOrFail($formConfigurationId);
        $formField = $formConfig->fields()->findOrFail($formFieldId);

        $formConfig->fields()->detach($formField);

        return $this->infoResponse([
            'success' => true,
            'message' => 'Successfully removed field from form'
        ]);
    }

}
