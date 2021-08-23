<?php

namespace jhoopes\LaravelVueForms\App\ValidationRules;


use Illuminate\Contracts\Validation\Rule;
use jhoopes\LaravelVueForms\Support\ValidationRule;

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
