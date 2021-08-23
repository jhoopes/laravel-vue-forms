<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use jhoopes\LaravelVueForms\DTOs\DefaultFormForTypeDTO;
use jhoopes\LaravelVueForms\Models\FormConfiguration;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class GetDefaultActiveFormForType
{
    public function execute(DefaultFormForTypeDTO $typeDTO): ?FormConfiguration
    {
        $formConfigurationsQuery = LaravelVueForms::model('form_configuration')
            ->newQuery()
            ->where('active', 1);

        if($typeDTO->type !== null) {
            $formConfigurationsQuery->where('type', $typeDTO->type);
        }

        if($typeDTO->entityType !== null) {

            $entityType = LaravelVueForms::model('entity_type')
                ->newQuery()
                ->where('name', $typeDTO->type)
                ->firstOrFail();

            if($entityType->default_form_configuration_id === null && $typeDTO->throwError) {
                throw new \Exception('Invalid entity type to get a default form configuration');
            } else if($entityType->default_form_configuration_id === null) {
                return null;
            }

            $formConfigurationsQuery->where('id', $entityType->default_form_configuration_id);
        }


        if($typeDTO->throwError && $formConfigurationsQuery->count() > 1) {
            throw new \Exception('Invalid form configuration and types. More than one active form for type was found');
        } elseif($formConfigurationsQuery->count() > 1 || $formConfigurationsQuery->count() === 0) {
            return null;
        }

        return $formConfigurationsQuery->first();
    }


}
