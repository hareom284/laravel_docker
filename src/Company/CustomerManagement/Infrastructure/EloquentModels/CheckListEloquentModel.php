<?php

declare(strict_types=1);

namespace Src\Company\CustomerManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CheckListEloquentModel extends Model
{

    use SoftDeletes;

    protected $table = 'checklist_items';

    protected $fillable = ['description', 'is_completed','customer_id'];

    public function customer()
    {
        return $this->belongsTo(CustomerEloquentModel::class,'customer_id','id');
    }

}
