<?php

declare(strict_types=1);

namespace Src\Company\CompanyManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;

class QuickBookEloquentModel extends Model
{
    protected $table = 'quickbook_credentials';

    protected $fillable = [
        'company_id',
        'accounting_software_company_id',
        'refresh_token',
    ];
}
