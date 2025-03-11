<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Illuminate\Http\Request;
use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;

class UpdateMarkedSaleReportCommand implements CommandInterface
{
    private SaleReportRepositoryInterface $repository;

    public function __construct(
        private readonly int $saleReportId,
        private readonly Request $request
    )
    {
        $this->repository = app()->make(SaleReportRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->markedSaleReport($this->saleReportId, $this->request);
    }
}