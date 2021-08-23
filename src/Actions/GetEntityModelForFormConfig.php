<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Database\Eloquent\Model;
use jhoopes\LaravelVueForms\DTOs\EntityModelForFormConfigurationDTO;
use jhoopes\LaravelVueForms\Models\Entity;

class GetEntityModelForFormConfig
{

    public function __construct(
        public Application $application,
    ){}

    public function execute(EntityModelForFormConfigurationDTO $entityModelForFormConfigurationDTO): Model
    {

        if($entityModelForFormConfigurationDTO->formConfiguration->entity_type_id !== null) {

            $entityType = $entityModelForFormConfigurationDTO->formConfiguration->entity_type;

            // if using built in type, then return that type
            if($entityType->type === "model") {
                $entityModelClass = $this->application->make($entityType->built_in_type);
            } else {
                // make custom entity Class
                $entityModelClass = $this->application->make(Entity::class);

                // set the entity type id for new custom entities
                if($entityModelForFormConfigurationDTO->entityId === null) {
                    $entityModelClass->entity_type_id = $entityType->id;
                }

            }

        } else if($entityModelForFormConfigurationDTO->formConfiguration->entity_model === null) {
            throw new \Exception('Invalid form configuration to process for saving');
        } else {

            $entityModelClass =  $this->application
                ->make(
                    $entityModelForFormConfigurationDTO
                        ->formConfiguration
                        ->entity_model,
                    []
                );
        }

        if($entityModelForFormConfigurationDTO->entityId === null) {
            return $entityModelClass;
        }

        return $entityModelClass->findOrFail($entityModelForFormConfigurationDTO->entityId);
    }


}
