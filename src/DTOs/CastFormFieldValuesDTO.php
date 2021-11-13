<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

use Illuminate\Support\Collection;

class CastFormFieldValuesDTO
{

    public function __construct(
        public Collection $data,
        public Collection $formFields
    ){}

    public static function fromProcessFormAction(array|Collection $data, Collection $formFields): self
    {
        return new self(
            data: collect($data),
            formFields:  $formFields
        );
    }

}
