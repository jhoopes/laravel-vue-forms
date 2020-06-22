<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Form;
use jhoopes\LaravelVueForms\Validation;
use jhoopes\LaravelVueForms\Facades\LaravelVueForms;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class FormFieldController extends Controller
{
    public function create(Request $request, $formConfigId)
    {
        $this->authorizeAdminRequest();
        $formConfig = LaravelVueForms::model('form_configuration')->findOrFail($formConfigId);
        $this->validate($request, [
            'formConfigurationId' => 'required|integer',
            'data'                => 'required|array'
        ]);

        $savedFormField = (new Form($request->get('formConfigurationId'), $request, new Validation()))
            ->validate()
            ->save();

        $order = $request->get('data.order', ($formConfig->fields()->count() + 1));

        $formConfig->fields()->attach($savedFormField, [
            'order' => $order
        ]);

        return $this->resourceResponse($savedFormField);
    }
}
