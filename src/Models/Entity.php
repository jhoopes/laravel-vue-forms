<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use jhoopes\LaravelVueForms\Models\Helpers\EntityHasFiles;
use jhoopes\LaravelVueForms\Models\Helpers\HasCustomAttributes;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class Entity extends Model
{
    use HasCustomAttributes, EntityHasFiles, SoftDeletes;

    protected $fillable = [
        'entity_type_id'
    ];

    public function entity_type()
    {
        return $this->belongsTo(LaravelVueForms::getModels()['entity_type']);
    }

}
