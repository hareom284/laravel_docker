<?php

namespace Src\Company\System\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\System\Domain\Repositories\UserRepositoryInterface;

class AssignRankToSalepersonCommand implements CommandInterface
{
    private UserRepositoryInterface $repository;

    public function __construct(
        private readonly int $salepersonId,
        private readonly int $rankId
    )
    {
        $this->repository = app()->make(UserRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeCompany', UserPolicy::class);
        return $this->repository->assignRank($this->salepersonId,$this->rankId);
    }
}
