<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Src\Company\Project\Domain\Services\MediaLibrary\CustomPathGenerator;
use Src\Company\Document\Infrastructure\EloquentModels\SectionsEloquentModel;

class RenovationSectionsEloquentModel extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $table = 'renovation_item_sections';

    protected $fillable = [
        'section_id',
        'document_id',
        'index',
        'total_price',
        'total_items_count',
        'calculation_type',
        'total_cost_price',
        'name',
        'description',
        'is_page_break'
    ];

    protected $casts = [
        'is_page_break' => 'boolean',
    ];

    public function getIsPageBreakAttribute($value): bool
    {
        return (bool) $value;
    }

    public function sections(): BelongsTo
    {
        return $this->belongsTo(SectionsEloquentModel::class, 'section_id');
    }

    public function renovation_items(): HasMany
    {
        return $this->hasMany(RenovationItemsEloquentModel::class, 'renovation_item_section_id');
    }

    public function renovation_document(): BelongsTo
    {
        return $this->belongsTo(RenovationDocumentsEloquentModel::class, 'document_id');
    }
}
