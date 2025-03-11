<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\CancellationRepositoryInterface;

class FindCancellationItemsQuery implements QueryInterface
{
    private CancellationRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId
    )
    {
        $this->repository = app()->make(CancellationRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getCancellationItems($this->projectId);
    }
}