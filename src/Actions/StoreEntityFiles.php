<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use jhoopes\LaravelVueForms\DTOs\EntityFilesDTO;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class StoreEntityFiles
{
    public function execute(EntityFilesDTO $entityFilesDTO): Collection
    {
        $fileable = $this->getFileable($entityFilesDTO);

        return collect($entityFilesDTO->files)->map(function(UploadedFile $file) use($fileable, $entityFilesDTO) {

            $filePath = $fileable->getBasePath();
            $fileRecord = LaravelVueForms::model('entity_file')->fill([
                'fileable_type' => get_class($fileable),
                'fileable_id' => $fileable->getAttribute($fileable->getKeyName()),
                'collection_type' => $entityFilesDTO->collectionType,
                'disk' => $fileable->getDisk(),
                'file_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
                'file_path' => $filePath
            ]);

            $fileRecord->file_path = $file->store($filePath, $fileable->getDisk());
            $fileRecord->save();
            return $fileRecord;
        });
    }

    protected function getFileable(EntityFilesDTO $entityFilesDTO): Model
    {
        if($entityFilesDTO->entityTypeId) {
            $entityType = LaravelVueForms::model('entity_type')
                ->findOrFail($entityFilesDTO->entityTypeId);

            if($entityType->type === 'custom') {
                $fileableClass = get_class(LaravelVueForms::model('entity'));
            } else {
                $fileableClass = $entityType->built_in_type;
            }

            return app($fileableClass)
                ->findOrFail($entityFilesDTO->fileableId);

        }

        return app($entityFilesDTO->fileableType)
            ->findOrFail($entityFilesDTO->fileableId);
    }

    protected function makeFileRecord(Model $fileable, EntityFilesDTO $entityFilesDTO)
    {
        return LaravelVueForms::model('entity_file')->fill([
            'fileable_type' => get_class($fileable),
            'fileable_id' => $fileable->getAttribute($fileable->getKeyName()),
            'collection_type' => $entityFilesDTO->collectionType,
            'disk' => $fileable->getDisk(),
        ]);
    }
}
