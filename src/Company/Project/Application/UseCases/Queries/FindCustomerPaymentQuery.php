<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\CustomerPaymentRepositoryInterface;

class FindCustomerPaymentQuery implements QueryInterface
{
    private CustomerPaymentRepositoryInterface $repository;

    public function __construct(
        private readonly int $saleReportId
    )
    {
        $this->repository = app()->make(CustomerPaymentRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getBySaleReportId($this->saleReportId);
    }
}