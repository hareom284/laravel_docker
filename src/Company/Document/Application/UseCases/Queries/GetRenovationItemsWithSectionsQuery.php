<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;

class GetRenovationItemsWithSectionsQuery implements QueryInterface
{
    private RenovationDocumentInterface $repository;

    public function __construct(
        private readonly int $projectId
    )
    {
        $this->repository = app()->make(RenovationDocumentInterface::class);
    }

    public function handle()
    {
        return $this->repository->getRenovationItemWithSections($this->projectId);
    }
}