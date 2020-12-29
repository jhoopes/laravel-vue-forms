<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Models\GenericOption;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class GetCastTypesController extends Controller
{


    public function __invoke(Request $request)
    {
        return $this->collectedResponse(collect(config('laravel-vue-forms.cast_types'))
            ->map(function($option, $value) {
                return new GenericOption([
                    'value' => $value,
                    'title' => $option
                ]);
            })->toArray());
    }

}
