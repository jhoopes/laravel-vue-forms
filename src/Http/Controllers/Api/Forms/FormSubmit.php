<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Forms;

use jhoopes\LaravelVueForms\Form;
use jhoopes\LaravelVueForms\Validation;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FormSubmit extends Controller
{


    public function store(Request $request)
    {
        $this->validate($request, [
            'formConfigurationId' => 'required|integer',
            'data'                => 'required|array'
        ]);

        return (new Form($request->get('formConfigurationId'), $request, new Validation()))
            ->validate()
            ->save();
    }

    public function update(Request $request)
    {
        $this->validate($request, [
            'formConfigurationId' => 'required|integer',
            'data'                => 'required|array',
            'entityId'            => 'required|integer'
        ]);

        return (new Form($request->get('formConfigurationId'), $request, new Validation()))
            ->validate()
            ->save();
    }

}
