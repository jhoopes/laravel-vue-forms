<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models;

use Illuminate\Database\Eloquent\Casts\AsArrayObject;
use Illuminate\Database\Eloquent\Model;

class EntityFile extends Model
{

    protected $fillable = [
        'id',
        'fileable_type',
        'fileable_id',
        'collection_type',
        'file_name',
        'disk',
        'file_path',
        'mime_type',
        'file_size',
        'thumbnail',
        'generated_conversions',
        'order'
    ];

    protected $casts = [
        'generated_conversions' => AsArrayObject::class,
    ];


}
