<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\App\Policies;

class EntityTypePolicy
{

    public function create($user)
    {
        return true;
    }

    public function update($user, $entityType)
    {
        return true;
    }

    public function delete($user, $entityType)
    {
        return true;
    }
}
