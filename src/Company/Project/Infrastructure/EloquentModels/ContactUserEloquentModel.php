<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class ContactUserEloquentModel extends Model
{
    protected $table = 'contact_users';

    protected $fillable = ['project_id', 'user_id'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'user_id');
    }
}
