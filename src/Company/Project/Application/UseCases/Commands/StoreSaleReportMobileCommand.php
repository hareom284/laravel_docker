<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\SaleReportMobileRepositoryInterface;

class StoreSaleReportMobileCommand implements CommandInterface
{
    private SaleReportMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId
    )
    {
        $this->repository = app()->make(SaleReportMobileRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->projectId);
    }
}