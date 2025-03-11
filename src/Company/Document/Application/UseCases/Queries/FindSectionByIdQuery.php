<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\SectionData;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\SectionRepositoryInterface;

class FindSectionByIdQuery implements QueryInterface
{
    private SectionRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(SectionRepositoryInterface::class);
    }

    public function handle(): SectionData
    {
        // authorize('findFolderById', DocumentPolicy::class);
        return $this->repository->findSectionById($this->id);
    }
}