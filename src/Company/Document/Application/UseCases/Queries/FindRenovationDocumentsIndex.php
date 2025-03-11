<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;

class FindRenovationDocumentsIndex implements QueryInterface
{
    private RenovationDocumentInterface $repository;

    public function __construct(
        private readonly int $project_id,
        private readonly string $type
    )
    {
        $this->repository = app()->make(RenovationDocumentInterface::class);
    }

    public function handle()
    {
        // authorize('findAllDesignWork', DocumentPolicy::class);
        return $this->repository->getRenovationDocumentsIndex($this->project_id, $this->type);
    }
}