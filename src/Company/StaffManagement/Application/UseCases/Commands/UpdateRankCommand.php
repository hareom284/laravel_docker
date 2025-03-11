<?php

namespace Src\Company\StaffManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\StaffManagement\Domain\Model\Entities\Rank;
use Src\Company\StaffManagement\Domain\Repositories\RankRepositoryInterface;

class UpdateRankCommand implements CommandInterface
{
    private RankRepositoryInterface $repository;

    public function __construct(
        private readonly Rank $rank,
    )
    {
        $this->repository = app()->make(RankRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('updateRole', UserPolicy::class);
        return $this->repository->update($this->rank);
    }
}
