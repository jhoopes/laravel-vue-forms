<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Forms;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class FormConfiguration extends Controller
{

    /**
     * Index function to retrieve multiple form configurations based on their IDs or names
     *
     * @param Request $request
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Database\Eloquent\Model|null
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'formConfigIds'     => 'nullable|array',
            'formConfigIds.*'   => 'integer',
            'formConfigNames'   => 'nullable|array',
            'formConfigNames.*' => 'string',
            'formConfigName'    => 'nullable|string|exists:form_configurations,name'
        ]);

        $query = \jhoopes\LaravelVueForms\Models\FormConfiguration::query();
        $query->with('fields');

        if (!is_null($request->get('formConfigName'))) {
            $query->where('name', $request->get('formConfigName'));
            return $query->first();
        } elseif (!is_null($request->get('formConfigNames'))) {
            $query->whereIn('name', $request->get('formConfigNames'));
        } elseif (!is_null($request->get('formConfigIds'))) {
            $query->whereIn('id', $request->get('formConfigIds'));
        }

        return $query->get();
    }


    public function show(Request $request, $formConfigId)
    {
        return \jhoopes\LaravelVueForms\Models\FormConfiguration::with('fields')->findOrFail($formConfigId);
    }
}
