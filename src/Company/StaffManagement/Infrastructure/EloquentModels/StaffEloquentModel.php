<?php

declare(strict_types=1);

namespace Src\Company\StaffManagement\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\RankEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\SalepersonMonthlyKpiEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\SalepersonYearlyKpiEloquentModel;

class StaffEloquentModel extends Model
{
    use HasFactory;

    protected $table = 'staffs';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'rank_id',
        'signature',
        'registry_no',
        'mgr_id',
        'rank_updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(UserEloquentModel::class,'user_id','id');
    }

    public function mgr()
    {
        return $this->belongsTo(UserEloquentModel::class,'mgr_id','id');
    }

    public function rank()
    {
        return $this->belongsTo(RankEloquentModel::class,'rank_id','id');
    }

    public function saleperson_yearly_kpi_records(): HasOne
    {
        return $this->hasOne(SalepersonYearlyKpiEloquentModel::class,'saleperson_id');
    }

    public function saleperson_monthly_kpi_records(): HasOne
    {
        return $this->hasOne(SalepersonMonthlyKpiEloquentModel::class,'saleperson_id');
    }

    public function customers()
    {
        return $this->belongsToMany(CustomerEloquentModel::class,'salespersons_customers','salesperson_uid','customer_uid');
    }

}
