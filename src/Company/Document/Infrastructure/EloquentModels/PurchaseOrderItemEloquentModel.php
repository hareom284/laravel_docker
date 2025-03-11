<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItemEloquentModel extends Model
{
	use SoftDeletes;

	protected $table = 'purchase_order_items';

    protected $fillable = [
        'purchase_order_id',
        'description',
        'code',
        'quantity',
		'size'
    ];
}