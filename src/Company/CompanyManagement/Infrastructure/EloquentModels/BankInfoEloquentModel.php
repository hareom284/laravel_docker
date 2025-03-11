<?php

declare(strict_types=1);

namespace Src\Company\CompanyManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;

class BankInfoEloquentModel extends Model
{
    protected $table = 'bank_infos';

    protected $fillable = [
        'bank_name',
        'quick_book_account_id',
        'xero_account_id',
        'company_id'
    ];
}
