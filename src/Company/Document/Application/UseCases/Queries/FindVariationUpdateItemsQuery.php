<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\VariationRepositoryInterface;

class FindVariationUpdateItemsQuery implements QueryInterface
{
    private VariationOrderMobileRepositoryInterface $repository;

    public function __construct(
        private readonly int $document_id,
        private readonly mixed $saleperson_id
    )
    {
        $this->repository = app()->make(VariationOrderMobileRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findFolderById', DocumentPolicy::class);
        return $this->repository->getUpdateVariationItems($this->document_id, $this->saleperson_id);
    }
}