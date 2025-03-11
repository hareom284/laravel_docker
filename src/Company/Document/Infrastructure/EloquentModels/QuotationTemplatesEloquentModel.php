<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationTemplatesEloquentModel extends Model
{
    use SoftDeletes;

    protected $table = 'quotation_templates';

    protected $fillable = [
        'salesperson_id',
        'quotation_template_category_id',
        'name',
    ];

    public function category()
    {
        return $this->belongsTo(QuotationTemplateCategoryEloquentModel::class, 'quotation_template_category_id');
    }
}
