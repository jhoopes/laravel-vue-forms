<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Forms;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class FormConfiguration extends Controller
{

    /**
     * Index function to retrieve multiple form configurations based on their IDs or names
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
            'formConfigName'    => 'nullable|string|exists:form_configurations,name'
        ]);

        $query = \jhoopes\LaravelVueForms\Models\FormConfiguration::query();
        $query->with('fields');

        $active = $request->get('active', 1);
        $query->where('active', $active);

        if ($request->get('formConfigType') !== null) {
            $query->where('type', $request->get('formConfigType'));
            $results = $query->get();

            if ($active && $results->count() > 1) {
                throw new \InvalidArgumentException('Invalid form config type / active query.  More than 2 result is returned');
            }

            return $this->resourceResponse($results->first(), null, ['fields']);
        }

        if (!is_null($request->get('formConfigName'))) {
            $query->where('name', $request->get('formConfigName'));
            return $this->resourceResponse($query->first(), null, ['fields']);
        } elseif (!is_null($request->get('formConfigNames'))) {
            $query->whereIn('name', $request->get('formConfigNames'));
        } elseif (!is_null($request->get('formConfigIds'))) {
            $query->whereIn('id', $request->get('formConfigIds'));
        }

        return $this->collectedResponse($query->get());
    }


    public function show(Request $request, $formConfigId)
    {
        $request->validate([
            'include' => [
                'sometimes',
                'array'
            ],
            'include.*' => [
                'sometimes',
                'string'
            ]
        ]);

        $formConfig = \jhoopes\LaravelVueForms\Models\FormConfiguration::with($request->get('include'))->findOrFail($formConfigId);
        return $this->resourceResponse($formConfig, [], $request->get('include'));
    }
}
