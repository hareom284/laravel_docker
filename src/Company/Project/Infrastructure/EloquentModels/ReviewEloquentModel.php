<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class ReviewEloquentModel extends Model
{
    protected $table = 'reviews';

    protected $fillable = [
        'title',
        'comments',
        'stars',
        'date',
        'project_id',
        'review_by',
        'salesperson_id'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class,'project_id');
    }

    public function saleperson(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class,'salesperson_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class,'review_by');
    }

}
