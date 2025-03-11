<?php

declare(strict_types=1);

namespace Src\Company\CompanyManagement\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;

class QboExpenseTypeEloquentModel extends Model
{
    protected $table = 'expense_types';

    protected $fillable = [
        'name',
        'quick_book_id',
        'xero_id',
        'company_id'
    ];
}
