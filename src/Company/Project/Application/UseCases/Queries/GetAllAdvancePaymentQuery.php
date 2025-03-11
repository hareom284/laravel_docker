<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Domain\Repositories\AdvancePaymentRepositoryInterface;

class GetAllAdvancePaymentQuery implements QueryInterface
{
    private AdvancePaymentRepositoryInterface $repository;

    public function __construct(
        private readonly array $filters = [],
    )
    {
        $this->repository = app()->make(AdvancePaymentRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getAll($this->filters);
    }
}