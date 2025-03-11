<?php

namespace Src\Company\Document\Domain\Repositories;

use Src\Company\Document\Application\DTO\QuotationTemplateItemsData;
use Src\Company\Document\Domain\Model\Entities\QuotationTemplateItems;
use Src\Company\Document\Infrastructure\EloquentModels\QuotationTemplateItemsEloquentModel;

interface QuotationTemplateItemsRepositoryMobileInterface
{
    public function getQuotationItems($templateId);

    public function store(QuotationTemplateItems $quotationTemplateItems): QuotationTemplateItemsData;

    public function templateStore($quotationTemplate);

    public function salepersonTemplateStore($quotationTemplate);

    public function duplicateTemplate($request);

    public function retrieveAllTemplate($salepersonId);

    public function retrieveTemplate($templateId);

    public function update(QuotationTemplateItems $quotationTemplateItems): QuotationTemplateItemsEloquentModel;

    public function delete(int $document_standard_id): void;

    public function createTemplate($quotationTemplate);

    public function updateTemplate($quotationTemplate);
}
