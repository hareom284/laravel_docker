<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\CustomerManagement\Domain\Repositories\RejectedReasonRepositoryInterface;

class UpdateRejectedReasonOrderCommand implements CommandInterface
{
    private RejectedReasonRepositoryInterface $repository;

    public function __construct(
        private readonly string $rejectedReasons
    )
    {
        $this->repository = app()->make(RejectedReasonRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('updateRole', UserPolicy::class);
        return $this->repository->updateOrder($this->rejectedReasons);
    }
}
