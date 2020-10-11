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
        $attributes = $resource->toArray();
        // add field order if this schema is being loaded as included with a form configuration
        if($order = data_get($resource, 'pivot.order')) {
            unset($attributes['pivot']);
            $attributes['order'] = $order;
        }

        return $attributes;
    }

    public function getRelationships($resource, ContextInterface $context): iterable
    {
        $relationships = [];

        return $relationships;
    }

}
