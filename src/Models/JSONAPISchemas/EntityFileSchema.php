<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models\JSONAPISchemas;

use Neomerx\JsonApi\Schema\BaseSchema;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class EntityFileSchema extends BaseSchema
{

    public function getType(): string
    {
        return 'entity_file';
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
