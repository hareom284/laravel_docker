<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Entities\Contract;
use Src\Company\Project\Domain\Repositories\TermAndConditionRepositoryInterface;

class StoreTermAndConditionSignaturesMobileCommand implements CommandInterface
{
    private TermAndConditionRepositoryInterface $repository;

    public function __construct(
        private readonly Contract $contract,
    )
    {
        $this->repository = app()->make(TermAndConditionRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('storeRole', UserPolicy::class);
        return $this->repository->storeTermAndConditionSignatures($this->contract);
    }
}
