<?php

declare(strict_types=1);

namespace Src\Company\UserManagement\Infrastructure\EloquentModels;


use Illuminate\Database\Eloquent\Model;


class PermissionEloquentModel extends Model
{
    protected $table = 'permissions';

    protected $fillable = [
        'name',
        'description'
    ];

    public function roles()
    {
        return $this->belongsToMany(RoleEloquentModel::class,'permission_role','permission_id','role_id');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['name'])) {
            $query->where('name', 'like','%' . $filters['name']. '%');
        }
    }
}
