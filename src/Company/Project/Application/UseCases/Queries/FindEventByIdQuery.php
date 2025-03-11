<?php

namespace Src\Company\Project\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Project\Application\DTO\EventData;
use Src\Company\Project\Domain\Repositories\EventRepositoryInterface;

class FindEventByIdQuery implements QueryInterface
{
    private EventRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(EventRepositoryInterface::class);
    }

    public function handle(): EventData
    {
        // authorize('findEventById', ProjectPolicy::class);
        return $this->repository->findById($this->id);
    }
}