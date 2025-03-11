<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Domain\Repositories\CompanyRepositoryInterface;

class FindCompanyByIdQuery implements QueryInterface
{
    private CompanyRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(CompanyRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findById($this->id);
    }
}