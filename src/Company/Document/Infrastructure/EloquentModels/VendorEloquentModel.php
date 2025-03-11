<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorEloquentModel extends Model
{

    use SoftDeletes;

    protected $table = 'vendors';

    protected $fillable = [
        'vendor_name',
        'contact_person',
        'contact_person_number',
        'email',
        'street_name',
        'block_num',
        'unit_num',
        'postal_code',
        'fax_number',
        'rebate',
        'user_id',
        'name_prefix',
        'contact_person_last_name',
        'prefix',
        'vendor_category_id',
        'quick_book_vendor_id',
        'xero_vendor_id',
    ];

    public function vendorCategory(): BelongsTo
    {
        return $this->belongsTo(VendorCategoryEloquentModel::class);
    }

    public function scopeFilter($query, $filters)
    {
        $query->when($filters['name'] ?? false, function ($query, $name) {
            $query->where('vendor_name', 'like', '%' . $name . '%');
        })
        ->orderBy('vendor_name',isset($filters['order']) ? $filters['order'] : 'asc');
    }

}
