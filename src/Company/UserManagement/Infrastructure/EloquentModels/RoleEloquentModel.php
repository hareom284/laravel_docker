<?php

declare(strict_types=1);

namespace Src\Company\UserManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;


class RoleEloquentModel extends Model
{

    protected $table = 'roles';
    protected $fillable = ['name', 'description'];

    public function users()
    {
        return $this->belongsToMany(UserEloquentModel::class,'users','user_id',);
    }

    public function permissions()
    {
        return $this->belongsToMany(PermissionEloquentModel::class,'permission_role','role_id','permission_id');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['name'])) {
            $query->where('name', 'like','%' . $filters['name']. '%');
        }
    }
}
