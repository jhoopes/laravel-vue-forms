<?php

namespace jhoopes\LaravelVueForms\Models;

use Illuminate\Database\Eloquent\Model;

class FormValue extends Model
{

    protected $fillable = [
        'form_field_id',
        'entity_id',
        'entity_type',
        'value'
    ];

    public function form_field()
    {
        return $this->belongsTo(FormField::class);
    }

}