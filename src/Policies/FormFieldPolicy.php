<?php

namespace jhoopes\LaravelVueForms\Policies;

class FormFieldPolicy
{
    public function create($user)
    {
        return true;
    }


    public function update($user, $formConfig)
    {
        return true;
    }

    public function delete($user, $formConfig)
    {
        return true;
    }
}
