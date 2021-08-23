<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;


class EntityTypeDTO
{
    public function __construct(
        public string $name,
        public string $title,
        public string $type,
        public array $entityConfiguration = [],
        public ?int $defaultFormConfigurationId = null,
    ) {
    }

}
