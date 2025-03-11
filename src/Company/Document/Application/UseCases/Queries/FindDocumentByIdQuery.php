<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Application\DTO\DocumentData;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\DocumentRepositoryInterface;

class FindDocumentByIdQuery implements QueryInterface
{
    private DocumentRepositoryInterface $repository;

    public function __construct(
        private readonly int $id,
    )
    {
        $this->repository = app()->make(DocumentRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findDocumentById', DocumentPolicy::class);
        return $this->repository->findDocumentById($this->id);
    }
}