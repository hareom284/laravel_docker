<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

class ProjectPorfolioEloquentModel extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'project_portfolios';
    protected $fillable = ['project_id', 'title', 'description'];


    public function getImageAttribute()
    {
        return $this->getMedia('image');
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class,'project_id','id');
    }
}
