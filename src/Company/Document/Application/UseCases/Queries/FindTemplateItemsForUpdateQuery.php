<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;

class FindTemplateItemsForUpdateQuery implements QueryInterface
{
    private RenovationDocumentInterface $repository;

    public function __construct(
        private readonly int $document_id
    )
    {
        $this->repository = app()->make(RenovationDocumentInterface::class);
    }

    public function handle()
    {
        // authorize('findFolderById', DocumentPolicy::class);
        return $this->repository->findTemplateItemsForUpdate($this->document_id);
    }
}