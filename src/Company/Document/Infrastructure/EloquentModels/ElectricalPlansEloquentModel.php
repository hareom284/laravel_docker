<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;

class ElectricalPlansEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'electrical_plans';

    protected $fillable = [
        'project_id',
        'date_uploaded',
        'document_file',
        'customer_signature'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
    }

    public function assistantDesigner()
    {
        return $this->belongsToMany(StaffEloquentModel::class,'electrical_plan_assistant_salesperson','electrical_plan_id','salesperson_id')->withTimestamps();
    }

    public function materials()
    {
        return $this->belongsToMany(MaterialEloquentModel::class,'electrical_plan_materials','electrical_plan_id','material_id')->withPivot('color_code')->withTimestamps();
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
