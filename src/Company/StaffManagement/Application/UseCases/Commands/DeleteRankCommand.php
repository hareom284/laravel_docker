<?php

namespace Src\Company\StaffManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\StaffManagement\Domain\Repositories\RankRepositoryInterface;

class DeleteRankCommand implements CommandInterface
{
    private RankRepositoryInterface $repository;

    public function __construct(
        private readonly int $rankId
    )
    {
        $this->repository = app()->make(RankRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteRole', UserPolicy::class);
        return $this->repository->delete($this->rankId);
    }
}
