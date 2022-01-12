<?php

namespace jhoopes\LaravelVueForms\Models\Helpers;

use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;
use jhoopes\LaravelVueForms\Models\FormValue;

trait HasValues
{
    protected $eavChanges = [
        'old' => [],
        'new' => [],
    ];

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
            if (config('laravel-vue-forms.uses_soft_delete')) {
                $item->eav_values()->update(['deleted_at' => Carbon::now()]);
            } else {
                $item->eav_values()->delete();
            }
        });
    }

    public function getEAVChanges($type = null, $field = null)
    {
        if($type !== null && $field === null) {
            return Arr::get($this->eavChanges, $type . '.' . $field);
        } else if ($type !== null) {
            return Arr::get($this->eavChanges, $type);
        } else {
            return $this->eavChanges;
        }
    }

    /**
     * OVERRIDE of Laravel's HasChanges Method to also check for EAV changes
     *
     * @param  array  $changes
     * @param  array|string|null  $attributes
     * @return bool
     */
    protected function hasChanges($changes, $attributes = null)
    {
        if (parent::hasChanges($changes, $attributes)) {
            return true;
        }else if(empty($attributes)) {
            // if no attributes and parent model has no changes return count of eavChanges
            return count($this->eavChanges['new']) > 0;
        }

        // finally check for changes in the attributes passed
        return collect(Arr::wrap($attributes))->filter(function($attribute) {
            return Arr::exists($this->eavChanges['new'], $attribute);
        })->count() > 0;
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
        if ($name !== 'eav_values' &&
            $this->eav_values !== null &&
            $eavValue = $this->eav_values->filter(function($eavValue) use ($name) {
                $valueFieldParts = explode('.', $eavValue->form_field->value_field);
                return end($valueFieldParts) === $name;
            })->first()) {
            return $eavValue->value;
        }

        return parent::__get($name);
    }

    public function __isset($name)
    {
        if ($name !== 'eav_values' &&
            $this->eav_values !== null &&
            $eavValue = $this->eav_values->filter(function($eavValue) use ($name) {
                return $eavValue->form_field->value_field === $name;
            })->first()) {
            return ! is_null($eavValue->value);
        }

        return parent::__isset($name);
    }

    public function attributesToArray()
    {
        $normalAttrs = parent::attributesToArray();
        $eavAttrs = [];
        if ($this->eav_values->count() > 0) {
            $this->eav_values->each(function($eavValue) use (&$eavAttrs) {
                $valueField = $eavValue->form_field->value_field;
                if (str_contains($eavValue->form_field->value_field, '.')) {
                    // TODO: currently EAV functionality only supports a single has one parameter, when support is added for has many, many to many, this will need to change
                    $valueField = explode('.', $eavValue->form_field->value_field)[1];
                }

                $eavAttrs[$valueField] = $eavValue->value;
            });
        }

        return array_merge($normalAttrs, $eavAttrs);
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
        // instead of updating or creating on the relationship through queries, we should really update the existing
        // form value record so that the currently loaded model's relationship that is cached will be updated as well
        $eavValue = $this->eav_values
            ->where('form_field_id', $field->id)
            ->first();

        // if the FormValue record for this field hasn't been created, create it
        if (!$eavValue) {
            $oldValue = '';
            $newValue = $this->eav_values()->create([
                'form_field_id' => $field->id,
                'entity_type' => self::class,
                'value' => $value
            ]);
            // push the new value onto our already loaded relationship
            $this->eav_values->push($newValue);
        } else {
            $oldValue = $eavValue->value;
            $eavValue->value = $value;
            $eavValue->save();
        }


        if ($oldValue !== $value) {
            $this->eavChanges['old'][$field->value_field] = $oldValue;
            $this->eavChanges['new'][$field->value_field] = $value;
        }
    }
}
