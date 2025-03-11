<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\HDBFormsData;
use Src\Company\Document\Domain\Repositories\HDBFormsRepositoryInterface;

class FindHDBFormsByIdQuery implements QueryInterface
{
    private HDBFormsRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(HDBFormsRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findFolderById', DocumentPolicy::class);
        return $this->repository->findHDBFormsById($this->id);
    }
}