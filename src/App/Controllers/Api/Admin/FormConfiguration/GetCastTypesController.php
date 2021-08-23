<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Models\GenericOption;
use jhoopes\LaravelVueForms\App\Controllers\Controller;

class GetCastTypesController extends Controller
{
    public function index()
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
