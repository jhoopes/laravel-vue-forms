<?php

namespace jhoopes\LaravelVueForms\Models\Helpers;

use Carbon\Carbon;
use jhoopes\LaravelVueForms\Models\FormValue;

trait HasValues
{
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes = []);
        array_unshift($this->with, 'eav_values');
    }

    public static function bootHasValues()
    {
        self::deleting(function($item) {

            if(config('laravel-vue-forms.uses_soft_delete')) {
                $this->eav_values()->update(['deleted_at' => Carbon::now()]);
            } else {
                $this->eav_values()->delete();
            }

        });
    }


    public function eav_values()
    {
        return $this->hasMany(FormValue::class, 'entity_id', 'id')
            ->where('entity_type', self::class)
            ->with('form_field');
    }

    public function __get($name)
    {

        if($name !== 'eav_values' &&
            $this->eav_values !== null &&
            $eavValue = $this->eav_values->filter(function($eavValue) use($name) {
                return $eavValue->form_field->value_field === $name;
        })->first() ) {
            return $eavValue->value;
        }

        return parent::__get($name);
    }

}