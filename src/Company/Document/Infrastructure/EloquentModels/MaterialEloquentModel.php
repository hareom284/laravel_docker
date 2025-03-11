<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Src\Company\Project\Infrastructure\EloquentModels\DesignWorkEloquentModel;

class MaterialEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'materials';

    protected $fillable = [
        'name',
        'is_predefined'
    ];

    public function designWork(): BelongsToMany
    {
        return $this->belongsToMany(DesignWorkEloquentModel::class, 'design_work_materials','material_id','design_work_id');
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
