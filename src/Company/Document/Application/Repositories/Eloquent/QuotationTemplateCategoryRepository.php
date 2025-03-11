<?php

namespace Src\Company\Document\Application\Repositories\Eloquent;

use Src\Company\Document\Application\DTO\QuotationTemplateCategoryData;
use Src\Company\Document\Application\Mappers\QuotationTemplateCategoryMapper;
use Src\Company\Document\Domain\Model\Entities\QuotationTemplateCategory;
use Src\Company\Document\Domain\Repositories\QuotationTemplateCategoryRepositoryInterface;
use Src\Company\Document\Domain\Resources\QuotationTemplateCategoryResource;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateCategoryEloquentModel;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplatesEloquentModel;

class QuotationTemplateCategoryRepository implements QuotationTemplateCategoryRepositoryInterface
{

    public function findAllQuotationTemplateCategories()
    {
        $quotationTemplateCategoryEloquent = QuotationTemplateCategoryEloquentModel::query()
        ->whereNull('salesperson_id')
        ->get();

        $quotationTemplateCategories = QuotationTemplateCategoryResource::collection($quotationTemplateCategoryEloquent);

        return $quotationTemplateCategories;
    }


    public function store(QuotationTemplateCategory $quotationTemplateCategory): QuotationTemplateCategoryData
    {
        $quotationTemplateCategoryEloquent = QuotationTemplateCategoryMapper::toEloquent($quotationTemplateCategory);

        $quotationTemplateCategoryEloquent->save();


        return QuotationTemplateCategoryData::fromEloquent($quotationTemplateCategoryEloquent);
    }

    public function findQuotationTemplateCategory($id)
    {
        $quotationTemplateCategory = QuotationTemplateCategoryEloquentModel::with('fromTransitions')->find($id);
        return new QuotationTemplateCategoryResource($quotationTemplateCategory);
    }

    public function update(QuotationTemplateCategory $quotationTemplateCategory): QuotationTemplateCategoryData
    {
        $quotationTemplateCategoryEloquent = QuotationTemplateCategoryMapper::toEloquent($quotationTemplateCategory);

        $quotationTemplateCategoryEloquent->save();

        return QuotationTemplateCategoryData::fromEloquent($quotationTemplateCategoryEloquent);
    }


    public function delete(int $quotationTemplateCategoryId): void
    {
        $quotationTemplateCategoryEloquent = QuotationTemplateCategoryEloquentModel::query()->findOrFail($quotationTemplateCategoryId);

        $quotationTemplateCategoryEloquent->delete();
    }

    public function findSalespersonQuotationTemplateCategory($user_id)
    {
        $quotationTemplateCategoryEloquent = QuotationTemplateCategoryEloquentModel::query()
        ->where('salesperson_id', $user_id)
        ->get();

        $quotationTemplateCategories = QuotationTemplateCategoryResource::collection($quotationTemplateCategoryEloquent);

        return $quotationTemplateCategories;
    }

    public function moveTemplate($data, $template_id)
    {
        $template = QuotationTemplatesEloquentModel::find($template_id);
        if($template){
            $template->update([
                'quotation_template_category_id' => $data['quotation_template_category_id'] ?? null
            ]);
        }
    }

}
