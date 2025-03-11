<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\RenovationDocumentInterface;

class GetPendingRenoDocQuery implements QueryInterface
{
    private RenovationDocumentInterface $repository;

    public function __construct(
        private readonly array $filters
    )
    {
        $this->repository = app()->make(RenovationDocumentInterface::class);
    }

    public function handle()
    {
        // authorize('findAllDesignWork', DocumentPolicy::class);
        return $this->repository->getPendingRenoDoc($this->filters);
    }
}