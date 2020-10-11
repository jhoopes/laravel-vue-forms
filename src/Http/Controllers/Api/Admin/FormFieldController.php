<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Facades\LaravelVueForms;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class FormFieldController extends Controller
{

    public function index(Request $request)
    {
        $this->authorizeAdminRequest();
        $request->validate([
            'q' => [
                'nullable',
                'string'
            ]
        ]);

        $query = LaravelVueForms::model('form_field')->newQuery();
        $q = $request->get('q');
        if(!empty($q)) {
            $query->where('name', 'like', '%' . $q . '%')
                ->orWhere('value_field', 'like', '%' . $q . '%')
                ->orWhere('label', 'like', '%' . $q . '%');
        }

        return $this->collectedResponse($query->get());
    }


    public function delete($formFieldId)
    {
        $this->authorizeAdminRequest();
        $formField = LaravelVueForms::model('form_field')
            ->findOrFail($formFieldId);

        $formField->form_configurations()->sync([]);
        $formField->delete();

        return $this->infoResponse([
            'success' => true,
            'message' => 'Successfully deleted form field: ' . $formField->name
        ]);
    }


}
