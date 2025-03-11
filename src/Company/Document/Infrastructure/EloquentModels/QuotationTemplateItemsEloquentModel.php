<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class QuotationTemplateItemsEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'quotation_template_items';

    protected $fillable = [
        'salesperson_id',
        'description',
        'index',
        'unit_of_measurement',
        'is_fixed_measurement',
        'section_id',
        'area_of_work_id',
        'price_without_gst',
        'price_with_gst',
        'cost_price',
        'profit_margin',
        'quantity',
        'measurement_id',
        'document_id',
        'parent_id',
        'is_active',
        'sub_description'
    ];

    public function staffs(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'salesperson_id');
    }

    public function sections(): BelongsTo
    {
        return $this->belongsTo(SectionsEloquentModel::class, 'section_id');
    }

    public function areaOfWorks(): BelongsTo
    {
        return $this->belongsTo(SectionAreaOfWorkEloquentModel::class, 'area_of_work_id');
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
    public function measurement(): BelongsTo
    {
        return $this->belongsTo(MeasurementEloquentModel::class, 'measurement_id');
    }

    // Define the relationship to its parent item
    public function parentItem()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    // Define the recursive relationship for sub-items
    public function items()
    {
        return $this->hasMany(self::class, 'parent_id')->with(['items' => function ($query) {
            $query->where('is_active', 1)->orderBy('index');
        }])->where('is_active', 1)->whereNull('document_id')->orderBy('index');
    }
}
