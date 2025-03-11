<?php

namespace Src\Company\CustomerManagement\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\CustomerManagement\Domain\Repositories\RejectedReasonRepositoryInterface;

class FindRejectedReasonQuery implements QueryInterface
{
    private RejectedReasonRepositoryInterface $repository;


    public function __construct(
        private readonly int $id
    )
    {
        $this->repository = app()->make(RejectedReasonRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findRejectedReason($this->id);
    }
}
