<?php

namespace jhoopes\LaravelVueForms\Models\Helpers;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use jhoopes\LaravelVueForms\Models\FormValue;

trait HasValues
{
    /**
     * Set the eav_values eager load as the first item on the model's with in order to avoid n+1
     * and so that we don't get a loop with the __get function
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        array_unshift($this->with, 'eav_values');
    }

    /**
     * Boot Trait for delete EAV values when the model is being deleted
     */
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

    /**
     * Override of the getter function in order to check if the get of an attribute is an EAV value
     *
     * @param $name
     * @return mixed
     */
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

    /**
     * Save the EAV field value to the model
     * TODO: Eventually, it'd be nice to hijack the set method, so the model access on setting a value also works like __get
     *
     * @param Model $field
     * @param $value
     */
    public function setEAVValue(Model $field, $value)
    {
        $this->eav_values()->updateOrCreate([
            'form_field_id' => $field->id,
            'entity_type' => self::class,
            'entity_id' => $this->getKey(),
        ], [
            'value' => $value
        ]);
    }

}