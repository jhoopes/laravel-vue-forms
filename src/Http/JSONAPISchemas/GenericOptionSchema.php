<?php

namespace jhoopes\LaravelVueForms\Http\JSONAPISchemas;

use Illuminate\Support\Collection;
use Neomerx\JsonApi\Schema\BaseSchema;
use Illuminate\Database\Eloquent\Model;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class GenericOptionSchema extends BaseSchema
{

    public function getType(): string
    {
        return 'generic';
    }

    public function getId($resource): ?string
    {
        return $resource->id;
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
