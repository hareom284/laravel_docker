<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;

class IncreaseQuotationNoCommand implements CommandInterface
{
    private CompanyRepositoryInterface $repository;

    public function __construct(
        private readonly ?int $company_id
    )
    {
        $this->repository = app()->make(CompanyRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->increaseQuotationNo($this->company_id);
    }
}
