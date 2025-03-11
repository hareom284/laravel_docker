<?php

namespace Src\Company\Project\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Project\Domain\Repositories\TermAndConditionRepositoryInterface;

class DeleteTermAndConditionCommand implements CommandInterface
{
    private TermAndConditionRepositoryInterface $repository;

    public function __construct(
        private readonly int $termAndConditionId
    )
    {
        $this->repository = app()->make(TermAndConditionRepositoryInterface::class);
    }

    public function execute()
    {
        // authorize('deleteRole', UserPolicy::class);
        return $this->repository->delete($this->termAndConditionId);
    }
}
