<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Models\GenericOption;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class GetEntityTypesController extends Controller
{


    public function __invoke(Request $request)
    {
        return $this->collectedResponse(collect(config('laravel-vue-forms.entity_types'))
            ->keys()
            ->map(function($option) {
                return new GenericOption([
                    'name' => $option,
                    'title' => \Str::title(str_replace('_', ' ', $option))
                ]);
            })->toArray());
    }

}
