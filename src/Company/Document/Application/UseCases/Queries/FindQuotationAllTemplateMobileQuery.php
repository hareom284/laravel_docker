<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;


use Src\Company\Document\Domain\Repositories\QuotationTemplateItemsRepositoryMobileInterface;

class FindQuotationAllTemplateMobileQuery implements QueryInterface
{
    private QuotationTemplateItemsRepositoryMobileInterface $repository;

    public function __construct(
        private readonly mixed $salepersonId
    )
    {
        $this->repository = app()->make(QuotationTemplateItemsRepositoryMobileInterface::class);
    }

    public function handle()
    {
        return $this->repository->retrieveAllTemplate($this->salepersonId);
    }
}
