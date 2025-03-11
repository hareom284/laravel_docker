<?php

declare(strict_types=1);

namespace Src\Company\System\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Src\Company\CustomerManagement\Infrastructure\EloquentModels\CustomerEloquentModel;

class CampaignAudiencesEloquentModel extends Model
{

    protected $table = 'campaign_audiences';

    protected $fillable = ['secret', 'customer_id', 'read_at', 'campaign_id'];

    // public function customer()
    // {
    //     return $this->belongsTo(CustomerEloquentModel::class,'customer_id','id');
    // }

    public function campaign()
    {
        return $this->belongsTo(CampaignEloquentModel::class,'campaign_id','id');
    }

    public function customer()
    {
        return $this->hasOne(CustomerEloquentModel::class,'customer_id');
    }

}
