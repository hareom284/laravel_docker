<?php

namespace Src\Company\Document\Application\UseCases\Commands;

use Src\Common\Domain\CommandInterface;
use Src\Company\Document\Domain\Model\Document;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\DocumentRepositoryInterface;

class UpdateDocumentCommand implements CommandInterface
{
    private DocumentRepositoryInterface $repository;

    public function __construct(
        private readonly Document $document
    )
    {
        $this->repository = app()->make(DocumentRepositoryInterface::class);
    }

    public function execute(): mixed
    {
        // authorize('update', DocumentPolicy::class);
        return $this->repository->update($this->document);
    }
}