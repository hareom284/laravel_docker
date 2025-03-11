<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class RenovationAreaOfWorkEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'renovation_item_area_of_works';

    protected $fillable = [
        'section_area_of_work_id',
        'document_id',
        'index',
        'name'
    ];

    public function areaOfWork(): BelongsTo
    {
        return $this->belongsTo(SectionAreaOfWorkEloquentModel::class, 'section_area_of_work_id');
    }

    public function renovation_items(): HasMany
    {
        return $this->hasMany(RenovationItemsEloquentModel::class, 'renovation_item_area_of_work_id');
    }

}
