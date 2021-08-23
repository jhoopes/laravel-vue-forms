<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use jhoopes\LaravelVueForms\Actions\StoreEntityFiles;
use jhoopes\LaravelVueForms\App\Controllers\Controller;
use jhoopes\LaravelVueForms\DTOs\EntityFilesDTO;
use jhoopes\LaravelVueForms\Models\Helpers\EntityHasFiles;

class FilesController extends Controller
{
    public function __construct(
        public StoreEntityFiles $storeEntityFiles
    ) {}

    public function store(Request $request)
    {

        $rules = [
            'collectionType' => 'string',
        ];

        if($request->has('entity_type_id')) {
            $rules['entity_type_id'] = [
                'required',
                'integer',
                Rule::exists('entity_types', 'id')
            ];
        }else {
            $rules['fileable_type'] = [
                'required',
                'string',
                function($attribute, $value, $fail) {

                    if(!class_exists($value)) {
                        $fail('You must specify a valid fileable type');
                    }
                    $recursiveTraits = $this->getRecursiveTraits($value);
                    if(!in_array(EntityHasFiles::class, $recursiveTraits, true)) {
                        $fail('You must specify a valid fileable type');
                    }
                }
            ];

            $rules['fileable_id'] = [
                'required',
                'integer'
            ];
        }

        if (is_array($request->file('file'))) {
            $rules['file.*'] = 'file';
        } else {
            $rules['file'] = 'file';
        }
        $this->validate($request, $rules);

        $savedFiles = $this->storeEntityFiles->execute(EntityFilesDTO::fromRequest($request));

        if (count($savedFiles) == 1) {
            return [
                'type' => 'success',
                'msg' => 'File(s) successfully saved',
                'file' => $savedFiles
            ];
        } else {
            return [
                'type' => 'success',
                'msg' => 'File(s) successfully saved',
                'file' => $savedFiles
            ];
        }
    }



    protected function getRecursiveTraits($class): array
    {
        $reflection = new \ReflectionClass($class);
        $traits = array_keys($reflection->getTraits());

        foreach ($traits as $trait) {
            $traits = array_merge($traits, getRecursiveTraits($trait));
        }

        if ($parent = $reflection->getParentClass()) {
            $traits = array_merge($traits, static::getRecursiveTraits($parent->getName()));
        }

        return $traits;
    }
}
