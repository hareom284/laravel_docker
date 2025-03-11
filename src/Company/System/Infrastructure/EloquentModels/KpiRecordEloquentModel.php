<?php

declare(strict_types=1);

namespace Src\Company\System\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;

class KpiRecordEloquentModel extends Model
{
    
    protected $table = 'kpi_records';

    protected $fillable = [
        'type',
        'period',
        'target',
        'company_id'
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyEloquentModel::class, 'company_id','id');
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
