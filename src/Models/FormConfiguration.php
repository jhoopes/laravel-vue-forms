<?php

namespace jhoopes\LaravelVueForms\Models;

use Illuminate\Database\Eloquent\Model;

class FormConfiguration extends Model
{

    protected $with = ['fields'];

    protected $fillable = [
        'name',
        'entity_name',
        'entity_model'
    ];

    public function fields()
    {
        return $this->belongsToMany(FormField::class)->orderBy('form_configuration_form_field.order');
    }

}