<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Facades\LaravelVueForms;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class FormConfigurationsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdminRequest();
        $request->validate([
            'pp' => [
                'nullable',
                'integer',
            ],
            'name' => [
                'nullable',
                'string'
            ]
        ]);

        $query = LaravelVueForms::model('form_configuration')
            ->setEagerLoads([]);

        if($request->has('name') && $request->get('name') !== null) {
            $query->where('name', 'like', $request->get('name'));
        }

        $pp = 20;
        if($request->has('pp') && $request->get('pp') !== null) {
            $pp = $request->get('pp');
        }

        if($pp = 0) { // if the request was for all objectsd, return them all without pagination
            return $query->all();
        }

        return $this->collectedResponse($query->paginate($pp), [], ['fields']);
    }
}
