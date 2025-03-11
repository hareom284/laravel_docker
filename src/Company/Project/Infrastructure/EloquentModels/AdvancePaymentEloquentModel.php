<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class AdvancePaymentEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'advance_payments';

    protected $fillable = [
        'title',
        'amount',
        'payment_date',
        'remark',
        'status',
        'user_id',
        'sale_report_id',
    ];

    public function saleReport(): BelongsTo
    {
        return $this->belongsTo(SaleReportEloquentModel::class, 'sale_report_id', 'id');
    }

    public function salePerson(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'user_id','id');
    }
}
