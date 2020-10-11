<?php

namespace jhoopes\LaravelVueForms\ValidationRules;


use Illuminate\Contracts\Validation\Rule;
use jhoopes\LaravelVueForms\ValidationRule;

class FieldExtraValidation extends ValidationRule implements Rule
{


    public function passes($attribute, $value)
    {
        return true;
    }

    public function message()
    {
        return '';
    }
}
