<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\DocumentStandardRepositoryInterface;

class DeleteDocumentStandardCommand implements CommandInterface
{
    private DocumentStandardRepositoryInterface $repository;

    public function __construct(
        private readonly int $document_standard_id
    )
    {
        $this->repository = app()->make(DocumentStandardRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('delete', DocumentPolicy::class);
        return $this->repository->delete($this->document_standard_id);
    }
}