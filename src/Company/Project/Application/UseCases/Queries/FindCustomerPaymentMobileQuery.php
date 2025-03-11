<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\CustomerPaymentMobileRepositoryInterface;

class FindCustomerPaymentMobileQuery implements QueryInterface
{
    private CustomerPaymentMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $saleReportId
    )
    {
        $this->repository = app()->make(CustomerPaymentMobileRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getBySaleReportId($this->saleReportId);
    }
}