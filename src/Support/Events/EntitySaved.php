<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Support\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EntitySaved
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public string $saveType,
        public Model $entity
    ){}


}
