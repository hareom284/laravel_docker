<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TermAndConditionEloquentModel extends Model implements HasMedia
{

    use InteractsWithMedia;

    protected $table = 'term_and_conditions';
    protected $fillable = ['title'];
    protected $appends = ['file'];

    public function getFileAttribute()
    {
        $media = $this->getFirstMedia('termAndConditionPdf');
        return $media ? $media->getUrl() : null;
    }

    public function pages()
    {
        return $this->hasMany(TermAndConditionPageEloquentModel::class, 'term_and_condition_id');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['name'])) {
            $query->where('title', 'like','%' . $filters['name']. '%');
        }
    }
}
