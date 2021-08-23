<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rule;
use jhoopes\LaravelVueForms\Actions\DeleteEntity;
use jhoopes\LaravelVueForms\Actions\ProcessFormUpdateOrCreate;
use jhoopes\LaravelVueForms\App\Controllers\Controller;
use jhoopes\LaravelVueForms\DTOs\DeleteEntityDTO;
use jhoopes\LaravelVueForms\DTOs\FormUpdateOrCreateDTO;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class EntitiesController extends Controller
{
    public function __construct(
        public ProcessFormUpdateOrCreate $processFormUpdateOrCreate,
        public DeleteEntity $deleteEntity
    ){}

    public function index(Request $request, $entityType)
    {
        $request->request->set('entityType', $entityType);
        $request->validate([
            'pp' => [
                'sometimes',
                'numerical',
                'min:0'
            ],
            'entityType' => [
                'required',
                'string',
                Rule::exists('entity_types', 'name')
            ]
        ]);

        $entityType = LaravelVueForms::model('entity_type')
            ->newQuery()
            ->where('name', $entityType)
            ->firstOrFail();

        $entityQuery = LaravelVueForms::model('entity')
            ->newQuery()
            ->where('entity_type_id', $entityType->id);

        $pp = $request->get('pp', 20);
        if($pp === 0) {
            return $this->collectedResponse($entityQuery->get());
        }

        return $this->paginatedResponse($entityQuery->paginate($pp));
    }

    public function create(Request $request)
    {
        $rules = [
            'formConfigurationId' => 'required|integer',
            'data'                => 'required|array'
        ];

        $this->validate($request, $rules);

        $resourceDTO = $this->processFormUpdateOrCreate->execute(
            FormUpdateOrCreateDTO::fromFormApi(
                formConfigurationId: $request->get('formConfigurationId'),
                data: $request->get('data'),
            )
        );

        return $this->resourceResponse($resourceDTO->entity);

    }

    public function show($entityType, $entityId)
    {
        $entityType = LaravelVueForms::model('entity_type')
            ->where('name', $entityType)
            ->firstOrFail();

        return $this->resourceResponse(LaravelVueForms::model('entity')
            ->where('entity_type_id', $entityType->id)
            ->where('id', $entityId)
            ->with('files')
            ->firstOrFail(), [], ['files']);
    }

    public function update(Request $request)
    {
        $rules = [
            'formConfigurationId' => 'required|integer',
            'data'                => 'required|array'
        ];

        if($request->method() === 'PATCH') {
            $rules['entityId'] = 'required|integer';
        }
        $this->validate($request, $rules);

        $resourceDTO = $this->processFormUpdateOrCreate->execute(
            FormUpdateOrCreateDTO::fromFormApi(
                formConfigurationId: $request->get('formConfigurationId'),
                data: $request->get('data'),
                entityId: $request->get('entityId'),
            )
        );

        return $this->resourceResponse($resourceDTO->entity);
    }

    public function delete($entityType, $entityId)
    {
        $entityType = LaravelVueForms::model('entity_type')
            ->where('name', $entityType)
            ->firstOrFail();

        if(!Arr::get($entityType->entity_configuration, 'allowDelete', true) ) {
            throw new \Exception('This entity is not currently allowed to be deleted');
        }

        $this->deleteEntity->execute(DeleteEntityDTO::fromEntityDeleteApi(
            $entityType,
            (int) $entityId
        ));

        return $this->infoResponse([
            'success' => true,
            'message' => 'Successfully deleted entity'
        ]);
    }
}
