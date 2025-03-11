<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

class ThreeDDesignEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = '3d_designs';

    protected $fillable = [
        'name',
        'project_id',
        'uploader_id',
        'design_work_id',
        'date',
        'document_file',
        'last_edited'
    ];

    public function uploader()
    {
        return $this->belongsTo(UserEloquentModel::class, 'uploader_id');
    }

    public function design_work(): BelongsTo
    {
        return $this->belongsTo(DesignWorkEloquentModel::class, 'design_work_id');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
    }

}
