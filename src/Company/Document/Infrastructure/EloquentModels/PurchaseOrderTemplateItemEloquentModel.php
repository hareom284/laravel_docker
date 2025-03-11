<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderTemplateItemEloquentModel extends Model
{
	use SoftDeletes;

	protected $table = 'purchase_order_template_items';

    protected $fillable = [
        'description',
        'code',
        'quantity',
		'size',
        'vendor_category_id',
        'company_id'
    ];

    public function vendorCategory(): BelongsTo
    {
        return $this->belongsTo(VendorCategoryEloquentModel::class);
    }
}