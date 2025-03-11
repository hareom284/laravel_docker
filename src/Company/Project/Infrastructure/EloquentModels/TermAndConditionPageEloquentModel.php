<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TermAndConditionPageEloquentModel extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'term_and_condition_pages';
    protected $fillable = ['term_and_condition_id', 'page_number'];

    public function termAndCondition()
    {
        return $this->belongsTo(TermAndConditionEloquentModel::class, 'term_and_condition_id');
    }

    public function paragraphs()
    {
        return $this->hasMany(TermAndConditionParagraphEloquentModel::class, 'term_and_condition_page_id');
    }
}
