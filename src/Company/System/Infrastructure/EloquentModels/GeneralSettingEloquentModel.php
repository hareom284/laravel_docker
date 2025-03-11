<?php

declare(strict_types=1);

namespace Src\Company\System\Infrastructure\EloquentModels;


use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;

class GeneralSettingEloquentModel extends Model
{

    protected $table = 'settings';

    protected $fillable = [
        'setting',
        'value',
        'is_array',
    ];

}
