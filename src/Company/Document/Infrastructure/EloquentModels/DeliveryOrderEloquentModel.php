<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

class DeliveryOrderEloquentModel extends Model
{

    protected $table = 'delivery_orders';

    protected $fillable = [
        'project_id',
        'do_no',
        'po_no',
        'quotation_no',
        'date'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
    }

}
