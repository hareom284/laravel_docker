<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class EvoSignatureEloquentModel extends Model
{


    protected $table = 'evo_signatures';

    // protected $fillable = [
    //     'handover_certificate_id',
    //     'customer_id',
    //     'customer_signature'
    // ];
    protected $guarded = [];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'customer_id', 'id');
    }

    public function evo(): BelongsTo
    {
        return $this->belongsTo(EvoEloquentModel::class, 'evo_id');
    }
}
