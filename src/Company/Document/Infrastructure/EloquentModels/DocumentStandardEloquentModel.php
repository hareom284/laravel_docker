<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Src\Company\System\Infrastructure\EloquentModels\CompanyEloquentModel;

class DocumentStandardEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'document_standards';

    protected $fillable = [
        'name',
        'header_text',
        'footer_text',
        'disclaimer',
        'terms_and_conditions',
        'payment_terms',
        'company_id'
    ];

    public function companies(): BelongsTo
    {
        return $this->belongsTo(CompanyEloquentModel::class, 'company_id');
    }

    public function renovation_documents(): HasOne
    {
        return $this->hasOne(RenovationDocumentsEloquentModel::class, 'document_standard_id');
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
