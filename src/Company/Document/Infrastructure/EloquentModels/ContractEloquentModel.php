<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\Project\Infrastructure\EloquentModels\TermAndConditionSignatureEloquentModel;

class ContractEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'contracts';

    protected $fillable = [
        'date',
        'contract_sum',
        'contractor_payment',
        'owner_signature',
        'contractor_signature',
        'employer_witness_name',
        'contractor_days',
        'termination_days',
        'contractor_witness_name',
        'project_id',
        'name',
        'nric',
        'company',
        'law',
        'address',
        'pdpa_authorization',
        'pdpa_pdf_file',
        'contract_pdf_file',
        'is_already_signed'
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
    }

    public function termAndConditionSignatures() 
    {
        return $this->hasMany(TermAndConditionSignatureEloquentModel::class, 'contract_id');
    }

    public function setIsAlreadySignedAttribute($value)
    {
        $this->attributes['is_already_signed'] = $value ?? false;
    }
    
    public function scopeFilter($query, $filters)
    {
        $query->when($filters['name'] ?? false, function ($query, $name) {
            $query->where('name', 'like', '%' . $name . '%');
        });
        $query->when($filters['search'] ?? false, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        });
    }
}
