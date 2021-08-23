<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models\JSONAPISchemas;

use Neomerx\JsonApi\Contracts\Schema\ContextInterface;
use Neomerx\JsonApi\Schema\BaseSchema;

class GenericOptionSchema extends BaseSchema
{

    public function getType(): string
    {
        return 'generic';
    }

    public function getId($resource): ?string
    {
        return (string) $resource->id;
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return $resource->getAttributes();
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        return [];
    }

}
