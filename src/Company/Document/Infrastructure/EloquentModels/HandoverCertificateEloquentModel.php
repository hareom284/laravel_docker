<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;

class HandoverCertificateEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'handover_certificates';

    protected $fillable = [
        'project_id',
        'signed_by_manager_id',
        'date',
        'last_edited',
        'customer_signature',
        'salesperson_signature',
        'signed_by_salesperson_id',
        'manager_signature',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
    }

    public function salesperson(): BelongsTo
    {
        return $this->belongsTo(StaffEloquentModel::class, 'signed_by_salesperson_id');
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
    public function customer_signatures(): HasMany
    {
        return $this->hasMany(HandoverCertificateSignatureEloquentModel::class, 'handover_certificate_id');
    }
}
