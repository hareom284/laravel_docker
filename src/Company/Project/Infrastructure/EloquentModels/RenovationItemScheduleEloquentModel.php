<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationItemsEloquentModel;

class RenovationItemScheduleEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'renovation_item_schedules';

    protected $fillable = [
        'project_id',
        'renovation_item_id',
        'start_date',
        'end_date',
        'show_in_timeline',
        'is_checked'
    ];

    public function renovationItem()
    {
        return $this->belongsTo(RenovationItemsEloquentModel::class, 'renovation_item_id');
    }

}
