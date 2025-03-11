<?php

namespace Src\Company\CompanyManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CompanyManagement\Application\DTO\FAQItemData;
use Src\Company\CompanyManagement\Domain\Repositories\FAQItemRepositoryInterface;

class FindCustomerFAQItemQuery implements QueryInterface
{
    private FAQItemRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(FAQItemRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getCustomerFAQ();
    }
}