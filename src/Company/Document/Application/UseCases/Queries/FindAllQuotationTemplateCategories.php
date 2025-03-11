<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\QuotationTemplateCategoryRepositoryInterface;

class FindAllQuotationTemplateCategories implements QueryInterface
{
    private QuotationTemplateCategoryRepositoryInterface $repository;

    public function __construct(
    )
    {
        $this->repository = app()->make(QuotationTemplateCategoryRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findAllQuotationTemplateCategories();
    }
}