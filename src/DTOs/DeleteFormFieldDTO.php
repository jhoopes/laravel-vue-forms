<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

use jhoopes\LaravelVueForms\Models\FormField;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class DeleteFormFieldDTO
{
    public function __construct(
        public FormField $formField,
        public bool $syncForms = true
    ){}


    public static function fromRequest(FormField | int $formField, bool $syncForms = true): self
    {
        if(is_int($formField)) {
            $formField = LaravelVueForms::model('form_field')
                ->newQuery()
                ->findOrFail($formField);
        }

        return new self(
            formField: $formField,
            syncForms: $syncForms
        );
    }

}
