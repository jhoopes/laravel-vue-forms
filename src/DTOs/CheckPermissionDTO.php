<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

use Illuminate\Database\Eloquent\Model;

class CheckPermissionDTO
{

    public function __construct(
        public string $action,
        public Model $entityModel
    ) {}

    public static function fromForm(string $action, Model $model): self
    {
        return new self(
            action: $action,
            entityModel: $model
        );
    }

}
