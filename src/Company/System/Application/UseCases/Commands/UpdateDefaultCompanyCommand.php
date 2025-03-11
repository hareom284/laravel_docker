<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;

class UpdateDefaultCompanyCommand implements CommandInterface
{
    private CompanyRepositoryInterface $repository;

    public function __construct(private readonly int $id)
    {
        $this->repository = app()->make(CompanyRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        return $this->repository->updateDefaultCompany($this->id);
    }
}