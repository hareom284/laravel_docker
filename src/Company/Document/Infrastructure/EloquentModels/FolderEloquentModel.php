<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FolderEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'folders';

    protected $fillable = [
        'title',
        'allow_customer_view',
        'project_id'
    ];

    public function documents() : HasMany
    {
        return $this->hasMany(DocumentEloquentModel::class,'folder_id');
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
