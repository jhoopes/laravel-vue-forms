<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

use Illuminate\Database\Eloquent\Model;
use jhoopes\LaravelVueForms\Models\FormConfiguration;

class ValidateDataForFormDTO
{
    public function __construct(
        public FormConfiguration $formConfiguration,
        public array $unValidatedData,
        public Model $entityModel,
        public array $params = [],
        public bool $defaultData = true
    ){}

    public static function fromForm(
        FormConfiguration $formConfiguration,
        array $unValidatedData,
        Model $entityModel,
        array $params = [],
        bool $defaultData = true
    ): self {
        return new self(
            formConfiguration: $formConfiguration,
            unValidatedData: $unValidatedData,
            entityModel: $entityModel,
            params: $params,
            defaultData: $defaultData
        );
    }

}
