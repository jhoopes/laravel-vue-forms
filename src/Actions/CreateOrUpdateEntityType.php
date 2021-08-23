<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use jhoopes\LaravelVueForms\DTOs\EntityTypeDTO;
use jhoopes\LaravelVueForms\DTOs\FormUpdateOrCreateDTO;
use jhoopes\LaravelVueForms\Models\EntityType;
use jhoopes\LaravelVueForms\Models\Helpers\FormAction;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class CreateOrUpdateEntityType
{

    public function __construct(
        public ProcessFormUpdateOrCreate $processFormUpdateOrCreate
    ) {}

    public function execute(EntityTypeDTO|FormUpdateOrCreateDTO $entityTypeDTO): EntityType
    {
        if($entityTypeDTO instanceof FormUpdateOrCreateDTO) {
            $processedForm = $this->processFormUpdateOrCreate->execute($entityTypeDTO);
            $entityType = $processedForm->entity;
        } else {
            $entityType = $this->createOrUpdate($entityTypeDTO);

        }

        return $entityType;
    }

    public function createOrUpdate(EntityTypeDTO $typeDTO): EntityType
    {
        return LaravelVueForms::model('entity_type')->updateOrCreate([
            'name' => $typeDTO->name
        ], [
            'name'                          => $typeDTO->name,
            'title'                         => $typeDTO->title,
            'type'                          => $typeDTO->type,
            'default_form_configuration_id' => $typeDTO->defaultFormConfigurationId,
            'entity_configuration'          => $typeDTO->entityConfiguration
        ]);
    }
}
