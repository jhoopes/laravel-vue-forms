<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class GetEntityTypesController extends Controller
{


    public function index(Request $request)
    {
        return collect(config('laravel-vue-forms.entity_types'))
                ->keys()
                ->map(function($option) {
                    return [
                        'name' => $option,
                        'title' => \Str::title(str_replace('_', ' ', $option))
                    ];
                });
    }

}
