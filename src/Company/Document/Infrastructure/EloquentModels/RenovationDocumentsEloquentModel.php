<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class RenovationDocumentsEloquentModel extends Model implements HasMedia
{
    use SoftDeletes, InteractsWithMedia;

    protected $table = 'renovation_documents';

    protected $fillable = [
        'project_id',
        'document_standard_id',
        'type',
        'version_number',
        'disclaimer',
        'special_discount_percentage',
        'total_amount',
        'signed_date',
        'salesperson_signature',
        'signed_by_salesperson_id',
        'updated_by_user',
        'customer_signature',
        'pdf_file',
        'payment_terms',
        'printable_pdf',
        'status',
        'agreement_no',
        'remarks'
    ];

    public function projects(): BelongsTo
    {
        return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
    }

    public function salesperson(): BelongsTo
    {
        return $this->belongsTo(StaffEloquentModel::class, 'signed_by_salesperson_id');
    }

    public function updatedUsers(): BelongsTo
    {
        return $this->belongsTo(UserEloquentModel::class, 'updated_by_user');
    }

    public function renovation_items(): HasMany
    {
        return $this->hasMany(RenovationItemsEloquentModel::class, 'renovation_document_id');
    }

    public function document_standard(): BelongsTo
    {
        return $this->belongsTo(DocumentStandardEloquentModel::class, 'document_standard_id');
    }

    public function customer_signatures(): HasMany
    {
        return $this->hasMany(RenovationDocumentSignaturesEloquentModel::class, 'renovation_document_id');
    }

    public function renovation_sections(): HasMany
    {
        return $this->hasMany(RenovationSectionsEloquentModel::class, 'document_id');
    }

    public function approvers()
    {
        return $this->belongsToMany(UserEloquentModel::class, 'document_approvers', 'renovation_document_id', 'user_id')
        ->withPivot(['created_at', 'updated_at'])
        ->select(['users.id', 'users.first_name', 'users.last_name']);
    }


}
