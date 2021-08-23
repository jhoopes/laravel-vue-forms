<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use jhoopes\LaravelVueForms\Models\GenericOption;
use jhoopes\LaravelVueForms\App\Controllers\Controller;

class GetWidgetTypesController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'type' => [
                'sometimes',
                'string',
                Rule::in(['normal', 'structural'])
            ]
        ]);

        $widgetTypes = collect(config('laravel-vue-forms.widget_types'));

        if($request->has('type')) {
            switch($request->get('type')) {
                case 'structural':
                    $widgetTypes = $widgetTypes->where('structural', true);
                    break;
                default:
                    $widgetTypes = $widgetTypes->filter(function($widgetType) {
                       return empty($widgetType['structural']) || $widgetType['structural'] === false;
                    });
            }
        }

        return  $this->collectedResponse($widgetTypes
            ->map(function($option, $value) {
                return new GenericOption([
                    'value' => $value,
                    'title' => $option['name'],
                    'structural' => isset($option['structural']) ?? false,
                ]);
            })->toArray());
    }
}
