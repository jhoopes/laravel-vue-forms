<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

use Illuminate\Database\Eloquent\Model;
use jhoopes\LaravelVueForms\Models\EntityType;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class DeleteEntityDTO
{
    public function __construct(
        public EntityType $entityType,
        public Model $entity
    ){}

    public static function fromEntityDeleteApi(EntityType | string $entityType, Model | int $entity): self
    {
        if(is_string($entityType)) {
            $entityType = LaravelVueForms::model('entity_type')
                ->where('name', $entityType)
                ->firstOrFail();
        }

        if(is_int($entity)) {
            $entity = LaravelVueForms::model('entity')
                ->where('entity_type_id', $entityType->id)
                ->where('id', $entity)
                ->firstOrFail();
        }

        return new self(
            entityType: $entityType,
            entity: $entity
        );
    }

}
