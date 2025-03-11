<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class EvoTemplateRoomEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'evo_template_rooms';

    protected $fillable = [
        'salesperson_id',
        'room_name'
    ];

}
