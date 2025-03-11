<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\CancellationRepositoryInterface;

class GetCountListsCNQuery implements QueryInterface
{
    private CancellationRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(CancellationRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findFolderById', DocumentPolicy::class);
        return $this->repository->getCountLists($this->projectId);
    }
}