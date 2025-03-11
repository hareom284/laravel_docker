<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class SupplierCostingPaymentEloquentModel extends Model
{

    protected $table = 'vendor_payments';

    protected $fillable = [
        'bank_transaction_id',
        'payment_date',
        'payment_type',
        'amount',
        'remark',
        'status',
        'payment_method',
        'payment_made_by',
        'manager_signature',
        'signed_by_manager_id'
    ];

    public function oldSupplierCostings(): HasMany
    {
        return $this->hasMany(SupplierCostingEloquentModel::class, 'vendor_payment_id', 'id');
    }

    public function supplierCostings(): BelongsToMany
    {
        return $this->belongsToMany(SupplierCostingEloquentModel::class, 'vendor_invoices_and_payments', 'vendor_payment_id', 'vendor_invoice_id');
    }

    public function accountant(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'payment_made_by', 'id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'signed_by_manager_id', 'id');
    }
}
