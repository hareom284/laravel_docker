<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\SaleReportRepositoryInterface;

class SignSaleReportCommand implements CommandInterface
{
    private SaleReportRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
        private readonly array $data
    )
    {
        $this->repository = app()->make(SaleReportRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->signSaleReport($this->id, $this->data);
    }
}