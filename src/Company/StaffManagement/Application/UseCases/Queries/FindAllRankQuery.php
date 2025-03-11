<?php

namespace Src\Company\StaffManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\StaffManagement\Domain\Repositories\RankRepositoryInterface;

class FindAllRankQuery implements QueryInterface
{
    private RankRepositoryInterface $repository;

    public function __construct()
    {
        $this->repository = app()->make(RankRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getRanks();
    }
}