<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorCategoryEloquentModel extends Model
{

    use SoftDeletes;

    protected $table = 'vendor_categories';

    protected $fillable = [
        'type'
    ];

    public function vendors(): HasMany
    {
        return $this->hasMany(VendorEloquentModel::class);
    }
}
