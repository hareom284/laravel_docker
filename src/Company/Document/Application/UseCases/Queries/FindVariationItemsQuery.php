<?php

namespace Src\Company\Document\Application\UseCases\Queries;

use Src\Common\Domain\QueryInterface;
use Src\Company\Document\Domain\Policies\DocumentPolicy;
use Src\Company\Document\Domain\Repositories\VariationRepositoryInterface;

class FindVariationItemsQuery implements QueryInterface
{
    private VariationRepositoryInterface $repository;

    public function __construct(
        private readonly int $project_id,
        private readonly mixed $saleperson_id
    )
    {
        $this->repository = app()->make(VariationRepositoryInterface::class);
    }

    public function handle()
    {
        // authorize('findFolderById', DocumentPolicy::class);
        return $this->repository->getVariationItems($this->project_id, $this->saleperson_id);
    }
}