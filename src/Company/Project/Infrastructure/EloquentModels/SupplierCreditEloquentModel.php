<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;

class SupplierCreditEloquentModel extends Model
{
    
    protected $table = 'supplier_credits';

    protected $fillable = [
        'invoice_no',
        'description',
        'is_gst_inclusive',
        'total_amount',
        'amount',
        'gst_amount',
        'pdf_path',
        'invoice_date',
        'quick_book_vendor_credit_id',
        'xero_credit_note_id',
        'vendor_id',
        'sale_report_id'
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(VendorEloquentModel::class, 'vendor_id', 'id');
    }

    public function saleReport(): BelongsTo
    {
        return $this->belongsTo(SaleReportEloquentModel::class, 'sale_report_id', 'id');
    }
}
