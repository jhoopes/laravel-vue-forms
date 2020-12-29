<?php

namespace jhoopes\LaravelVueForms\Http\Controllers\Api\Admin;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Models\GenericOption;
use jhoopes\LaravelVueForms\Http\Controllers\Controller;

class GetStandardValidationRulesController extends Controller
{

    public function __invoke(Request $request)
    {
        return $this->collectedResponse(collect(config('laravel-vue-forms.standard_validation_rules'))
            ->map(function($option) {
                return new GenericOption($option);
            })->toArray());
    }

}
