<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\QuotationTemplateCategoryRepositoryInterface;

class StoreQuotationTemplateCategoryCommand implements CommandInterface
{
    private QuotationTemplateCategoryRepositoryInterface $repository;

    public function __construct(
        private readonly mixed $quotationTemplateCategory
    )
    {
        $this->repository = app()->make(QuotationTemplateCategoryRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->quotationTemplateCategory);
    }
}