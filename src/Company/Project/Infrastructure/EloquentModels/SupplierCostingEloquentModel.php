<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\QboExpenseTypeEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class SupplierCostingEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'vendor_invoices';

    protected $fillable = [
        'invoice_no',
        'description',
        'payment_amt',
        'amount_paid',
        'amended_amt',
        'remark',
        'to_pay',
        'discount_percentage',
        'discount_amt',
        'credit_amt',
        'is_gst_inclusive',
        'gst_value',
        'invoice_date',
        'document_file',
        'status',
        'project_id',
        'vendor_id',
        'purchase_order_id',
        'vendor_payment_id',
        'vendor_invoice_expense_type_id',
        'quick_book_expense_id',
        'quick_book_bill_id',
        'xero_bill_id',
        'verify_by'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id', 'id');
    }

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(VendorEloquentModel::class, 'vendor_id', 'id');
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderEloquentModel::class, 'purchase_order_id', 'id');
    }

    public function oldPayment(): BelongsTo
    {
        return $this->belongsTo(SupplierCostingPaymentEloquentModel::class, 'vendor_payment_id', 'id');
    }

    public function payments(): BelongsToMany
    {
        return $this->belongsToMany(SupplierCostingPaymentEloquentModel::class, 'vendor_invoices_and_payments', 'vendor_invoice_id', 'vendor_payment_id');
    }

    public function verifyBy(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'verify_by','id');
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(SupplierCostingApprovalEloquentModel::class, 'vendor_invoice_id', 'id');
    }

    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(VendorInvoiceExpenseTypeEloquentModel::class, 'vendor_invoice_expense_type_id', 'id');
    }

    public function qboExpenseType(): BelongsTo
    {
        return $this->belongsTo(QboExpenseTypeEloquentModel::class, 'quick_book_expense_id', 'quick_book_id');
    }
}
