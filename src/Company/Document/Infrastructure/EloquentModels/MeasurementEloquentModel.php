<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class MeasurementEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'measurement';

    protected $fillable = [
        'fixed',
        'name',
        'has_sqft_calculation'
    ];
}
