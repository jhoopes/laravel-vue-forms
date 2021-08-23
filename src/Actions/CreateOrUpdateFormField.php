<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use jhoopes\LaravelVueForms\DTOs\FormFieldDTO;
use jhoopes\LaravelVueForms\DTOs\FormUpdateOrCreateDTO;
use jhoopes\LaravelVueForms\Models\FormField;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class CreateOrUpdateFormField
{

    public function __construct(
        public ProcessFormUpdateOrCreate $processFormUpdateOrCreate
    ) {}

    public function execute(FormFieldDTO|FormUpdateOrCreateDTO $formFieldDTO): FormField
    {
        if($formFieldDTO instanceof FormUpdateOrCreateDTO) {
            $processedForm = $this->processFormUpdateOrCreate->execute($formFieldDTO);
            $formField = $processedForm->entity;
        } else {
            $formField = $this->createOrUpdate($formFieldDTO);
        }

        return $formField;
    }

    public function createOrUpdate(FormFieldDTO $ffDTO): FormField
    {
        return LaravelVueForms::model('form_field')->updateOrCreate([
            'name' => $ffDTO->name
        ], [
            'name'        => $ffDTO->name,
            'value_field' => $ffDTO->value_field,
            'label'       => $ffDTO->label,
            'widget'      => $ffDTO->widget,
            'visible'     => $ffDTO->visible,
            'disabled'    => $ffDTO->disabled,
            'is_eav'      => $ffDTO->is_eav,
            'parent_id'   => $ffDTO->parent_id,
            'cast_to'     => $ffDTO->cast_to,
            'field_extra' => $ffDTO->field_extra
        ]);
    }

}
