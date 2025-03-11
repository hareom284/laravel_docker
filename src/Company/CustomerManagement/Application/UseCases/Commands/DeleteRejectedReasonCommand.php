<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Repositories\RejectedReasonRepositoryInterface;

class DeleteRejectedReasonCommand implements CommandInterface
{
    private RejectedReasonRepositoryInterface $repository;

    public function __construct(
        private readonly int $rejected_reason_id
    )
    {
        $this->repository = app()->make(RejectedReasonRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('deleteCompany', UserPolicy::class);
        return $this->repository->delete($this->rejected_reason_id);
    }
}
