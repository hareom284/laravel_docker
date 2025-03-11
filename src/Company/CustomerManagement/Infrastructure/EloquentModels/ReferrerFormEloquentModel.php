<?php

declare(strict_types=1);

namespace Src\Company\CustomerManagement\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class ReferrerFormEloquentModel extends Model
{
    protected $table = 'referrer_forms';

    protected $fillable = [
        'owner_id',
        'referrer_id',
        'referrer_properties',
        'signed_by_salesperson_id',
        'signed_by_management_id',
        'owner_signature',
        'salesperson_signature',
        'management_signature',
        'date_of_referral'
    ];

    public function owner()
    {
        return $this->belongsTo(UserEloquentModel::class, 'owner_id', 'id');
    }

    public function referrer()
    {
        return $this->belongsTo(UserEloquentModel::class, 'referrer_id', 'id');
    }

    public function salesperson()
    {
        return $this->belongsTo(UserEloquentModel::class, 'signed_by_salesperson_id', 'id');
    }

    public function management()
    {
        return $this->belongsTo(UserEloquentModel::class, 'signed_by_management_id', 'id');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['name'])) {
            $query->where(function ($query) use ($filters) {
                $query->where('first_name', 'like', '%' . $filters['name'] . '%')
                    ->orWhere('last_name', 'like', '%' . $filters['name'] . '%');
            });
        }
    }
}
