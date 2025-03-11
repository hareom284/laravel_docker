<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleReportEloquentModel extends Model implements HasMedia
{
    use InteractsWithMedia;
    
    protected $table = 'sale_reports';

    protected $fillable = [
        'total_cost',
        'total_sales',
        'comm_issued',
        'special_discount',
        'gst',
        'rebate',
        'net_profit_and_loss',
        'carpentry_job_amount',
        'carpentry_cost',
        'carpentry_comm',
        'carpentry_special_discount',
        'net_profit',
        'paid',
        'remaining',
        'project_id',
        'file_status',
    ];
    protected $appends = ['document_file', 'manager_signature', 'salesperson_signature'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id', 'id');
    }

    public function customer_payments(): HasMany
    {
        return $this->hasMany(CustomerPaymentEloquentModel::class,'sale_report_id');
    }

    public function supplier_credits(): HasMany
    {
        return $this->hasMany(SupplierCreditEloquentModel::class,'sale_report_id');
    }

    public function saleCommissions()
    {
        return $this->hasMany(SaleCommissionEloquentModel::class, 'sale_report_id');
    }

    public function getDocumentFileAttribute()
    {
        return $this->getMedia('document_file')->mapWithKeys(function ($media) {
            return [$media->file_name => $media->getUrl()];
        })->toArray();
    }    

    public function getManagerSignatureAttribute()
    {
        return $this->getFirstMediaUrl('manager_signatures') ?? null;
    }  

    public function getSalespersonSignatureAttribute()
    {
        return $this->getFirstMediaUrl('salesperson_signatures') ?? null;
    }  

}
