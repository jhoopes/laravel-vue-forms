<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use jhoopes\LaravelVueForms\DTOs\SetFormConfigFieldOrderDTO;

class UpdateFormFieldOrderOnFormConfiguration
{
    public function execute(SetFormConfigFieldOrderDTO $configFieldOrderDTO): void
    {
        if($configFieldOrderDTO->formConfiguration
            ->fields()
            ->where('form_fields.id', $configFieldOrderDTO->formField->id)
            ->count() > 0
        ) {

            $configFieldOrderDTO->formConfiguration->fields()->updateExistingPivot($configFieldOrderDTO->formField->id, [
                'order' => $configFieldOrderDTO->order
            ]);

        }else {
            $configFieldOrderDTO->formConfiguration->fields()->attach($configFieldOrderDTO->formField, [
                'order' => $configFieldOrderDTO->order
            ]);
        }
    }

}
