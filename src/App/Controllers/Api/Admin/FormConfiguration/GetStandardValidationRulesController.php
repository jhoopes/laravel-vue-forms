<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Models\GenericOption;
use jhoopes\LaravelVueForms\App\Controllers\Controller;

class GetStandardValidationRulesController extends Controller
{
    public function index()
    {
        return $this->collectedResponse(collect(config('laravel-vue-forms.standard_validation_rules'))
            ->map(function($option) {
                return new GenericOption($option);
            })->toArray());
    }
}
