<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Src\Company\Project\Infrastructure\EloquentModels\EventCommentEloquentModel;


class EventEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'events';

    protected $fillable = [
        'title',
        'description',
        'start_date',
        'end_date',
        'status',
        'staff_id',
        'project_id'
    ];

    public function comments()
    {
        return $this->hasMany(EventCommentEloquentModel::class);
    }

    public function scopeFilter($query, $filters)
    {
        $query->when($filters['name'] ?? false, function ($query, $name) {
            $query->where('name', 'like', '%' . $name . '%');
        });
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        });
    }
}
