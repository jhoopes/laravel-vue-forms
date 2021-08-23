<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

class DefaultFormForTypeDTO
{
    public function __construct(
        public ?string $type,
        public ?string $entityType,
        public bool $throwError = false
    ) {}


    public static function fromController($type = null, $entityType = null, $throwError = false)
    {
        return new self(
            type: $type,
            entityType: $entityType,
            throwError: $throwError
        );
    }
}
