<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\DocumentStandardData;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\DocumentStandardRepositoryInterface;

class FindDocumentStandardByCompanyIdQuery implements QueryInterface
{
    private DocumentStandardRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(DocumentStandardRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findDocumentById', DocumentPolicy::class);
        return $this->repository->findDocumentStandardByCompanyId($this->id);
    }
}