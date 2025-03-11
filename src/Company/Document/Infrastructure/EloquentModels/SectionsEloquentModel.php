<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class SectionsEloquentModel extends Model
{

    use SoftDeletes;

    protected $table = 'sections';

    protected $fillable = [
        'salesperson_id',
        'index',
        'name',
        'calculation_type',
        'is_active',
        'document_id',
        'description',
        'quotation_template_id',
        'is_page_break'
    ];

    protected $casts = [
        'is_page_break' => 'boolean',
    ];

    public function getIsPageBreakAttribute($value): bool
    {
        return (bool) $value;
    }

    public function staffs(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'salesperson_id');
    }

    public function quotation_items(): HasMany
    {
        return $this->hasMany(QuotationTemplateItemsEloquentModel::class, 'section_id');
    }

    public function areaOfWorks(): HasMany
    {
        return $this->hasMany(SectionAreaOfWorkEloquentModel::class, 'section_id');
    }

    public function renovation_sections(): HasMany
    {
        return $this->hasMany(RenovationSectionsEloquentModel::class, 'section_id');
    }

    public function vendors()
    {
        return $this->belongsToMany(VendorEloquentModel::class, 'section_vendor', 'section_id', 'vendor_id')->withTimestamps();
    }

    public function items()
    {
        return $this->hasMany(QuotationTemplateItemsEloquentModel::class, 'section_id')->with('items');
    }
}
