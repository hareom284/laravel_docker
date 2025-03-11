<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;

class StoreSaleReportCommand implements CommandInterface
{
    private SaleReportRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId
    )
    {
        $this->repository = app()->make(SaleReportRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->store($this->projectId);
    }
}