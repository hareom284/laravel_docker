<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;

class CustomerPaymentEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'customer_payments';

    protected $fillable = [
        'payment_type',
        'invoice_no',
        'invoice_date',
        'description',
        'index',
        'amount',
        'remark',
        'refund_date',
        'refund_reason', 
        'refund_amount',
        'unpaid_invoice_file_path',
        'paid_invoice_file_path',
        'credit_note_file_path',
        'status',
        'is_refunded',
        'is_sale_receipt',
        'sale_report_id',
        'customer_id',
        'quick_book_invoice_id',
        'quick_book_payment_id',
        'quick_book_credit_note_id',
        'xero_invoice_id',
        'xero_payment_id',
        'xero_credit_note_id',
        'estimated_date'
    ];

    public function saleReport(): BelongsTo
    {
        return $this->belongsTo(SaleReportEloquentModel::class, 'sale_report_id');
    }

    public function paymentType()
    {
        return $this->belongsTo(PaymentTypeEloquentModel::class, 'payment_type', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(CustomerEloquentModel::class, 'customer_id', 'id');
    }
}
