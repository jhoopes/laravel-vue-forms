<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Events\Dispatcher;
use jhoopes\LaravelVueForms\DTOs\CheckPermissionDTO;
use jhoopes\LaravelVueForms\DTOs\EntityModelForFormConfigurationDTO;
use jhoopes\LaravelVueForms\DTOs\FormProcessedDTO;
use jhoopes\LaravelVueForms\DTOs\FormUpdateOrCreateDTO;
use jhoopes\LaravelVueForms\DTOs\ValidateDataForFormDTO;
use jhoopes\LaravelVueForms\Models\FormConfiguration;
use jhoopes\LaravelVueForms\Models\Helpers\FormAction;
use jhoopes\LaravelVueForms\Support\Events\EntitySaved;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class ProcessFormUpdateOrCreate
{
    use FormAction;

    public function __construct(
        public Application $application,
        public GetEntityModelForFormConfig $entityModelForFormConfig,
        public CheckPermissionForEntityModel $checkPermissions,
        public ValidateDataForForm $validateDataForForm,
        public Dispatcher $eventDispatcher
    ) {}


    public function execute(FormUpdateOrCreateDTO $formUpdateOrCreateDTO): FormProcessedDTO
    {
        if($formUpdateOrCreateDTO->formConfigurationId instanceof FormConfiguration) {
            $formConfiguration = $formUpdateOrCreateDTO->formConfigurationId;
        }else {
            $formConfiguration = LaravelVueForms::model('form_configuration')
                ->findOrFail($formUpdateOrCreateDTO->formConfigurationId);
        }


        if($formUpdateOrCreateDTO->entityId !== null) {
            $action = 'update';
            $entityModel = $this->entityModelForFormConfig->execute(
                EntityModelForFormConfigurationDTO::fromFormProcessing(
                    $formConfiguration,
                    $formUpdateOrCreateDTO->entityId
                )
            );
        } else {
            $action = 'create';
            $entityModel = $this->entityModelForFormConfig->execute(
                EntityModelForFormConfigurationDTO::fromFormProcessing($formConfiguration)
            );
        }

        if(config('laravel-vue-forms.check_permissions')) {
            $hasPermission = $this->checkPermissions->execute(CheckPermissionDTO::fromForm(
                $action,
                $entityModel
            ));

            if(!$hasPermission) {
                throw new AuthorizationException('Invalid access to save entity.');
            }

        }

        $validData = $formUpdateOrCreateDTO->data;
        if($formUpdateOrCreateDTO->validateData) {
            $validData = $this->validateDataForForm->execute(
                ValidateDataForFormDTO::fromForm(
                    formConfiguration: $formConfiguration,
                    unValidatedData: $formUpdateOrCreateDTO->data,
                    entityModel: $entityModel,
                    params: $formUpdateOrCreateDTO->validationParams,
                    defaultData: $formUpdateOrCreateDTO->defaultData
                )
            );
        }


        $processedDTO = new FormProcessedDTO($formConfiguration, $validData);

        if(!$formUpdateOrCreateDTO->persistData) {
            return $processedDTO;
        }

        $entity = $this->persistEntity($entityModel, $formConfiguration, $validData);
        $processedAction = ($action === 'create') ? 'created': 'updated';
        $this->eventDispatcher->dispatch(
            new EntitySaved($processedAction, $entity)
        );

        $processedDTO->entity = $entity;
        $processedDTO->processedAction = $processedAction;
        return $processedDTO;
    }

}
