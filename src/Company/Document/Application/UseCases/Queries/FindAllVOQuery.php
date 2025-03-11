<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\VariationRepositoryInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class FindAllVOQuery implements QueryInterface
{
    private VariationRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(VariationRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findFolderById', DocumentPolicy::class);
        return $this->repository->index($this->projectId);
    }
}