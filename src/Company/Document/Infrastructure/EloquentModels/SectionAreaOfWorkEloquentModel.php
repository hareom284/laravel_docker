<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionAreaOfWorkEloquentModel extends Model
{

    use SoftDeletes;

    protected $table = 'section_area_of_works';

    protected $fillable = [
        'section_id',
        'index',
        'name',
        'is_active',
        'document_id'
    ];

    public function sections(): BelongsTo
    {
        return $this->belongsTo(SectionsEloquentModel::class, 'section_id');
    }

    public function quotation_items(): HasMany
    {
        return $this->hasMany(QuotationTemplateItemsEloquentModel::class, 'area_of_work_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationTemplateItemsEloquentModel::class, 'area_of_work_id');
    }

    public function renovation_aow(): HasMany
    {
        return $this->hasMany(RenovationAreaOfWorkEloquentModel::class, 'section_area_of_work_id');
    }
}
