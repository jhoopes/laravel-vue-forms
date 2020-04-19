<?php

namespace jhoopes\LaravelVueForms\Models;

use Illuminate\Database\Eloquent\Model;

class FormConfiguration extends Model
{

    protected $with = ['fields'];

    protected $fillable = [
        'name',
        'type',
        'active',
        'entity_name',
        'entity_model',
        'options'
    ];

    protected $casts = [
        'active' => 'boolean',
        'options' => 'array',
    ];

    public function fields()
    {
        return $this->belongsToMany(FormField::class)
            ->orderBy('form_configuration_form_field.order')
            ->withPivot('order');
    }

}
