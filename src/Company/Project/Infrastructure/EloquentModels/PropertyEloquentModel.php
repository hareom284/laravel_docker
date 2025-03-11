<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;

class PropertyEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'properties';

    protected $fillable = [
        'type_id',
        'street_name',
        'block_num',
        'unit_num',
        'postal_code'
    ];

    public function projects(): HasOne
    {
        return $this->hasOne(ProjectEloquentModel::class, 'property_id');
    }

    public function propertyType(): BelongsTo
    {
        return $this->belongsTo(PropertyTypeEloquentModel::class, 'type_id');
    }

    /**
     * The roles that belong to the PropertyEloquentModel
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customer_properties(): BelongsToMany
    {
        return $this->belongsToMany(CustomerEloquentModel::class, 'customer_properties', 'property_id', 'customer_id');
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
