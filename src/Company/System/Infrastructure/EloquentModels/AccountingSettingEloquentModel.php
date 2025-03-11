<?php

declare(strict_types=1);

namespace Src\Company\System\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;

class AccountingSettingEloquentModel extends Model
{
    protected $table = 'accounting_software_settings';

    protected $fillable = [
        'setting',
        'value',
        'company_id',
    ];

}
