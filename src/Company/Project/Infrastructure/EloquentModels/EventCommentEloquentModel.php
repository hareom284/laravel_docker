<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


class EventCommentEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'event_comments';

    protected $fillable = [
        'event_id',
        'description',
        'is_completed'
    ];

    public function event()
    { 
        return $this->belongTo(EventEloquentModel::class);
    }

}
