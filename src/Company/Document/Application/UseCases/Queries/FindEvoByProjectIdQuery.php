<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\EvoRepositoryInterface;

class FindEvoByProjectIdQuery implements QueryInterface
{
    private EvoRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId,
    )
    {
        $this->repository = app()->make(EvoRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->findEvoByProjectId($this->projectId);
    }
}