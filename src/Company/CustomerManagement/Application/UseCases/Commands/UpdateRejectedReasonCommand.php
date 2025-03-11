<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Model\Entities\RejectedReason;
use Src\Company\CustomerManagement\Domain\Repositories\RejectedReasonRepositoryInterface;

class UpdateRejectedReasonCommand implements CommandInterface
{
    private RejectedReasonRepositoryInterface $repository;

    public function __construct(
        private readonly RejectedReason $rejectedReason
    )
    {
        $this->repository = app()->make(RejectedReasonRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('updateRole', UserPolicy::class);
        return $this->repository->update($this->rejectedReason);
    }
}
