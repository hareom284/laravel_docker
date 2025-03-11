<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\Project\Infrastructure\EloquentModels\UserEloquentModel;

class MaterialEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'purchase_order_notifications';

    protected $fillable = [
        'from_user_id',
        'to_user_id',
        'message'
    ];

    public function fromUser(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'from_user_id');
    }

    public function toUser(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'to_user_id');
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
