<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api\Admin\Entities;

use Illuminate\Support\Str;
use jhoopes\LaravelVueForms\App\Controllers\Controller;
use jhoopes\LaravelVueForms\Models\GenericOption;

class GetBuiltInEntityTypesController extends Controller
{

    public function index()
    {
        return $this->collectedResponse(collect(config('laravel-vue-forms.built_in_entity_types'))
            ->map(function($option, $key) {
                return new GenericOption([
                    'name' => $option,
                    'title' => Str::of($key)->replace('_', ' ' )->title()
                ]);
            })->toArray());
    }

}
