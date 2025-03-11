<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Repositories\CompanyMobileRepositoryInterface;

class IncreaseQuotationNoMobileCommand implements CommandInterface
{
    private CompanyMobileRepositoryInterface $repository;

    public function __construct(
        private readonly ?int $company_id
    )
    {
        $this->repository = app()->make(CompanyMobileRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->increaseQuotationNo($this->company_id);
    }
}
