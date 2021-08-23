<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api\Admin\FormConfiguration;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;
use jhoopes\LaravelVueForms\App\Controllers\Controller;

class FormConfigurationsController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeAdminRequest();
        $request->validate([
            'pp' => [
                'nullable',
                'integer',
            ],
            'q' => [
                'sometimes',
                'string'
            ],
            'name' => [
                'sometimes',
                'string'
            ],
            'selected' => [
                'sometimes',
                'nullable',
                'numeric'
            ]
        ]);

        $query = LaravelVueForms::model('form_configuration')
            ->setEagerLoads([]);

        if($request->has('q') && $request->get('q') !== null) {
            $query->where('name', 'like', '%' . $request->get('q') . '%');
        }

        if($request->has('name') && $request->get('name') !== null) {
            $query->where('name', 'like', '%' . $request->get('name') . '%');
        }

        if(!config('laravel-vue-forms.edit_system_forms')) {
            $query->where(function(Builder $query) {
                $query->where('type', '!=', 'system')
                    ->orWhereNull('type');
            });
        }

        $selectedFormConfig = null;
        if($request->has('selected') && $request->get('selected') !== null) {
            $selectedFormConfig = \LaravelVueForms::model('form_configuration')
                ->findOrFail($request->get('selected'));
        }


        $pp = $request->get('pp', 20);
        if($pp === 0) {
            $results = $query->get();
            if($selectedFormConfig) {
                $results->push($selectedFormConfig);
            }

            return $this->collectedResponse($results);
        }

        /** @var LengthAwarePaginator $results */
        $results = $query->paginate($pp);
        if($selectedFormConfig) {
            $results = new LengthAwarePaginator(
                array_merge($results->items(), [$selectedFormConfig]),
                $results->total(),
                $results->perPage(),
                $results->currentPage()
            );
        }

        return $this->collectedResponse($results);
    }
}
