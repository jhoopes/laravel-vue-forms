<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api\Admin\Entities;

use Illuminate\Http\Request;
use jhoopes\LaravelVueForms\Actions\CreateOrUpdateEntityType;
use jhoopes\LaravelVueForms\App\Controllers\Controller;
use jhoopes\LaravelVueForms\DTOs\FormUpdateOrCreateDTO;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class EntityTypesController extends Controller
{

    public function __construct(
        public CreateOrUpdateEntityType $createOrUpdateEntityType
    ){}


    public function create(Request $request)
    {
        $formConfiguration = null;
        if($request->has('formConfigurationId')) {
            $this->validate($request, LaravelVueForms::getDefaultFormSubmissionValidationRules());
            $formConfiguration = $request->get('formConfigurationId');
            $data = $request->get('data');
        } else {

            $this->validate(
                $request,
                LaravelVueForms::getDefaultFormConfigurationValidationRule($formConfiguration, 'entity_type_form')
            );
            $data = $request->all();
        }

        $entityType = $this->createOrUpdateEntityType->execute(FormUpdateOrCreateDTO::fromFormApi(
            $formConfiguration,
            $data
        ));

        return $this->resourceResponse($entityType);
    }

    public function show(Request $request, $entityTypeId)
    {
        if($request->has('include') && is_string($request->get('include'))) {
            $request->request->set('include', [$request->get('include')]);
        }

        $request->validate([
            'include' => [
                'sometimes',
                'array'
            ],
            'include.*' => [
                'sometimes',
                'string'
            ]
        ]);

        $entityType = LaravelVueForms::model('entity_type')
            ->newQuery()
            ->with($request->get('include', []))
            ->findOrFail($entityTypeId);
        return $this->resourceResponse($entityType, [], $request->get('include', []));
    }

    public function update(Request $request, $entityTypeId)
    {
        $formConfiguration = null;
        $entityId = $entityTypeId;
        if($request->has('formConfigurationId')) {
            $this->validate($request, LaravelVueForms::getDefaultFormSubmissionValidationRules(true));
            $formConfiguration = $request->get('formConfigurationId');
            $data = $request->get('data');
            $entityId = $request->get('entityId');
        } else {

            $this->validate(
                $request,
                LaravelVueForms::getDefaultFormConfigurationValidationRule($formConfiguration, 'entity_type_form')
            );
            $data = $request->all();
        }

        $entityType = $this->createOrUpdateEntityType->execute(FormUpdateOrCreateDTO::fromFormApi(
            $formConfiguration,
            $data,
            $entityId
        ));

        return $this->resourceResponse($entityType);
    }

}
