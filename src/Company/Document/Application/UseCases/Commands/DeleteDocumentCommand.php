<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Document;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\DocumentRepositoryInterface;

class DeleteDocumentCommand implements CommandInterface
{
    private DocumentRepositoryInterface $repository;

    public function __construct(
        private readonly int $document_id
    )
    {
        $this->repository = app()->make(DocumentRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('delete', DocumentPolicy::class);
        return $this->repository->delete($this->document_id);
    }
}