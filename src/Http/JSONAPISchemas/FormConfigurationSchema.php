<?php

namespace jhoopes\LaravelVueForms\Http\JSONAPISchemas;


use Neomerx\JsonApi\Schema\BaseSchema;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class FormConfigurationSchema extends BaseSchema
{

    public function getType(): string
    {
        return 'form_configuration';
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
        if(in_array('fields', $context->getIncludePaths(), true)) {
            $relationships['fields'] = [
                self::RELATIONSHIP_DATA => $resource->fields
            ];
        }

        return $relationships;
    }

}
