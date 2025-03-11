<?php

declare(strict_types=1);

namespace Src\Company\Document\Infrastructure\EloquentModels;

use Illuminate\Database\Eloquent\Model;
use Src\Company\UserManagement\Infrastructure\EloquentModels\UserEloquentModel;

class QuotationTemplateCategoryEloquentModel extends Model
{

    protected $table = 'quotation_template_categories';

    protected $fillable = [
        'salesperson_id',
        'name',
    ];

    public function quotationTemplates()
    {
        return $this->hasMany(QuotationTemplatesEloquentModel::class, 'quotation_template_category_id');
    }

    public function salesperson()
    {
        return $this->belongsTo(UserEloquentModel::class, 'salesperson_id');
    }
}
