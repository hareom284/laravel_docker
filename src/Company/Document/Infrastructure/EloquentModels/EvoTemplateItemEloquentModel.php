<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class EvoTemplateItemEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'evo_template_items';

    protected $fillable = [
        'salesperson_id',
        'description',
        'unit_rate_without_gst',
        'unit_rate_with_gst'
    ];

}
