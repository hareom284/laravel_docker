<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\AdvancePaymentRepositoryInterface;

class FindAdvancePaymentBySaleReportIdQuery implements QueryInterface
{
    private AdvancePaymentRepositoryInterface $repository;

    public function __construct(
        private readonly int $saleReportId
    )
    {
        $this->repository = app()->make(AdvancePaymentRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getBySaleReportId($this->saleReportId);
    }
}