<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\QuotationTemplateCategoryRepositoryInterface;

class FindQuotationTemplateCategory implements QueryInterface
{
    private QuotationTemplateCategoryRepositoryInterface $repository;

    public function __construct(
        private readonly int $quotation_category_id
    )
    {
        $this->repository = app()->make(QuotationTemplateCategoryRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findQuotationTemplateCategory($this->quotation_category_id);
    }
}