<?php

namespace jhoopes\LaravelVueForms\Http\JSONAPISchemas;

use Neomerx\JsonApi\Schema\BaseSchema;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class FormFieldSchema extends BaseSchema
{

    public function getType(): string
    {
        return 'form_field';
    }

    public function getId($resource): ?string
    {
        return $resource->getKey();
    }

    public function getAttributes($resource, ContextInterface $context): iterable
    {
        return $resource->toArray();
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        return $relationships;
    }

}
