<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\QuotationTemplateCategoryRepositoryInterface;

class FindSalespersonQuotationTemplateCategories implements QueryInterface
{
    private QuotationTemplateCategoryRepositoryInterface $repository;

    public function __construct(
        private readonly int $user_id
    )
    {
        $this->repository = app()->make(QuotationTemplateCategoryRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findSalespersonQuotationTemplateCategory($this->user_id);
    }
}