<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\QuotationTemplateCategoryData;
use Src\Company\Document\Domain\Model\Entities\QuotationTemplateCategory;

interface QuotationTemplateCategoryRepositoryInterface
{

    public function findAllQuotationTemplateCategories();

    public function store(QuotationTemplateCategory $quotationTemplateCategory): QuotationTemplateCategoryData;

    public function update(QuotationTemplateCategory $quotationTemplateCategory): QuotationTemplateCategoryData;

    public function findQuotationTemplateCategory($id);

    public function delete(int $quotationTemplateCategoryId): void;

    public function findSalespersonQuotationTemplateCategory($user_id);

    public function moveTemplate($data, $template_id);

}
