<?php

declare(strict_types=1);

namespace Src\Company\CustomerManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckListTemplateItemEloquentModel extends Model
{

    use SoftDeletes;

    protected $table = 'checklist_template_items';

    protected $fillable = [
        'checklist_item_name',
        'checklist_item_value',
        'checklist_item_desc'
    ];

    public function leadCheckLists()
    {
        return $this->belongsToMany(CustomerEloquentModel::class,'lead_checklist_items','checklist_template_item_id','customer_id')->withPivot('status', 'date_completed')->withTimestamps();
    }

}
