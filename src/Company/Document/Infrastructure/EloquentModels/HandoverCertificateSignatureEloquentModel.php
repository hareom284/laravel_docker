<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class HandoverCertificateSignatureEloquentModel extends Model
{


    protected $table = 'handover_certificate_signatures';

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

    public function handover_certificate(): BelongsTo
    {
        return $this->belongsTo(HandoverCertificateEloquentModel::class, 'handover_certificate_id');
    }
}
