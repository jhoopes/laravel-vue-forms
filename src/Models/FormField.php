<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{

    public $casts = [
        'field_extra' => 'array',
        'visible' => 'boolean',
        'disabled' => 'boolean',
        'is_eav' => 'boolean',
    ];

    protected $fillable = [
        'name',
        'value_field',
        'label',
        'widget',
        'visible',
        'disabled',
        'is_eav',
        'parent_id',
        'cast_to',
        'field_extra'
    ];

    public function forms()
    {
        return $this->belongsToMany(FormConfiguration::class)
            ->withPivot(['order']);
    }

}
