<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class ItemsIndexEloquentModel extends Model
{

    use SoftDeletes;

    protected $table = 'items_index';

    protected $fillable = [
        'document_id',
        'aow_id',
        'items_sequence',
    ];

}
