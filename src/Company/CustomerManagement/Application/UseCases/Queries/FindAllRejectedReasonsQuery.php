<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\RejectedReasonRepositoryInterface;

class FindAllRejectedReasonsQuery implements QueryInterface
{
    private RejectedReasonRepositoryInterface $repository;

    public function __construct(
    )
    {
        $this->repository = app()->make(RejectedReasonRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findAllRejectedReason();
    }
}
