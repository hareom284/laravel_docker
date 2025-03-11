<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class TaxInvoiceEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'tax_invoices';

    protected $fillable = [
        'project_id',
        'customer_id',
        'signed_by_manager_id',
        'signed_by_saleperson_id',
        'date',
        'last_edited',
        'salesperson_signature',
        'manager_signature',
        'status',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
    }

    public function salesperson(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'signed_by_saleperson_id');
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
