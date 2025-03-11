<?php

declare(strict_types=1);

namespace Src\Company\UserManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;


class TeamEloquentModel extends Model
{

    protected $table = 'teams';
    protected $fillable = ['team_name', 'team_leader_id','created_by'];

    public function teamLead()
    {
        return $this->hasOne(UserEloquentModel::class,'id','team_leader_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(UserEloquentModel::class,'created_by','id');
    }

    public function teamMemebers()
    {
        return $this->belongsToMany(UserEloquentModel::class,'team_members','team_id','team_member_id')->withTimestamps();
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['name'])) {
            $query->where('team_name', 'like','%' . $filters['name']. '%');
        }
    }
}
