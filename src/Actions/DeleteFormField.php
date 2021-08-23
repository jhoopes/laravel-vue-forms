<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use Illuminate\Events\Dispatcher;
use jhoopes\LaravelVueForms\DTOs\DeleteFormFieldDTO;
use jhoopes\LaravelVueForms\Support\Events\FormFieldDeleted;

class DeleteFormField
{

    public function __construct(
       public Dispatcher $eventDispatcher
    ){}


    public function execute(DeleteFormFieldDTO $deleteFormFieldDTO): void
    {
        if($deleteFormFieldDTO->syncForms) {
            $deleteFormFieldDTO->formField->forms()->sync([]);
        }
        $deleteFormFieldDTO->formField->delete();

        $this->eventDispatcher->dispatch(new FormFieldDeleted($deleteFormFieldDTO->formField));
    }


}
