<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models\JSONAPISchemas;

use Neomerx\JsonApi\Schema\BaseSchema;
use Neomerx\JsonApi\Contracts\Schema\ContextInterface;

class EntitySchema extends BaseSchema
{

    public function getType(): string
    {
        return 'entity';
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
        $relationships = [];
        if(in_array('files', $context->getIncludePaths(), true)) {
            $relationships['files'] = [
                self::RELATIONSHIP_DATA => $resource->files
            ];
        }

        return $relationships;
    }

}
