<?php

namespace jhoopes\LaravelVueForms;

use Illuminate\Database\Eloquent\Model;

abstract class ValidationRule
{

    /** @var Model */
    protected $entityModel;

    /**
     * Optionally provide a current id to not check the specific record for unique
     *
     * @param $currentId
     */
    public function __construct($entityModel)
    {
        $this->entityModel = $entityModel;
    }


}
