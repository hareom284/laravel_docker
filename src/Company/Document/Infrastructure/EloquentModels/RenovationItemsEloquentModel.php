<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Company\Project\Infrastructure\EloquentModels\RenovationItemScheduleEloquentModel;


class RenovationItemsEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'renovation_items';

    protected $fillable = [
        'renovation_document_id',
        'parent_id',
        'cancellation_id',
        'prev_item_id',
        'project_id',
        'name',
        'quotation_template_item_id',
        'renovation_item_section_id',
        'renovation_item_area_of_work_id',
        'quantity',
        'length',
        'breadth',
        'height',
        'price',
        'cost_price',
        'profit_margin',
        'is_FOC',
        'is_CN',
        'is_page_break',
        'unit_of_measurement',
        'is_fixed_measurement',
        'completed',
        'active',
        'is_excluded',
        'sub_description'
    ];


    /***
     * 
     * set price by default 0 if the price is null 
     */
    public function getPriceAttribute($value)
    {
        return $value ?? 0;
    }

    public function getCostPriceAttribute($value)
    {
        return $value ?? 0;
    }


    public function prev_item()
    {
        return $this->belongsTo(self::class, 'prev_item_id');
    }

    public function current_item()
    {
        return $this->hasMany(self::class, 'prev_item_id');
    }

    public function quotation_items(): BelongsTo
    {
        return $this->belongsTo(QuotationTemplateItemsEloquentModel::class, 'quotation_template_item_id');
    }

    public function renovation_documents(): BelongsTo
    {
        return $this->belongsTo(RenovationDocumentsEloquentModel::class, 'renovation_document_id');
    }

    public function renovation_sections(): BelongsTo
    {
        return $this->belongsTo(RenovationSectionsEloquentModel::class, 'renovation_item_section_id');
    }

    public function renovation_item_schedule()
    {
        return $this->hasOne(RenovationItemScheduleEloquentModel::class,'renovation_item_id','id');
    }

    public function renovation_area_of_work(): BelongsTo
    {
        return $this->belongsTo(RenovationAreaOfWorkEloquentModel::class, 'renovation_item_area_of_work_id');
    }

    // public function renovation_item_section(): BelongsTo
    // {
    //     return $this->belongsTo(RenovationSectionsEloquentModel::class, 'renovation_item_section_id');
    // }

    public static function boot()
    {
        parent::boot();    
    
        // static::deleted(function($category)
        // {
        //     $category->children()->delete();
        //     $category->items()->delete();
        // });
        
    }   
}
