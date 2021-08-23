<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Controllers;

use Neomerx\JsonApi\Encoder\Encoder;
use Neomerx\JsonApi\Http\Headers\MediaType;
use Illuminate\Pagination\AbstractPaginator;
use Illuminate\Foundation\Bus\DispatchesJobs;
use jhoopes\LaravelVueForms\Models\GenericOption;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use jhoopes\LaravelVueForms\Models\JSONAPISchemas\GenericOptionSchema;


class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected static $schemas = [
        'entity' => \jhoopes\LaravelVueForms\Models\JSONAPISchemas\EntitySchema::class,
        'entity_file' => \jhoopes\LaravelVueForms\Models\JSONAPISchemas\EntityFileSchema::class,
        'entity_type' => \jhoopes\LaravelVueForms\Models\JSONAPISchemas\EntityTypeSchema::class,
        'form_configuration' => \jhoopes\LaravelVueForms\Models\JSONAPISchemas\FormConfigurationSchema::class,
        'form_field' => \jhoopes\LaravelVueForms\Models\JSONAPISchemas\FormFieldSchema::class,
    ];

    protected function authorizeAdminRequest()
    {
        if(LaravelVueForms::adminAuthorization()) {
            $this->authorize(LaravelVueForms::adminAuthorization());
        }
    }

    protected function infoResponse($meta)
    {
        if(!LaravelVueForms::useJSONApi()) {
            return $meta;
        }

        $response = Encoder::instance(self::getSchemas())
            ->encodeMeta($meta);

        return response($response, 200)
            ->withHeaders([
                'Content-Type' => MediaType::JSON_API_MEDIA_TYPE
            ]);
    }

    protected function resourceResponse($resource, $meta = [], $includes = [])
    {
        if(!LaravelVueForms::useJSONApi()) {
            return $resource;
        }

        $response = Encoder::instance(self::getSchemas())
            ->withIncludedPaths($includes)
            ->withMeta($meta)
            ->encodeData($resource);

        return response($response, 200)
            ->withHeaders([
                'Content-Type' => MediaType::JSON_API_MEDIA_TYPE
            ]);
    }

    protected function collectedResponse($collectedResource, $meta = [], $includes = [])
    {
        if(!LaravelVueForms::useJSONApi()) {
            return $collectedResource;
        }

        if($collectedResource instanceof AbstractPaginator) {
            return $this->paginatedResponse($collectedResource, $meta, $includes);
        }

        $response = Encoder::instance(self::getSchemas())
            ->withIncludedPaths($includes)
            ->withMeta($meta)
            ->encodeData($collectedResource);

        return response($response, 200)
            ->withHeaders([
                'Content-Type' => MediaType::JSON_API_MEDIA_TYPE
            ]);
    }

    protected function paginatedResponse(AbstractPaginator $paginatedResource, $meta = [], $includes = [])
    {
        if(!LaravelVueForms::useJSONApi()) {
            return $paginatedResource;
        }

        $meta = array_merge([
            'current_page' => $paginatedResource->currentPage(),
            'from'         => $paginatedResource->firstItem(),
            'to'           => $paginatedResource->lastItem(),
            'per_page'     => $paginatedResource->perPage(),
            'last_page'    => $paginatedResource->lastPage(),
            'total'        => $paginatedResource->total()
        ], $meta);

        $response = Encoder::instance(self::getSchemas())
            ->withIncludedPaths($includes)
            ->withMeta($meta)
            ->encodeData($paginatedResource->items());

        return response($response, 200)
            ->withHeaders([
                'Content-Type' => MediaType::JSON_API_MEDIA_TYPE
            ]);
    }

    /**
     * @return string[]
     */
    public static function getSchemas(): array
    {
        $encoderSchemas = [];
        foreach(self::$schemas as $key => $schema) {
            $modelClass = get_class(LaravelVueForms::model($key));
            $encoderSchemas[$modelClass] = $schema;
        }
        $encoderSchemas[GenericOption::class] = GenericOptionSchema::class;

        return $encoderSchemas;
    }
}
