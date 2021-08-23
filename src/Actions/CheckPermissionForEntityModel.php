<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use jhoopes\LaravelVueForms\DTOs\CheckPermissionDTO;

class CheckPermissionForEntityModel
{
    public function execute(CheckPermissionDTO $permissionDTO): bool
    {
        if(\Gate::denies($permissionDTO->action, $permissionDTO->entityModel)) {
            return false;
        }
        return true;
    }
}
