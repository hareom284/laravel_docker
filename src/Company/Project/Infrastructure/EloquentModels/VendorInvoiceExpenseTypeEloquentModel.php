<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Src\Company\Document\Infrastructure\EloquentModels\VendorEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\SaleReportEloquentModel;

class VendorInvoiceExpenseTypeEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'vendor_invoice_expense_types';

    protected $fillable = [
        'name',
        'project_related',
    ];

    public function vendorInvoices(): HasMany
    {
        return $this->hasMany(SupplierCostingEloquentModel::class, 'vendor_invoice_expense_type_id', 'id');
    }
}
