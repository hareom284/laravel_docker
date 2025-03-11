<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierCostingApprovalEloquentModel extends Model
{    
    protected $table = 'vendor_invoice_approvals';

    protected $fillable = [
        'vendor_invoice_id',
        'approved_by'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(SupplierCostingEloquentModel::class, 'vendor_invoice_id', 'id');
    }
}
