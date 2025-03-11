<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Src\Company\Project\Infrastructure\EloquentModels\ProjectEloquentModel;
use Src\Company\StaffManagement\Infrastructure\EloquentModels\StaffEloquentModel;

class EvoEloquentModel extends Model
{
  use SoftDeletes;

  protected $table = 'evos';

  protected $fillable = [
    'version_number',
    'total_amount',
    'grand_total',
    'salesperson_signature',
    'customer_signature',
    'signed_date',
    'additional_notes',
    'project_id',
    'pdf_file'
  ];

  public function evo_items()
  {
    return $this->hasMany(EvoItemEloquentModel::class, 'evo_id');
  }

  public function salesperson(): BelongsTo
  {
    return $this->belongsTo(StaffEloquentModel::class, 'signed_by_salesperson_id');
  }

  public function projects(): BelongsTo
  {
    return $this->belongsTo(ProjectEloquentModel::class, 'project_id');
  }
  public function customer_signatures(): HasMany
  {
    return $this->hasMany(EvoSignatureEloquentModel::class, 'evo_id');
  }
}
