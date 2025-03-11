<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;


class PaymentTermEloquentModel extends Model
{

    protected $table = 'payment_terms';
    protected $fillable = ['name', 'payment_terms', 'project_id', 'is_default'];

    public function scopeFilter($query, $filters)
    {
        $query->when($filters['is_default'] ?? false, function ($query) {
            $query->where('is_default', 1);
        });

        $query->when($filters['project_id'] ?? false, function ($query, $project_id) {
            $query->orWhere('project_id', $project_id);
        });

        $query->when(!isset($filters['is_default']) && !isset($filters['project_id']), function ($query) {
            $query->whereNull('project_id');
        });
        $query->orderBy('is_default', 'desc');
    }
}
