<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api\Admin\Entities;

use jhoopes\LaravelVueForms\App\Controllers\Controller;
use jhoopes\LaravelVueForms\Models\GenericOption;

class GetEntityTypeOptionsController extends Controller
{
    public function index()
    {
        return $this->collectedResponse(collect(config('laravel-vue-forms.entity_type_options'))
            ->map(function($option, $key) {
                return new GenericOption([
                    'name' => $key,
                    'title' => $option
                ]);
            })->toArray());
    }
}
