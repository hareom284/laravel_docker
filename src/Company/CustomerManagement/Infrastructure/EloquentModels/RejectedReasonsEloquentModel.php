<?php

declare(strict_types=1);

namespace Src\Company\CustomerManagement\Infrastructure\EloquentModels;


use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class RejectedReasonsEloquentModel extends Model
{

    protected $table = 'rejected_reasons';

    protected $fillable = [
        'name',
        'noti_type',
        'user_type',
        'duration',
        'index',
        'color_code'
    ];

}
