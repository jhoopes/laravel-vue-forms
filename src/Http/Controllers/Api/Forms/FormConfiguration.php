<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Forms;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class FormConfiguration extends Controller
{

    /**
     * Index function to retrieve multipl form configurations based on their IDs or names
     *
     * @param Request $request
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'formConfigIds'     => 'nullable|array',
            'formConfigIds.*'   => 'integer',
            'formConfigNames'   => 'nullable|array',
            'formConfigNames.*' => 'string',
        ]);

        $query = \jhoopes\LaravelVueForms\Models\FormConfiguration::query();
        $query->with('fields');

        if(!is_null($request->get('formConfigIds'))) {
            $query->whereIn('id', $request->get('formConfigIds'));
        }else if(!is_null($request->get('formConfigNames'))) {
            $query->whereIn('name', $request->get('formConfigNames'));
        }

        return $query->get();
    }


    public function show(Request $request, $formConfigId)
    {
        return \jhoopes\LaravelVueForms\Models\FormConfiguration::with('fields')->findOrFail($formConfigId);
    }
}
