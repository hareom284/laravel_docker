<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;


class NotificationEloquentModel extends Model
{

    protected $table = 'purchase_order_notifications';

    protected $fillable = [
        'message',
        'from_user_id',
        'to_user_id'
    ];


    public function scopeFilter($query, $filters)
    {
        $query->when($filters['name'] ?? false, function ($query, $name) {
            $query->where('name', 'like', '%' . $name . '%');
        });
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        });
    }
}
