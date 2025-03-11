<?php

declare(strict_types=1);

namespace Src\Company\CustomerManagement\Infrastructure\EloquentModels;


use Illuminate\Database\Eloquent\Model;

class IdMilestonesEloquentModel extends Model
{

    protected $table = 'id_milestones';

    protected $fillable = [
        'name',
        'noti_type',
        'role',
        'duration',
        'index',
        'color_code',
        'status',
        'action',
        'whatsapp_template',
        'whatsapp_language',
        'title',
        'message'
    ];


    public function customers()
    {
        return $this->belongsToMany(CustomerEloquentModel::class,'status_histories','id_milestone_id','customer_id')->withPivot('remark','message_sent','duration','file')->withTimestamps();
    }

    public function fromTransitions()
    {
        return $this->hasMany(IdMilestoneTransitionEloquentModel::class, 'from_id_milestone_id');
    }

    public function toTransitions()
    {
        return $this->hasMany(IdMilestoneTransitionEloquentModel::class, 'to_id_milestone_id');
    }

}
