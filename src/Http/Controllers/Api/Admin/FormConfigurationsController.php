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
        ]);

        $query = LaravelVueForms::model('form_configuration')
            ->setEagerLoads([]);

        if($request->has('name') && $request->get('name') !== null) {
            $query->where('name', 'like', $request->get('name'));
        }

        if(!config('laravel-vue-forms.edit_system_forms')) {
            $query->where('type', '!=', 'system');
        }

        $pp = $request->get('pp', 20);
        if($pp === 0) {
            return $this->collectedResponse($query->all());
        }

        return $this->collectedResponse($query->paginate($pp));
    }
}
