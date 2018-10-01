<?php

namespace jhoopes\LaravelVueForms\Models;

use Illuminate\Database\Eloquent\Model;

class FormField extends Model
{

    public $casts = [
        'field_extra' => 'array'
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
        'field_extra'
    ];

    public function forms()
    {
        return $this->belongsToMany(FormConfiguration::class)
            ->withPivot(['order']);
    }

}