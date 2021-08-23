<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models;

use Illuminate\Database\Eloquent\Model;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

class FormConfiguration extends Model
{

    protected $with = ['fields'];

    protected $fillable = [
        'name',
        'type',
        'active',
        'entity_type_id',
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

    public function entity_type()
    {
        return $this->belongsTo(LaravelVueForms::getModels()['entity_type']);
    }

}
