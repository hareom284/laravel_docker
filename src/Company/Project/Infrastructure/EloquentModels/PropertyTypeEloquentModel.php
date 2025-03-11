<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class PropertyTypeEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'property_types';

    protected $fillable = [
        'type',
        'is_predefined'
    ];

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
