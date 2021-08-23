<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

use Illuminate\Http\Request;

class EntityFilesDTO
{
    public function __construct(
        public ?int $entityTypeId,
        public ?string $fileableType,
        public ?int $fileableId,
        public array $files,
        public string $collectionType
    ) {}


    public static function fromRequest(Request $request)
    {
        $files = $request->file('file');
        if(!is_array($request->file('file'))) {
            $files = [$files];
        }

        return new self(
            entityTypeId: (int) $request->get('entity_type_id'),
            fileableType: (string)$request->get('fileable_type'),
            fileableId: (int)$request->get('fileable_id'),
            files: $files,
            collectionType: (string)$request->get('collectionType')
        );
    }

}
