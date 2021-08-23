<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Actions;

use Illuminate\Events\Dispatcher;
use jhoopes\LaravelVueForms\DTOs\RemoveFormFieldFromFormDTO;
use jhoopes\LaravelVueForms\Support\Events\FormFieldRemovedFromForm;

class RemoveFormFieldFromForm
{
    public function __construct(
        public Dispatcher $eventDispatcher
    ){}

    public function execute(RemoveFormFieldFromFormDTO $removeFormFieldFromFormDTO)
    {
        $removeFormFieldFromFormDTO->formConfiguration->fields()->detach($removeFormFieldFromFormDTO->formField);
        $this->eventDispatcher->dispatch(new FormFieldRemovedFromForm(
            $removeFormFieldFromFormDTO->formConfiguration,
            $removeFormFieldFromFormDTO->formField
        ));
    }

}
