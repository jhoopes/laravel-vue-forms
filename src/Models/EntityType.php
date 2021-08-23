<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use jhoopes\LaravelVueForms\Models\Helpers\HasCustomAttributes;

class EntityType extends Model
{
    use SoftDeletes, HasCustomAttributes;

    protected $fillable = [
        'id',
        'name',
        'title',
        'type',
        'built_in_type',
        'entity_configuration',
        'default_form_configuration_id'
    ];

    protected $casts = [
        'entity_configuration' => 'array'
    ];

}
