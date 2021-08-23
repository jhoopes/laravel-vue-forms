<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models\Helpers;

use jhoopes\LaravelVueForms\Models\Entity;
use jhoopes\LaravelVueForms\Support\Facades\LaravelVueForms;

trait EntityHasFiles
{
    public function getDisk(): string
    {
        return 'local';
    }

    public function getBasePath(): string
    {
        if(self::class === Entity::class) {
            return $this->entity_type->name . '/' . $this->getAttribute($this->getKeyName());
        }

        return get_class(self::class) . '/' . $this->getAttribute($this->getKeyName());
    }

    public function files()
    {
        return $this->morphMany(
            get_class(LaravelVueForms::model('entity_file')),
            'fileable'
        );
    }



}
