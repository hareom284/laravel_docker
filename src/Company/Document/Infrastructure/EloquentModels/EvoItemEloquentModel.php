<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Company\Document\Infrastructure\EloquentModels\EvoTemplateRoomEloquentModel;

class EvoItemEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'evo_items';

    protected $fillable = [
        'template_item_id',
        'evo_id',
        'item_description',
        'quantity',
        'unit_rate',
        'total'
    ];

    public function rooms()
    {
        return $this->belongsToMany(EvoTemplateRoomEloquentModel::class,'evo_item_rooms','item_id','room_id')->withPivot('quantity','name','start_date','end_date','is_checked');
    }
}
