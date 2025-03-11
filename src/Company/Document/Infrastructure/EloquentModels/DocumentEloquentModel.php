<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;

class DocumentEloquentModel extends Model
{
    use SoftDeletes;
    
    protected $table = 'documents';

    protected $fillable = [
        'title',
        'document_file',
        'allow_customer_view',
        'file_type',
        'folder_id',
        'project_id',
        'date'
    ];

    public function folder(): BelongsTo
    {
        return $this->belongsTo(FolderEloquentModel::class,'folder_id','id');
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
