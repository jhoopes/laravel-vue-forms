<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api\Admin\Entities;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use jhoopes\LaravelVueForms\Models\GenericOption;
use jhoopes\LaravelVueForms\App\Controllers\Controller;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class GetEntityTypesController extends Controller
{
    public function index(Request $request): \Illuminate\Http\Response|\Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Contracts\Foundation\Application|\Illuminate\Pagination\AbstractPaginator
    {
        $request->validate([
            'name' => [
                'sometimes',
                'string'
            ],
            'type' => [
                'sometimes',
                'string',
                Rule::in('model', 'custom')
            ],
            'pp' => [
                'sometimes',
                'numeric',
                'min:0'
            ]
        ]);

        $query = LaravelVueForms::model('entity_type')->newQuery();

        if($request->has('name')) {
            $query->where('name', $request->get('name'));
        }

        if($request->has('type')) {
            $query->where('type', $request->get('type'));
        }

        $pp = $request->get('pp', 20);
        if($pp === 0) {
            return $this->collectedResponse($query->all());
        }

        return $this->collectedResponse($query->paginate($pp));
    }

}
