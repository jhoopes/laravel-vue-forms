<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

use jhoopes\LaravelVueForms\Models\FormConfiguration;
use jhoopes\LaravelVueForms\Models\FormField;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class SetFormConfigFieldOrderDTO
{


    public function __construct(
        public FormConfiguration $formConfiguration,
        public FormField $formField,
        public int $order
    ) {}


    public static function fromNewField(FormConfiguration|int $formConfiguration, FormField|int $formField, int $order): self
    {
        if(is_int($formConfiguration)) {
            $formConfiguration = LaravelVueForms::model('form_configuration')
                ->newQuery()
                ->findOrFail($formConfiguration);
        }

        if(is_int($formField)) {
            $formField = \LaravelVueForms::model('form_field')
                ->newQuery()
                ->findOrFail($formField);
        }

        return new self(
            formConfiguration: $formConfiguration,
            formField: $formField,
            order: $order
        );

    }

}
