<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Policies;

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
