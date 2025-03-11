<?php

declare(strict_types=1);

namespace Src\Company\StaffManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


class RankEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'ranks';

    protected $fillable = [
        'rank_name',
        'tier',
        'commission_percent',
        'or_percent'
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
