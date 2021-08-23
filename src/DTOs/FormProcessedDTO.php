<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\DTOs;

use Illuminate\Database\Eloquent\Model;
use jhoopes\LaravelVueForms\Models\FormConfiguration;

class FormProcessedDTO
{
    public function __construct(
        public FormConfiguration $formConfiguration,
        public array $validData,
        public ?Model $entity = null,
        public ?string $processedAction = null
    ){}
}
