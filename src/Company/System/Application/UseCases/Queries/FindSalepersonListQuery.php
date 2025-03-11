<?php

namespace Src\Company\System\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\System\Application\DTO\UserData;
use Src\Company\System\Domain\Repositories\UserRepositoryInterface;

class FindSalepersonListQuery implements QueryInterface
{
    private UserRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(UserRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findCustomerBySalepersonId', SystemPolicy::class);
        return $this->repository->getSalepersonList($this->filters);
    }
}