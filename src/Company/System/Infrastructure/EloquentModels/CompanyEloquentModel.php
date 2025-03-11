<?php

declare(strict_types=1);

namespace Src\Company\System\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Src\Company\CompanyManagement\Infrastructure\EloquentModels\QuickBookEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentStandardEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\KpiRecordEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

class CompanyEloquentModel extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;
    
    protected $table = 'companies';
    protected $appends = ['qr_code'];
    
    protected $fillable = [
        'name',
        'tel',
        'fax',
        'email',
        'main_office',
        'design_branch_studio',
        'hdb_license_no',
        'reg_no',
        'gst_reg_no',
        'gst',
        'logo',
        'company_stamp',
        'docu_prefix',
        'invoice_no_start',
        'fy_start',
        'fy_end',
        'is_default',
        'accounting_software_company_id',
        'project_no',
        'quotation_no',
        'customer_invoice_running_number_values',
        'enable_customer_running_number_by_month',
        'quotation_prefix',
        'invoice_prefix'
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(ProjectEloquentModel::class, 'company_id');
    }

    public function document_standards(): HasOne
    {
        return $this->hasOne(DocumentStandardEloquentModel::class, 'company_id');
    }

    public function kpi_records()
    {
        return $this->hasOne(KpiRecordEloquentModel::class,'user_id');
    }

    public function quickbookCredentials()
    {
        return $this->hasOne(QuickBookEloquentModel::class, 'company_id', 'id');
    }

    public function scopeFilter($query, $filters)
    {
        if (isset($filters['name'])) {
            $query->where('name', 'like','%' . $filters['name']. '%');
        }
        if (isset($filters['reg_no'])) {
            $query->where('reg_no', '=', $filters['reg_no']);
        }
        if (isset($filters['email'])) {
            $query->where('email', 'like','%' . $filters['email']. '%');
        }
        if (isset($filters['tel'])) {
            $query->where('tel', '=', $filters['tel']);
        }
    }

    public function getQrCodeAttribute()
    {
        return $this->getFirstMediaUrl('qr_code');
    }
}
