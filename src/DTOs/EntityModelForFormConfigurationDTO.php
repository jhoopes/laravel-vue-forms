<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

use jhoopes\LaravelVueForms\Models\FormConfiguration;

class EntityModelForFormConfigurationDTO
{
    public function __construct(
        public FormConfiguration $formConfiguration,
        public ?int $entityId
    ){}

    public static function fromFormProcessing(
        FormConfiguration $formConfiguration,
        int $entityId = null
    ): self
    {
       return new self($formConfiguration, $entityId);
    }

}
