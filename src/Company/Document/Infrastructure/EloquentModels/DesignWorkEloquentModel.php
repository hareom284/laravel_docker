<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class DesignWorkEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'design_works';

    protected $fillable = [
        'date',
        'document_date',
        'name',
        'document_file',
        'scale',
        'request_status',
        'last_edited',
        'signature',
        'signed_date',
        'designer_in_charge_id',
        'project_id',
        'drafter_in_charge_id'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
    }

    public function designer()
    {
        return $this->belongsTo(StaffEloquentModel::class, 'designer_in_charge_id');
    }

    public function assistantDesigner()
    {
        return $this->belongsToMany(StaffEloquentModel::class,'design_work_assistant_sales_person','design_work_id','saleperson_id')->withTimestamps();
    }

    public function drafter()
    {
        return $this->belongsTo(UserEloquentModel::class, 'drafter_in_charge_id');
    }

    public function materials()
    {
        return $this->belongsToMany(MaterialEloquentModel::class,'design_work_materials','design_work_id','material_id')->withPivot('color_code')->withTimestamps();
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
