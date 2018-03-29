<?php

namespace jhoopes\LaravelVueForms;

use Illuminate\Database\Eloquent\Model;

abstract class ValidationRule
{

    /** @var Model */
    protected $entityModel;

    protected $params;

    /**
     * Constructor for Form Configuration Custom Validation Rules
     * The entity model, if exists, will be passed in, along with
     * any parameters passed to the validation class
     *
     *
     * @param $entityModel
     * @param array $params
     */
    public function __construct($entityModel, array $params)
    {
        $this->entityModel = $entityModel;
        $this->params = $params;
    }


}
