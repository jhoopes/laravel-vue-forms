<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Policies;

class EntityPolicy
{
    public function create($user)
    {
        return true;
    }

    public function update($user, $entity)
    {
        return true;
    }

    public function delete($user, $entity)
    {
        return true;
    }
}
