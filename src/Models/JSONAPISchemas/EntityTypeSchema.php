<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models\JSONAPISchemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\BaseSchema;

class EntityTypeSchema extends BaseSchema
{

    public function getType(): string
    {
        return 'entity_type';
    }

    public function getId($resource): ?string
    {
        return (string) $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return $resource->toArray();
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }

}
