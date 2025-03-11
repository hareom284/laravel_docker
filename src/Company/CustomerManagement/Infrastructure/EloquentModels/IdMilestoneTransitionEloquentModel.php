<?php

declare(strict_types=1);

namespace Src\Company\CustomerManagement\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class IdMilestoneTransitionEloquentModel extends Model
{
    protected $table = 'id_milestone_transitions';

    protected $fillable = [
        'from_id_milestone_id',
        'to_id_milestone_id',
        'action',
    ];

    public function fromMilestone()
    {
        return $this->belongsTo(IdMilestonesEloquentModel::class, 'from_id_milestone_id');
    }

    public function toMilestone()
    {
        return $this->belongsTo(IdMilestonesEloquentModel::class, 'to_id_milestone_id');
    }
}
