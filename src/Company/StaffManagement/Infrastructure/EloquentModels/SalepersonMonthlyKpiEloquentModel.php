<?php

declare(strict_types=1);

namespace Src\Company\StaffManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;

class SalepersonMonthlyKpiEloquentModel extends Model
{

    protected $table = 'salesperson_monthly_kpi_records';

    protected $fillable = [
        'saleperson_id',
        'year',
        'month',
        'target'
    ];

    public function saleperson(): BelongsTo
    {
        return $this->belongsTo(StaffEloquentModel::class, 'saleperson_id','id');
    }

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
