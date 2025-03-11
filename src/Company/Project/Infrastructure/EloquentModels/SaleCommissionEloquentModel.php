<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class SaleCommissionEloquentModel extends Model
{
    
    protected $table = 'sale_commissions';

    protected $fillable = [
        'sale_report_id',
        'user_id',
        'commission_percent'
    ];

    public function saleReport()
    {
        return $this->belongsTo(SaleReportEloquentModel::class, 'sale_report_id');
    }

    public function user()
    {
        return $this->belongsTo(UserEloquentModel::class, 'user_id');
    }

}
