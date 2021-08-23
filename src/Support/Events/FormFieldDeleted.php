<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Support\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use jhoopes\LaravelVueForms\Models\FormField;

class FormFieldDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public FormField $formField
    ) {}

}
