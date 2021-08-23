<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

use jhoopes\LaravelVueForms\Models\FormConfiguration;

class FormUpdateOrCreateDTO
{
    public function __construct(
        public int|Form $formConfigurationId,
        public array $data,
        public ?int $entityId = null,
        public bool $validateData = true,
        public bool $defaultData = true,
        public bool $persistData = true,
        public array $validationParams = []
    ) {}

    public static function fromFormApi(
        int|FormConfiguration $formConfigurationId,
        array $data,
        int $entityId = null,
        bool $validateData = true,
        bool $defaultData = true,
        bool $persistData = true,
        array $validationParams = []
    ): self {
        return new self(
            formConfigurationId: $formConfigurationId,
            data: $data,
            entityId: $entityId,
            validateData: $validateData,
            defaultData: $defaultData,
            persistData: $persistData,
            validationParams: $validationParams
        );
    }
}
