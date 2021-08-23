<?php declare(strict_types=1);

namespace jhoopes\LaravelVueForms\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FormValue extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'form_field_id',
        'entity_id',
        'entity_type',
        'value'
    ];

    public function form_field()
    {
        return $this->belongsTo(FormField::class);
    }

}
