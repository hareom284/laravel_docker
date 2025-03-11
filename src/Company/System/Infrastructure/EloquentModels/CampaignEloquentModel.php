<?php

declare(strict_types=1);

namespace Src\Company\System\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CampaignEloquentModel extends Model
{

    protected $table = 'campaigns';

    protected $fillable = ['title', 'content'];

    // public function customer()
    // {
    //     return $this->belongsTo(CustomerEloquentModel::class,'customer_id','id');
    // }

    public function campaignAudiences()
    {
        return $this->hasMany(CampaignAudiencesEloquentModel::class, 'campaign_id');
    }

    public function scopeFilter($query, $filters)
    {

        if (isset($filters['order'])) {
            if (isset($filters['name'])) {
                $query->where(function ($query) use ($filters) {
                    $query->where('title', 'like', '%' . $filters['name'] . '%');
                });
                $query->orderBy('title', $filters['order']);
            }
            if (isset($filters['created_at'])) {
                $query->whereDate('created_at', $filters['created_at']);
            }
        } else {
            if (isset($filters['name'])) {
                $query->where(function ($query) use ($filters) {
                    $query->where('title', 'like', '%' . $filters['name'] . '%');
                });
            }
            if (isset($filters['created_at'])) {
                $query->whereDate('created_at', $filters['created_at']);
            }
        }
    }
}
