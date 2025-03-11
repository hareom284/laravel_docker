<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

class HDBFormsEloquentModel extends Model
{
    protected $table = 'hdb_acknowledgement_forms';

    protected $fillable = [
        'project_id',
        'name',
        'date_uploaded',
        'document_file'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
    }

}
