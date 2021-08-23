<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use Illuminate\Events\Dispatcher;
use jhoopes\LaravelVueForms\DTOs\DeleteEntityDTO;
use jhoopes\LaravelVueForms\Support\Events\EntityDeleted;

class DeleteEntity
{
    public function __construct(
        public Dispatcher $eventDispatcher
    ){}

    public function execute(DeleteEntityDTO $deleteEntityDTO)
    {
        $deleteEntityDTO->entity->delete();
        $this->eventDispatcher->dispatch(new EntityDeleted(
            $deleteEntityDTO->entityType,
            $deleteEntityDTO->entity
        ));
    }

}
