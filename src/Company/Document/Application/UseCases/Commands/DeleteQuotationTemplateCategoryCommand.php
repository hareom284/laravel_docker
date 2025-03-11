<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Repositories\QuotationTemplateCategoryRepositoryInterface;

class DeleteQuotationTemplateCategoryCommand implements CommandInterface
{
    private QuotationTemplateCategoryRepositoryInterface $repository;

    public function __construct(
        private readonly int $quotation_template_category_id
    )
    {
        $this->repository = app()->make(QuotationTemplateCategoryRepositoryInterface::class);
    }

    public function execute()
    {

        return $this->repository->delete($this->quotation_template_category_id);
    }
}