<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class TermAndConditionParagraphEloquentModel extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'term_and_condition_paragraphs';
    protected $fillable = ['term_and_condition_page_id', 'content', 'is_need_signature', 'signature_position'];
    protected $appends = ['file'];

    public function getFileAttribute()
    {
        $media = $this->getFirstMedia('termAndCondition');
        return $media ? $media->getUrl() : null;
    }

    public function termAndConditionPage()
    {
        return $this->belongsTo(TermAndConditionPageEloquentModel::class, 'term_and_condition_page_id');
    }

    public function signatures()
    {
        return $this->hasMany(TermAndConditionSignatureEloquentModel::class, 'term_and_condition_page_id');
    }
}
