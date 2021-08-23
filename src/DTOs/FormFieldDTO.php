<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

class FormFieldDTO
{
    public function __construct(
        public string $name,
        public string $value_field,
        public string $label,
        public string $widget,
        public bool $visible,
        public bool $disabled,
        public bool $is_eav,
        public ?int $parent_id,
        public ?string $cast_to,
        public array $field_extra
    ) {}
}
