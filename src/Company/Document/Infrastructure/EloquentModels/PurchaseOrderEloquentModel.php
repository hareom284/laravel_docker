<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SupplierCostingEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class PurchaseOrderEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_orders';

    protected $fillable = [
        'date',
        'time',
        'pages',
        'attn',
        'sales_rep_signature',
        'manager_signature',
        'purchase_order_number',
        'remark',
        'delivery_date',
        'delivery_time_of_the_day',
        'status',
        'vendor_remark',
        'document_file',
        'signed_by_manager_id',
        'project_id',
        'sale_rep_id',
        'vendor_id',
        'pdf_file'
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(VendorEloquentModel::class, 'vendor_id');
    }

    public function vendor_invoice()
    {
        return $this->hasOne(SupplierCostingEloquentModel::class, 'purchase_order_id');
    }

    public function poItems(): HasMany
    {
        return $this->hasMany(PurchaseOrderItemEloquentModel::class, 'purchase_order_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'sale_rep_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'signed_by_manager_id');
    }


    public function scopeFilter($query, $filters)
    {
        if (isset($filters['status'])) {
            $query->where('status', '=', $filters['status']);
        }
        if (isset($filters['vendor_id'])) {
            $query->where('vendor_id', '=', $filters['vendor_id']);
        }
        if (isset($filters['project_id'])) {
            $query->where('project_id', '=', $filters['project_id']);
        }
    }
}
