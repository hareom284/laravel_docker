<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\ReferrerFormRepositoryInterface;

class FindReferrerFormQuery implements QueryInterface
{
    private ReferrerFormRepositoryInterface $repository;


    public function __construct(
        private readonly int $id
    )
    {
        $this->repository = app()->make(ReferrerFormRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findReferrerForm($this->id);
    }
}
