<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;


class RenovationSettingEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'renovation_document_settings';

    protected $fillable = [
        'renovation_document_id',
        'setting',
        'value'
    ];
 
}
