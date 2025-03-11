<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class RenovationDocumentSignaturesEloquentModel extends Model
{


    protected $table = 'renovation_document_signatures';

    protected $fillable = [
        'renovation_document_id',
        'customer_id',
        'customer_signature'
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'customer_id', 'id');
    }

    public function renovation_document(): BelongsTo
    {
        return $this->belongsTo(RenovationDocumentsEloquentModel::class, 'renovation_document_id');
    }
}
