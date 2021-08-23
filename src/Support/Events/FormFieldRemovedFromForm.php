<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Support\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use jhoopes\LaravelVueForms\Models\FormConfiguration;
use jhoopes\LaravelVueForms\Models\FormField;

class FormFieldRemovedFromForm
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public FormConfiguration $formConfiguration,
        public FormField $formField,
    ){}


}
