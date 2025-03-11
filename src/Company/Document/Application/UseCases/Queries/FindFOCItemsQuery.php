<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\FOCRepositoryInterface;

class FindFOCItemsQuery implements QueryInterface
{
    private FOCRepositoryInterface $repository;

    public function __construct(
        private readonly int $projectId
    )
    {
        $this->repository = app()->make(FOCRepositoryInterface::class);
    }

    public function handle()
    {
        return $this->repository->getFOCItems($this->projectId);
    }
}