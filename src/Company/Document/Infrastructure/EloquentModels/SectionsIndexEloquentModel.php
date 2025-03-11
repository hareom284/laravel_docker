<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SectionsIndexEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'section_index';

    protected $fillable = [
        'document_id',
        'section_sequence',
    ];
}
