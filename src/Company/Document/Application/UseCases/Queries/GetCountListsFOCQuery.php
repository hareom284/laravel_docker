<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\FOCRepositoryInterface;
use Src\Company\Project\Domain\Repositories\ProjectRepositoryInterface;

class GetCountListsFOCQuery implements QueryInterface
{
    private FOCRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(FOCRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findFolderById', DocumentPolicy::class);
        return $this->repository->getCountLists($this->projectId);
    }
}