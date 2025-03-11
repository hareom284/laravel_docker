<?php

namespace Src\Company\Document\Application\Mappers;
use Illuminate\Http\Request;
use Src\Company\Document\Domain\Model\Entities\QuotationTemplateCategory;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateCategoryEloquentModel;

class QuotationTemplateCategoryMapper
{
    public static function fromRequest(Request $request, ?int $quotatoinTemplateCategory_id = null): QuotationTemplateCategory
    {
        return new QuotationTemplateCategory(
            id: $quotatoinTemplateCategory_id,
            name: $request->string('name'),
            salesperson_id: $request->filled('salesperson_id') ? $request->integer('salesperson_id') : null
        );
    }

    public static function fromEloquent(QuotationTemplateCategoryEloquentModel $quotationTemplateCategory): QuotationTemplateCategory
    {
        return new QuotationTemplateCategory(
            id: $quotationTemplateCategory->id,
            name: $quotationTemplateCategory->name,
            salesperson_id: $quotationTemplateCategory->salesperson_id,
        );
    }

    public static function toEloquent(QuotationTemplateCategory $quotatoinTemplateCategory): QuotationTemplateCategoryEloquentModel
    {
        $quotationTemplateCategoryEloquent = new QuotationTemplateCategoryEloquentModel();
        if ($quotatoinTemplateCategory->id) {
            $quotationTemplateCategoryEloquent = QuotationTemplateCategoryEloquentModel::query()->findOrFail($quotatoinTemplateCategory->id);

        }
        $quotationTemplateCategoryEloquent->name = $quotatoinTemplateCategory->name;
        $quotationTemplateCategoryEloquent->salesperson_id = $quotatoinTemplateCategory->salesperson_id;

        return $quotationTemplateCategoryEloquent;
    }
}
