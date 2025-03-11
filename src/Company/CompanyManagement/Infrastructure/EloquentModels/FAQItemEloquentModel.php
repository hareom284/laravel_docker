<?php

declare(strict_types=1);

namespace Src\Company\CompanyManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class FAQItemEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'faq_items';

    protected $fillable = [
        'question',
        'answer',
        'project_id',
        'customer_id',
        'status'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class,'project_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'customer_id');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['keyword'])) {
            $query->where('question', 'like', '%' . $filters['keyword'] . '%');
        }
    }
}
