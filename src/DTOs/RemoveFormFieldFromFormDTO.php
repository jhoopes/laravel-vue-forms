<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

use jhoopes\LaravelVueForms\Models\FormConfiguration;
use jhoopes\LaravelVueForms\Models\FormField;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class RemoveFormFieldFromFormDTO
{
    public function __construct(
        public FormConfiguration $formConfiguration,
        public FormField $formField,
    ){}


    public static function fromFormAdminRequest(FormConfiguration|int $formConfiguration, FormField|int $formField): self
    {
        if(is_int($formConfiguration)) {
            $formConfiguration = LaravelVueForms::model('form_configuration')
                ->newQuery()
                ->findOrFail($formConfiguration);
        }

        if(is_int($formField)) {
            $formField = $formConfiguration->fields()->findOrFail($formField);
        }

        return new self(formConfiguration: $formConfiguration,formField: $formField);
    }

}
