<?php

declare(strict_types=1);

namespace Src\Company\Project\Infrastructure\EloquentModels;

use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\MediaLibrary\InteractsWithMedia;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ContractEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\DocumentEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\HDBFormsEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\DesignWorkEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\TaxInvoiceEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\ContactUserEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ThreeDDesignEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\PurchaseOrderEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ProjectPorfolioEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\ProjectRequirementEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\HandoverCertificateEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\RenovationDocumentsEloquentModel;

class ProjectEloquentModel extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $table = 'projects';

    protected $fillable = [
        'invoice_no',
        'description',
        'collection_of_keys',
        'expected_date_of_completion',
        'completed_date',
        'project_status',
        'company_id',
        'customer_id',
        'property_id',
        'agreement_no',
        'created_by',
        'customer_signed_contract_and_quotation_date',
        'freezed',
        'payment_status',
        'request_note',
        'term_and_condition_id',
        'quickbook_class_id'
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(PropertyEloquentModel::class, 'property_id'); //added by ym
    }

    public function properties(): BelongsTo
    {
        return $this->belongsTo(PropertyEloquentModel::class, 'property_id');
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(CompanyEloquentModel::class, 'company_id'); //remove s
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class); //added by ym to confirm with wy
    }

    public function customers(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'customer_id');
    }

    public function customersPivot(): BelongsToMany
    {
        return $this->belongsToMany(UserEloquentModel::class, 'customer_project', 'project_id', 'user_id')->withPivot('property_id')->with('customers.customer_properties')->withTimestamps();
    }

    public function customerUsers()
    {
        return $this->belongsToMany(UserEloquentModel::class, 'customer_project', 'project_id', 'user_id');

    }

    public function salespersons(): BelongsToMany
    {
        return $this->belongsToMany(UserEloquentModel::class, 'salesperson_projects', 'project_id', 'salesperson_id')->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DocumentEloquentModel::class, 'project_id');
    }

    public function renovation_documents(): HasMany
    {
        return $this->hasMany(RenovationDocumentsEloquentModel::class, 'project_id');
    }

    public function contract(): HasOne
    {
        return $this->hasOne(ContractEloquentModel::class, 'project_id');
    }

    public function projectRequirements(): HasMany
    {
        return $this->hasMany(ProjectRequirementEloquentModel::class, 'project_id');
    }

    public function designWorks(): HasMany
    {
        return $this->hasMany(DesignWorkEloquentModel::class, 'project_id');
    }

    public function contactUser(): HasOne
    {
        return $this->hasOne(ContactUserEloquentModel::class, 'project_id');
    }

    public function hdbForms(): HasMany
    {
        return $this->hasMany(HDBFormsEloquentModel::class, 'project_id');
    }

    public function handoverCertificates(): HasMany
    {
        return $this->hasMany(HandoverCertificateEloquentModel::class, 'project_id');
    }

    public function taxInvoices(): HasMany
    {
        return $this->hasMany(TaxInvoiceEloquentModel::class, 'project_id');
    }

    public function threeDDesigns(): HasMany
    {
        return $this->hasMany(ThreeDDesignEloquentModel::class, 'project_id');
    }

    public function purchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrderEloquentModel::class, 'project_id');
    }

    public function supplierCostings(): HasMany
    {
        return $this->hasMany(SupplierCostingEloquentModel::class, 'project_id');
    }

    public function saleReport(): HasOne
    {
        return $this->hasOne(SaleReportEloquentModel::class, 'project_id');
    }

    public function termAndCondition(): BelongsTo
    {
        return $this->belongsTo(TermAndConditionEloquentModel::class, 'term_and_condition_id');
    }
    
    public function projectPortFolios(): HasMany
    {
        return $this->hasMany(ProjectPorfolioEloquentModel::class, 'project_id');
    }

    public function scopeFilter($query, $filters)
    {
        $query->when($filters['name'] ?? false, function ($query, $name) {
            $query->where('name', 'like', '%' . $name . '%');
        });
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        });

        if (isset($filters['customerName'])) {
            $filterText = $filters['customerName'] ?? '';
            $words = explode(' ', trim($filterText)); // Splitting the input text by spaces

            $query->where(function ($query) use ($words) {
                $query->whereHas('properties', function ($query) use ($words) {
                    $query->where(function ($query) use ($words) {
                        foreach ($words as $word) {
                            $query->orWhere('street_name', 'LIKE', "%{$word}%")
                                    ->orWhere('block_num', 'LIKE', "%{$word}%")
                                    ->orWhere('unit_num', 'LIKE', "%{$word}%")
                                    ->orWhere('postal_code', 'LIKE', "%{$word}%");
                        }
                    });
                })
                ->orWhereHas('customers', function ($query) use ($words) {
                    $query->where(function ($query) use ($words) {
                        foreach ($words as $word) {
                            $query->orWhere('first_name', 'LIKE', "%{$word}%")
                                    ->orWhere('last_name', 'LIKE', "%{$word}%");
                        }
                    });
                })
                ->orWhereHas('salespersons', function ($query) use ($words) {
                    $query->where(function ($query) use ($words) {
                        foreach ($words as $word) {
                            $query->orWhere('first_name', 'LIKE', "%{$word}%")
                                    ->orWhere('last_name', 'LIKE', "%{$word}%");
                        }
                    });
                });
            });
        }

        if (isset($filters['status'])) {

            $query->when($filters['status'] ?? false, function ($query, $status) {
                if($status == 'approve_cancell_project') {
                    $query->where('project_status', 'Cancelled')->orWhere('project_status', 'Pending');
                }else {
                    $query->where('project_status', $status);
                }
            });
        }

        if (isset($filters['postal_code'])) {
            $query->when($filters['postal_code'] ?? false, function ($query, $postal_code) {
                $query->whereHas('properties', function ($query) use ($postal_code) {
                    $query->where('postal_code', $postal_code);
                });
            });
        }

        // making orderBy desc as default in all list view
        // if(isset($filters['cardView']))
        // {
        //     if($filters['cardView'] == 'false'){
                // $query->orderBy('id', 'desc');
        //     }
        // }

    }

}
